<?php

namespace obray\reflectdb\dataTypes;

interface DataTypeInterface
{
    public function getValue();
    public function __toSQL(string $column): string;
}