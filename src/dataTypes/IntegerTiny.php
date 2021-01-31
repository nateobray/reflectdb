<?php

namespace obray\reflectdb\dataTypes;

class IntegerTiny extends \obray\reflectdb\dataTypes\Integer
{
    protected int $size = 4;
    protected bool $unsigned = false;
}