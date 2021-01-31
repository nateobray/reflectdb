<?php

namespace obray\reflectdb\dataTypes;

class DateTime implements \obray\reflectdb\dataTypes\DataTypeInterface
{
    public function __construct($datetime)
    {
        $this->value = new \DateTime($datetime);
    }

    public function __getSQLDataType()
    {
        return \PDO::PARAM_STRING|\PDO::PARAM_NULL;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toSQL(string $column): string
    {
        return '"' . $this->value->format("Y-m-d H:i:s") . '"';
    }
}