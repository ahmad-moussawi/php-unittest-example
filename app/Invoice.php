<?php

namespace App;

class Invoice extends Operation
{
    private $lines = [];

    public function addLine(Line $line)
    {

    }

    public function getEntries(): array
    {
        return [];
    }

}
