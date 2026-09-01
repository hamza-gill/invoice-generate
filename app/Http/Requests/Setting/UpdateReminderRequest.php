<?php

namespace App\Http\Requests\Setting;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enable_invoice_reminders' => 'nullable|boolean',
            'reminder_days' => 'nullable|array|max:10',
            'reminder_days.*' => 'nullable|integer|min:1|max:365',
        ];
    }

    public function messages(): array
    {
        return [
            'reminder_days.*.integer' => 'Reminder day offsets must be whole numbers.',
            'reminder_days.*.min' => 'Reminder day offsets must be at least 1 day after the due date.',
            'reminder_days.*.max' => 'Reminder day offsets cannot exceed 365 days.',
        ];
    }

    /**
     * Persist the reminder configuration onto the org settings row.
     */
    public function persist(): void
    {
        $setting = Setting::firstOrNew();

        $enabled = $this->boolean('enable_invoice_reminders');

        $steps = [];
        if ($enabled && $this->filled('reminder_days')) {
            foreach ($this->input('reminder_days', []) as $days) {
                if (is_numeric($days) && (int) $days > 0) {
                    $steps[] = ['days' => (int) $days];
                }
            }
        }

        $setting->enable_invoice_reminders = $enabled;
        $setting->reminder_schedule = $enabled ? $steps : null;
        $setting->save();
    }
}
