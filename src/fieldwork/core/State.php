<?php

namespace fieldwork\core;

/**
 * High-level object state container
 */
class State
{

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct ($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue ()
    {
        return $this->value;
    }
}