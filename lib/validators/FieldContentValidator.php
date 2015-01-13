<?php

namespace jannieforms\validators;



class FieldContentValidator extends RegexFieldValidator
{

    public function __construct ($error = "Verplicht veld.")
    {
        parent::__construct("/./", $error);
    }

}