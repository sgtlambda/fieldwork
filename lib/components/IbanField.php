<?php

namespace fieldwork\components;

use fieldwork\sanitizers;
use fieldwork\validators;

class IbanField extends TextField
{

    /**
     * Creates a new text field and adds IBAN sanitizer and validator
     *
     * @param string $slug
     * @param string $label
     * @param string $openIbanUsername openiban API username
     * @param string $openIbanPassword openiban API password
     * @param string $value
     */
    public function __construct ($slug, $label, $openIbanUsername, $openIbanPassword, $value = '')
    {
        components\parent::__construct($slug, $label, $value, 0);
        $this->addSanitizer(new sanitizers\FieldUppercaser());
        $this->addSanitizer(new sanitizers\IbanSanitizer($openIbanUsername, $openIbanPassword));
        $this->addValidator(new validators\IbanFieldValidator());
    }

}