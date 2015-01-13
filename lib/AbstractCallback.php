<?php

namespace jannieforms;

abstract class AbstractCallback
{

    private $slug;

    public function __construct ($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug ()
    {
        return $this->slug;
    }

    /**
     * Performs an action based on given form values
     *
     * @param FormData $form
     */
    public abstract function run (FormData $form);
}