<?php

namespace fieldwork\validators;

class RadioValidator extends FieldValidator
{

    private $value;

    const ANY = null;

    /**
     * @param string $errorMsg The message to show if the field does not pass validation
     * @param string $value    The value to allow
     */
    public function __construct ($errorMsg, $value = self::ANY)
    {
        parent::__construct($errorMsg);
        $this->value = $value;
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'any'   => $this->allowAny(),
            'value' => $this->value
        ));
    }

    public function isValid ($value)
    {
        return $this->allowAny() ? (!empty($value)) : $value === $this->value;
    }

    public function describeObject ()
    {
        return 'radio';
    }

    /**
     * Whether the validator returns true as long as any value is checked
     * @return bool
     */
    private function allowAny ()
    {
        return $this->value === self::ANY;
    }

}
