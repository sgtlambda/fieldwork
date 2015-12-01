<?php

namespace fieldwork\core;

abstract class Component
{

    /**
     * @var bool
     */
    private $active = true;

    /**
     * @var CompoundComponent|null
     */
    protected $parent = null;

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