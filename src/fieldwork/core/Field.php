<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Sanitizer;
use fieldwork\core\interfaces\Stateful;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\Validator;
use fieldwork\core\traits\HasSanitizers;
use fieldwork\core\traits\HasValidators;

abstract class Field extends Component implements Stateful, Symmetrical, Validator, Sanitizer
{

    use HasValidators, HasSanitizers {
        HasValidators::serialize as serializeValidators;
        HasSanitizers::serialize as serializeSanitizers;
    };

    protected
        $label,
        $visible = true,
        $enabled = true,
        $collectData = true;

    /**
     * Creates a new form field component
     *
     * @param string          $name         internal slug to use
     * @param string          $label        label to display
     * @param string|string[] $defaultValue initial value
     */
    public function __construct ($name, $label = '', $defaultValue = '')
    {
        parent::__construct($name);
        $this->label = $label;
    }

    public function setCollectData ($collectData)
    {
        $this->collectData = $collectData;
    }

    public function getCollectData ()
    {
        return $this->collectData;
    }

    public function getLabel ()
    {
        return $this->label;
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
    public function serialize ()
    {
        return array_merge([
            'id'          => $this->getIdentifier(),
            'name'        => $this->getName(),
            'collectData' => $this->collectData
        ],
            $this->serializeValidators(),
            $this->serializeSanitizers()
        );
    }

    /**
     * Adds sanitizer
     *
     * @param SymmetricalSanitizer $s
     *
     * @return Field
     */
    public function addSanitizer (SymmetricalSanitizer $s)
    {
        $this->sanitizers[] = $s;
        return $this;
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
        return "jQuery(function($){ $('#" . $this->getIdentifier() . "').fieldwork(" . json_encode($this->serialize(), JSON_PRETTY_PRINT) . "); });";
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