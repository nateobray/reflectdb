<?php

namespace obray\reflectdb\dataTypes;

class Varchar implements \obray\reflectdb\dataTypes\DataTypeInterface, \JsonSerializable
{
    protected int $size = 255;
    protected ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toSQL(string $column): string
    {
        return '`'.$column.'` varchar('.$this->size.') ' . (!empty($this->default)?'DEFAULT NULL':'DEFAULT `'.$this->default.'`');
    }

    public function __getSQLDataType()
    {
        return \PDO::PARAM_STR|\PDO::PARAM_NULL;
    }

    public function jsonSerialize()
    {    
        return $this->value;
    }
}