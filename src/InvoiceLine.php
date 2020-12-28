<?php

namespace App;

class InvoiceLine
{
    public $item;
    public $quantity;
    public $unitPrice;
    public $discountRate;
    public $taxRate;

    public function __construct($item, $unitPrice, $quantity = 1, $discountRate = 0, $taxRate = 0)
    {
        $this->item = $item;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->discountRate = $this->ensureBetween($discountRate, 0, 1);
        $this->taxRate = $this->ensureBetween($taxRate, 0, 1);
    }

    /**
     * Gross total, before calculating the discount and tax
     *
     * @return float
     */
    public function grossTotal()
    {
        return $this->unitPrice * $this->quantity;
    }

    function discountValue()
    {
        return $this->grossTotal() * $this->discountRate;
    }

    function taxValue()
    {
        return $this->totalAfterDiscount() * $this->taxRate;
    }

    /**
     * Total after discount without and before calculating the tax
     *
     * @return float
     */
    public function totalAfterDiscount()
    {
        return $this->grossTotal() * (1 - $this->discountRate);
    }

    /**
     * Net total
     *
     * @return float
     */
    public function totalAfterTax()
    {
        return $this->totalAfterDiscount() * (1 + $this->taxRate);
    }

    private function ensureBetween($value, $min, $max)
    {
        return min($max, max($min, $value));
    }
}
