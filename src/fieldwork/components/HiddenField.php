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

    public function getHTML ($showLabel = true)
    {
        return "<input type='hidden'" . $this->getAttributesString() . ">";
    }

}