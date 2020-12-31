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

        $this->assertLedgerEntries([
            ['credit' => 10, 'account' => 'SALE'],
        ], $entries);
    }

    /** @test */
    public function sales_with_discount()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0.1)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertLedgerEntries([
            [
                'credit' => 9.0,
                'account' => 'SALE',
            ],
        ], $entries);
    }

    /** @test */
    public function sales_with_tax()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0, 0.5)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertLedgerEntries([
            ['credit' => 10, 'account' => 'SALE'],
            ['credit' => 5.0, 'account' => 'TAX'],
        ], $entries);
    }

    /** @test */
    public function sales_with_discount_and_tax()
    {
        $invoice = new Invoice;

        $invoice->addLine(
            new InvoiceLine('Item 1', 10, 1, 0.1, 0.5)
        );

        $entries = $this->generator->generate($invoice);

        $this->assertLedgerEntries([
            ['credit' => 9.0, 'account' => 'SALE'],
            ['credit' => 9 * 0.5, 'account' => 'TAX'],
        ], $entries);
    }

    /** @test */
    public function sales_with_global_discount()
    {
        $invoice = new Invoice;

        $invoice->addLine(new InvoiceLine('Item', 100, 1));

        $invoice->setDiscountRate(.2);

        $entries = $this->generator->generate($invoice);

        $this->assertCount(2, $entries);

        $this->assertLedgerEntries([
            ['debit' => 20, 'account' => 'DISCOUNT'],
            ['credit' => 100, 'account' => 'SALE'],
        ], $entries);
    }

    /** @test */
    public function only_simple()
    {
        $arr = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
        ];

        $this->assertEquals([
            ['a' => 1, 'b' => 2],
            ['b' => 5, 'a' => 4],
        ], $this->array_only($arr, ['a', 'b']));
    }

    private function assertLedgerEntries(array $expected, array $actual, array $keys = ['account', 'debit', 'credit'])
    {
        return $this->assertEquals($expected, $this->array_only($actual, $keys));
    }

    private function array_only(array $arr, array $keys)
    {
        return array_map(function ($row) use ($keys) {
            $mapped = [];

            foreach ($keys as $key) {
                if (array_key_exists($key, $row)) {
                    $mapped[$key] = $row[$key];
                }
            }

            return $mapped;
        }, $arr);
    }

}
