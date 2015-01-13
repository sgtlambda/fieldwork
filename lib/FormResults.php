<?php

namespace jannieforms;

use jannieforms\components\Field;

class FormResults implements FormData
{

    private $values = array();

    public function __construct ($values)
    {
        $this->values = $values;
    }

    /**
     * Searches form fields

     *
*@param string $query id to search for

     *
*@return bool|Field closest match or false if not found
     */
    public function f ($query)
    {
        $minLength = -1;
        $f         = false;
        foreach ($this->values as $field => $value)
            if (preg_match("/^(.*)" . preg_quote($query) . "$/", $field, $matches)) {
                $l = strlen($matches[1]);
                if ($l < $minLength || $minLength == -1) {
                    $minLength = $l;
                    $f         = $field;
                }
            }
        return $f;
    }

    /**
     * Searches form fields and returns its value
     *
     * @param string $query   id to search for
     * @param string $default default value
     *
     * @return string value of closest match or default value if field not found
     */
    public function v ($query, $default = '')
    {
        $f = $this->f($query);
        if ($f != false)
            return $this->values[$f];
        else
            return $default;
    }

}