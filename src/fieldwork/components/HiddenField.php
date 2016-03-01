<?php

namespace fieldwork\components;

class HiddenField extends Field
{

    public function isVisible ()
    {
        return false;
    }

    public function renderWhenHidden ()
    {
        return self::RM_DEFAULT;
    }

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), [
            "type" => "hidden"
        ]);
    }

    public function getHTML ($showLabel = true)
    {
        return "<input " . $this->getAttributesString() . ">";
    }
}