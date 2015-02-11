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

    function getJsonData ();

    /**
     * Returns a short human-readable slug/string describing the object
     * @return string
     */
    function describeObject ();

}