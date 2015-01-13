<?php

namespace jannieforms\components;

abstract class AbstractComponent
{

    protected
        $parent,
        $children         = array();
    private
        $slug,
        $active           = true,
        $customClasses    = array(),
        $customAttributes = array();

    public function __construct ($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Resets the component
     */
    public function reset ()
    {
        foreach ($this->getChildren(false) as $child)
            $child->reset();
    }

    public abstract function getHTML ();

    public abstract function isValid ();

    public function isVisible ()
    {
        return true;
    }

    protected function add (AbstractComponent $component)
    {
        $this->children[] = $component;
    }

    protected function getCustomClasses ()
    {
        return $this->customClasses;
    }

    /**
     * Adds component to given parent component
     *
     * @param AbstractComponent $parent
     *
     * @return $this
     */
    public function addTo (AbstractComponent $parent)
    {
        $parent->add($this);
        $this->parent = $parent;
        return $this;
    }

    /**
     * Adds class(es) to this component's node
     *
     * @param string|array $class
     *
     * @return $this
     */
    public function addClass ($class)
    {
        $targetArray = &$this->customClasses;
        if (!is_array($class))
            $targetArray[] = $class;
        else
            $targetArray = array_merge($targetArray, $class);
        return $this;
    }

    /**
     * Sets a custom attribute
     *
     * @param string $attr  Attribute name
     * @param string $value Attribtue value
     *
     * @return $this
     */
    public function attr ($attr, $value)
    {
        $this->customAttributes[$attr] = $value;
        return $this;
    }

    /**
     * Sets whether the component is active
     *
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive ($active = true)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Checks whether the component is active
     * @return boolean
     */
    public function isActive ()
    {
        return $this->active;
    }

    /**
     * Returns a flat list of the component's enabled children
     *
     * @param boolean $recursive
     * @param boolean $includeInactiveFields whether to include inactive fields
     *
     * @return array
     */
    public function getChildren ($recursive = true, $includeInactiveFields = false)
    {
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
     *
     * @param AbstractComponent $child     component to search for
     * @param boolean           $recursive whether or not to search recursively
     *
     * @return boolean
     */
    public function hasChild ($child, $recursive = true)
    {
        foreach ($this->children as $component)
            if ($component == $child || ($recursive && $component->hasChild($child, true)))
                return true;
        return false;
    }

    /**
     * Gets all HTML attributes
     * @return array array of attributes
     */
    public function getAttributes ()
    {
        return array_merge(
            $this->customAttributes, array('class' => implode(' ', $this->getClasses())));
    }

    /**
     * Gets all HTML attributes
     * @return string attributes as string
     */
    public function getAttributesString ()
    {
        $attributePairs = array();
        foreach ($this->getAttributes() as $attr => $value)
            $attributePairs[] = "$attr=\"" . str_replace("\"", "\\\"", $value) . "\"";
        return implode(' ', $attributePairs);
    }

    /**
     * Gets HTML class attribute
     * @return array array of classes
     */
    public function getClasses ()
    {
        return array_merge(array(
            'jfcomponent'
        ), $this->customClasses);
    }

    public function getLocalSlug ()
    {
        return $this->slug;
    }

    public function getGlobalSlug ()
    {
        if ($this->parent)
            return $this->parent->getGlobalSlug() . '-' . $this->slug;
        else
            return $this->slug;
    }

    public function getRoot ()
    {
        if ($this->parent)
            return $this->parent->getRoot();
        else
            return $this;
    }

}