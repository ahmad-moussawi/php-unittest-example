<?php

namespace App;

class EntryGenerator
{

    private $salesAccount = 'SALE';
    private $cashAccount = 'CASH';
    private $clientAccount = 'CLIENT';
    private $discountAccount = 'DISCOUNT';
    private $taxAccount = 'TAX';

    public function generate(Operation $operation): array
    {
        if ($operation instanceof Invoice) {
            return $this->generateInvoice($operation);
        }
    }

    public function generateInvoice(Invoice $invoice)
    {
        $entries = [];

        if ($invoice->getDiscountRate() > 0) {
            $entries[] = [
                'account' => $this->discountAccount,
                'debit' => $invoice->discountValue(),
            ];
        }

        $lines = $invoice->getLines();
        $count = count($lines);

        for ($i = 0; $i < $count; $i++) {

            /** @var InvoiceLine $line */
            $line = $lines[$i];

            $entries[] = [
                'account' => $this->salesAccount,
                'credit' => $line->totalAfterDiscount(),
            ];

            if ($line->taxRate) {
                $entries[] = [
                    'account' => $this->taxAccount,
                    'credit' => $line->taxValue(),
                    'description' => "Added tax for item #{($i + 1)} {$line->item}",
                ];
            }

        }

        return $entries;
    }
}
