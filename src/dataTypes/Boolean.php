<?php

namespace obray\reflectdb\dataTypes;

class Boolean extends \obray\reflectdb\dataTypes\Integer
{
    private int $size = 1;
    private bool $unsigned = true;
}