<?php

namespace fieldwork\core\traits;

use fieldwork\core\Component;
use fieldwork\core\CompoundComponent;
use fieldwork\core\CompoundState;
use fieldwork\core\State;

trait CompoundOperator
{

    /**
     * Performs operations on each of the substates contained within the provided state.
     *
     * @param State    $state
     * @param callable $typeCheck
     * @param callable $callback
     */
    private function doCompoundOperation (State $state, callable $typeCheck, callable $callback)
    {
        if ($this instanceof CompoundComponent && $state instanceof CompoundState) {
            $scanQueue = [];
            $addQueue  = function (CompoundState $state, array $children) use (&$scanQueue) {
                $scanQueue = array_merge($scanQueue, array_map(function (Component $child) use ($state) {
                    return [$child, $state->getChild($child)];
                }, $children));
            };
            $addQueue($this->getChildren(false));
            while (list($component, $subState) = array_shift($scanQueue)) {
                if (call_user_func($typeCheck, $component))
                    call_user_func($callback, $component, $subState);
                elseif ($component instanceof CompoundComponent)
                    $addQueue($component->getChildren(false));
            }
        }
    }
}