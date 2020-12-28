<?php

use App\EntryGenerator;
use App\Invoice;
use App\InvoiceLine;
use PHPUnit\Framework\TestCase;

class EntryGeneratorTest extends TestCase
{

    /** @var EntryGenerator  */
    private $generator;

    public function setUp(): void
    {
        $this->generator = new EntryGenerator();
    }

    /** @test */
    public function it_should_return_an_array()
    {
        $invoice = new Invoice;

        $this->assertIsArray($this->generator->generate($invoice));
    }

    /** @test */
    public function simple_sales()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertCount(1, $entries);

        $this->assertEquals(10, $entries[0]['credit']);
    }

    /** @test */
    public function sales_with_discount()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0.1)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertCount(1, $entries);

        $this->assertEquals(9.0, $entries[0]['credit']);
    }

    /** @test */
    public function sales_with_tax()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0, 0.5)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertCount(2, $entries);
        $this->assertEquals(10, $entries[0]['credit']);
        $this->assertEquals('SALE', $entries[0]['account']);

        $this->assertEquals(5.0, $entries[1]['credit']);
        $this->assertEquals('TAX', $entries[1]['account']);
    }

    /** @test */
    public function sales_with_discount_and_tax()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0.1, 0.5)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertCount(2, $entries);
        $this->assertEquals(9.0, $entries[0]['credit']);
        $this->assertEquals(9 * 0.5, $entries[1]['credit']);
    }

    /** @test */
    public function sales_with_global_discount()
    {
        $invoice = new Invoice;

        $invoice->addLine(new InvoiceLine('Item', 100, 1));

        $invoice->setDiscountRate(.2);

        $entries = $this->generator->generate($invoice);

        $this->assertCount(2, $entries);

        $this->assertEquals(20, $entries[0]['debit']);
        $this->assertEquals('DISCOUNT', $entries[0]['account']);

        $this->assertEquals(100, $entries[1]['credit']);
        $this->assertEquals('SALE', $entries[1]['account']);
    }

}
