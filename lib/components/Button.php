<?php

namespace fieldwork\components;

use fieldwork\methods\Method;

class Button extends Field
{

    const TYPE_SUBMIT = "submit";
    const TYPE_BUTTON = "button";

    private $type, $icon, $glyphIcon, $isClicked = false, $title = "";

    public function __construct ($name, $label, $value = "", $type = Button::TYPE_BUTTON)
    {
        parent::__construct($name, $label, $value);
        $this->type        = $type;
        $this->collectData = false;
    }

    public function isClicked ()
    {
        return $this->isClicked;
    }

    public function setTitle ($title = "")
    {
        $this->title = $title;
        return $this;
    }

    public function setIcon ($file)
    {
        $this->icon = $file;
        return $this;
    }

    public function setGlyphIcon ($code)
    {
        $this->glyphIcon = $code;
        return $this;
    }

    public function restoreValue (Method $method, $sanitize = true)
    {
        $this->isClicked = $method->hasValue($this->getName());
    }

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), array(
            'type' => $this->type
        ));
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array(
                'button',
                $this->type === self::TYPE_SUBMIT ? 'btn btn-primary' : 'btn-default'
            )
        );
    }

    public function getHTML ()
    {
        $icon      = ($this->icon == '' ? '' : '<img class="button-icon" src="' . $this->icon . '"> ');
        $glyphIcon = !empty($this->glyphIcon) ? '<i class="button-icon icon-' . $this->glyphIcon . '"></i> ' : '';
        return
            "<button " . $this->getAttributesString() . ">" . $icon . $glyphIcon . $this->label . "</button>";
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'isButton' => true
        ));
    }

}