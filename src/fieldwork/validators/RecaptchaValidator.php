<?php

namespace fieldwork\validators;

use fieldwork\components\Recaptcha;

class RecaptchaValidator extends FieldValidator
{

    private $recaptchaField;

    const ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct ($errorMsg, Recaptcha $recaptchaField)
    {
        parent::__construct($errorMsg);
        $this->recaptchaField = $recaptchaField;
    }

    public function isValid ($value)
    {
        $response = \Requests::post(self::ENDPOINT, array(), array(
            'secret'   => $this->recaptchaField->getSecretKey(),
            'response' => $value,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ));
        $data     = json_decode($response->body, true);
        return $data !== null ? $data['success'] : false;
    }

    /**
     * Returns a short human-readable slug/string describing the object
     * @return string
     */
    function describeObject ()
    {
        return 'recaptcha';
    }
}