<?php

namespace App;

abstract class Operation
{
    protected function ensureBetween($value, $min, $max)
    {
        return min($max, max($min, $value));
    }
}
