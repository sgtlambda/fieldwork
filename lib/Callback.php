<?php

namespace fieldwork;

abstract class Callback
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

    /**
     * Function added in to maintain compatibility with legacy callbacks
     *
     * @param FormData $formData
     */
    function __invoke (FormData $formData)
    {
        $this->run($formData);
    }

}