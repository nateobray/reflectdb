<?php

namespace obray\reflectdb\dataTypes;

class IntegerTinyUnsigned extends \obray\reflectdb\dataTypes\Integer
{
    protected int $size = 4;
    protected bool $unsigned = true;
}