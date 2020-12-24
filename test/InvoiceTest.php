<?php declare (strict_types = 1);

use App\Invoice;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    /** @test */
    public function it_should_have_an_array_of_entries(): void
    {
        $invoice = new Invoice;
        $this->assertIsArray($invoice->getEntries());
    }
}
