<?php

namespace fieldwork\core\interfaces;

/**
 * Represents an object whose properties are shared with the client through JSON.
 * Requires a javascript implementation in order to provide any functionality.
 */
interface Symmetrical
{

    /**
     * Gets the JSON data array that will be passed to the front-end library
     *
     * @return array
     */
    function serialize ();
}