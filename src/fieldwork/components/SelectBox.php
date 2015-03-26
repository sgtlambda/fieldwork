<?php

namespace fieldwork\components;

/**
 * Select2 powered select box
 * @package fieldwork\components
 */
class SelectBox extends Field
{

    const SELECT2_OPTION_PLACEHOLDER                = "placeholder";
    const SELECT2_OPTION_ALLOW_CLEAR                = "allowClear";
    const SELECT2_OPTION_MINIMUM_RESULTS_FOR_SEARCH = "minimumResultsForSearch";

    private $options;

    /**
     * The value to return if the "empty" option is selected
     * @var string|null
     */
    private $emptyValue;

    /**
     * Array of options passed to the select2 constructor as a javascript object
     * @var array
     */
    private $select2options;

    /**
     * @var boolean
     */
    private $usePlaceholder;

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
        $this->options        = $options;
        $this->emptyValue     = $emptyValue;
        $this->select2options = [];
        $this->setPlaceholder();
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
        if ($this->usePlaceholder) {
            $r .= "<option></option>";
        }
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

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), [
            'select2' => $this->select2options
        ]);
    }

    /**
     * Merges the options
     *
     * @param array $select2options
     *
     * @return $this
     */
    private function setSelect2Options ($select2options)
    {
        foreach ($select2options as $key => $option)
            $this->select2options[$key] = $option;
        return $this;
    }

    /**
     * Determines the placeholder behaviour. Set to FALSE if the first option should be selected on default. Set to
     * NULL if the label should be used. Otherwise specify a placeholder.
     *
     * @param null|boolean|string $placeholder
     *
     * @return $this
     */
    public function setPlaceholder ($placeholder = null)
    {
        if ($placeholder === false)
            $this->usePlaceholder = false;
        else {
            $this->setSelect2Options(array(
                self::SELECT2_OPTION_PLACEHOLDER => $placeholder === null ? $this->label : $placeholder
            ));
            $this->usePlaceholder = true;
        }
        return $this;
    }

    /**
     * Whether to allow the user to clear the value once it has been selected
     *
     * @param $allowClear
     *
     * @return $this
     */
    public function allowClear ($allowClear)
    {
        $this->setSelect2Options(array(
            self::SELECT2_OPTION_ALLOW_CLEAR => $allowClear
        ));
        return $this;
    }

    /**
     * If set, the search box is only displayed if the amount of results in the select box is above $minimum
     *
     * @param int $minimum
     *
     * @return $this
     */
    public function setMinimumResultsForSearchBox ($minimum)
    {
        $this->setSelect2Options(array(
            self::SELECT2_OPTION_MINIMUM_RESULTS_FOR_SEARCH => $minimum
        ));
        return $this;
    }
}