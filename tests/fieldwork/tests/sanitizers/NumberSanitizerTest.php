<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */

namespace fieldwork\tests\sanitizers;

use fieldwork\sanitizers\NumberSanitizer;

class NumberSanitizerTest extends FieldSanitizer_TestCase
{

    public function testSanitization ()
    {
        $this->setSanitizer(new NumberSanitizer());

        $this->assertOutcome('0.a1', '0.1');
        $this->assertOutcome('.1', '0.1');
        $this->assertOutcome('a,777', '0.78');
        $this->assertOutcome('', '');
        $this->assertOutcome('hello world', '');
        $this->assertOutcome('.', '');
        $this->assertOutcome('0.565464', '0.57');
    }

    public function testPrecision ()
    {
        $this->setSanitizer(new NumberSanitizer(null, '5'));

        $this->assertOutcome('7', '5');
        $this->assertOutcome('1-8!!%', '20');
    }
}