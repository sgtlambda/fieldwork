<?php

namespace fieldwork\components;

class DateTimePicker extends TextField
{

    var $dtConfig;

    public function __construct ($slug, $label, $value = '', $dtConfig = array(), $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $value, $storeValueLocally);
        $this->dtConfig = $dtConfig;
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'dtConfig' => $this->dtConfig
        ));
    }

}