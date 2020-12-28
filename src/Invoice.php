<?php

namespace App;

class Invoice extends Operation
{
    /** @var InvoiceLine[] */
    private $lines = [];

    private $discountRate = 0;

    public function addLine(InvoiceLine $line): Invoice
    {
        $this->lines[] = $line;
        return $this;
    }

    public function setDiscountRate($rate): Invoice
    {
        $this->discountRate = $this->ensureBetween($rate, 0, 1);
        return $this;
    }

    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function total()
    {
        $sum = 0;

        foreach ($this->lines as $line) {
            $sum += $line->totalAfterTax();
        }

        return $sum;
    }

    public function discountValue()
    {
        return $this->discountRate * $this->total();
    }

    public function totalAfterDiscount()
    {
        return $this->total() * (1 - $this->discountRate);
    }

}
