<?php

namespace jannieforms\validators;

use jannieforms\components\RadioSelect;
use jannieforms\Form;

class RadioGroupFormValidator extends AbstractSynchronizableFormValidator
{

    private $select;

    public function __construct ($errorMsg, RadioSelect $select)
    {
        validators\parent::__construct($errorMsg, [$select]);
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