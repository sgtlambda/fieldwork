<?php

namespace fieldwork\methods;

class POST extends Method
{

    public function getFormAttributes ()
    {
        return array('method' => 'POST');
    }

    public function getValue ($key, $default)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public function hasValue ($key)
    {
        return isset($_POST[$key]);
    }

}