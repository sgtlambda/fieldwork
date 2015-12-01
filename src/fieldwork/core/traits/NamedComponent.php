<?php

namespace fieldwork\core\traits;

use fieldwork\core\Component;

trait NamedComponent
{

    /**
     * @var string
     */
    private $name;

    public function __construct ($name)
    {
        $this->name = $name;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function getIdentifier ()
    {
        $parts  = [];
        $target = $this;
        while ($target instanceof Component) {
            if ($target instanceof NamedComponent)
                array_unshift($parts, $target->getName());
            $target = $target->getParent();
        }
        return join('-', $parts);
    }
}