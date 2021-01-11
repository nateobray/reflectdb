<?php

namespace obray\reflectdb\dataTypes;

use \obray\reflectdb\dataTypes\DataTypeInterface;

class Integer implements DataTypeInterface
{
    private int $size = 11;
    private bool $unsigned = false;
    private bool $isNullable = true;
    private bool $zeroFilled = false;
    private bool $autoincrement = false;
    private bool $nullable = true;
    private ?int $default = null;

    private ?int $value = null;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
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
}