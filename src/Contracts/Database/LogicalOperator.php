<?php

namespace Khronos\MongoDB\Contracts\Database;

interface LogicalOperator
{
    public function append($expression);
}
