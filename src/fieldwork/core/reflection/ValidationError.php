<?php

namespace fieldwork\core\reflection;

use fieldwork\core\interfaces\Stateful;

class ValidationError
{

    /**
     * @var Stateful
     */
    private $target;

    /**
     * ValidationError constructor.
     * @param Stateful $target
     */
    public function __construct (Stateful $target)
    {
        $this->target = $target;
    }

    /**
     * @return Stateful
     */
    public function getTarget ()
    {
        return $this->target;
    }
}