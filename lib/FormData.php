<?php

namespace jannieforms;

interface FormData
{
    /**
     * Searches form fields and returns its value
     *
     * @param string $query   id to search for
     * @param string $default default value
     *
     * @return string value of closest match or default value if field not found
     */
    public function v ($query, $default = '');
}