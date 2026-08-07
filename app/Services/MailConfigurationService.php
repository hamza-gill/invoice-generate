<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailConfigurationService
{
    /**
     * Provider presets so the UI can prefill sensible defaults.
     *
     * @return array<string, array{host: string, port: int, encryption: string, username_hint: string}>
     */
    public static function providerPresets(): array
    {
        return [
            'brevo' => [
                'host' => 'smtp-relay.brevo.com',
                'port' => 587,
                'encryption' => 'tls',
                'username_hint' => 'SMTP login (e.g. info@yourcompany.com)',
            ],
            'sendgrid' => [
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'encryption' => 'tls',
                'username_hint' => 'Use the literal value: apikey',
            ],
            'smtp' => [
                'host' => '',
                'port' => 587,
                'encryption' => 'tls',
                'username_hint' => 'SMTP username',
            ],
        ];
    }

    public static function providerLabel(string $key): string
    {
        return match ($key) {
            'brevo' => 'Brevo',
            'sendgrid' => 'SendGrid',
            'microsoft' => 'Microsoft 365',
            'smtp' => 'Custom SMTP',
            'log' => 'Log (for testing)',
            default => 'Platform Default',
        };
    }

    /**
     * Build a Symfony transport for the organization's configured provider.
     * Returns null when the organization should use the platform default mailer.
     */
    public function transportFor(?Setting $setting): ?TransportInterface
    {
        if (! $setting || ! $setting->hasCustomMailConfig()) {
            return null;
        }

        $provider = $setting->mail_mailer;

        if ($provider === 'microsoft') {
            return app(MicrosoftMailer::class)->getTransport(
                $setting->organization_id,
                $setting->mail_username
            );
        }

        if ($provider === 'log') {
            return app('mail.manager')->createSymfonyTransport(['transport' => 'log']);
        }

        // smtp / brevo / sendgrid are all SMTP transports
        $config = [
            'transport' => 'smtp',
            'host' => $setting->mail_host,
            'port' => $setting->mail_port,
            'username' => $setting->mail_username,
            'password' => $setting->mail_password,
        ];

        if (($setting->mail_encryption ?? '') === 'ssl' || (int) $setting->mail_port === 465) {
            $config['scheme'] = 'smtps';
        }

        return app('mail.manager')->createSymfonyTransport($config);
    }

    /**
     * Build a Mailer bound to the organization's transport (or null to use the
     * platform default). When built, the "from" address is overridden to the
     * organization's configured sender.
     */
    public function mailerFor(?Setting $setting): ?Mailer
    {
        $transport = $this->transportFor($setting);

        if (! $transport) {
            return null;
        }

        $mailer = new Mailer(
            'org-mailer-' . ($setting->organization_id ?? 'default'),
            app('view'),
            $transport,
            app('events')
        );

        $mailer->alwaysFrom(
            $setting->mail_from_address ?: config('mail.from.address'),
            $setting->mail_from_name ?: config('mail.from.name')
        );

        return $mailer;
    }

    /**
     * Send a mailable through the organization's configured transport, falling
     * back to the platform default mailer when no custom mail is configured.
     */
    public function send(?Setting $setting, string|array $to, $mailable): void
    {
        $mailer = $this->mailerFor($setting);

        if ($mailer) {
            $mailer->to($to)->send($mailable);
            return;
        }

        Mail::to($to)->send($mailable);
    }
}
