<?php

namespace Dcabrejas\WidgetFileUploader\Plugin\Widget;

use Dcabrejas\WidgetFileUploader\Model\Widget\FileHandler;
use Dcabrejas\WidgetFileUploader\Model\Widget\WidgetFieldManager;

class FilesHandlerPlugin
{
    /**
     * @var array
     */
    private $fileHandlers;

    public function __construct(array $fileHandlers = [])
    {
        $this->fileHandlers = $fileHandlers;
    }

    /**
     * Handle the file upload upon saving the widget
     *
     * @param Instance $widgetInstance
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeBeforeSave(Instance $widgetInstance)
    {
        foreach ($this->fileHandlers as $fileHandler) {
            if ($fileHandler instanceof FileHandler) {
                $fileHandler->handleBeforeSave($widgetInstance);
            }
        }
    }

    /**
     * Upon saving the widget, clean out unused files from the file system.
     *
     * @param Instance $widgetInstance
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeAfterSave(Instance $widgetInstance)
    {
        foreach ($this->fileHandlers as $fileHandler) {
            if ($fileHandler instanceof FileHandler) {
                $fileHandler->handleAfterSave($widgetInstance);
            }
        }
    }
}
