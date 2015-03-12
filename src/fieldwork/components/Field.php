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
     * @param string $slug              internal slug to use
     * @param string $label             label to display
     * @param string $value             default value
     * @param int    $storeValueLocally how long to store last used value in cookie (set to 0 for no cookie)
     */
    public function __construct ($slug, $label, $value = '', $storeValueLocally = 0)
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

    public function restoreValue (Method $method, $sanitize = true)
    {
        $v = stripslashes($method->getValue($this->getName(), $this->getRestoreDefault()));
        if ($sanitize)
            foreach ($this->sanitizers as $s)
                /* @var $s FieldSanitizer */
                $v = $s->sanitize($v);
        $this->value = $v;
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

    public function getName ()
    {
        return $this->getGlobalSlug();
    }

    public function getLabel ()
    {
        return $this->label;
    }

    public function getValue ()
    {
        return $this->value;
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
        return array_merge(parent::getAttributes(), array(
            "name"  => $this->getName(),
            "value" => $this->getValue(),
            "id"    => $this->getId()
        ));
    }

    public function getClasses ()
    {
        $form = $this->getRoot();
        /* @var $form Form */
        return array_merge(parent::getClasses(), array(
            "fieldwork-field",
            ($form->isRequested() ? ($this->isValid() ? "valid" : "invalid") : "")
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

}