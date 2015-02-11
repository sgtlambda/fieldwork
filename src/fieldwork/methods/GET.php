<?php

namespace fieldwork\methods;

class GET extends Method
{

    public function getFormAttributes ()
    {
        return array('method' => 'GET');
    }

    public function getValue ($key, $default)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function hasValue ($key)
    {
        return isset($_GET[$key]);
    }

}