<?php

namespace jannieforms\components;

class HiddenField extends AbstractField
{

    public function isVisible ()
    {
        return false;
    }

    public function renderWhenHidden ()
    {
        return self::RM_DEFAULT;
    }

    public function getHTML ()
    {
        return "<input type='hidden'" . $this->getAttributesString() . ">";
    }

}