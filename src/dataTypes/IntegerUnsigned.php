<?php

namespace obray\reflectdb\dataTypes;

class Integer extends \obray\reflectdb\dataTypes\Integer
{
    private int $size = 11;
    private bool $unsigned = true;
}