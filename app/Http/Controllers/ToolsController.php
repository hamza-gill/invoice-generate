<?php

namespace App\Http\Controllers;

class ToolsController extends Controller
{
    protected function relatedTools(?string $current = null): array
    {
        $tools = [
            ['title' => 'Free Invoice Generator', 'slug' => 'free-invoice-generator', 'desc' => 'Build a professional invoice in seconds, for free.'],
            ['title' => 'Invoice Template', 'slug' => 'invoice-template', 'desc' => 'Browse professional invoice templates you can brand.'],
            ['title' => 'Invoice Number Generator', 'slug' => 'invoice-number-generator', 'desc' => 'Generate a unique invoice number automatically.'],
            ['title' => 'Estimate Generator', 'slug' => 'estimate-generator', 'desc' => 'Create a professional estimate or quote for free.'],
            ['title' => 'Late Fee Calculator', 'slug' => 'late-fee-calculator', 'desc' => 'Calculate the late fee to charge on an overdue invoice.'],
            ['title' => 'Invoice Calculator', 'slug' => 'invoice-calculator', 'desc' => 'Add up line items, taxes and discounts in one go.'],
        ];

        return collect($tools)
            ->reject(fn ($t) => $t['slug'] === $current)
            ->values()
            ->all();
    }

    public function freeInvoiceGenerator()
    {
        return view('tools.free-invoice-generator', [
            'tool' => [
                'title' => 'Free Invoice Generator',
                'h1' => 'Free invoice generator',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('free-invoice-generator'),
        ]);
    }

    public function invoiceTemplate()
    {
        return view('tools.invoice-template', [
            'tool' => [
                'title' => 'Invoice Template',
                'h1' => 'Invoice template',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('invoice-template'),
        ]);
    }

    public function invoiceNumberGenerator()
    {
        return view('tools.invoice-number-generator', [
            'tool' => [
                'title' => 'Invoice Number Generator',
                'h1' => 'Invoice number generator',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('invoice-number-generator'),
        ]);
    }

    public function estimateGenerator()
    {
        return view('tools.estimate-generator', [
            'tool' => [
                'title' => 'Estimate Generator',
                'h1' => 'Estimate generator',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('estimate-generator'),
        ]);
    }

    public function lateFeeCalculator()
    {
        return view('tools.late-fee-calculator', [
            'tool' => [
                'title' => 'Late Fee Calculator',
                'h1' => 'Late fee calculator',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('late-fee-calculator'),
        ]);
    }

    public function invoiceCalculator()
    {
        return view('tools.invoice-calculator', [
            'tool' => [
                'title' => 'Invoice Calculator',
                'h1' => 'Invoice calculator',
                'kicker' => 'Free Online Tool',
            ],
            'related' => $this->relatedTools('invoice-calculator'),
        ]);
    }
}
