<?php

namespace fieldwork\components;

class TextField extends Field
{

    const ON_ENTER_NEXT   = 'next';
    const ON_ENTER_SUBMIT = 'submit';

    private $onEnter = '',
        $mask = null;

    public function getAttributes ()
    {
        $att = array();
        if (!empty($this->onEnter))
            $att['data-input-action-on-enter'] = $this->onEnter;
        if ($this->mask !== null)
            $att['data-input-mask'] = $this->mask;
        return array_merge(parent::getAttributes(), $att);
    }

    public function onEnter ($action = '')
    {
        $this->onEnter = $action;
        return $this;
    }

    /**
     * Sets input mask for this field
     *
     * @param string $mask
     *
     * @return static
     */
    public function setMask ($mask)
    {
        $this->mask = $mask;
        return $this;
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('textfield', 'fieldwork-inputfield')
        );
    }

    public function getHTML ($showLabel = true)
    {
        return sprintf("<div class=\"input-field\"><input type='text' %s><label for=\"%s\">%s</label></div>",
            $this->getAttributesString(),
            $this->getId(),
            $this->label);
    }

}