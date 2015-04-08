<?php

namespace fieldwork;

/**
 * Class Synchronizable
 * Represents an object which properties are shared with the clientside app through JSON. Requires a javascript
 * implementation in order to provide any actual functionality
 * @package fieldwork
 */
interface Synchronizable
{

    /**
     * Gets the JSON data array that will be passed to the front-end library
     * @return array
     */
    function getJsonData ();

    /**
     * Returns a short human-readable slug/string describing the object
     * @return string
     */
    function describeObject ();

}