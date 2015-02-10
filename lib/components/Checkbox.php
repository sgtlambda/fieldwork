<?php

namespace fieldwork\components;

class Checkbox extends Field
{

    const CHECKED = 'checked';

    public function __construct ($slug, $label, $value = '', $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $value, $storeValueLocally);
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('checkbox')
        );
    }

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), array(
            'type'  => 'checkbox',
            'value' => 'on'
        ));
    }

    public function getHTML ()
    {
        return "<div class=\"checkbox-wrapper\"><input " . $this->getAttributesString() . "><label for=\"" . $this->getId() . "\">" . $this->label . "</label></div>";
    }

}