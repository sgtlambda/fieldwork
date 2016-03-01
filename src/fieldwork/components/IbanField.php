<?php

namespace fieldwork\components;

use fieldwork\sanitizers;
use fieldwork\validators;

class IbanField extends TextField
{

    /**
     * Creates a new text field and adds IBAN sanitizer and validator
     *
     * @param string     $slug
     * @param string     $label
     * @param string     $value
     * @param int|string $errorMsg         The error message to show if the field value is not valid
     * @param bool       $convertBban      Whether to permit input of bban number (will be converted using openiban API)
     * @param string     $openIbanUsername openiban API username
     * @param string     $openIbanPassword openiban API password
     */
    public function __construct ($slug, $label, $value = '', $errorMsg = validators\IbanFieldValidator::ERROR, $convertBban = false, $openIbanUsername = null, $openIbanPassword = null)
    {
        parent::__construct($slug, $label, $value, 0);
        $this->addSanitizer(new sanitizers\FieldUppercaser());
        $this->addSanitizer(new sanitizers\IbanSanitizer($convertBban ? $openIbanUsername : null, $convertBban ? $openIbanPassword : null));
        $this->addValidator(new validators\IbanFieldValidator($errorMsg));
    }
}