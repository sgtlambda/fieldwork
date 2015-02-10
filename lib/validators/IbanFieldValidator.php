<?php

namespace fieldwork\validators;



class IbanFieldValidator extends RegexFieldValidator
{

    const PATT       = "/^[A-Z]{2}[0-9]{2} [A-Z0-9]{4} [0-9]{4} [0-9]{4}( [0-9]{4})?( [0-9]{2})?$/";
    const PATT_LOCAL = "/^([A-Z]{2}[0-9]{2} [A-Z0-9]{4} [0-9]{4} [0-9]{4}( [0-9]{4})?( [0-9]{2})?|[0-9]{1,10})$/";
    const ERROR      = "Ongeldig rekeningnummer";

    public function __construct ()
    {
        parent::__construct(
            self::PATT, self::ERROR, self::PATT_LOCAL
        );
    }

}
