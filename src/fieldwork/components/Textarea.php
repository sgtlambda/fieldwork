<?php

namespace fieldwork\components;

class Textarea extends Field
{

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), array(
            'placeholder' => $this->label
        ));
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('textarea', 'fieldwork-inputfield')
        );
    }

    public function getHTML ()
    {
        return "<textarea " . $this->getAttributesString() . ">" . $this->value . "</textarea>";
    }

}