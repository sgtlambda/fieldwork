<?php

namespace jannieforms;

/**
 * Represents an object which properties are shared with the clientside app through JSON. Requires a javascript
 * implementation in order to provide any actual functionality
 * @package jannieforms
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