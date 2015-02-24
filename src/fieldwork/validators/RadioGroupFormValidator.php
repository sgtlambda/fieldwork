<?php

namespace fieldwork\validators;

use fieldwork\components\RadioSelect;
use fieldwork\Form;

class RadioGroupFormValidator extends SynchronizableFormValidator
{

    private $select;

    public function __construct ($errorMsg, RadioSelect $select)
    {
        parent::__construct($errorMsg, array($select));
        $this->select = $select;
    }

    public function validate (Form $form)
    {
        return $this->select->getValue();
    }

    public function describeObject ()
    {
        return 'radio';
    }

}