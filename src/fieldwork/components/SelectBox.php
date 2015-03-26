<?php

namespace fieldwork\components;

class SelectBox extends Field
{

    private $options;

    /**
     * The value to return if the "empty" option is selected
     * @var string|null
     */
    private $emptyValue;

    /**
     * Constructs a new SelectBox
     *
     * @param string $slug       The field identifier
     * @param string $label      The label to display
     * @param array  $options    The available options for the field
     * @param string $value      The current value
     * @param string $emptyValue The value to return when an empty option is selected (this is useful for relational
     *                           fields in the database where you should provide NULL )
     * @param int    $storeValueLocally
     */
    public function __construct ($slug, $label, array $options = array(), $value = '', $emptyValue = '', $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $value, $storeValueLocally);
        $this->options    = $options;
        $this->emptyValue = $emptyValue;
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array(
                'select',
                'selectbox')
        );
    }

    public function getHTML ()
    {
        $r = "<select " . $this->getAttributesString() . ">";
        foreach ($this->options as $v => $l) {
            $selected = $this->value === $v ? " selected=\"selected\"" : "";
            $r .= "<option value=\"$v\"$selected>$l</option>";
        }
        $r .= "</select>";
        return $r;
    }

    public function getValue ()
    {
        if ($this->value === "")
            return $this->emptyValue;
        return parent::getValue();
    }
}