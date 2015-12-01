<?php

namespace fieldwork\core\interfaces;

interface Identifiable
{

    /**
     * Gets a unique identifier string for the object
     *
     * @return string
     */
    public function getIdentifier ();
}