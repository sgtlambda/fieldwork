<?php

namespace jannieforms\sanitizers;

use jannieforms\Synchronizable;

abstract class FieldSanitizer implements Synchronizable
{

    public abstract function sanitize ($value);

    public abstract function isLive ();

    public function isRealtime ()
    {
        return true;
    }

    public function getJsonData ()
    {
        return [
            'method'   => $this->describeObject(),
            'live'     => $this->isLive(),
            'realtime' => $this->isRealtime()
        ];
    }

}