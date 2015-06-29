<?php

namespace fieldwork\components;

use fieldwork\Form;
use fieldwork\methods\Method;
use fieldwork\sanitizers\FieldSanitizer;
use fieldwork\validators\FieldValidator;

abstract class Field extends Component
{

    protected
        $label,
        $visible = true,
        $enabled = true,
        $value,
        $initialValue,
        $latestErrorMsg = "",
        $forceInvalid = false,
        $validators = array(),
        $sanitizers = array(),
        $collectData = true,
        $storeValueLocally;

    /**
     * Creates a new form field component
     *
     * @param string          $slug              internal slug to use
     * @param string          $label             label to display
     * @param string|string[] $value             initial value
     * @param int             $storeValueLocally how long to store last used value in cookie (set to 0 for no cookie)
     */
    public function __construct ($slug, $label = '', $value = '', $storeValueLocally = 0)
    {
        parent::__construct($slug);
        $this->label             = $label;
        $this->initialValue      = $this->value = $value;
        $this->storeValueLocally = $storeValueLocally;
    }

    public function reset ()
    {
        $this->value = $this->initialValue;
        parent::reset();
    }

    public function setCollectData ($collectData)
    {
        $this->collectData = $collectData;
    }

    public function getCollectData ()
    {
        return $this->collectData;
    }

    /**
     * Treats the value that was fetched from the request variables. If the control uses a non-scalar to represent its
     * value, it should be instantiated here
     * @param string $value
     * @return string
     */
    public function importValue ($value)
    {
        return stripslashes($value);
    }

    /**
     * Restores the value from the provided method
     * @param Method $method
     * @param bool   $sanitize Whether to sanitize the fetched value
     */
    public function restoreValue (Method $method, $sanitize = true)
    {
        $v = $method->getValue($this->getName(), $this->getRestoreDefault());
        if ($this->isMultiple() && is_array($v)) {
            foreach ($v as &$value)
                $value = $this->importValue($value);
        } else {
            $v = $this->importValue($v);
        }
        if ($sanitize)
            foreach ($this->sanitizers as $s)
                /* @var $s FieldSanitizer */
                $v = $s->sanitize($v);
        $this->value = $v;
    }

    /**
     * Condenses multiple values (e.g. from a multiple select box) into a single string
     *
     * @param string[] $values
     *
     * @return string
     */
    public function condenseMultiple ($values = [])
    {
        return implode(',', $values);
    }

    /**
     * Extracts multiple values from a single string
     *
     * @param string $value
     *
     * @return string[]
     */
    public function extractMultiple ($value)
    {
        return explode(',', $value);
    }

    /**
     * Determines whether this field allows selection of multiple values
     * @return bool
     */
    public function isMultiple ()
    {
        return false;
    }

    public function submit ()
    {
        if ($this->storeValueLocally)
            setcookie($this->getCookieName(), $this->value, time() + $this->storeValueLocally, '/');
    }

    public function preprocess ()
    {
        if ($this->storeValueLocally && isset($_COOKIE[$this->getCookieName()]))
            $this->value = $_COOKIE[$this->getCookieName()];
    }

    private function getCookieName ()
    {
        return $this->getGlobalSlug() . '-vc';
    }

    /**
     * Gets the value of the name attribute for this field
     *
     * @param bool $asMarkup Whether to retrieve the value used in the markup (if true, [] will by default be appended
     *                       to fields that allow multiple values)
     *
     * @return string
     */
    public function getName ($asMarkup = false)
    {
        return $this->getGlobalSlug() . (($this->isMultiple() && $asMarkup) ? '[]' : '');
    }

    public function getLabel ()
    {
        return $this->label;
    }

    /**
     * Whether to condense multiple values into a single string
     *
     * @param bool $condense
     *
     * @return string|string[]
     */
    public function getValue ($condense = true)
    {
        $v = $this->value;
        if ($condense && $this->isMultiple() && is_array($v))
            $v = $this->condenseMultiple($v);
        return $v;
    }

    public function setValue ($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Sets whether the component is visible
     *
     * @param boolean $visible
     *
     * @return $this
     */
    public function setVisible ($visible = true)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Checks whether the component is visible
     * @return boolean
     */
    public function isVisible ()
    {
        return $this->visible;
    }

    /**
     * @return array
     */
    public function getJsonData ()
    {
        $v = array();
        $s = array();
        foreach ($this->validators as $validator)
            /* @var $validator FieldValidator */
            array_push($v, $validator->getJsonData());
        foreach ($this->sanitizers as $sanitizer)
            /* @var $sanitizer FieldSanitizer */
            $s[] = $sanitizer->getJsonData();
        return array(
            'validators'  => $v,
            'sanitizers'  => $s,
            'id'          => $this->getID(),
            'name'        => $this->getName(),
            'collectData' => $this->collectData
        );
    }

    public function getId ()
    {
        return "field-" . $this->getGlobalSlug();
    }

    public function getAttributes ()
    {
        $attributes = array(
            "name"  => $this->getName(true),
            "value" => $this->getValue(),
            "id"    => $this->getId()
        );
        if (!$this->enabled)
            $attributes['disabled'] = 'disabled';
        return array_merge(parent::getAttributes(), $attributes);
    }

    public function getClasses ()
    {
        $form = $this->getRoot();
        /* @var $form Form */
        $formClass = ($form instanceof Form) && $form->isRequested() ? ($this->isValid() ? "valid" : "invalid") : "";
        return array_merge(parent::getClasses(), array(
            "fieldwork-field",
            $formClass
        ));
    }

    /**
     * Adds validator
     *
     * @param FieldValidator $v
     * @param boolean        $unshift Whether to add the validator to the front of the array
     *
     * @return Field
     */
    public function addValidator (FieldValidator $v, $unshift = false)
    {
        if ($unshift)
            array_unshift($this->validators, $v);
        else
            $this->validators[] = $v;
        $v->setField($this);
        return $this;
    }

    /**
     * Adds sanitizer
     *
     * @param FieldSanitizer $s
     *
     * @return Field
     */
    public function addSanitizer (FieldSanitizer $s)
    {
        $this->sanitizers[] = $s;
        return $this;
    }

    public function forceInvalid ()
    {
        $this->forceInvalid = true;
    }

    public function isValid ()
    {
        if ($this->forceInvalid)
            return false;
        foreach ($this->validators as $validator) {
            /* @var $validator FieldValidator */
            if (!$validator->isValid($this->value)) {
                $this->latestErrorMsg = $validator->getErrorMsg();
                return false;
            } else if ($validator->skipValidation($this->value)) {
                return true;
            }
        }
        return true;
    }

    public function sanitize ()
    {
        foreach ($this->sanitizers as $sanitizer)
            /* @var $sanitizer FieldSanitizer */
            $this->value = $sanitizer->sanitize($this->value);
    }

    public function getLatestErrorMsg ()
    {
        return $this->latestErrorMsg;
    }

    /**
     * Returns the default value used if the POST var is not set
     * @return string
     */
    protected function getRestoreDefault ()
    {
        return $this->value;
    }

    /**
     * Sets whether the control is enabled
     * @param boolean $enabled
     * @return $this
     */
    public function setEnabled ($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Gets the script that will instantiate ONLY this field in an anonymous form
     * @return string
     */
    public function getScript ()
    {
        return "jQuery(function($){ $('#" . $this->getID() . "').fieldwork(" . json_encode($this->getJsonData(), JSON_PRETTY_PRINT) . "); });";
    }

    /**
     * Gets the script tag that will instantiate ONLY this field in an anonymous form
     * @return string
     */
    public function getScriptHTML ()
    {
        $openingTag = "<script type='text/javascript'>";
        $closingTag = "</script>";
        return $openingTag . $this->getScript() . $closingTag;
    }
}