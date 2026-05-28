<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Models\Setting;
use Carbon\Carbon;

class InvoiceTemplateRenderer
{
    public function resolveForInvoice(Invoice $invoice): ?InvoiceTemplate
    {
        $invoice->loadMissing(['items.product', 'customer']);

        if ($invoice->invoice_template_id) {
            $template = InvoiceTemplate::find($invoice->invoice_template_id);
            if ($template && $template->is_active) {
                return $template;
            }
        }

        $settings = Setting::withoutGlobalScopes()
            ->where('organization_id', $invoice->organization_id)
            ->first();

        if ($settings?->default_template_id) {
            $template = InvoiceTemplate::find($settings->default_template_id);
            if ($template && $template->is_active) {
                return $template;
            }
        }

        return InvoiceTemplate::query()
            ->where('is_active', true)
            ->whereNull('organization_id')
            ->orderBy('sort_order')
            ->first();
    }

    public function render(Invoice $invoice, ?Setting $settings = null): string
    {
        $template = $this->resolveForInvoice($invoice);

        if (!$template || empty($template->html_layout)) {
            return '';
        }

        $settings = $settings ?? Setting::withoutGlobalScopes()
            ->where('organization_id', $invoice->organization_id)
            ->first();

        $html = $template->html_layout;
        $replacements = $this->buildReplacements($invoice, $settings);
        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        if (!empty($template->css_styles)) {
            if (stripos($html, '</head>') !== false) {
                $html = str_replace('</head>', '<style>' . $template->css_styles . '</style></head>', $html);
            } else {
                $html = '<style>' . $template->css_styles . '</style>' . $html;
            }
        }

        if (!empty($settings->custom_invoice_css)) {
            $customCss = '<style>' . $settings->custom_invoice_css . '</style>';
            if (stripos($html, '</head>') !== false) {
                $html = str_replace('</head>', $customCss . '</head>', $html);
            } else {
                $html = $customCss . $html;
            }
        }

        return $this->applyLayoutFixes($html);
    }

    /**
     * Same HTML as render() but tuned for DomPDF (matches on-screen design).
     */
    public function renderForPdf(Invoice $invoice, ?Setting $settings = null): string
    {
        $html = $this->render($invoice, $settings);

        if ($html === '') {
            return '';
        }

        $html = $this->normalizeHeaderForPdf($html);

        $pdfCss = '<style>'
            . 'html,body{margin:0;padding:0;}'
            . '.header-table{width:100%!important;table-layout:fixed!important;border-collapse:collapse!important;margin:0 0 22px 0!important;}'
            . '.header-table td{vertical-align:top!important;word-wrap:break-word!important;overflow-wrap:break-word!important;}'
            . '.header-table .header-left{width:52%!important;padding:28px 14px 28px 24px!important;}'
            . '.header-table .header-right{width:48%!important;padding:28px 24px 28px 14px!important;text-align:right!important;}'
            . '.header-title{font-size:26px!important;font-weight:700!important;display:block!important;margin:0 0 6px 0!important;}'
            . '.header-company{font-size:16px!important;font-weight:700!important;display:block!important;margin:0 0 6px 0!important;}'
            . '.header-line{display:block!important;margin:3px 0!important;font-size:13px!important;line-height:1.45!important;}'
            . '.footer-grid,.footer-card,.totals-table{page-break-inside:avoid;}'
            . 'table{page-break-inside:auto;}'
            . 'tr{page-break-inside:avoid;page-break-after:auto;}'
            . '</style>';

        if (stripos($html, '</head>') !== false) {
            $html = str_replace('</head>', $pdfCss . '</head>', $html);
        } else {
            $html = $pdfCss . $html;
        }

        return $html;
    }

    /**
     * Fix legacy templates still stored with negative header margins or old notes block.
     */
    protected function applyLayoutFixes(string $html): string
    {
        $fixes = '<style>'
            . '.header-table{width:100%!important;table-layout:fixed!important;border-collapse:collapse!important;}'
            . '.header-table td{vertical-align:top!important;word-wrap:break-word!important;}'
            . '.header-table .header-left{width:52%!important;}'
            . '.header-table .header-right{width:48%!important;text-align:right!important;}'
            . '.header{margin:0 0 22px 0!important;padding:0!important;width:100%!important;}'
            . '.header table{width:100%!important;table-layout:fixed!important;}'
            . '.header td{vertical-align:top!important;word-wrap:break-word!important;}'
            . '.header h1{font-size:26px!important;margin:0 0 6px!important;}'
            . '.header h1,.header p{overflow:visible!important;}'
            . 'html,body{overflow:visible!important;}'
            . '.footer-grid{width:100%;border-collapse:separate;border-spacing:14px 0;margin-top:32px;}'
            . '.footer-grid td{width:50%;vertical-align:top;}'
            . '.footer-card{background:#f8f9fa;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;min-height:72px;}'
            . '.footer-card h3{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#475569;margin:0 0 10px;}'
            . '.footer-card .footer-body{font-size:12px;color:#4b5563;line-height:1.55;white-space:pre-line;}'
            . '</style>';

        if (stripos($html, '</head>') !== false) {
            $html = str_replace('</head>', $fixes . '</head>', $html);
        }

        $html = $this->upgradeLegacyNotesBlock($html);

        return $this->normalizeHeaderForPdf($html);
    }

    /**
     * DomPDF renders nested div+table headers poorly; flatten to a single fixed-layout table.
     */
    protected function normalizeHeaderForPdf(string $html): string
    {
        if (str_contains($html, 'class="header-table"')) {
            return $html;
        }

        if (!preg_match('/<div class="header">\s*<table[^>]*>\s*<tr>(.*?)<\/tr>\s*<\/table>\s*<\/div>/is', $html, $match)) {
            return $html;
        }

        $row = $match[1];

        // Convert h1/p tags inside cells to div-based lines for consistent PDF layout.
        $row = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', '<div class="header-title">$1</div>', $row);
        $row = preg_replace('/<p([^>]*)style="[^"]*font-weight:\s*700[^"]*"([^>]*)>(.*?)<\/p>/is', '<div class="header-company">$3</div>', $row);
        $row = preg_replace('/<p[^>]*>(.*?)<\/p>/is', '<div class="header-line">$1</div>', $row);

        $headerTable = '<table class="header-table" width="100%" cellpadding="0" cellspacing="0"><tr>'
            . '<td class="header-left" width="52%" style="width:52%;vertical-align:top;">' . $this->extractHeaderCell($row, 1) . '</td>'
            . '<td class="header-right" width="48%" style="width:48%;vertical-align:top;text-align:right;">' . $this->extractHeaderCell($row, 2) . '</td>'
            . '</tr></table>';

        // Preserve inline background/color from legacy header cells when present.
        if (preg_match('/<td[^>]*style="([^"]*)"[^>]*>.*<\/td>\s*<td[^>]*style="([^"]*)"[^>]*>/is', $match[0], $styles)) {
            $headerTable = '<table class="header-table" width="100%" cellpadding="0" cellspacing="0"><tr>'
                . '<td class="header-left" width="52%" style="width:52%;vertical-align:top;' . $styles[1] . '">' . $this->extractHeaderCell($row, 1) . '</td>'
                . '<td class="header-right" width="48%" style="width:48%;vertical-align:top;text-align:right;' . $styles[2] . '">' . $this->extractHeaderCell($row, 2) . '</td>'
                . '</tr></table>';
        }

        return preg_replace('/<div class="header">\s*<table[^>]*>\s*<tr>.*?<\/tr>\s*<\/table>\s*<\/div>/is', $headerTable, $html, 1);
    }

    protected function extractHeaderCell(string $rowHtml, int $cellIndex): string
    {
        if (!preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $rowHtml, $cells)) {
            return '';
        }

        return trim($cells[1][$cellIndex - 1] ?? '');
    }

    /**
     * Convert old stacked Notes/Terms markup to the two-column card layout.
     */
    protected function upgradeLegacyNotesBlock(string $html): string
    {
        if (!str_contains($html, 'class="notes"') || str_contains($html, 'class="footer-grid"')) {
            return $html;
        }

        if (!preg_match('/<div class="notes">(.*?)<\/div>/s', $html, $match)) {
            return $html;
        }

        $notesContent = '—';
        $termsContent = '—';

        if (preg_match('/<h3>\s*Notes\s*<\/h3>\s*<p>(.*?)<\/p>/is', $match[1], $notesMatch)) {
            $notesContent = trim(strip_tags($notesMatch[1])) ?: '—';
        }

        if (preg_match('/<h3>\s*Terms\s*<\/h3>\s*<p>(.*?)<\/p>/is', $match[1], $termsMatch)) {
            $termsContent = trim(strip_tags($termsMatch[1])) ?: '—';
        }

        $footer = '<table class="footer-grid"><tr>'
            . '<td><div class="footer-card"><h3>Notes</h3><div class="footer-body">' . e($notesContent) . '</div></div></td>'
            . '<td><div class="footer-card"><h3>Terms &amp; Conditions</h3><div class="footer-body">' . e($termsContent) . '</div></div></td>'
            . '</tr></table>';

        return str_replace($match[0], $footer, $html);
    }

    /**
     * @return array<string, string>
     */
    protected function buildReplacements(Invoice $invoice, ?Setting $settings): array
    {
        $currency = $settings->base_currency ?? '$';
        $subtotal = $invoice->items->sum(fn ($item) => (float) $item->quantity * (float) $item->amount);
        $rushFee = $invoice->rush_enabled_value ? (float) ($invoice->rush_fee ?? 0) : 0;
        $discount = (float) ($invoice->discount ?? 0);

        $taxRate = 0.0;
        if ($settings && ($settings->enable_tax ?? false)) {
            $taxRate = ((float) ($settings->tax_percentage ?? 0)) / 100;
        }
        $taxAmount = ($subtotal + $rushFee) * $taxRate;
        $total = max(0, $subtotal + $rushFee + $taxAmount - $discount);

        $notesText = $this->buildNotesText($invoice, $settings);
        $termsText = $this->buildTermsText($settings);

        return [
            '{{invoice_number}}' => e($invoice->invoice_number),
            '{{issue_date}}' => $invoice->issue_date
                ? Carbon::parse($invoice->issue_date)->format('M d, Y')
                : '',
            '{{due_date}}' => $invoice->due_date
                ? Carbon::parse($invoice->due_date)->format('M d, Y')
                : '',
            '{{status}}' => strtoupper($invoice->status ?? 'N/A'),
            '{{customer_name}}' => e($invoice->customer->full_name ?? 'N/A'),
            '{{customer_email}}' => e($invoice->customer->email ?? ''),
            '{{customer_address}}' => e($invoice->customer->address ?? ''),
            '{{company_name}}' => e($settings->company_name ?? config('app.name')),
            '{{company_address}}' => e($settings->address ?? ''),
            '{{company_email}}' => e($settings->contact_email ?? ''),
            '{{tax_id}}' => e($settings->tax_id ?? ''),
            '{{project_address}}' => e($invoice->project_address ?? ''),
            '{{subtotal}}' => $currency . number_format($subtotal, 2),
            '{{tax_amount}}' => $currency . number_format($taxAmount, 2),
            '{{tax_percentage}}' => $settings->tax_percentage ?? '0',
            '{{discount}}' => $currency . number_format($discount, 2),
            '{{rush_fee}}' => $currency . number_format($rushFee, 2),
            '{{total}}' => $currency . number_format($total, 2),
            '{{notes}}' => $notesText,
            '{{terms}}' => $termsText,
            '{{invoice_notes}}' => e($settings->invoice_notes ?? ''),
            '{{line_items_rows}}' => $this->buildLineItemsRows($invoice, $currency),
            '{{custom_fields_html}}' => $notesText,
        ];
    }

    protected function buildNotesText(Invoice $invoice, ?Setting $settings): string
    {
        $parts = [];

        if (!empty($invoice->custom_fields) && is_array($invoice->custom_fields)) {
            foreach ($invoice->custom_fields as $field) {
                $key = trim($field['key'] ?? '');
                $value = trim($field['value'] ?? '');
                if ($key !== '' || $value !== '') {
                    $parts[] = ($key !== '' ? $key . ': ' : '') . $value;
                }
            }
        }

        if (!empty(trim($invoice->note ?? ''))) {
            $parts[] = trim($invoice->note);
        }

        if ($settings && ($settings->enable_invoice_notes ?? false) && !empty(trim($settings->invoice_notes ?? ''))) {
            $parts[] = trim($settings->invoice_notes);
        }

        return $parts !== [] ? e(implode("\n\n", $parts)) : '—';
    }

    protected function buildTermsText(?Setting $settings): string
    {
        $terms = trim($settings->invoice_terms ?? '');

        return $terms !== '' ? e($terms) : '—';
    }

    protected function buildLineItemsRows(Invoice $invoice, string $currency): string
    {
        $rows = '';
        foreach ($invoice->items as $item) {
            $lineTotal = (float) $item->quantity * (float) $item->amount;
            $service = e($item->activity ?: ($item->product->name ?? 'Item'));
            $description = e($item->description ?: ($item->product->description ?? ''));
            $rows .= '<tr>'
                . '<td>' . $service . '</td>'
                . '<td>' . $description . '</td>'
                . '<td style="text-align:right">' . $currency . number_format($lineTotal, 2) . '</td>'
                . '</tr>';
        }

        if ($invoice->rush_enabled_value && (float) ($invoice->rush_fee ?? 0) > 0) {
            $label = 'Rush Add-On' . ($invoice->rush_delivery_type ? ' (' . e(ucfirst($invoice->rush_delivery_type)) . ')' : '');
            $rows .= '<tr>'
                . '<td>' . $label . '</td>'
                . '<td>' . e($invoice->rush_description ?? '') . '</td>'
                . '<td style="text-align:right">' . $currency . number_format((float) $invoice->rush_fee, 2) . '</td>'
                . '</tr>';
        }

        return $rows;
    }
}
