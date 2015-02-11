<?php

namespace fieldwork\components;

class GroupComponent extends Component
{

    public function getHTML ($before = '', $after = '', $prefix = '', $suffix = '')
    {
        $html = '';
        foreach ($this->children as $component)
            /* @var $component static */
            if ($component->isActive()) {
                $componentHTML = (
                ($component->isVisible() || $component->renderWhenHidden() == Field::RM_DEFAULT) ?
                    $component->getHTML() :
                    ($component->renderWhenHidden() == Field::RM_HIDDENFIELD ? $component->renderHiddenField() : '')
                );
                $html .= $prefix . ($component->isVisible() ? (str_replace('%slug%', $component->getGlobalSlug(), $before) . $componentHTML . $after) : $component->getHTML()) . $suffix;
            }

        return $html;
    }

    public function isValid ()
    {
        foreach ($this->children as $component)
            /* @var $component Component */
            if (!$component->isValid() && $component->isActive())
                return false;
        return true;
    }

}