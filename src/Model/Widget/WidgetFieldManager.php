<?php

declare(strict_types = 1);

namespace Dcabrejas\WidgetFileUploader\Model\Widget;

use Magento\Widget\Model\Widget\Instance;

class WidgetFieldManager
{
    /**
     * @var Instance
     */
    private $widget;

    /**
     * @var string
     */
    private $fieldName;

    public function __construct(Instance $widget, string $fieldName)
    {
        $this->widget = $widget;
        $this->fieldName = $fieldName;
    }

    /**
     * Get value from the widget's original data array
     */
    public function getOrigValue(): ?string
    {
        $origParameters = $this->widget->getOrigData('widget_parameters');
        $origParameters = json_decode($origParameters, true);

        if (!isset($origParameters[$this->fieldName])) {
            return null;
        }

        return (string) $origParameters[$this->fieldName];
    }

    /**
     * Get value from the widget's data array
     */
    public function getValue(): ?string
    {
        $parameters = $this->widget->getWidgetParameters();
        return $parameters[$this->fieldName] ?? null;
    }

    /**
     * Set value on the widget's data array
     */
    public function setValue(string $value) : WidgetFieldManager
    {
        $parameters = $this->widget->getWidgetParameters();
        $parameters[$this->fieldName] = $value;
        $this->widget->setWidgetParameters($parameters);
        return $this;
    }

    /**
     * Replace the value from the current data array with the value in the original array
     */
    public function useOriginal() : WidgetFieldManager
    {
        return $this->setValue($this->getOrigValue());
    }

    /**
     * Does the widget contain a value in the original data array?
     */
    public function hasOriginalValue() : bool
    {
        return !empty($this->getOrigValue());
    }

    /**
     * Has the value changed?
     */
    public function hasValueChanged() : bool
    {
        return $this->getOrigValue() !== $this->getValue();
    }
}
