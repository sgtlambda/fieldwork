<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Identifiable;
use fieldwork\core\interfaces\Request;
use fieldwork\core\interfaces\Stateful;

class CompoundComponent extends Component implements Stateful
{

    /**
     * @var Component[]
     */
    protected $children;

    /**
     * Adds the provided component to this component's list of children
     *
     * @param Component $component
     *
     * @return $this
     */
    public function add (Component $component)
    {
        $this->children[]  = $component;
        $component->parent = $this;
        return $this;
    }

    /**
     * Returns a flattened list of the component's enabled children
     *
     * @param boolean $recursive
     * @param boolean $includeInactiveFields whether to include inactive fields
     *
     * @return Component[]
     */
    public function getChildren ($recursive = true, $includeInactiveFields = false)
    {
        $children = [];
        foreach ($this->children as $component)
            if ($component->isActive() || $includeInactiveFields) {
                array_push($children, $component);
                if ($recursive && $component instanceof CompoundComponent)
                    $children = array_merge($children, $component->getChildren(true, $includeInactiveFields));
            }
        return $children;
    }

    /**
     * @param Request $request
     *
     * @return State
     */
    public function getState (Request $request)
    {
        $compoundState = new CompoundState();
        foreach ($this->children as $child)
            if ($child instanceof Stateful && $child instanceof Identifiable)
                $compoundState->setChild($child, $child->getState($request));
        return $compoundState;
    }
}