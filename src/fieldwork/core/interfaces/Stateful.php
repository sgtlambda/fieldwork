<?php

namespace fieldwork\core\interfaces;

use fieldwork\core\State;

interface Stateful
{

    /**
     * @param Request $request
     *
     * @return State
     */
    public function getState (Request $request);
}