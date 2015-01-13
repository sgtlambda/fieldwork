<?php

namespace jannieforms\validators;

use jannieforms\SynchronizableObject;
use jannieforms\validators;

abstract class AbstractSynchronizableFormValidator extends validators\AbstractFormValidator implements SynchronizableObject
{

    public function getJsonData ()
    {
        $fields = [];
        foreach ($this->inflictsFields as $field)
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