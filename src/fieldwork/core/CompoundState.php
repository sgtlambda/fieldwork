<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */

namespace fieldwork\core;

use fieldwork\core\interfaces\Identifiable;
use fieldwork\core\util\IdentifiableArray;

class CompoundState extends State
{

    private $children;

    public function __construct ($value = null)
    {
        $this->children = new IdentifiableArray();
        parent::__construct($value);
    }

    public function setChild (Identifiable $identifiable, State $state)
    {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        $this->children[$identifiable] = $state;
    }

    public function getChild (Identifiable $identifiable)
    {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $this->children[$identifiable];
    }
}