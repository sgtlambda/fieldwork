<?php

namespace fieldwork\components;

/**
 * Displays a Google ReCaptcha field
 * @package fieldwork\components
 */
class Recaptcha extends Field
{

    private $siteKey;
    private $secretKey;

    /**
     * Creates a new recaptcha field with the provided keys
     * @param string $label
     * @param string $siteKey
     * @param string $secretKey
     */
    function __construct ($label, $siteKey, $secretKey)
    {
        parent::__construct('recaptcha', $label);
        $this->siteKey   = $siteKey;
        $this->secretKey = $secretKey;
    }

    public function getAttributes ()
    {
        $attributes = array_merge(parent::getAttributes(), array(
            'data-sitekey' => $this->siteKey
        ));
        unset($attributes['value']);
        unset($attributes['name']);
        return $attributes;
    }

    public function getClasses ()
    {
        return array_merge(parent::getClasses(), array(
            'g-recaptcha'
        ));
    }

    /**
     * @return string
     */
    public function getSiteKey ()
    {
        return $this->siteKey;
    }

    /**
     * @return string
     */
    public function getSecretKey ()
    {
        return $this->secretKey;
    }

    /**
     * Gets the HTML markup of the component
     *
     * @param bool $showLabel
     *
     * @return mixed
     */
    public function getHTML ($showLabel = true)
    {
        return sprintf('<div %s></div>', $this->getAttributesString());
    }

    public function getName ($asMarkup = false)
    {
        return 'g-recaptcha-response';
    }
}