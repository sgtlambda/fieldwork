<?php

namespace fieldwork\components;

/**
 * Select2 powered select box
 * @package fieldwork\components
 */
class SelectBox extends Field
{

    const SELECT2_OPTION_MULTIPLE                   = "multiple";
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
     * @var boolean
     */
    private $isMultipleAllowed;
    /**
     * @var bool
     */
    private $allowMultiple;

    /**
     * Constructs a new SelectBox
     *
     * @param string           $slug              The field identifier
     * @param string           $label             The label to display
     * @param array            $options           The available options for the field
     * @param string|string[]  $value             The current value or values
     * @param bool             $isMultipleAllowed Whether multiple fields can be selected
     * @param bool|string|null $emptyValue        The value to return when an empty option is selected (this is useful
     *                                            for relational fields in the database where you should provide NULL ).
     *                                            Set to FALSE if you wish to use the default empty value
     * @param int              $storeValueLocally
     */
    public function __construct ($slug, $label, array $options = array(), $value = '', $isMultipleAllowed = false, $emptyValue = false, $storeValueLocally = 0)
    {
        parent::__construct($slug, $label, $value, $storeValueLocally);
        $this->options           = $options;
        $this->select2options    = [];
        $this->isMultipleAllowed = $isMultipleAllowed;
        $this->emptyValue        = $emptyValue === false ? ($this->isMultipleAllowed ? [] : '') : $emptyValue;
        $this->setPlaceholder();
        $this->allowMultiple = $isMultipleAllowed;
    }

    public function getClasses ()
    {
        return array_merge(
            parent::getClasses(), array(
                'select',
                'selectbox')
        );
    }

    public function getAttributes ()
    {
        $attributes = parent::getAttributes();
        if ($this->isMultipleAllowed)
            $attributes['multiple'] = 'multiple';
        return $attributes;
    }

    public function getHTML ()
    {
        $r = "<select " . $this->getAttributesString() . ">";
        if ($this->usePlaceholder) {
            $r .= "<option></option>";
        }
        foreach ($this->options as $v => $l) {
            $selected = $this->isSelected($v) ? " selected=\"selected\"" : "";
            $r .= "<option value=\"$v\"$selected>$l</option>";
        }
        $r .= "</select>";
        return $r;
    }

    public function getValue ($condense = true)
    {
        if ($this->value === "" || is_array($this->value) && !count($this->value))
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
     * Determines the selection behaviour. If enabled, the user will be allowed to select more than one option.
     *
     * @param bool $multiple
     *
     * @return $this
     */
    public function setMultipleAllowed ($multiple = true)
    {
        $this->isMultipleAllowed = $multiple;
        return $this;
    }

    public function isMultiple ()
    {
        return $this->isMultipleAllowed;
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

    /**
     * Determines whether the given value is currently selected
     *
     * @param $v
     *
     * @return bool
     */
    protected function isSelected ($v)
    {
        return $this->value === $v || (is_array($this->value) && in_array($v, $this->value));
    }

    protected function getRestoreDefault ()
    {
        return $this->emptyValue;
    }
}