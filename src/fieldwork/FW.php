<?php

namespace fieldwork;

/**
 * Static class used to globally register and retrieve forms
 * @package fieldwork
 */
class FW
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
    static function beforeContent ()
    {
        ob_start();
    }

    /**
     * If any of your form callbacks uses header redirects, make sure to call before_content() and after_content()
     * before output starts and after output ends.
     */
    static function afterContent ()
    {
        ob_end_flush();
    }
}