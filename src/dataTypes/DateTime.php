<?php

namespace obray\reflectdb\dataTypes;

class DateTime implements \obray\reflectdb\dataTypes\DateTypeInterface
{
    public function __construct(string $datetime)
    {
        $this->value = new \DateTime($datetime);
    }

    public function getValue(): \DateTime
    {
        return $this->value;
    }

    public function __toSQL(string $column): string
    {
        return '`'. $column . '` datetime ' . (!empty($this->default)?'DEFAULT '.$this->default:'DEFAULT NULL');
    }
}