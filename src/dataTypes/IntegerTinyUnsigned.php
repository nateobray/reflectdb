<?php

namespace obray\reflectdb\dataTypes;

class IntegerTinyUnsigned extends \obray\reflectdb\dataTypes\Integer
{
    private int $size = 4;
    private bool $unsigned = true;
}