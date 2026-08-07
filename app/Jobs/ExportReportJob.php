<?php

namespace App\Jobs;

use App\Exports\InvoiceReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    protected int $userId;
    protected int $organizationId;
    protected ?string $startDate;
    protected ?string $endDate;
    protected ?string $status;

    public function __construct(int $userId, int $organizationId, ?string $startDate, ?string $endDate, ?string $status)
    {
        $this->userId = $userId;
        $this->organizationId = $organizationId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function handle(): string
    {
        $fileName = 'invoice-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $directory = 'exports';

        Excel::store(
            new InvoiceReportExport($this->startDate, $this->endDate, $this->status, $this->organizationId),
            $directory . '/' . $fileName
        );

        return storage_path('app/' . $directory . '/' . $fileName);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExportReportJob failed: ' . $exception->getMessage());
    }
}
