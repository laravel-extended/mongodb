<?php

namespace Khronos\MongoDB\Database\Aggregation\Stages;

use Khronos\MongoDB\Database\Aggregation\FieldExpression;
use Illuminate\Contracts\Support\Arrayable;

class Unwind implements Arrayable
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function toArray()
    {
        return ['$unwind' => ['path' => (string) new FieldExpression(null, $this->path)]];
    }
}
