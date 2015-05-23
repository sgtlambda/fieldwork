<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */

namespace fieldwork\tests\sanitizers;

use fieldwork\sanitizers\FieldSanitizer;

class FieldSanitizer_TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FieldSanitizer The sanitizer to test
     */
    private $sanitizer;

    function __construct (FieldSanitizer $sanitizer = null)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * @param FieldSanitizer $sanitizer
     */
    public function setSanitizer ($sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * Asserts that the given input value matches the expected outcome when sanitized using the currently set sanitizer
     * @param string $value
     * @param string $expectedOutcome
     */
    function assertOutcome ($value, $expectedOutcome)
    {
        self::assertThat($value, self::isOutcome($this->sanitizer, $expectedOutcome));
    }

    public static function isOutcome (FieldSanitizer $fieldSanitizer, $expectedOutcome)
    {
        return new FieldSanitizer_Constraint_Outcome($fieldSanitizer, $expectedOutcome);
    }
}