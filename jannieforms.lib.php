<?php

/**
 * @author JM Versteeg
 */
if (!defined('JANNIEFORMS_LOADED')) {

    class JannieForms {

        private static $forms = array();
        private static $ajaxMethods = array();

        /**
         * Registers given form globally
         * @param JannieForm $form
         */
        static function registerForm(JannieForm $form) {
            array_push(self::$forms, $form);
        }

        /**
         * Registers given ajax method globally.
         * @param JannieAjaxMethod $ajaxMethod
         */
        static function registerAjaxMethod(JannieAjaxMethod $ajaxMethod) {
            self::$ajaxMethods[$ajaxMethod->getSlug()] = $ajaxMethod;
        }

        /**
         * Retrieves a form by its slug
         * @param string $slug
         * @return JannieForm|boolean form or false if not found
         */
        static function getForm($slug) {
            foreach (self::$forms as $form)
                if ($form->getGlobalSlug() == $slug)
                    return $form;
            return false;
        }

        /**
         * Retrieves an ajax method by its slug
         * @param string $slug
         * @return JannieAjaxMethod ajaxmethod
         */
        static function getAjaxMethod($slug) {
            return self::$ajaxMethods[$slug];
        }

        /**
         * Lists slugs of all ajax methods
         * @return array
         */
        static function listAjaxMethods() {
            return array_keys(self::$ajaxMethods);
        }

        /**
         * If any of your form callbacks uses header redirects, make sure to call before_content() and after_content() before output starts and after output ends.
         */
        static function before_content() {
            ob_start();
        }

        /**
         * If any of your form callbacks uses header redirects, make sure to call before_content() and after_content() before output starts and after output ends.
         */
        static function after_content() {
            ob_end_flush();
        }

    }

    abstract class JannieFormComponent {

        protected
                $parent,
                $children = array();
        private
                $slug,
                $active = true,
                $customClasses = array(),
                $customAttributes = array();

        public function __construct($slug) {
            $this->slug = $slug;
        }

        public abstract function getHTML();

        public abstract function isValid();

        public function isVisible() {
            return true;
        }

        protected function add(JannieFormComponent $component) {
            $this->children[] = $component;
        }

        protected function getCustomClasses() {
            return $this->customClasses;
        }

        public function addTo(JannieFormComponent $parent) {
            $parent->add($this);
            $this->parent = $parent;
            return $this;
        }

        /**
         * Adds class(es) to this component's node
         * @param string|array $class
         * @return \JannieFormComponent
         */
        public function addClass($class) {
            $targetArray = &$this->customClasses;
            if (!is_array($class))
                $targetArray[] = $class;
            else
                $targetArray = array_merge($targetArray, $class);
            return $this;
        }

        public function attr($attr, $value) {
            $this->customAttributes[$attr] = $value;
            return $this;
        }

        /**
         * Sets whether the component is active
         * @param boolean $active
         */
        public function setActive($active = true) {
            $this->active = $active;
            return $this;
        }

        /**
         * Checks whether the component is active
         * @return boolean
         */
        public function isActive() {
            return $this->active;
        }

        /**
         * Returns a flat list of the component's enabled children
         * @param boolean $recursive
         * @param boolean $includeInactiveFields whether to include inactive fields as well
         * @return array
         */
        public function getChildren($recursive = true, $includeInactiveFields = false) {
            $children = array();
            foreach ($this->children as $component)
                if ($component->isActive() || $includeInactiveFields) {
                    array_push($children, $component);
                    if ($recursive)
                        $children = array_merge($children, $component->getChildren(true, $includeInactiveFields));
                }
            return $children;
        }

        /**
         * Check if given component is child
         * @param JannieFormComponent $child component to search for
         * @param boolean $recursive whether or not to search recursively
         * @return boolean 
         */
        public function hasChild($child, $recursive = true) {
            foreach ($this->children as $component)
                if ($component == $child || ($recursive && $component->hasChild($child, true)))
                    return true;
            return false;
        }

        /**
         * Gets all HTML attributes
         * @return array array of attributes
         */
        public function getAttributes() {
            return array_merge(
                    $this->customAttributes, array('class' => implode(' ', $this->getClasses())));
        }

        /**
         * Gets all HTML attributes
         * @return string attributes as string
         */
        public function getAttributesString() {
            $attributePairs = array();
            foreach ($this->getAttributes() as $attr => $value)
                $attributePairs[] = "$attr=\"" . str_replace("\"", "\\\"", $value) . "\"";
            return implode(' ', $attributePairs);
        }

        /**
         * Gets HTML class attribute
         * @return array array of classes
         */
        public function getClasses() {
            return array_merge(array(
                'jfcomponent'
                    ), $this->customClasses);
        }

        public function getLocalSlug() {
            return $this->slug;
        }

        public function getGlobalSlug() {
            if ($this->parent)
                return $this->parent->getGlobalSlug() . '-' . $this->slug;
            else
                return $this->slug;
        }

        public function getRoot() {
            if ($this->parent)
                return $this->parent->getRoot();
            else
                return $this;
        }

    }

    class JannieFormGroupComponent extends JannieFormComponent {

        public function getHTML($before = '', $after = '', $prefix = '', $suffix = '') {
            $html = '';
            foreach ($this->children as $component)
                if ($component->isActive()) {
                    /* @var $component JannieFormFieldComponent */
                    $componentHTML = (
                            ($component->isVisible() || $component->renderWhenHidden() == JannieFormFieldComponent::RM_DEFAULT ) ?
                                    $component->getHTML() :
                                    ($component->renderWhenHidden() == JannieFormFieldComponent::RM_HIDDENFIELD ? $component->renderHiddenField() : '')
                            );
                    $html .= $prefix . ( $component->isVisible() ? ($before . $componentHTML . $after) : $component->getHTML() ) . $suffix;
                }

            return $html;
        }

        public function isValid() {
            foreach ($this->children as $component)
                if (!$component->isValid() && $component->isActive())
                    return false;
            return true;
        }

    }

    abstract class JannieAjaxMethod {

        private $slug,
                $active = false;

        public function __construct($slug) {
            $this->slug = $slug;
        }

        public function getSlug() {
            return $this->slug;
        }

        /**
         * Enables this ajax method on the current page
         * @param boolean $active
         */
        public function activate($active = true) {
            $this->active = $active;
            return $this;
        }

        /**
         * Checks whether the method has been activated
         * @return boolean
         */
        public function isActive() {
            return $this->active;
        }

        /**
         * Performs an action based on given form values
         */
        public abstract function run(JannieFormData $form);
    }

    interface JannieFormData {

        public function v($q, $d = '');
    }

    class JannieFormResults implements JannieFormData {

        private $values = array();

        public function __construct($values) {
            $this->values = $values;
        }

        /**
         * Searches form fields
         * @param string $q id to search for
         * @return bool|JannieFormFieldComponent closest match or false if not found
         */
        public function f($q) {
            $minLength = -1;
            $f = false;
            foreach ($this->values as $field => $value)
                if (preg_match("/^(.*)" . preg_quote($q) . "$/", $field, $matches)) {
                    $l = strlen($matches[1]);
                    if ($l < $minLength || $minLength == -1) {
                        $minLength = $l;
                        $f = $field;
                    }
                }
            return $f;
        }

        /**
         * Searches form fields and returns its value
         * @param string $q id to search for
         * @param string $d default value
         * @return string value of closest match or default value if field not found
         */
        public function v($q, $d = '') {
            $f = $this->f($q);
            if ($f != false)
                return $this->values[$f];
            else
                return $d;
        }

    }

    class JannieForm extends JannieFormGroupComponent implements JannieFormData {

        const TARGET_SELF = "_self";
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
                $attachRefererOnInstantiate = false,
                $activateCallbacks = array(),
                $javascriptCallback = array(),
                $isUserSubmitted = false,
                $isProcessed = false,
                $isCallbacksubmitted = false,
                $dataFields;

        /**
         * Creates a new JannieForm
         * @param string $slug
         * @param string $action
         * @param JannieFormMethod $method
         * @param string $target 
         */
        public function __construct($slug, $action, $method, $target = self::TARGET_SELF) {
            parent::__construct($slug);
            $this->action = $action;
            $this->method = $method;
            $this->target = $target;
            $this->dataFields[$this->getSubmitConfirmFieldName()] = 'true';
            $this->register();
        }

        /**
         * Registers form globally
         * @return \JannieForm this
         */
        private function register() {
            JannieForms::registerForm($this);
            return $this;
        }

        /**
         * Searches form fields
         * @param string $q id to search for
         * @return bool|JannieFormFieldComponent closest match or false if not found
         */
        public function f($q, $includeInactiveFields = false) {
            $minLength = -1;
            $f = false;
            foreach ($this->getFields($includeInactiveFields) as $field)
                if (preg_match("/^(.*)" . preg_quote($q) . "$/", $field->getGlobalSlug(), $matches)) {
                    $l = strlen($matches[1]);
                    if ($l < $minLength || $minLength == -1) {
                        $minLength = $l;
                        $f = $field;
                    }
                }
            return $f;
        }

        /**
         * Searches form fields and returns its value
         * @param string $q id to search for
         * @param string $d default value
         * @return string value of closest match or default value if field not found
         */
        public function v($q, $d = '') {
            $f = $this->f($q);
            if ($f != false)
                return $f->getValue();
            else
                return $d;
        }

        /**
         * Returns an array of fields of which data is to be collected
         * @return array
         */
        public function c() {
            $fields = array();
            /* @var $field JannieFormFieldComponent */
            foreach ($this->getFields() as $field)
                if ($field->getCollectData())
                    $fields[] = $field;
            return $fields;
        }

        /**
         * Adds a new form-level validator
         * @param JannieFormValidator $v validator
         * @return \JannieForm this
         */
        public function addValidator(JannieFormValidator $v) {
            $this->validators[] = $v;
            return $this;
        }

        public function getDataFieldName($slug) {
            return $this->getGlobalSlug() . '-data-' . $slug;
        }

        public function getSubmitConfirmFieldName() {
            return $this->getDataFieldName('submit');
        }

        public function getField($localSlug) {
            foreach ($this->getFields() as $field)
                if ($field->getLocalSlug() == $localSlug)
                    return $field;
        }

        public function getValue($key, $default = '') {
            return $this->method->getValue($key, $default);
        }

        public function hasValue($key) {
            return $this->method->hasValue($key);
        }

        public function enableAJAX(JannieAjaxMethod $method, $ajaxSubmitEnabled = true) {
            $this->ajaxMethod = $method;
            $this->ajaxSubmitEnabled = $ajaxSubmitEnabled;
        }

        public function attachCallback($callback) {
            $this->callback[] = $callback;
        }

        public function attachActivateCallback($callback) {
            $this->activateCallbacks[] = $callback;
        }

        public function attachJavascriptCallback($callback) {
            $this->javascriptCallback[] = $callback;
        }

        public function getDataFields() {
            return $this->dataFields;
        }

        public function getAction() {
            return $this->action;
        }

        public function setAction($action) {
            $this->action = $action;
        }

        public function getAttributes() {
            return array_merge(parent::getAttributes(), $this->method->getFormAttributes(), array(
                'id' => $this->getID(),
                'action' => $this->action,
                'target' => $this->target
            ));
        }

        public function getClasses() {
            return array_merge(parent::getClasses(), array(
                'jannieform'
            ));
        }

        public function getScript() {
            return "jQuery(function(){ jQuery('#" . $this->getID() . "').jannieform(" . json_encode($this->getJsonData()) . "); });";
        }

        public function getScriptHTML() {
            return "<script type='text/javascript'>
            " . $this->getScript() . "
                </script>";
        }

        protected function renderFormError($errorMsg) {
            return "<div class=\"form-error\">" . $errorMsg . "</div>";
        }

        /**
         * Renders complete form markup. Use this to echo the form to the webpage.
         * @return string
         */
        public function getHTML() {
            if ($this->attachRefererOnInstantiate)
                $this->attachReferer();
            $dataFields = '';
            foreach ($this->dataFields as $key => $value)
                $dataFields .= "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
            $errors = '';
            foreach ($this->validators as $validator)
                if (!$validator->isValid())
                    $errors .= $this->renderFormError($validator->getErrorMsg());
            return "<form " . $this->getAttributesString() . ">" . $errors . $dataFields . parent::getHTML() . "</form>" . $this->getScriptHTML();
        }

        public function getID() {
            return "form-" . $this->getGlobalSlug();
        }

        /**
         * Retrieves an array of this form's fields
         * @param boolean $includeInactiveFields whether to include inactive fields as well
         * @return array
         */
        public function getFields($includeInactiveFields = false) {
            $fields = array();
            foreach ($this->getChildren(true, $includeInactiveFields) as $component)
                if (is_subclass_of($component, "JannieFormFieldComponent"))
                    array_push($fields, $component);
            return $fields;
        }

        public function getJsonData() {
            $fields = array();
            foreach ($this->getFields() as $component)
                $fields[$component->getID()] = $component->getJsonData();
            return array(
                "slug" => $this->getLocalSlug(),
                "fields" => $fields,
                "dataFields" => $this->dataFields,
                "submitCallback" => $this->javascriptCallback,
                "isProcessed" => $this->isProcessed,
                "isActivated" => $this->isUserSubmitted,
                "isSubmitted" => $this->isCallbacksubmitted,
                "ajax" => array(
                    "submitEnabled" => $this->ajaxSubmitEnabled,
                    "method" => $this->ajaxMethod !== null ? $this->ajaxMethod->getSlug() : "",
                    "results" => $this->ajaxResult
                )
            );
        }

        public function activateHandlers() {
            foreach ($this->activateCallbacks as $psCallback)
                if (is_callable($psCallback))
                    call_user_func($psCallback, $this);
        }

        /**
         * Submits the form internally. You're not usually supposed to call this function directly.
         */
        public function submit() {
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

        /**
         * Determines referer URL and attaches it to this form as a field with given slug and label
         * 
         * @param type $via
         * @param type $slug
         * @param type $label
         */
        public function attachReferer($via = "", $slug = "referer", $label = "Referer URL") {
            $refererUrl = $_SERVER['HTTP_REFERER'];
            $referer = $refererUrl;
            $page_id = url_to_postid($refererUrl);

            if ($page_id != 0) {
                $page = get_page($page_id);
                $referer = $page->post_title . (!empty($via) ? " > {$via}" : "");
            }

            $refererField = new JannieHiddenField($fieldId, $fieldLabel, $referer);
            $refererField->
                    addTo($this);
        }

        public function attachRefererOnInstantiate() {
            $this->attachRefererOnInstantiate = true;
        }

        public function isValid() {
            if (!parent::isValid())
                return false;
            foreach ($this->validators as $validator)
                if (!$validator->process($this))
                    return false;
            return true;
        }

        public function getErrorMessages() {
            $e = array();
            foreach ($this->validators as $validator)
                if (!$validator->isValid())
                    $e[] = $validator->getErrorMsg();
            return $e;
        }

        /**
         * If the form has Ajax handlers but was still submitted to a new page, handles Ajax handlers.
         */
        private function processAjaxLocally() {
            $this->ajaxResult = $this->ajaxMethod->run($this);
        }

        /**
         * Check if form was activated, then validates, calls handlers, then submits. This is the primary function to be called before displaying the form.
         */
        public function process() {
            if ($this->isProcessed)
                return;
            $this->isProcessed = true;
            foreach ($this->getFields() as $field)
                $field->preprocess();
            $this->isUserSubmitted = $this->getValue($this->getSubmitConfirmFieldName(), 'false') == 'true';
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
        public function isProcessed() {
            return $this->isProcessed;
        }

        /**
         * Indicates whether the form was submitted, regardless of whether it validated correctly
         * @return boolean
         */
        public function isSubmitted() {
            return $this->isUserSubmitted;
        }

        /**
         * Indicates whether the form was validated correctly and submitted.
         * @return boolean
         */
        public function isSuccess() {
            return $this->isCallbacksubmitted;
        }

        // TODO name these methods more intuitively
    }

    abstract class JannieFormMethod {

        public function getFormAttributes() {
            return array();
        }

        public abstract function getValue($key, $default);

        public abstract function hasValue($key);
    }

    class JannieFormGetMethod extends JannieFormMethod {

        public function getFormAttributes() {
            return array('method' => 'GET');
        }

        public function getValue($key, $default) {
            return isset($_GET[$key]) ? $_GET[$key] : $default;
        }

        public function hasValue($key) {
            return isset($_GET[$key]);
        }

    }

    class JannieFormPostMethod extends JannieFormMethod {

        public function getFormAttributes() {
            return array('method' => 'POST');
        }

        public function getValue($key, $default) {
            return isset($_POST[$key]) ? $_POST[$key] : $default;
        }

        public function hasValue($key) {
            return isset($_POST[$key]);
        }

    }

    class JannieFormArbitraryHTML extends JannieFormComponent {

        private $html;

        public function __construct($slug, $html) {
            parent::__construct($slug);
            $this->html = $html;
        }

        public function getHTML() {
            return $this->html;
        }

        public function isValid() {
            return true;
        }

    }

    abstract class JannieFormFieldComponent extends JannieFormComponent {

        const DEFAULT_COOKIE_LIFETIME = 2592000; // 60 * 60 * 24 * 30  ->  30 days
        const RM_NONE = 'none';
        const RM_HIDDENFIELD = 'hidden';
        const RM_DEFAULT = 'default';

        protected
                $label,
                $visible = true,
                $value,
                $latestErrorMsg = "",
                $forceInvalid = false,
                $validators = array(),
                $sanitizers = array(),
                $collectData = true,
                $storeValueLocally;

        /**
         * Creates a new form field component
         * @param string $slug internal slug to use
         * @param string $label label to display
         * @param string $value default value
         * @param int $storeValueLocally how long to store last used value in cookie (set to 0 for no cookie)
         */
        public function __construct($slug, $label, $value = '', $storeValueLocally = 0) {
            parent::__construct($slug);
            $this->label = $label;
            $this->value = $value;
            $this->storeValueLocally = $storeValueLocally;
        }

        public function setCollectData($collectData) {
            $this->collectData = $collectData;
        }

        public function getCollectData() {
            return $this->collectData;
        }

        public function restoreValue($method, $sanitize = true) {
            $v = stripslashes($method->getValue($this->getName(), $this->value));
            if ($sanitize)
                foreach ($this->sanitizers as $s)
                    $v = $s->sanitize($v);
            $this->value = $v;
        }

        public function submit() {
            if ($this->storeValueLocally)
                setcookie($this->getCookieName(), $this->value, time() + $this->storeValueLocally, '/');
        }

        public function preprocess() {
            if ($this->storeValueLocally && isset($_COOKIE[$this->getCookieName()]))
                $this->value = $_COOKIE[$this->getCookieName()];
        }

        private function getCookieName() {
            return $this->getGlobalSlug() . '-vc';
        }

        public function getName() {
            return $this->getGlobalSlug();
        }

        public function getLabel() {
            return $this->label;
        }

        public function getValue() {
            return $this->value;
        }

        public function setValue($value) {
            $this->value = $value;
            return $this;
        }

        /**
         * Sets whether the component is visible
         * @param type $visible
         */
        public function setVisible($visible = true) {
            $this->visible = $visible;
            return $this;
        }

        /**
         * Checks whether the component is visible
         * @return type
         */
        public function isVisible() {
            return $this->visible;
        }

        public function renderWhenHidden() {
            return self::RM_HIDDENFIELD;
        }

        public function renderHiddenField() {
            return "<input type='hidden'" . $this->getAttributesString() . ">";
        }

        public function getJsonData() {
            $v = [];
            $s = [];
            foreach ($this->validators as $validator)
                array_push($v, $validator->getJsonData());
            foreach ($this->sanitizers as $sanitizer)
                $s[] = $sanitizer->getJsonData();
            return array(
                'validators' => $v,
                'sanitizers' => $s,
                'id' => $this->getID(),
                'name' => $this->getName(),
                'collectData' => $this->collectData
            );
        }

        public function getId() {
            return "field-" . $this->getGlobalSlug();
        }

        public function getAttributes() {
            return array_merge(parent::getAttributes(), array(
                "name" => $this->getName(),
                "value" => $this->getValue(),
                "id" => $this->getId()
            ));
        }

        public function getClasses() {
            return array_merge(parent::getClasses(), array(
                "janniefield",
                ($this->getRoot()->isSubmitted() ? ($this->isValid() ? "valid" : "invalid") : "")
            ));
        }

        public function addValidator(JannieFormFieldValidator $v) {
            $this->validators[] = $v;
            $v->setField($this);
            return $this;
        }

        public function addSanitizer(JannieFormFieldSanitizer $s) {
            $this->sanitizers[] = $s;
            return $this;
        }

        public function forceInvalid() {
            $this->forceInvalid = true;
        }

        public function isValid() {
            if ($this->forceInvalid)
                return false;
            foreach ($this->validators as $validator)
                if (!$validator->isValid($this->value)) {
                    $this->latestErrorMsg = $validator->getErrorMsg();
                    return false;
                }
            return true;
        }

        public function sanitize() {
            foreach ($this->sanitizers as $sanitizer)
            /* @var $sanitizer JannieFormFieldSanitizer */
                $this->value = $sanitizer->sanitize($this->value);
        }

        public function getLatestErrorMsg() {
            return $this->latestErrorMsg;
        }

    }

    class JannieRadioSelect extends JannieFormFieldComponent {

        private $options;

        public function __construct($slug, $label, $options, $value = '', $storeValueLocally = 0) {
            parent::__construct($slug, $label, $value, $storeValueLocally);
            $this->options = $options;
        }

        public function getClasses() {
            return array_merge(
                    parent::getClasses(), array('radios', 'jannieradios')
            );
        }

        public function getHTML() {
            $r = "<div id=\"wrap-" . $this->getName() . "\" class=\"radios-group\"><div class=\"radios-label\">" . $this->getLabel() . "</div>";
            foreach ($this->options as $v => $l) {
                $r .= "<div class=\"radio-option\"><input type=\"radio\" id=\"" . $this->getGlobalSlug() . "-" . $v . "\" name=\"" . $this->getName() . "\" value=\"" . $v . "\" " . ($v == $this->getValue() ? "checked" : "") . "><label for=\"" . $this->getGlobalSlug() . "-" . $v . "\">" . $l . "</label></div>";
            }
            $r .= "</div>";
            return $r;
        }

    }

    class JannieHiddenField extends JannieFormFieldComponent {

        public function isVisible() {
            return false;
        }

        public function renderWhenHidden() {
            return self::RM_DEFAULT;
        }

        public function getHTML() {
            return "<input type='hidden'" . $this->getAttributesString() . ">";
        }

    }

    class JannieTextField extends JannieFormFieldComponent {

        const ON_ENTER_NEXT = 'next';
        const ON_ENTER_SUBMIT = 'submit';

        private $onEnter = '',
                $mask = null;

        public function getAttributes() {
            $att = array('placeholder' => $this->label);
            if (!empty($this->onEnter))
                $att['data-input-action-on-enter'] = $this->onEnter;
            if ($this->mask !== null)
                $att['data-input-mask'] = $this->mask;
            return array_merge(parent::getAttributes(), $att);
        }

        public function onEnter($action = '') {
            $this->onEnter = $action;
            return $this;
        }

        /**
         * Sets input mask for this field
         * @param string $mask
         * @return \JannieTextField
         */
        public function setMask($mask) {
            $this->mask = $mask;
            return $this;
        }

        public function getClasses() {
            return array_merge(
                    parent::getClasses(), array('textfield', 'jannieinputfield')
            );
        }

        public function getHTML() {
            return "<input type='text'" . $this->getAttributesString() . ">";
        }

    }

    class JanniePassField extends JannieTextField {

        public function getClasses() {
            return array_merge(
                    parent::getClasses(), array('passfield')
            );
        }

        public function getHTML() {
            return "<input type='password'" . $this->getAttributesString() . ">";
        }

    }

    class JannieTextarea extends JannieFormFieldComponent {

        public function getAttributes() {
            return array_merge(parent::getAttributes(), array(
                'placeholder' => $this->label
            ));
        }

        public function getClasses() {
            return array_merge(
                    parent::getClasses(), array('textarea', 'jannieinputfield')
            );
        }

        public function getHTML() {
            return "<textarea " . $this->getAttributesString() . ">" . $this->value . "</textarea>";
        }

    }

    class JannieButton extends JannieFormFieldComponent {

        const TYPE_SUBMIT = "submit";
        const TYPE_BUTTON = "button";

        private $type, $icon, $glyphIcon, $isClicked = false, $title = "", $use_shim = true;

        public function __construct($name, $label, $value = "", $type = JannieButton::TYPE_BUTTON) {
            parent::__construct($name, $label, $value);
            $this->type = $type;
            $this->collectData = false;
        }

        public function isClicked() {
            return $this->isClicked;
        }

        /**
         * Sets whether the button uses a shim display node
         * @param boolean $use_shim
         * @return \JannieButton
         */
        public function setUseShim($use_shim = true) {
            $this->use_shim = $use_shim;
            return $this;
        }

        public function setTitle($title = "") {
            $this->title = $title;
            return $this;
        }

        public function setIcon($file) {
            $this->icon = $file;
            return $this;
        }

        public function setGlyphIcon($code) {
            $this->glyphIcon = $code;
            return $this;
        }

        public function restoreValue($method) {
            $this->isClicked = $method->hasValue($this->getName());
        }

        public function getAttributes() {
            return array_merge(parent::getAttributes(), array(
                'type' => $this->type,
                'value' => $this->label
            ));
        }

        public function getClasses() {
            return array_merge(
                    parent::getClasses(), array('button', 'invisible-target-button')
            );
        }

        public function getHTML() {
            $icon = ($this->icon == '' ? '' : '<img class="button-icon" src="' . $this->icon . '"> ');
            $glyphIcon = !empty($this->glyphIcon) ? '<i class="button-icon icon-' . $this->glyphIcon . '"></i> ' : '';
            if ($this->use_shim)
                return
                        "<div class=\"button-wrap " . ($this->use_shim ? "uses-shim" : "") . "\">"
                        . "<input " . $this->getAttributesString() . ">"
                        . "<a id=\"target-" . $this->getId() . "\" " . (!empty($this->title) ? " title=\"{$this->title}\" " : "") . " class=\"small_button targeting-button " . implode(' ', $this->getCustomClasses()) . "\">" . $icon . $glyphIcon . $this->label .
                        "</a>"
                        . "</div>";
            else
                return
                        "<input " . $this->getAttributesString() . ">";
        }

        public function getJsonData() {
            return array_merge(parent::getJsonData(), array(
                'isButton' => true
            ));
        }

    }

    class JannieHorizontalGroup extends JannieFormGroupComponent {

        const CLASS_TWO_ITEMS = 'two-items';
        
        public function getClasses() {
            return array_merge(parent::getClasses(), array(
                'horizontalgroup'
            ));
        }

        public function getHTML() {
            return '<div ' . $this->getAttributesString() . '>' . parent::getHTML('<div class="group-item horizontal-group-item">', '</div>') . '</div>';
        }

    }

    class JannieRecaptcha extends JannieFormFieldComponent {

        private $public_key = "";

        public function __construct($label, $public_key) {
            parent::__construct('recaptcha-response-field', $label, "");
            $this->public_key = $public_key;
            $this->collectData = false;
        }

        public function getClasses() {
            return array_merge(parent::getClasses(), array(
                'jannierecaptcha'
            ));
        }

        public function getHTML() {
            $rs = recaptcha_get_html($this->public_key);
            return '<div data-recaptcha-response-field-name="' . $this->getName() . '" field-id="' . $this->getID() . '" class="' . implode(' ', $this->getClasses()) . '">' . $rs . '</div>';
        }

    }

    abstract class SynchronizedObject {

        abstract function getJsonData();
    }

    abstract class JannieFormFieldSanitizer extends SynchronizedObject {

        public abstract function sanitize($value);

        public abstract function describeMethod();

        public abstract function isLive();

        public function getJsonData() {
            return [
                'method' => $this->describeMethod(),
                'live' => $this->isLive()
            ];
        }

    }

    class JannieFormFieldCapitalizer extends JannieFormFieldSanitizer {

        public function sanitize($value) {
            return ucwords($value);
        }

        public function isLive() {
            return true;
        }

        public function describeMethod() {
            return 'capitalize';
        }

    }

    class JannieFormFieldUppercaser extends JannieFormFieldSanitizer {

        public function sanitize($value) {
            return strtoupper($value);
        }

        public function isLive() {
            return true;
        }

        public function describeMethod() {
            return 'uppercase';
        }

    }

    abstract class JannieFormValidator {

        private $errorMsg = '', $inflictsFields, $isValid = true;

        /**
         * @param string $errorMsg Error message to show if this validator returns false
         * @param array $inflictsFields Array of names of fields to be marked "invalid" upon rendering the form if this validator returns false
         */
        public function __construct($errorMsg, $inflictsFields = array()) {
            $this->errorMsg = $errorMsg;
            $this->inflictsFields = $inflictsFields;
        }

        public function getErrorMsg() {
            return $this->errorMsg;
        }

        public function process($form) {
            if (( $this->isValid = $this->validate($form)))
                return true;
            else {
                foreach ($this->inflictsFields as $if)
                    if (($field = $form->f($if)))
                        $field->forceInvalid();
                return false;
            }
        }

        public function isValid() {
            return $this->isValid;
        }

        public abstract function validate(JannieForm $form);
    }

    abstract class JannieFormFieldValidator extends SynchronizedObject {
        /* @var $field JannieFormFieldComponent */

        private $errorMsg = '', $field;

        public function __construct($errorMsg) {
            $this->errorMsg = $errorMsg;
        }

        public function getJsonData() {
            return array(
                'error' => $this->errorMsg,
                'method' => $this->describeMethod()
            );
        }

        public function setField($f) {
            $this->field = $f;
        }

        public function getField() {
            return $this->field;
        }

        public function getErrorMsg() {
            return $this->errorMsg;
        }

        public abstract function isValid($value);

        public abstract function describeMethod();
    }

    class JannieFormRegexFieldValidator extends JannieFormFieldValidator {

        private $pattern;

        public function __construct($pattern, $errorMsg) {
            parent::__construct($errorMsg);
            $this->pattern = $pattern;
        }

        public function getJsonData() {
            return array_merge(parent::getJsonData(), array(
                'pattern' => $this->pattern
            ));
        }

        public function isValid($value) {
            return preg_match($this->pattern, $value);
        }

        public function describeMethod() {
            return 'regex';
        }

    }

    class JannieFormFieldContentValidator extends JannieFormRegexFieldValidator {

        public function __construct($error = "Verplicht veld.") {
            parent::__construct("/./", $error);
        }

    }

    class JannieFormFieldLengthValidator extends JannieFormRegexFieldValidator {

        public function __construct($min, $max, $allowed = null, $message = null) {
            if ($allowed == null)
                $allowed = 'a-zéëèA-Z0-9 .\-\'\"';
            if ($message == null)
                $message = "Moet minimaal $min en maximaal $max tekens bevatten.";
            parent::__construct("/^[" . $allowed . "]{" . $min . "," . $max . "}$/", $message);
        }

    }

    class JannieFormPhoneValidator extends JannieFormRegexFieldValidator {

        const PATT = "/^[0-9.+()\/ -]{4,}$/";
        const ERROR = "Vul een geldig telefoonnummer in.";

        public function __construct() {
            parent::__construct(self::PATT, self::ERROR);
        }

    }

    class JannieFormEmailValidator extends JannieFormRegexFieldValidator {

        const PATT = "/^[A-Z0-9._%\-+]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
        const ERROR = "Vul een geldig e-mailadres in.";

        public function __construct() {
            parent::__construct(self::PATT, self::ERROR);
        }

    }

    define('JANNIEFORMS_LOADED', true);
}