<?php

namespace Khronos\MongoDB\Database\Aggregation;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\ConnectionInterface;

class Builder
{
    protected $connection;

    protected $collection;

    protected $pipeline = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function from($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function first()
    {
        return $this->get()->first();
    }

    public function get()
    {
        [$method, $query, $options] = $this->toQueryOptions();

        return collect($this->connection->aggregate($query, $options));
    }

    public function pipeline($stage)
    {
        $this->pipeline[] = $stage->toArray();

        return $this;
    }

    public function toQueryOptions()
    {
        return ['aggregate', [
            'collection' => $this->collection,
            'pipeline' => $this->pipeline], []
        ];
    }

    public function __call($method, $parameters)
    {
        $stage = __NAMESPACE__.'\\Stages\\'.Str::studly($method);
        if (class_exists($stage)) {
            return $this->pipeline(new $stage(...$parameters));
        }

        throw new \Exception(sprintf('Invalid %s stage.', Str::studly($method)));
    }

    public function dump($stages = [])
    {
        return $this->debug(__FUNCTION__, $stages);
    }

    public function dd($stages = [])
    {
        return $this->debug(__FUNCTION__, $stages);
    }

    public function debug($function, $stages = [])
    {
        $function(array_filter($this->pipeline, function ($stage) use ($stages) {
            return $stages ? in_array(ltrim(key($stage), '$'), Arr::wrap($stages)) : true;
        }));

        return $this;
    }
}
