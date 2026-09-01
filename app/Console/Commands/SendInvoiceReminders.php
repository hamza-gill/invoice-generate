<?php

namespace App\Console\Commands;

use App\Mail\InvoiceReminderMail;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Setting;
use App\Services\MailConfigurationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoice:send-reminders';

    protected $description = 'Send escalating overdue invoice reminders to customers';

    public function handle(MailConfigurationService $mailService): int
    {
        $today = now()->startOfDay();
        $totalSent = 0;
        $totalSkipped = 0;

        $organizations = Organization::query()->get();

        foreach ($organizations as $organization) {
            $setting = Setting::withoutGlobalScopes()
                ->where('organization_id', $organization->id)
                ->first();

            if (! $setting || ! $setting->enable_invoice_reminders) {
                continue;
            }

            $steps = $setting->reminderSteps();

            if (empty($steps)) {
                continue;
            }

            // Overdue = sent and due date strictly before today.
            $overdueInvoices = Invoice::query()
                ->where('organization_id', $organization->id)
                ->where('status', 'sent')
                ->whereDate('due_date', '<', $today)
                ->with(['customer', 'items'])
                ->get();

            foreach ($overdueInvoices as $invoice) {
                $result = $this->sendDueReminder($invoice, $setting, $steps, $mailService);

                if ($result === 'sent') {
                    $totalSent++;
                } elseif ($result === 'skipped') {
                    $totalSkipped++;
                }
            }
        }

        $this->info("Invoice reminders sent: {$totalSent} (skipped {$totalSkipped}).");
        Log::info('invoice:send-reminders completed', [
            'sent' => $totalSent,
            'skipped' => $totalSkipped,
        ]);

        return self::SUCCESS;
    }

    /**
     * Send the highest applicable escalation for an overdue invoice, once per level.
     *
     * @param  array<int, int>  $steps
     */
    protected function sendDueReminder(Invoice $invoice, Setting $setting, array $steps, MailConfigurationService $mailService): string
    {
        $customer = $invoice->customer;

        if (! $customer || empty($customer->email)) {
            return 'skipped';
        }

        $overdueDays = max(0, (int) \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now(), false));

        // Highest escalation whose threshold the invoice has crossed.
        $targetLevel = null;

        foreach ($steps as $index => $days) {
            if ($overdueDays >= $days) {
                $targetLevel = $index + 1;
            }
        }

        if ($targetLevel === null) {
            return 'skipped';
        }

        // Already sent this (or a higher) level before -> do not re-send.
        if ((int) $invoice->reminder_level >= $targetLevel) {
            return 'skipped';
        }

        try {
            $mailService->send(
                $setting,
                $customer->email,
                new InvoiceReminderMail($invoice, $overdueDays, $setting)
            );

            $invoice->reminder_level = $targetLevel;
            $invoice->last_reminder_sent_at = now();
            $invoice->save();

            $this->line("  → Reminder #{$targetLevel} sent for invoice #{$invoice->invoice_number} ({$overdueDays} days overdue).");

            return 'sent';
        } catch (\Throwable $e) {
            Log::error('Failed to send invoice reminder', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            $this->error("  ✗ Failed to send reminder for invoice #{$invoice->invoice_number}: {$e->getMessage()}");

            return 'skipped';
        }
    }
}
