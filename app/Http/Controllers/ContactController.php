<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureRequestRequest;
use App\Models\FeatureRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('landing.contact', [
            'types' => FeatureRequest::TYPES,
            'priorities' => FeatureRequest::PRIORITIES,
            'prefill' => [
                'name' => $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : old('name'),
                'email' => $user->email ?? old('email'),
                'company' => $user?->organization?->name ?? old('company'),
                'phone' => $user->phone ?? old('phone'),
            ],
        ]);
    }

    public function store(StoreFeatureRequestRequest $request)
    {
        $user = Auth::user();

        $submission = FeatureRequest::create([
            'user_id' => $user?->id,
            'organization_id' => $user?->organization_id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
            'phone' => $request->input('phone'),
            'request_type' => $request->input('request_type'),
            'module_name' => $request->input('module_name'),
            'title' => $request->input('title'),
            'requirements' => $request->input('requirements'),
            'use_case' => $request->input('use_case'),
            'priority' => $request->input('priority'),
            'ip_address' => $request->ip(),
        ]);

        $this->notifyTeam($submission);

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you! Your request has been received. Our team will review it and get back to you soon.');
    }

    protected function notifyTeam(FeatureRequest $submission): void
    {
        $to = config('mail.support_address', config('mail.from.address'));

        if (!$to) {
            return;
        }

        try {
            Mail::raw($this->buildNotificationBody($submission), function ($message) use ($to, $submission) {
                $message->to($to)
                    ->replyTo($submission->email, $submission->name)
                    ->subject('[Inveqi] ' . $submission->typeLabel() . ': ' . $submission->title);
            });
        } catch (\Throwable $e) {
            Log::warning('Feature request email failed: ' . $e->getMessage(), [
                'feature_request_id' => $submission->id,
            ]);
        }
    }

    protected function buildNotificationBody(FeatureRequest $submission): string
    {
        $lines = [
            'New contact / feature request',
            '-----------------------------',
            'Type: ' . $submission->typeLabel(),
            'Title: ' . $submission->title,
            'Priority: ' . (FeatureRequest::PRIORITIES[$submission->priority] ?? $submission->priority),
            'From: ' . $submission->name . ' <' . $submission->email . '>',
        ];

        if ($submission->company) {
            $lines[] = 'Company: ' . $submission->company;
        }
        if ($submission->phone) {
            $lines[] = 'Phone: ' . $submission->phone;
        }
        if ($submission->module_name) {
            $lines[] = 'Module: ' . $submission->module_name;
        }

        $lines[] = '';
        $lines[] = 'Requirements:';
        $lines[] = $submission->requirements;

        if ($submission->use_case) {
            $lines[] = '';
            $lines[] = 'Use case:';
            $lines[] = $submission->use_case;
        }

        return implode("\n", $lines);
    }
}
