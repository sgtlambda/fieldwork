<?php

namespace jannieforms\components;

class HTMLComponent extends AbstractComponent
{

    private $html;

    public function __construct ($slug, $html)
    {
        parent::__construct($slug);
        $this->html = $html;
    }

    public function getHTML ()
    {
        return $this->html;
    }

    public function isValid ()
    {
        return true;
    }

}