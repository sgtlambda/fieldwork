<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Identifiable;
use fieldwork\core\interfaces\StateSanitizer;
use fieldwork\core\interfaces\Stateful;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\StateValidator;
use fieldwork\core\traits\CompoundImplementedSanitizer;
use fieldwork\core\traits\CompoundImplementedValidator;
use fieldwork\core\traits\NamedComponent;

class Form extends CompoundComponent implements Identifiable, Stateful, Symmetrical, StateValidator, StateSanitizer
{

    use NamedComponent,
        CompoundImplementedValidator,
        CompoundImplementedSanitizer {
        CompoundImplementedValidator::serialize as serializeValidators;
        CompoundImplementedSanitizer::serialize as serializeSanitizers;
    };

    /**
     * Searches form fields
     *
     * @param string $query                 ID to search for
     * @param bool   $includeInactiveFields Whether to include inactive fields in the search
     *
     * @return null|Field closest match or null if not found
     */
    public function find ($query, $includeInactiveFields = false)
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
     *
     * @return Field[]
     */
    public function collect ()
    {
        return array_filter($this->getFields(), function (Field $field) {
            return $field->getCollectData();
        });
    }

    /**
     * Finds a field by its short name
     *
     * @param $name
     *
     * @return Field|null
     */
    public function getField ($name)
    {
        return array_first($this->getFields(), function (Field $field) use ($name) {
            return $field->getName() === $name;
        });
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
        return array_filter($this->getChildren(true, $includeInactiveFields), function (Component $component) {
            return $component instanceof Field;
        });
    }

    public function serialize ()
    {
        return array_merge([
            "slug"   => $this->getName(),
            "fields" => $this->getFields(),
        ],
            $this->serializeValidators(),
            $this->serializeSanitizers()
        );
    }
}