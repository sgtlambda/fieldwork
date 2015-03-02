<?php

namespace fieldwork\components;

class Checkbox extends Field
{

    const CHECKED = 'checked';

    const ON = 'on';

    /**
     * Constructs a new checkbox
     *
     * @param string $slug
     * @param string $label   The string to display next to the checkbox
     * @param bool   $checked Whether the checkbox should be checked initially. Defaults to false
     * @param int    $storeValueLocally
     */
    public function __construct ($slug, $label, $checked = false, $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $checked ? self::ON : '', $storeValueLocally);
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array('checkbox')
        );
    }

    /**
     * Is the checkbox checked in its current state?
     * @return bool
     */
    public function isChecked ()
    {
        return $this->value === self::ON;
    }

    public function getAttributes ()
    {
        $attrs = array(
            'type'  => 'checkbox',
            'value' => self::ON
        );
        if ($this->isChecked())
            $attrs[self::CHECKED] = self::CHECKED;
        return array_merge(parent::getAttributes(), $attrs);
    }

    public function getHTML ()
    {
        return "<div class=\"checkbox-wrapper\"><input " . $this->getAttributesString() . "><label for=\"" . $this->getId() . "\">" . $this->label . "</label></div>";
    }

    protected function getRestoreDefault ()
    {
        return '';
        // override this so that the checkbox is not read as "checked" when the post var is not sent along
    }
}