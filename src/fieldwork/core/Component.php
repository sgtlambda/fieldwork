<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Identifiable;

abstract class Component implements Identifiable
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $active = true;

    /**
     * @var CompoundComponent|null
     */
    protected $parent = null;

    public function __construct ($name)
    {
        $this->name = $name;
    }

    /**
     * Sets whether the component is active
     * @param boolean $active
     * @return $this
     */
    public function setActive ($active = true)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Checks whether the component is active
     * @return boolean
     */
    public function isActive ()
    {
        return $this->active;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function getIdentifier ()
    {
        return $this->parent instanceof Component ? $this->parent->getIdentifier() . '-' . $this->name : $this->name;
    }

    /**
     * @return CompoundComponent|null
     */
    public function getParent ()
    {
        return $this->parent;
    }

    /**
     * @return Component
     */
    public function getRoot ()
    {
        return $this->parent instanceof Component ? $this->parent->getRoot() : $this;
    }
}