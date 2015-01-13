<?php

namespace jannieforms\components;

class GroupComponent extends AbstractComponent
{

    public function getHTML ($before = '', $after = '', $prefix = '', $suffix = '')
    {
        $html = '';
        foreach ($this->children as $component)
            if ($component->isActive()) {
                $componentHTML = (
                ($component->isVisible() || $component->renderWhenHidden() == AbstractField::RM_DEFAULT) ?
                    $component->getHTML() :
                    ($component->renderWhenHidden() == AbstractField::RM_HIDDENFIELD ? $component->renderHiddenField() : '')
                );
                $html .= $prefix . ($component->isVisible() ? (str_replace('%slug%', $component->getGlobalSlug(), $before) . $componentHTML . $after) : $component->getHTML()) . $suffix;
            }

        return $html;
    }

    public function isValid ()
    {
        foreach ($this->children as $component)
            if (!$component->isValid() && $component->isActive())
                return false;
        return true;
    }

}