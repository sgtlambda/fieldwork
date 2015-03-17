<?php

namespace fieldwork\sanitizers;

class RegexpReplaceSanitizer extends RegexReplaceSanitizer
{

    private $regexp, $regexpmod;

    public function __construct ($from, $regexp, $to, $regexpmod = 'g')
    {
        parent::__construct($from, $to);
        $this->regexp    = $regexp;
        $this->regexpmod = $regexpmod;
    }

    public function isLive ()
    {
        return true;
    }

    public function describeObject ()
    {
        return 'regexp';
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'regexp'    => $this->regexp,
            'regexpmod' => $this->regexpmod,
            'replace'   => $this->to
        ));
    }
}