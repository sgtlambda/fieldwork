<?php

namespace jannieforms;

use jannieforms\components\Field;
use jannieforms\components\GroupComponent;
use jannieforms\methods\Method;
use jannieforms\validators\FormValidator;

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
     * Instantiates a new JannieForm
     *
     * @param string $slug
     * @param string $action
     * @param Method $method
     * @param string $target
     */
    public function __construct ($slug, $action, $method, $target = self::TARGET_SELF)
    {
        parent::__construct($slug);
        $this->action                                         = $action;
        $this->method                                         = $method;
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
        JF::registerForm($this);
        return $this;
    }

    /**
     * Searches form fields
     *
     * @param string $query id to search for
     * @param bool   $includeInactiveFields
     *
     * @return bool|Field closest match or false if not found
     */
    public function f ($query, $includeInactiveFields = false)
    {
        $minLength = -1;
        $f         = false;
        foreach ($this->getFields($includeInactiveFields) as $field)
            if (preg_match("/^(.*)" . preg_quote($query) . "$/", $field->getGlobalSlug(), $matches)) {
                $l = strlen($matches[1]);
                if ($l < $minLength || $minLength == -1) {
                    $minLength = $l;
                    $f         = $field;
                }
            }
        return $f;
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
        if ($f != false)
            return $f->getValue();
        else
            return $default;
    }

    /**
     * Returns an array of fields of which data is to be collected
     * @return array
     */
    public function c ()
    {
        $fields = array();
        foreach ($this->getFields() as $field)
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

    public function getField ($localSlug)
    {
        foreach ($this->getFields() as $field)
            if ($field->getLocalSlug() == $localSlug)
                return $field;
    }

    public function getValue ($key, $default = '')
    {
        return $this->method->getValue($key, $default);
    }

    public function hasValue ($key)
    {
        return $this->method->hasValue($key);
    }

    public function enableAJAX (AbstractCallback $method, $ajaxSubmitEnabled = true)
    {
        $this->ajaxMethod        = $method;
        $this->ajaxSubmitEnabled = $ajaxSubmitEnabled;
    }

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
            'jannieform'
        ));
    }

    public function getScript ()
    {
        return "jQuery(function(){ jQuery('#" . $this->getID() . "').jannieform(" . json_encode($this->getJsonData()) . "); });";
    }

    public function getScriptHTML ()
    {
        return "<script type='text/javascript'>
            " . $this->getScript() . "
                </script>";
    }

    protected function renderFormError ($errorMsg)
    {
        return "<div class=\"form-error\">" . $errorMsg . "</div>";
    }

    /**
     * Renders and returns complete form markup as HTML. Use this to echo the form to the webpage.
     * @return string
     */
    public function getHTML ()
    {
        $dataFields = '';
        foreach ($this->dataFields as $key => $value)
            $dataFields .= "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
        $errors = '';
        foreach ($this->validators as $validator)
            if (!$validator->isValid())
                $errors .= $this->renderFormError($validator->getErrorMsg());
        return "<form " . $this->getAttributesString() . ">" . $errors . $dataFields . parent::getHTML() . "</form>" . $this->getScriptHTML();
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
     * @return array
     */
    public function getFields ($includeInactiveFields = false)
    {
        $fields = array();
        foreach ($this->getChildren(true, $includeInactiveFields) as $component)
            if (is_subclass_of($component, "JannieFormFieldComponent"))
                array_push($fields, $component);
        return $fields;
    }

    public function getJsonData ()
    {
        $fields = array();
        foreach ($this->getFields() as $component)
            $fields[$component->getID()] = $component->getJsonData();
        $liveValidators = array();
        foreach ($this->validators as $validator)
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
            "isSubmitted"    => $this->isCallbacksubmitted,
            "ajax"           => array(
                "submitEnabled" => $this->ajaxSubmitEnabled,
                "method"        => $this->ajaxMethod !== null ? $this->ajaxMethod->getSlug() : "",
                "results"       => $this->ajaxResult
            )
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
            $field->submit();
        $this->isCallbacksubmitted = true;
        foreach ($this->callback as $callback)
            if (is_callable($callback))
                call_user_func($callback, $this);
        if ($this->ajaxMethod !== null) {
            $this->processAjaxLocally();
        }
    }

    public function isValid ()
    {
        if (!parent::isValid())
            return false;
        foreach ($this->validators as $validator)
            if (!$validator->process($this))
                return false;
        return true;
    }

    public function getErrorMessages ()
    {
        $e = array();
        foreach ($this->validators as $validator)
            if (!$validator->isValid())
                $e[] = $validator->getErrorMsg();
        return $e;
    }

    /**
     * If the form has Ajax handlers but was still submitted to a new page, handles Ajax handlers.
     */
    private function processAjaxLocally ()
    {
        $this->ajaxResult = $this->ajaxMethod->run($this);
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

}