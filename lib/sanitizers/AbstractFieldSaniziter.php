<?php

namespace jannieforms\sanitizers;

use jannieforms\SynchronizableObject;

abstract class AbstractFieldSaniziter implements SynchronizableObject
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