<?php

namespace obray\reflectdb\dataTypes;

class Integer extends \obray\reflectdb\dataTypes\Integer
{
    private bool $unsigned = true;
    private bool $nullable = false;
    private bool $autoincrement = true;
}