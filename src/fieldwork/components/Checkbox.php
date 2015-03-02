<?php

namespace fieldwork\components;

class Checkbox extends Field
{

    const CHECKED = 'checked';

    const ON = 'on';

    public function __construct ($slug, $label, $checked, $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $checked ? self::ON : '', $storeValueLocally);
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('checkbox')
        );
    }

    public function isChecked ()
    {
        return $this->value === self::ON;
    }

    public function getAttributes ()
    {
        $attrs = array(
            'type'  => 'checkbox',
            'value' => self::ON
        );
        if ($this->isChecked())
            $attrs[self::CHECKED] = self::CHECKED;
        return array_merge(parent::getAttributes(), $attrs);
    }

    public function getHTML ()
    {
        return "<div class=\"checkbox-wrapper\"><input " . $this->getAttributesString() . "><label for=\"" . $this->getId() . "\">" . $this->label . "</label></div>";
    }

}