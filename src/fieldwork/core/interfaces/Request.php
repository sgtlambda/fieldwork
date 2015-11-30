<?php

namespace fieldwork\core\interfaces;

use fieldwork\core\Field;

interface Request
{

    public function getValue (Field $field);
}