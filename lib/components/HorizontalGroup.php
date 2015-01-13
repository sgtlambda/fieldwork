<?php

namespace jannieforms\components;

class HorizontalGroup extends GroupComponent
{

    const CLASS_TWO_ITEMS = 'two-items';

    public function getClasses ()
    {
        return array_merge(parent::getClasses(), array(
            'horizontalgroup'
        ));
    }

    public function getHTML ()
    {
        return '<div ' . $this->getAttributesString() . '>' . parent::getHTML('<div data-wrapper-for="%slug%" class="group-item horizontal-group-item">', '</div>') . '</div>';
    }

}