<?php

namespace fieldwork\components;

class PasswordField extends TextField
{

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('passfield')
        );
    }

    public function getHTML ($showLabel = true)
    {
        return "<input type='password'" . $this->getAttributesString() . ">";
    }

}