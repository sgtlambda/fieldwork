<?php
/**
 * Copyright (C) Jan-Merijn Versteeg - All Rights Reserved
 * Unauthorized copying of this file, via any medium, is strictly prohibited
 * Proprietary and confidential
 */

namespace fieldwork\core\reflection;

use fieldwork\core\reflection\StateValidationReflection;

class CompoundStateValidationReflection extends StateValidationReflection
{

    /**
     * @var StateValidationReflection[]
     */
    private $reflections;

    public function __construct (array $reflections)
    {
        $this->reflections = $reflections;
        parent::__construct([]);
    }

    public function getErrors ()
    {
        $errors = [];
        foreach ($this->reflections as $reflection)
            $errors = array_merge($errors, $reflection->getErrors());
        return $errors;
    }

    /**
     * @return StateValidationReflection[]
     */
    public function getReflections ()
    {
        return $this->reflections;
    }
}