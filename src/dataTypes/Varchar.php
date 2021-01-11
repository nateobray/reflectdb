<?php

namespace obray\reflectdb\dataTypes;

class Varchar implements \obray\reflectdb\dataTypes\DataTypeInterface
{
    private int $size = 255;
    private string $value;

    public function __construct(string $value)
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

}