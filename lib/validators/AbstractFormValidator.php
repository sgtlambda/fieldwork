<?php

namespace jannieforms\validators;

use jannieforms\Form;

abstract class AbstractFormValidator
{

    private   $isValid                   = true;
    protected $inflictsFields, $errorMsg = '';

    /**
     * @param string $errorMsg       Error message to show if this validator returns false
     * @param array  $inflictsFields Array of names of fields to be marked "invalid" upon rendering the form if this
     *                               validator evaluates to false
     */
    public function __construct ($errorMsg, array $inflictsFields = array())
    {
        $this->errorMsg       = $errorMsg;
        $this->inflictsFields = $inflictsFields;
    }

    public function getErrorMsg ()
    {
        return $this->errorMsg;
    }

    public function process (Form $form)
    {
        if (($this->isValid = $this->validate($form)))
            return true;
        else {
            foreach ($this->inflictsFields as $if)
                if (($field = $form->f($if)))
                    $field->forceInvalid();
            return false;
        }
    }

    public function isValid ()
    {
        return $this->isValid;
    }

    public function isLive ()
    {
        return false;
    }

    public abstract function validate (Form $form);
}