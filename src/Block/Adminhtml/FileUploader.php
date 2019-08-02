<?php

namespace Dcabrejas\WidgetFileUploader\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Layout\Generator\Block as BlockGenerator;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Backend\Block\Template\Context;

class FileUploader extends Template
{
    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var BlockGenerator
     */
    private $blockGenerator;

    public function __construct(
        Context $context,
        Factory $elementFactory,
        BlockGenerator $blockGenerator,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
        $this->blockGenerator = $blockGenerator;
    }

    /**
     * File uploader field
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $input = $this->elementFactory->create("file", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        if ($name = $this->getData('name')) {
            $input->setName($name);
        }
        if ($element->getRequired() && !$element->getEscapedValue()) {
            $input->addClass('required-entry');
        }
        $element->setData('after_element_html', $input->getElementHtml());
        return $element;
    }
}
