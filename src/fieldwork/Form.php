<?php

namespace fieldwork;


use fieldwork\components\Field;
use fieldwork\components\GroupComponent;
use fieldwork\methods\Method;
use fieldwork\methods\POST;
use fieldwork\validators\FormValidator;
use fieldwork\validators\SynchronizableFormValidator;

class Form extends GroupComponent implements FormData, Synchronizable
{

    const TARGET_SELF  = "_self";
    const TARGET_BLANK = "_blank";

    private
        $action,
        $method,
        $target,
        $validators = array(),
        $callback = array(),
        $ajaxMethod = null,
        $ajaxResult = null,
        $ajaxSubmitEnabled = false,
        $activateCallbacks = array(),
        $javascriptCallback = array(),

        $forceSubmit = false,
        $dataFields,

        $isUserSubmitted = false,
        $isProcessed = false,
        $isCallbacksubmitted = false;

    /**
     * Instantiates a new Form
     *
     * @param string $slug
     * @param string $action
     * @param Method $method
     * @param string $target
     */
    public function __construct ($slug, $action = '', $method = null, $target = self::TARGET_SELF)
    {
        parent::__construct($slug);
        $this->action                                         = $action;
        $this->method                                         = ($method === null ? new POST() : $method);
        $this->target                                         = $target;
        $this->dataFields[$this->getSubmitConfirmFieldName()] = 'true';
        $this->register();
    }

    /**
     * Resets state and field values. DOES NOT remove validators and callbacks
     */
    public function reset ()
    {
        $this->isUserSubmitted     = false;
        $this->isProcessed         = false;
        $this->isCallbacksubmitted = false;
        parent::reset();
    }

    /**
     * Registers the form globally
     * @return Form this
     */
    private function register ()
    {
        FW::registerForm($this);
        return $this;
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
            if (preg_match("/^(.*)" . preg_quote($query) . "$/", $field->getGlobalSlug(), $matches)) {
                $l = strlen($matches[1]);
                if ($l < $minLength || $minLength == -1) {
                    $minLength = $l;
                    $match     = $field;
                }
            }
        return $match;
    }

    /**
     * Searches form fields and returns its value
     *
     * @param string $query   id to search for
     * @param string $default default value
     *
     * @return string value of closest match or default value if field not found
     */
    public function v ($query, $default = '')
    {
        $f = $this->f($query);
        if ($f !== null)
            return $f->getValue();
        else
            return $default;
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

    /**
     * Adds a new form-level validator
     *
     * @param FormValidator $validator validator
     * @param boolean       $unshift   Whether to add the validator to the front of the array
     *
     * @return static
     */
    public function addValidator (FormValidator $validator, $unshift = false)
    {
        if ($unshift)
            array_unshift($this->validators, $validator);
        else
            $this->validators[] = $validator;
        return $this;
    }

    public function getDataFieldName ($slug)
    {
        return $this->getGlobalSlug() . '-data-' . $slug;
    }

    public function getSubmitConfirmFieldName ()
    {
        return $this->getDataFieldName('submit');
    }

    /**
     * Finds a field by its localslug value
     *
     * @param $localSlug
     *
     * @return Field|null
     */
    public function getField ($localSlug)
    {
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            if ($field->getLocalSlug() == $localSlug)
                return $field;
        return null;
    }

    public function getValue ($key, $default = '')
    {
        return $this->method->getValue($key, $default);
    }

    public function hasValue ($key)
    {
        return $this->method->hasValue($key);
    }

    /**
     * Attach a function that will be called upon submitting the form. The first argument passed to the function will
     * be an instance of FormData.
     *
     * @param callable $callback
     */
    public function attachCallback ($callback)
    {
        $this->callback[] = $callback;
    }

    public function attachActivateCallback ($callback)
    {
        $this->activateCallbacks[] = $callback;
    }

    public function attachJavascriptCallback ($callback)
    {
        $this->javascriptCallback[] = $callback;
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

    public function getAttributes ()
    {
        return array_merge(parent::getAttributes(), $this->method->getFormAttributes(), array(
            'id'     => $this->getID(),
            'action' => $this->action,
            'target' => $this->target
        ));
    }

    public function getClasses ()
    {
        return array_merge(parent::getClasses(), array(
            'fieldwork-form'
        ));
    }

    /**
     * Gets the script that will instantiate the form
     * @return string
     */
    public function getScript ()
    {
        return "jQuery(function($){ $('#" . $this->getID() . "').fieldwork(" . json_encode($this->getJsonData(), JSON_PRETTY_PRINT) . "); });";
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

    public function renderFormError ($errorMsg)
    {
        return sprintf("<div class=\"form-error card red white\"><div class=\"card-content red-text text-darken-4\">%s</div></div>", $errorMsg);
    }

    /**
     * Gets the markup that is to be outputted before the actual contents of the form. This method could be used for
     * even more manual control over outputting with a custom markup.
     * @return string
     */
    public function getWrapBefore ()
    {
        $dataFields = $this->getDataFieldsHTML();
        $errors     = '';
        foreach ($this->validators as $validator)
            /* @var $validator FormValidator */
            if (!$validator->isValid())
                $errors .= $this->renderFormError($validator->getErrorMsg());
        return "<form " . $this->getAttributesString() . ">" . $errors . $dataFields;
    }

    /**
     * Gets the markup that is to be outputted after the actual contents of the form. This method could be used for
     * even more manual control over outputting with a custom markup.
     *
     * @param bool $includeScripts Whether to include the scripts
     *
     * @return string
     */
    public function getWrapAfter ($includeScripts = true)
    {
        return "</form>" . ($includeScripts ? $this->getScriptHTML() : '');
    }

    /**
     * Wraps the <pre>$content</pre> inside a form tag, declaring error messages, datafields and the script contents.
     * This method could be useful for using a form with custom custom markup.
     *
     * @param string $content        The form "content" to be wrapped. Should declare all the required input fields.
     * @param bool   $includeScripts Whether to include the scripts
     *
     * @return string The HTML content
     */
    public function wrap ($content, $includeScripts = true)
    {
        return $this->getWrapBefore() . $content . $this->getWrapAfter($includeScripts);
    }

    /**
     * Renders and returns complete form markup as HTML. Use this to echo the form using default markup to the webpage.
     *
     * @param bool $showLabel
     *
     * @return string
     */
    public function getHTML ($showLabel = true)
    {
        return $this->wrap($this->getInnerHTML());
    }

    public function getID ()
    {
        return "form-" . $this->getGlobalSlug();
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

    public function getJsonData ()
    {
        $fields = array();
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            $fields[$field->getID()] = $field->getJsonData();
        $liveValidators = array();
        foreach ($this->validators as $validator)
            /* @var $validator SynchronizableFormValidator */
            if ($validator->isLive())
                $liveValidators[] = $validator->getJsonData();
        return array(
            "slug"           => $this->getLocalSlug(),
            "fields"         => $fields,
            "liveValidators" => $liveValidators,
            "dataFields"     => $this->dataFields,
            "submitCallback" => $this->javascriptCallback,
            "isProcessed"    => $this->isProcessed,
            "isActivated"    => $this->isUserSubmitted,
            "isSubmitted"    => $this->isCallbacksubmitted
        );
    }

    public function activateHandlers ()
    {
        foreach ($this->activateCallbacks as $psCallback)
            if (is_callable($psCallback))
                call_user_func($psCallback, $this);
    }

    /**
     * Will attempt to submit the form upon calling the process function, even if the user has not activated it
     */
    public function forceSubmit ()
    {
        $this->forceSubmit = true;
    }

    /**
     * Submits the form internally. You're not usually supposed to call this function directly.
     */
    private function submit ()
    {
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            $field->submit();
        $this->isCallbacksubmitted = true;
        foreach ($this->callback as $callback)
            if (is_callable($callback))
                call_user_func($callback, $this);
    }

    public function isValid ()
    {
        if (!parent::isValid())
            return false;
        foreach ($this->validators as $validator)
            /* @var $validator FormValidator */
            if (!$validator->process($this))
                return false;
        return true;
    }

    public function getErrorMessages ()
    {
        $e = array();
        foreach ($this->validators as $validator)
            /* @var $validator FormValidator */
            if (!$validator->isValid())
                $e[] = $validator->getErrorMsg();
        return $e;
    }

    /**
     * Check if form was activated, then validates, calls handlers, then submits. Call this function before displaying
     * the form.
     */
    public function process ()
    {
        if ($this->isProcessed)
            return;
        $this->isProcessed = true;
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            $field->preprocess();
        $this->isUserSubmitted = $this->getValue($this->getSubmitConfirmFieldName(), 'false') == 'true' || $this->forceSubmit;
        if ($this->isUserSubmitted) {
            foreach ($this->getFields() as $field)
                $field->restoreValue($this->method);

            $this->activateHandlers();

            if ($this->isValid()) {
                $this->submit();
            }
        }
    }

    /**
     * Checks whether the form has been processed
     * @return boolean
     */
    public function isProcessed ()
    {
        return $this->isProcessed;
    }

    /**
     * Indicates whether the user has tried to submit the form
     * @return boolean
     */
    public function isRequested ()
    {
        return $this->isUserSubmitted;
    }

    /**
     * Indicates whether the form was validated correctly
     * @return boolean
     */
    public function isSubmitted ()
    {
        return $this->isCallbacksubmitted;
    }

    /**
     * Returns a short human-readable slug/string describing the object
     * @return string
     */
    function describeObject ()
    {
        return "form";
    }

    /**
     * Gets a complete associated array containing all the data that needs to be stored
     *
     * @param bool $useName           Whether to use the field name (if not, the fields local slug is used)
     * @param bool $includeDataFields Whether to include the data fields
     *
     * @return array
     */
    function getValues ($useName = true, $includeDataFields = true)
    {
        $values = array();
        foreach ($this->getFields() as $field)
            /* @var $field Field */
            if ($field->getCollectData()) {
                $key          = $useName ? $field->getName() : $field->getLocalSlug();
                $values[$key] = $field->getValue();
            }
        if ($includeDataFields)
            $values = array_merge($values, $this->dataFields);
        return $values;
    }

    /**
     * Restores the values from an associated array. Only defined properties will be overwritten
     *
     * @param array $values
     */
    function setValues (array $values = array())
    {
        foreach ($this->getFields() as $field)
            if (array_key_exists($field->getName(), $values))
                $field->setValue($values[$field->getName()]);
        foreach ($this->dataFields as $dataKey => $dataVal)
            if (array_key_exists($dataKey, $values))
                $this->dataFields[$dataKey] = $values[$dataKey];
    }

    /**
     * @return string
     */
    protected function getDataFieldsHTML ()
    {
        $dataFields = '';
        foreach ($this->dataFields as $key => $value)
            $dataFields .= "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
        return $dataFields;
    }

    /**
     * @return string
     */
    public function getInnerHTML ()
    {
        return parent::getHTML();
    }
}