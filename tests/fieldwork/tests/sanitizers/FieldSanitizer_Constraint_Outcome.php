<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */


namespace fieldwork\tests\sanitizers;


use fieldwork\sanitizers\FieldSanitizer;

class FieldSanitizer_Constraint_Outcome extends \PHPUnit_Framework_Constraint
{

    /**
     * @var string The expected outcome
     */
    private $expectedOutcome;

    /**
     * @var FieldSanitizer The sanitizer to use
     */
    private $fieldSanitizer;

    function __construct (FieldSanitizer $fieldSanitizer, $expectedOutcome)
    {
        parent::__construct();
        $this->expectedOutcome = $expectedOutcome;
        $this->fieldSanitizer  = $fieldSanitizer;
    }

    protected function matches ($other)
    {
        return $this->fieldSanitizer->sanitize($other) === $this->expectedOutcome;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString ()
    {
        return sprintf("matches the expected sanitization outcome \"%s\"", $this->expectedOutcome);
    }
}