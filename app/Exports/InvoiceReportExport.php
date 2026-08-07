<?php

namespace App\Exports;

use App\Models\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $organizationId;

    public function __construct(?string $startDate = null, ?string $endDate = null, ?string $status = null, ?int $organizationId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->organizationId = $organizationId;
    }

    public function query()
    {
        $query = Invoice::with('customer');

        if ($this->organizationId) {
            $query->where('organization_id', $this->organizationId);
        }

        if ($this->startDate) {
            $query->whereDate('issue_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('issue_date', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('issue_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Customer Name',
            'Customer Email',
            'Issue Date',
            'Due Date',
            'Amount (USD)',
            'Status',
            'Payment Status',
            'Project Address',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            trim(($invoice->customer->first_name ?? '') . ' ' . ($invoice->customer->last_name ?? '')) ?: 'N/A',
            $invoice->customer->email ?? 'N/A',
            $invoice->issue_date ? Carbon::parse($invoice->issue_date)->format('M d, Y') : 'N/A',
            $invoice->due_date ? Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A',
            number_format($invoice->amount, 2),
            ucfirst($invoice->status ?? 'N/A'),
            ucfirst($invoice->payment_status ?? 'N/A'),
            $invoice->project_address ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
