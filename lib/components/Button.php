<?php

namespace jannieforms\components;

class Button extends AbstractField
{

    const TYPE_SUBMIT = "submit";
    const TYPE_BUTTON = "button";

    private $type, $icon, $glyphIcon, $isClicked = false, $title = "", $use_shim = true;

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

    /**
     * Sets whether the button uses a shim display node
     *
     * @param boolean $use_shim
     *
     * @return \JannieButton
     */
    public function setUseShim ($use_shim = true)
    {
        $this->use_shim = $use_shim;
        return $this;
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

    public function restoreValue ($method, $sanitize = true)
    {
        $this->isClicked = $method->hasValue($this->getName());
    }

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), array(
            'type'  => $this->type,
            'value' => $this->label
        ));
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('button', $this->use_shim ? 'invisible-target-button' : 'target-button')
        );
    }

    public function getHTML ()
    {
        $icon      = ($this->icon == '' ? '' : '<img class="button-icon" src="' . $this->icon . '"> ');
        $glyphIcon = !empty($this->glyphIcon) ? '<i class="button-icon icon-' . $this->glyphIcon . '"></i> ' : '';
        if ($this->use_shim)
            return
                "<div class=\"button-wrap " . ($this->use_shim ? "uses-shim" : "") . "\">"
                . "<input " . $this->getAttributesString() . ">"
                . "<a id=\"target-" . $this->getId() . "\" " . (!empty($this->title) ? " title=\"{$this->title}\" " : "") . " class=\"small_button targeting-button " . implode(' ', $this->getCustomClasses()) . "\">" . $icon . $glyphIcon . $this->label .
                "</a>"
                . "</div>";
        else
            return
                "<input " . $this->getAttributesString() . ">";
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'isButton' => true
        ));
    }

}