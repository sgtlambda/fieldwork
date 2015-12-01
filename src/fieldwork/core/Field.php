<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Identifiable;
use fieldwork\core\interfaces\Request;
use fieldwork\core\interfaces\StateSanitizer;
use fieldwork\core\interfaces\Stateful;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\StateValidator;
use fieldwork\core\traits\NamedComponent;
use fieldwork\core\traits\SimpleImplementedSanitizer;
use fieldwork\core\traits\SimpleImplementedValidator;

abstract class Field extends Component implements Identifiable, Stateful, Symmetrical, StateValidator, StateSanitizer
{

    use NamedComponent,
        SimpleImplementedValidator,
        SimpleImplementedSanitizer {
        SimpleImplementedValidator::serialize as serializeValidators;
        SimpleImplementedSanitizer::serialize as serializeSanitizers;
    };

    protected $label;
    protected $visible     = true;
    protected $enabled     = true;
    protected $collectData = true;

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

    public function getState (Request $request)
    {
        return new State($request->getValue($this));
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
     * Sets whether the control is enabled
     * @param boolean $enabled
     * @return $this
     */
    public function setEnabled ($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
}