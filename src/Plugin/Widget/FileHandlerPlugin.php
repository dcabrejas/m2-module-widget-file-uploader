<?php

namespace Dcabrejas\WidgetFileUploader\Plugin\Widget;

use Magento\Framework\Exception\LocalizedException;
use Magento\Widget\Model\Widget\Instance;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Dcabrejas\WidgetFileUploader\Model\Widget\WidgetFieldManager;

class FileHandlerPlugin
{
    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var WriteFactory
     */
    private $writeFactory;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $relativeStoragePath;

    /**
     * @var string
     */
    private $widgetCode;

    public function __construct(
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        WriteFactory $writeFactory,
        string $fieldName,
        string $widgetCode,
        string $relativeStoragePath
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->writeFactory = $writeFactory;
        $this->fieldName = $fieldName;
        $this->widgetCode = $widgetCode;
        $this->relativeStoragePath = $relativeStoragePath;


        $this->storagePath = $fileSystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath($this->relativeStoragePath);
        $this->relativeStoragePath = $relativeStoragePath;
    }

    /**
     * Handle the file upload upon saving the widget
     *
     * @param Instance $widgetInstance
     * @throws LocalizedException
     */
    public function beforeBeforeSave(Instance $widgetInstance)
    {
        if ($widgetInstance->getCode() !== $this->widgetCode) {
            return;
        }

        $widgetManager = new WidgetFieldManager($widgetInstance, $this->fieldName);

        //if file was not sent, keep the old value.
        if (!$this->wasFileSent()) {
            $widgetManager->useOriginal();
            return;
        }

        //Upload the new PDF
        $uniqId = uniqid();

        $absolutePath = $this->storagePath . $uniqId . DIRECTORY_SEPARATOR;
        $relativePath = $this->relativeStoragePath . $uniqId . DIRECTORY_SEPARATOR;

        $uploader = $this->uploaderFactory->create(['fileId' => $this->fieldName])
            ->setAllowCreateFolders(true)
            ->setAllowedExtensions(['pdf']);

        if (!$uploader->save($absolutePath)) {
            throw new LocalizedException(
                __('File cannot be saved to path: $1', $absolutePath)
            );
        }

        //Set the PDF relative path onto the widget's data to be saved in the DB.
        $widgetManager->setValue($relativePath . $uploader->getUploadedFileName());
    }

    /**
     * Upon saving the widget, clean out unused files from the file system.
     *
     * @param Instance $widgetInstance
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeAfterSave(Instance $widgetInstance)
    {
        if ($widgetInstance->getCode() !== $this->widgetCode) {
            return;
        }

        $widgetManager = new WidgetFieldManager($widgetInstance, $this->fieldName);

        if (!$widgetManager->hasOriginalValue() || !$widgetManager->hasValueChanged()) {
            return;
        }

        //extract unique folder name and delete it
        $pregResult = preg_match(
            "/catalogue\/(?<folder_name>\w+)\//",
            $widgetManager->getOrigValue(),
            $matches
        );

        if ($pregResult !== 1) {
            return;
        }

        $this->writeFactory->create($this->storagePath)
            ->delete($matches['folder_name']);
    }

    /**
     * Identify if a file was uploaded
     *
     * @return bool
     */
    private function wasFileSent() : bool
    {
        return isset($_FILES[$this->fieldName]['name']) && !empty($_FILES[$this->fieldName]['name']);
    }
}
