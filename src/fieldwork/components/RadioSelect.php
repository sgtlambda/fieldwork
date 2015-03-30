<?php

namespace fieldwork\components;

class RadioSelect extends Field
{

    private $options;

    public function __construct ($slug, $label, array $options = array(), $value = '', $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $value, $storeValueLocally);
        $this->options = $options;
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('radios')
        );
    }

    public function getHTML ($showLabel = true)
    {
        $r = "<div id=\"". $this->getId() . "\" class=\"radios-group\"><div class=\"radios-label\">" . $this->getLabel() . "</div>";
        foreach ($this->options as $v => $l) {
            $r .= "<div class=\"radio-option\"><input type=\"radio\" id=\"" . $this->getGlobalSlug() . "-" . $v . "\" name=\"" . $this->getName() . "\" value=\"" . $v . "\" " . ($v == $this->getValue() ? "checked" : "") . "><label for=\"" . $this->getGlobalSlug() . "-" . $v . "\">" . $l . "</label></div>";
        }
        $r .= "</div>";
        return $r;
    }

}