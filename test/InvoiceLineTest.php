<?php declare (strict_types = 1);

use App\InvoiceLine;
use PHPUnit\Framework\TestCase;

final class InvoiceLineTest extends TestCase
{
    /** @test */
    public function totals_should_be_0_for_fresh_instances(): void
    {
        $line = new InvoiceLine("Item", 0);
        $this->assertEquals(0, $line->grossTotal());
        $this->assertEquals(0, $line->totalAfterDiscount());
        $this->assertEquals(0, $line->totalAfterTax());
    }

    /** @test */
    public function it_should_have_an_item_name()
    {
        $line = new InvoiceLine("Brushing", 10);

        $this->assertSame("Brushing", $line->item);
    }

    /** @test */
    public function total_should_equal_to_the_product_of_quantity_and_unit_price()
    {
        $line = new InvoiceLine("Brushing", 10, 5);

        $this->assertSame(50, $line->grossTotal());
    }
}
