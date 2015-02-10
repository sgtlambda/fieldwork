<?php

namespace fieldwork\validators;

use fieldwork\Synchronizable;

abstract class FieldValidator implements Synchronizable
{

    private $errorMsg = '', $field;

    public function __construct ($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }

    public function getJsonData ()
    {
        return array(
            'error'  => $this->errorMsg,
            'method' => $this->describeObject()
        );
    }

    public function setField ($f)
    {
        $this->field = $f;
    }

    public function getField ()
    {
        return $this->field;
    }

    public function getErrorMsg ()
    {
        return $this->errorMsg;
    }

    public abstract function isValid ($value);

    /**
     * Whether to skip the remaining field validators
     *
     * @param string $value
     *
     * @return bool
     */
    public function skipValidation ($value)
    {
        return false;
    }

}