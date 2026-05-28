<?php

namespace Database\Seeders;

use App\Models\InvoiceTemplate;
use Illuminate\Database\Seeder;

class InvoiceTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = $this->getTemplates();

        foreach ($templates as $template) {
            InvoiceTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function getTemplates(): array
    {
        return [
            $this->classicProfessional(),
            $this->modernMinimal(),
            $this->boldCorporate(),
            $this->creativeAgency(),
            $this->techStartup(),
            $this->elegant(),
            $this->construction(),
            $this->medical(),
            $this->legal(),
            $this->freelancer(),
        ];
    }

    private function baseLayout(string $headerBg, string $headerColor, string $accentColor, string $fontFamily, string $headerLabel = 'INVOICE'): string
    {
        return <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Invoice</title>
<style>
@page { size: A4; margin: 12mm; }
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; width: 100%; }
body { font-family: {$fontFamily}, DejaVu Sans, sans-serif; font-size: 13px; color: #1f2937; background: #fff; }
.invoice-wrap { width: 100%; max-width: 100%; padding: 0; }
.header-table { width: 100%; border-collapse: collapse; margin: 0 0 22px 0; table-layout: fixed; }
.header-table td { vertical-align: top; color: {$headerColor}; word-wrap: break-word; overflow-wrap: break-word; }
.header-table .header-left { width: 52%; padding: 28px 14px 28px 24px; background: {$headerBg}; }
.header-table .header-right { width: 48%; padding: 28px 24px 28px 14px; text-align: right; background: {$headerBg}; }
.header-title { font-size: 26px; font-weight: 700; line-height: 1.2; margin: 0 0 6px 0; }
.header-line { margin: 3px 0; line-height: 1.45; font-size: 13px; }
.header-company { font-weight: 700; font-size: 16px; margin: 0 0 6px 0; }
.container { padding: 0 12px 16px; }
.billing { margin-bottom: 20px; }
.billing table { width: 100%; border-collapse: collapse; }
.billing td { vertical-align: top; padding: 3px 0; }
table.items { width: 100%; border-collapse: collapse; margin: 15px 0 20px; }
table.items th { background: {$accentColor}15; color: {$accentColor}; border-bottom: 2px solid {$accentColor}; padding: 10px 8px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
table.items td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; }
table.items td:last-child { text-align: right; }
.totals-wrap { width: 100%; margin-top: 8px; }
.totals-wrap td { vertical-align: top; }
.totals-table { width: 260px; margin-left: auto; border-collapse: collapse; }
.totals-table td { padding: 7px 0; border-top: 1px solid #e5e7eb; font-size: 13px; }
.totals-table td:last-child { text-align: right; font-weight: 600; }
.totals-table tr.total-row td { border-top: 2px solid {$accentColor}; font-weight: 700; font-size: 16px; padding-top: 10px; }
.footer-grid { width: 100%; border-collapse: separate; border-spacing: 14px 0; margin-top: 32px; clear: both; }
.footer-grid td { width: 50%; vertical-align: top; padding: 0; }
.footer-card { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 10px; padding: 18px 20px; min-height: 72px; }
.footer-card h3 { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #475569; margin: 0 0 10px 0; }
.footer-card .footer-body { font-size: 12px; color: #4b5563; line-height: 1.55; margin: 0; white-space: pre-line; word-wrap: break-word; }
</style></head><body>
<div class="invoice-wrap">
<table class="header-table" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="header-left" width="52%" style="width:52%;vertical-align:top;background:{$headerBg};color:{$headerColor};"><div class="header-title">{$headerLabel}</div><div class="header-line">#{{invoice_number}}</div><div class="header-line" style="margin-top:8px">{{project_address}}</div></td>
<td class="header-right" width="48%" style="width:48%;vertical-align:top;text-align:right;background:{$headerBg};color:{$headerColor};"><div class="header-company">{{company_name}}</div><div class="header-line">{{company_address}}</div><div class="header-line">{{company_email}}</div></td>
</tr>
</table>
<div class="container">
<div class="billing"><table><tr>
<td style="width:55%"><p style="margin:0 0 6px"><strong>BILL TO:</strong></p><p style="margin:2px 0">{{customer_name}}</p><p style="margin:2px 0">{{customer_email}}</p><p style="margin:2px 0">{{customer_address}}</p></td>
<td style="width:45%;text-align:right"><table style="margin-left:auto;border-collapse:collapse"><tr><td style="padding:2px 8px 2px 0"><strong>Issue Date:</strong></td><td style="padding:2px 0">{{issue_date}}</td></tr><tr><td style="padding:2px 8px 2px 0"><strong>Due Date:</strong></td><td style="padding:2px 0">{{due_date}}</td></tr><tr><td style="padding:2px 8px 2px 0"><strong>Status:</strong></td><td style="padding:2px 0">{{status}}</td></tr></table></td>
</tr></table></div>
<table class="items"><thead><tr><th>Service</th><th>Description</th><th style="text-align:right">Amount</th></tr></thead><tbody>{{line_items_rows}}</tbody></table>
<table class="totals-wrap"><tr><td>&nbsp;</td><td>
<table class="totals-table">
<tr><td><strong>Subtotal:</strong></td><td>{{subtotal}}</td></tr>
<tr><td><strong>Tax ({{tax_percentage}}%):</strong></td><td>{{tax_amount}}</td></tr>
<tr><td>Discount:</td><td>-{{discount}}</td></tr>
<tr class="total-row"><td>Total:</td><td>{{total}}</td></tr>
</table>
</td></tr></table>
<table class="footer-grid"><tr>
<td><div class="footer-card"><h3>Notes</h3><div class="footer-body">{{notes}}</div></div></td>
<td><div class="footer-card"><h3>Terms &amp; Conditions</h3><div class="footer-body">{{terms}}</div></div></td>
</tr></table>
</div></div></body></html>
HTML;
    }

    private function classicProfessional(): array
    {
        return [
            'name' => 'Classic Professional',
            'slug' => 'classic-professional',
            'description' => 'Clean, traditional business layout with a timeless serif design.',
            'html_layout' => $this->baseLayout('#1f2937', '#ffffff', '#374151', 'Georgia, serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#1f2937', 'secondary_color' => '#374151', 'font_family' => 'Georgia', 'header_style' => 'dark-bar'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 1, 'organization_id' => null,
        ];
    }

    private function modernMinimal(): array
    {
        return [
            'name' => 'Modern Minimal',
            'slug' => 'modern-minimal',
            'description' => 'Ultra-clean sans-serif design with generous whitespace and blue accents.',
            'html_layout' => $this->baseLayout('#3B82F6', '#ffffff', '#3B82F6', 'Helvetica, Arial'),
            'css_styles' => null,
            'config' => ['primary_color' => '#3B82F6', 'secondary_color' => '#93C5FD', 'font_family' => 'Helvetica', 'header_style' => 'blue-bar'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 2, 'organization_id' => null,
        ];
    }

    private function boldCorporate(): array
    {
        return [
            'name' => 'Bold Corporate',
            'slug' => 'bold-corporate',
            'description' => 'Strong header bar with navy and gold accents for a corporate feel.',
            'html_layout' => $this->baseLayout('#1e3a5f', '#F5D78E', '#1e3a5f', 'Arial, sans-serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#1e3a5f', 'secondary_color' => '#F5D78E', 'font_family' => 'Arial', 'header_style' => 'navy-gold'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 3, 'organization_id' => null,
        ];
    }

    private function creativeAgency(): array
    {
        return [
            'name' => 'Creative Agency',
            'slug' => 'creative-agency',
            'description' => 'Vibrant purple gradient with modern typography for creative professionals.',
            'html_layout' => $this->baseLayout('linear-gradient(135deg, #7C3AED, #DB2777)', '#ffffff', '#7C3AED', 'Segoe UI, sans-serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#7C3AED', 'secondary_color' => '#DB2777', 'font_family' => 'Segoe UI', 'header_style' => 'gradient'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 4, 'organization_id' => null,
        ];
    }

    private function techStartup(): array
    {
        return [
            'name' => 'Tech Startup',
            'slug' => 'tech-startup',
            'description' => 'Dark theme header with green accents and monospace elements.',
            'html_layout' => $this->baseLayout('#0f172a', '#10B981', '#10B981', 'Consolas, monospace'),
            'css_styles' => null,
            'config' => ['primary_color' => '#0f172a', 'secondary_color' => '#10B981', 'font_family' => 'Consolas', 'header_style' => 'dark-tech'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 5, 'organization_id' => null,
        ];
    }

    private function elegant(): array
    {
        return [
            'name' => 'Elegant',
            'slug' => 'elegant',
            'description' => 'Sophisticated design with thin borders and warm beige tones.',
            'html_layout' => $this->baseLayout('#78716c', '#ffffff', '#78716c', 'Palatino, serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#78716c', 'secondary_color' => '#d6d3d1', 'font_family' => 'Palatino', 'header_style' => 'elegant'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 6, 'organization_id' => null,
        ];
    }

    private function construction(): array
    {
        return [
            'name' => 'Construction',
            'slug' => 'construction',
            'description' => 'Industrial yellow and black design built for construction businesses.',
            'html_layout' => $this->baseLayout('#fbbf24', '#1f2937', '#f59e0b', 'Arial Black, sans-serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#f59e0b', 'secondary_color' => '#1f2937', 'font_family' => 'Arial Black', 'header_style' => 'construction'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 7, 'organization_id' => null,
        ];
    }

    private function medical(): array
    {
        return [
            'name' => 'Medical',
            'slug' => 'medical',
            'description' => 'Clean clinical design with light blue and teal accents.',
            'html_layout' => $this->baseLayout('#0d9488', '#ffffff', '#0d9488', 'Calibri, sans-serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#0d9488', 'secondary_color' => '#99f6e4', 'font_family' => 'Calibri', 'header_style' => 'medical'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 8, 'organization_id' => null,
        ];
    }

    private function legal(): array
    {
        return [
            'name' => 'Legal',
            'slug' => 'legal',
            'description' => 'Formal design with borders and classic serif typography for legal firms.',
            'html_layout' => $this->baseLayout('#374151', '#ffffff', '#4b5563', 'Times New Roman, serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#374151', 'secondary_color' => '#6b7280', 'font_family' => 'Times New Roman', 'header_style' => 'formal'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 9, 'organization_id' => null,
        ];
    }

    private function freelancer(): array
    {
        return [
            'name' => 'Freelancer',
            'slug' => 'freelancer',
            'description' => 'Colorful and friendly design perfect for freelancers and solopreneurs.',
            'html_layout' => $this->baseLayout('#6366f1', '#ffffff', '#6366f1', 'Verdana, sans-serif'),
            'css_styles' => null,
            'config' => ['primary_color' => '#6366f1', 'secondary_color' => '#a78bfa', 'font_family' => 'Verdana', 'header_style' => 'colorful'],
            'is_system' => true, 'is_active' => true, 'sort_order' => 10, 'organization_id' => null,
        ];
    }
}
