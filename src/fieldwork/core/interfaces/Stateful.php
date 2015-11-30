<?php

namespace fieldwork\core\interfaces;

use fieldwork\core\State;

interface Stateful
{

    public function getIdentifier ();

    /**
     * @param Request $request
     *
     * @return State
     */
    public function getState (Request $request);
}