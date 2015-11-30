<?php

namespace fieldwork\core;

use fieldwork\core\Field;
use fieldwork\core\CompoundComponent;
use fieldwork\core\interfaces\Sanitizer;
use fieldwork\core\interfaces\Stateful;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\Validator;
use fieldwork\core\methods\Method;
use fieldwork\core\methods\POST;
use fieldwork\core\FormValidator;
use fieldwork\core\reflection\StateValidationReflection;
use fieldwork\core\SynchronizableFormValidator;
use fieldwork\core\traits\HasSanitizers;
use fieldwork\core\traits\HasValidators;

class Form extends CompoundComponent implements Stateful, Symmetrical, Validator, Sanitizer
{

    use HasValidators, HasSanitizers {
        HasValidators::serialize as serializeValidators;
        HasSanitizers::serialize as serializeSanitizers;
    };

    const TARGET_SELF  = "_self";
    const TARGET_BLANK = "_blank";

    private
        $action,
        $dataFields;

    /**
     * Instantiates a new Form
     *
     * @param string $name
     * @param string $action
     */
    public function __construct ($name, $action = '')
    {
        parent::__construct($name);
        $this->action                                         = $action;
        $this->dataFields[$this->getSubmitConfirmFieldName()] = 'true';
    }

    /**
     * Searches form fields
     *
     * @param string $query                 ID to search for
     * @param bool   $includeInactiveFields Whether to include inactive fields in the search
     *
     * @return null|Field closest match or null if not found
     */
    public function f ($query, $includeInactiveFields = false)
    {
        $minLength = -1;
        $match     = null;
        foreach ($this->getFields($includeInactiveFields) as $field)
            /* @var $field Field */
            if (preg_match("/^(.*)" . preg_quote($query) . "$/", $field->getIdentifier(), $matches)) {
                $l = strlen($matches[1]);
                if ($l < $minLength || $minLength == -1) {
                    $minLength = $l;
                    $match     = $field;
                }
            }
        return $match;
    }

    /**
     * Returns an array of fields of which data is to be collected
     * @return Field[]
     */
    public function c ()
    {
        $fields = array();
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            if ($field->getCollectData())
                $fields[] = $field;
        return $fields;
    }

    public function getDataFieldName ($slug)
    {
        return $this->getIdentifier() . '-data-' . $slug;
    }

    public function getSubmitConfirmFieldName ()
    {
        return $this->getDataFieldName('submit');
    }

    /**
     * Finds a field by its short name
     *
     * @param $shortName
     *
     * @return Field|null
     */
    public function getField ($shortName)
    {
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            if ($field->getName() == $shortName)
                return $field;
        return null;
    }

    public function getDataFields ()
    {
        return $this->dataFields;
    }

    public function getAction ()
    {
        return $this->action;
    }

    public function setAction ($action)
    {
        $this->action = $action;
    }

    /**
     * Gets the script that will instantiate the form
     * @return string
     */
    public function getScript ()
    {
        return "jQuery(function($){ $('#" . $this->getIdentifier() . "').fieldwork(" . json_encode($this->serialize(), JSON_PRETTY_PRINT) . "); });";
    }

    /**
     * Gets the script tag that will instantiate the form
     * @return string
     */
    public function getScriptHTML ()
    {
        $openingTag = "<script type='text/javascript'>";
        $closingTag = "</script>";
        return $openingTag . $this->getScript() . $closingTag;
    }

    /**
     * Retrieves an array of this form's fields
     *
     * @param boolean $includeInactiveFields whether to include inactive fields as well
     *
     * @return Field[]
     */
    public function getFields ($includeInactiveFields = false)
    {
        $fields = array();
        foreach ($this->getChildren(true, $includeInactiveFields) as $component)
            if ($component instanceof Field)
                array_push($fields, $component);
        return $fields;
    }

    public function serialize ()
    {
        return array_merge([
            "slug"       => $this->getName(),
            "fields"     => $this->getFields(),
            "dataFields" => $this->dataFields
        ],
            $this->serializeValidators(),
            $this->serializeSanitizers()
        );
    }
}