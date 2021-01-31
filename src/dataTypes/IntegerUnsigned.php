<?php

namespace obray\reflectdb\dataTypes;

class IntegerUnsigned extends \obray\reflectdb\dataTypes\Integer
{
    protected int $size = 11;
    protected bool $unsigned = true;
}