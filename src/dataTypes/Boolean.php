<?php

namespace obray\reflectdb\dataTypes;

class Boolean implements \obray\reflectdb\dataTypes\DataTypeInterface
{
    private int $size = 1;
    private bool $unsigned = true;

    private string $value;

    public function __construct($value)
    {
        print_r($value);
        $this->value = (bool)$value;
    }

    public function __getSQLDataType()
    {
        return \PDO::PARAM_BOOL|\PDO::PARAM_NULL;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toSQL(string $column): string
    {
        return $this->value;
    }

    public function __toSQLWhere(string $column, $operator='=')
    {
        return $column . $operator . ':' . $column;
    }
}