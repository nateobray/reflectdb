<?php

namespace obray\reflectdb\dataTypes;

use \obray\reflectdb\dataTypes\DataTypeInterface;

class Integer implements DataTypeInterface, \JsonSerializable
{
    protected int $size = 11;
    protected bool $unsigned = false;
    protected bool $isNullable = true;
    protected bool $zeroFilled = false;
    protected bool $autoincrement = false;
    protected bool $nullable = true;
    protected ?int $default = null;

    protected ?int $value = null;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __getSQLDataType()
    {
        return \PDO::PARAM_INT|\PDO::PARAM_NULL;
    }

    public function __toSQL(string $column): string
    {
        $sql = 'int(' . $this->size . ') '
             . ($this->unsigned?'unsigned ':'signed ')
             . ($this->nullable===false?' NOT NULL':'')
             . ' DEFAULT '
             . ($this->default==null?'NULL':$this->default);
        return $sql;
    }

    public function __toSQLWhere(string $column, $operator='=')
    {
        return $column . $operator . ':'.$column;
    }

    public function jsonSerialize()
    {    
        return $this->value;
    }
}