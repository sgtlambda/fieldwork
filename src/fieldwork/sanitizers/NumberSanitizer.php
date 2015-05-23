<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */

namespace fieldwork\sanitizers;

class NumberSanitizer extends FieldSanitizer
{

    /**
     * @var string[] Indicates characters that should be interpreted as radix point
     * @link http://en.wikipedia.org/wiki/Radix_point
     */
    private $radixes;

    /**
     * Indicates the precision factor
     * @var float
     */
    private $precision;

    /**
     * @param string[]|null $radixes   Indicates characters that should be interpreted as decimal seperators. By default, points and commas will be accepted.
     * @param float         $precision Indicates the precision factor
     */
    function __construct ($radixes = null, $precision = 0.01)
    {
        $this->radixes   = $radixes ?: ['.', ','];
        $this->precision = $precision;
    }

    public function sanitize ($value)
    {
        if ($value === '')
            return '';

        $radixEscaped = implode('', array_map(function ($radix) {
            return preg_quote($radix);
        }, $this->radixes));
        $primaryRadix = $this->radixes[0];

        // Remove all character other than 0-9 and the radixes string
        $value = preg_replace('/[^0-9' . $radixEscaped . ']+/', '', $value);

        $floatval        = floatval(preg_replace('/[' . $radixEscaped . ']+/', '.', $value));
        $roundedFloatVal = round($floatval / $this->precision) * $this->precision;

        return str_replace('.', $primaryRadix, $roundedFloatVal);
    }

    public function isLive ()
    {
        return true;
    }

    /**
     * Returns a short human-readable slug/string describing the object
     * @return string
     */
    function describeObject ()
    {
        return 'number';
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'radixes'   => $this->radixes,
            'precision' => $this->precision
        ));
    }
}