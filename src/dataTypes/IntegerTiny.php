<?php

namespace obray\reflectdb\dataTypes;

class Integer extends \obray\reflectdb\dataTypes\Integer
{
    private int $size = 4;
    private bool $unsigned = false;
}