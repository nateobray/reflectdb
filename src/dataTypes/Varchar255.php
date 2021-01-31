<?php

namespace obray\reflectdb\dataTypes;

class Varchar255 extends \obray\reflectdb\dataTypes\Varchar
{
    protected int $size = 255;
}