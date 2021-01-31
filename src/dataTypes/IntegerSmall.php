<?php

namespace obray\reflectdb\dataTypes;

class IntegerSmall extends \obray\reflectdb\dataTypes\Integer
{
    protected int $size = 5;
    protected bool $unsigned = false;
}