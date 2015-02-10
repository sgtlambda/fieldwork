<?php

namespace fieldwork\methods;

abstract class Method
{

    public function getFormAttributes ()
    {
        return array();
    }

    public abstract function getValue ($key, $default);

    public abstract function hasValue ($key);
}