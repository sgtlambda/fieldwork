<?php

namespace jannieforms;

/**
 * Static class used to globally register and retrieve forms
 * @package jannieforms
 */
class JF
{

    private static $forms       = array();
    private static $ajaxMethods = array();

    /**
     * Registers given form globally
     *
     * @param Form $form
     */
    static function registerForm (Form $form)
    {
        if (!in_array($form, self::$forms))
            array_push(self::$forms, $form);
    }

    /**
     * Registers given ajax method globally.
     *
     * @param AbstractCallback $ajaxMethod
     */
    static function registerAjaxMethod (AbstractCallback $ajaxMethod)
    {
        self::$ajaxMethods[$ajaxMethod->getSlug()] = $ajaxMethod;
    }

    /**
     * Retrieves a form by its slug
     *
     * @param string $slug
     *
     * @return Form|null form or null if not found
     */
    static function getForm ($slug)
    {
        foreach (self::$forms as $form)
            /* @var $form Form */
            if ($form->getGlobalSlug() == $slug)
                return $form;
        return null;
    }

    /**
     * Retrieves an ajax method by its slug
     *
     * @param string $slug
     *
     * @return AbstractCallback ajaxmethod
     */
    static function getAjaxMethod ($slug)
    {
        return self::$ajaxMethods[$slug];
    }

    /**
     * Lists slugs of all ajax methods
     * @return array
     */
    static function listAjaxMethods ()
    {
        return array_keys(self::$ajaxMethods);
    }

    /**
     * If any of your form callbacks uses header redirects, make sure to call before_content() and after_content()
     * before output starts and after output ends.
     */
    static function before_content ()
    {
        ob_start();
    }

    /**
     * If any of your form callbacks uses header redirects, make sure to call before_content() and after_content()
     * before output starts and after output ends.
     */
    static function after_content ()
    {
        ob_end_flush();
    }

}