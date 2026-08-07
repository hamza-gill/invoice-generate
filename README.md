<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Mail Configuration

Each organization can configure its own outbound mail provider for invoice emails from **Settings → Mail Configuration**. This lets every tenant send invoices through its own sending account instead of the platform default.

### Supported providers

| Provider | Transport | Authentication |
|---|---|---|
| **Brevo** | SMTP (`smtp-relay.brevo.com:587`) | SMTP login + SMTP key |
| **SendGrid** | SMTP (`smtp.sendgrid.net:587`) | Username `apikey` + API key |
| **Microsoft 365** | SMTP (`smtp.office365.com:587`) | OAuth2 (`offline_access SMTP.Send`), per-org token |
| **Custom SMTP** | Any SMTP host/port | Username + password, TLS/SSL/none |
| **Log** | `log` transport | None (writes to log, useful for testing) |
| **Platform Default** | Falls back to the platform mailer | Uses `.env` `MAIL_*` config (Brevo by default) |

### How it works

1. The org admin opens **Settings → Mail Configuration**, picks a provider, and fills in the credentials plus a From address/name.
2. Settings are stored per-organization in the `settings` table (`mail_mailer`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`, `mail_from_address`, `mail_from_name`). `mail_password` is encrypted at rest with Laravel `Crypt`.
3. When an invoice email is sent, `App\Services\MailConfigurationService` resolves the organization's transport and builds a dedicated `Mailer` instance (with `alwaysFrom` set to the org's sender). If the org has no custom config, sending falls back to the platform default mailer.
4. The **Send Test Email** button verifies the configuration by sending a test message to any address.

### Key files

| File | Purpose |
|---|---|
| `app/Services/MailConfigurationService.php` | Per-org transport resolution, `mailerFor()`, `send()` helper, provider presets |
| `app/Http/Controllers/SettingController.php` | `updateMail()` (save config) and `sendTestMail()` (test send) |
| `app/Http/Requests/Setting/UpdateMailRequest.php` | Validation rules for mail settings |
| `app/Mail/MailTestMail.php` + `resources/views/emails/mail-test.blade.php` | Test email mailable + template |
| `app/Jobs/SendInvoiceEmail.php`, `app/Http/Controllers/InvoiceController.php` | Invoice email send paths now route through the org mailer |
| `app/Services/MicrosoftMailer.php`, `app/Http/Controllers/Auth/MicrosoftController.php`, `app/Models/MicrosoftToken.php` | Per-org Microsoft OAuth2 token connect/refresh |
| `database/migrations/2026_08_08_000004_add_mail_config_to_settings_table.php` | Mail config columns on `settings` |
| `database/migrations/2026_08_08_000005_add_organization_id_to_microsoft_tokens_table.php` | `organization_id` on `microsoft_tokens` |

### Sending invoice emails

`SendInvoiceEmail` (queued job) and `InvoiceController::sendInvoiceEmail` both call `MailConfigurationService::send()`, which:

- Returns the org's `Mailer` when a custom provider is configured (SMTP/Brevo/SendGrid/Microsoft/Log).
- Falls back to `Mail::to(...)` (platform mailer) when the org is on `platform_default` or has no `settings` row.

### Microsoft 365 OAuth2

- Tokens are stored per organization in `microsoft_tokens` (one per org).
- Connect from **Settings → Mail Configuration → Connect Microsoft 365**, which redirects through `/auth/redirect` → Microsoft login → `/auth/callback`. The token is saved with the current org's id and auto-refreshed on expiry via `MicrosoftMailer::getValidToken()`.
- Requires `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, `AZURE_TENANT_ID`, and `AZURE_REDIRECT_URI` in `.env`.
- The previous global override in `App\Providers\MicrosoftMailServiceProvider` (which hijacked the `smtp` mailer at boot) was removed in favor of per-org resolution at send time.

### Setup

```bash
php artisan migrate
```

Then, as an admin/developer user, open **Settings → Mail Configuration**, choose a provider, and use **Send Test Email** to confirm it works.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
