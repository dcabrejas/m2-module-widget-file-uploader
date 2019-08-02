<?php

namespace Dcabrejas\WidgetFileUploader\Plugin\Widget;

use Magento\Backend\Block\Widget\Form as FormBlock;
use Magento\Framework\Registry;
use Magento\Widget\Model\Widget\Instance;

class FormEnctypePlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $widgetCodes;

    public function __construct(Registry $registry, array $widgetCodes = [])
    {
        $this->registry = $registry;
        $this->widgetCodes = $widgetCodes;
    }
    /**
     * Set the form enctype to multipart/form-data to allow for file uploads
     *
     * @param FormBlock $formBlock
     * @return null
     */
    public function beforeGetFormHtml(FormBlock $formBlock)
    {
        $widgetInstance = $this->registry->registry('current_widget_instance');
        if ($widgetInstance instanceof Instance && in_array($widgetInstance->getCode(), $this->widgetCodes)) {
            $formBlock->getForm()->setData('enctype', 'multipart/form-data');
        }
        return null;
    }
}
