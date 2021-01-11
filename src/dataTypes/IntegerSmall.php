<?php

namespace obray\reflectdb\dataTypes;

class Integer extends \obray\reflectdb\dataTypes\Integer
{
    private int $size = 5;
    private bool $unsigned = false;
}