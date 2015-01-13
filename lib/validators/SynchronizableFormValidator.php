<?php

namespace jannieforms\validators;

use jannieforms\components\Field;
use jannieforms\Synchronizable;
use jannieforms\validators;

abstract class SynchronizableFormValidator extends FormValidator implements Synchronizable
{

    public function getJsonData ()
    {
        $fields = [];
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