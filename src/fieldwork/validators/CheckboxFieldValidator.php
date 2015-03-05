<?php

namespace fieldwork\validators;

class CheckboxFieldValidator extends FieldValidator
{

    private $checked;

    public function __construct ($checked, $errorMsg)
    {
        parent::__construct($errorMsg);
        $this->checked = $checked;
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'checked' => $this->checked
        ));
    }

    public function isValid ($value)
    {
        return $this->checked === ($value === 'on');
    }

    public function describeObject ()
    {
        return 'checkbox';
    }

}
