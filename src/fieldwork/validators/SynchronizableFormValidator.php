<?php

namespace fieldwork\validators;

use fieldwork\components\Field;
use fieldwork\Synchronizable;
use fieldwork\validators;

abstract class SynchronizableFormValidator extends FormValidator implements Synchronizable
{

    public function getJsonData ()
    {
        $fields = array();
        foreach ($this->inflictsFields as $field)
            /* @var $field Field */
            $fields[] = $field->getName();
        return array(
            'inflictedFields' => $fields,
            'error'           => $this->errorMsg,
            'method'          => $this->describeObject()
        );
    }

    public function isLive ()
    {
        return true;
    }

}