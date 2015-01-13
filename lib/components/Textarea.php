<?php

namespace jannieforms\components;

class Textarea extends AbstractField
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
            parent::getClasses(), array('textarea', 'jannieinputfield')
        );
    }

    public function getHTML ()
    {
        return "<textarea " . $this->getAttributesString() . ">" . $this->value . "</textarea>";
    }

}