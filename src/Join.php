<?php

namespace obray\reflectdb;

use obray\reflectdb\Querier;

class Join
{
    private string $localColumn;
    private string $localObject;
    private string $foreignColumn;
    private string $foreignObject;
    private Querier $querier;
    private string $relation;

    private array $joins = [];

    public function __construct(string $localColumn, string $localObject, string $foreignColumn, string $foreignObject, string $relation)
    {
        if(!class_exists($foreignObject)) throw new \Exception("Foreign object \"" . $foreignObject . "\" does not exist.");
        if(!property_exists($foreignObject, "col_" . $foreignColumn)) throw new \Exception("Property \"" . $foreignColumn . "\" does not exist.");
        $this->localColumn = $localColumn;
        $this->localObject = $localObject;
        $this->foreignColumn = $foreignColumn;
        $this->foreignObject = $foreignObject;
        $this->querier = new Querier($foreignObject);
        $this->relation = $relation;
    }

    public function add(string $join, string $on)
    {
        $this->querier->join($join, $on);
    }

    public function on($values)
    {
        $this->querier->where($this->foreignObject, $this->foreignColumn, 'IN', $values);
        return $this;
    }

    public function orderBy(string $object, $column)
    {
        $this->querier->orderBy($object, $column);
    }

    public function execute($dbh)
    {
        return $this->querier->execute($dbh);
    }

    public function getLocalColumn()
    {
        return $this->localColumn;
    }

    public function getForeignObject()
    {
        return $this->foreignObject;
    }

    public function getForeignColumn()
    {
        return $this->foreignColumn;
    }

    public function isOneToOne(): bool
    {
        if($this->relation === 'one-to-one') return true;
        return false;
    }
}