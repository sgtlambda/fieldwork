<?php

namespace fieldwork\components;

class GroupComponent extends Component
{

    public function getHTML ($beforeTpl = '', $after = '', $prefix = '', $suffix = '')
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
                $globalSlug    = $component->getGlobalSlug();
                $before        = str_replace('%slug%', $globalSlug, $beforeTpl);
                $before        = str_replace('%classes%', implode(' ', $component->getWrapperClasses()), $before);
                $inner         = $component->isVisible() ? ($before . $componentHTML . $after) : $component->getHTML();
                $html .= $prefix . ($inner) . $suffix;
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