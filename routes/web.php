<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceResponseController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PlatformStripeWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\UserController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Landing & Marketing
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/terms', fn () => view('landing.terms'))->name('terms');
Route::get('/privacy', fn () => view('landing.privacy'))->name('privacy');
Route::get('/features', fn () => view('landing.features'))->name('features');
Route::get('/integrations', fn () => view('landing.integrations'))->name('integrations');
Route::get('/changelog', fn () => view('landing.changelog'))->name('changelog');
Route::get('/about', fn () => view('landing.about'))->name('about');
Route::get('/blog', fn () => view('landing.blog'))->name('blog');
Route::get('/careers', fn () => view('landing.careers'))->name('careers');
Route::get('/docs', fn () => view('landing.docs'))->name('docs');
Route::get('/help-center', fn () => view('landing.help-center'))->name('help-center');
Route::get('/status', fn () => view('landing.status'))->name('status');
Route::get('/api', fn () => view('landing.api'))->name('api');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| SEO Landing Pages
|--------------------------------------------------------------------------
| Dedicated keyword-targeting pages. Each is built from shared section
| partials and a per-page content config (config/seo_pages.php).
*/

Route::get('/invoicing-software', fn () => app(LandingController::class)->seo('invoicing-software'))->name('seo.invoicing-software');
Route::get('/online-invoice-software', fn () => app(LandingController::class)->seo('online-invoice-software'))->name('seo.online-invoice-software');
Route::get('/invoice-management-software', fn () => app(LandingController::class)->seo('invoice-management-software'))->name('seo.invoice-management-software');
Route::get('/invoice-generator', fn () => app(LandingController::class)->seo('invoice-generator'))->name('seo.invoice-generator');
Route::get('/billing-software', fn () => app(LandingController::class)->seo('billing-software'))->name('seo.billing-software');
Route::get('/recurring-invoicing', fn () => app(LandingController::class)->seo('recurring-invoicing'))->name('seo.recurring-invoicing');
Route::get('/invoice-automation', fn () => app(LandingController::class)->seo('invoice-automation'))->name('seo.invoice-automation');
Route::get('/estimate-invoice-software', fn () => app(LandingController::class)->seo('estimate-invoice-software'))->name('seo.estimate-invoice-software');
Route::get('/stripe-invoicing', fn () => app(LandingController::class)->seo('stripe-invoicing'))->name('seo.stripe-invoicing');
Route::get('/small-business-invoicing', fn () => app(LandingController::class)->seo('small-business-invoicing'))->name('seo.small-business-invoicing');
Route::get('/invoicing-software-for-freelancers', fn () => app(LandingController::class)->seo('invoicing-software-for-freelancers'))->name('seo.invoicing-software-for-freelancers');
Route::get('/invoicing-software-for-agencies', fn () => app(LandingController::class)->seo('invoicing-software-for-agencies'))->name('seo.invoicing-software-for-agencies');
Route::get('/invoicing-software-for-consultants', fn () => app(LandingController::class)->seo('invoicing-software-for-consultants'))->name('seo.invoicing-software-for-consultants');
Route::get('/invoicing-software-for-contractors', fn () => app(LandingController::class)->seo('invoicing-software-for-contractors'))->name('seo.invoicing-software-for-contractors');
Route::get('/invoicing-software-for-small-businesses', fn () => app(LandingController::class)->seo('invoicing-software-for-small-businesses'))->name('seo.invoicing-software-for-small-businesses');
Route::get('/invoicing-software-for-startups', fn () => app(LandingController::class)->seo('invoicing-software-for-startups'))->name('seo.invoicing-software-for-startups');

// Free SEO tools
Route::get('/tools/free-invoice-generator', [ToolsController::class, 'freeInvoiceGenerator'])->name('tools.free-invoice-generator');
Route::get('/tools/invoice-template', [ToolsController::class, 'invoiceTemplate'])->name('tools.invoice-template');
Route::get('/tools/invoice-number-generator', [ToolsController::class, 'invoiceNumberGenerator'])->name('tools.invoice-number-generator');
Route::get('/tools/estimate-generator', [ToolsController::class, 'estimateGenerator'])->name('tools.estimate-generator');
Route::get('/tools/late-fee-calculator', [ToolsController::class, 'lateFeeCalculator'])->name('tools.late-fee-calculator');
Route::get('/tools/invoice-calculator', [ToolsController::class, 'invoiceCalculator'])->name('tools.invoice-calculator');

Route::get('/robots.txt', function () {
    $rules = implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /forgot-password',
        'Disallow: /reset-password',
        'Disallow: /dashboard',
        'Disallow: /invoices',
        'Disallow: /settings',
        'Disallow: /users',
        'Disallow: /products',
        'Disallow: /recurring',
        'Disallow: /estimates',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);

    return response($rules)->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => '/', 'freq' => 'daily', 'priority' => '1.0'],
        ['loc' => '/pricing', 'freq' => 'weekly', 'priority' => '0.9'],
        ['loc' => '/features', 'freq' => 'weekly', 'priority' => '0.9'],
        ['loc' => '/integrations', 'freq' => 'weekly', 'priority' => '0.8'],
        ['loc' => '/changelog', 'freq' => 'weekly', 'priority' => '0.7'],
        ['loc' => '/about', 'freq' => 'monthly', 'priority' => '0.6'],
        ['loc' => '/blog', 'freq' => 'weekly', 'priority' => '0.8'],
        ['loc' => '/careers', 'freq' => 'monthly', 'priority' => '0.5'],
        ['loc' => '/docs', 'freq' => 'weekly', 'priority' => '0.8'],
        ['loc' => '/help-center', 'freq' => 'monthly', 'priority' => '0.7'],
        ['loc' => '/status', 'freq' => 'daily', 'priority' => '0.5'],
        ['loc' => '/api', 'freq' => 'monthly', 'priority' => '0.7'],
        ['loc' => '/contact', 'freq' => 'monthly', 'priority' => '0.6'],
        ['loc' => '/terms', 'freq' => 'yearly', 'priority' => '0.3'],
        ['loc' => '/privacy', 'freq' => 'yearly', 'priority' => '0.3'],
        // SEO landing pages
        ['loc' => '/invoicing-software', 'freq' => 'monthly', 'priority' => '0.9'],
        ['loc' => '/online-invoice-software', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoice-management-software', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoice-generator', 'freq' => 'monthly', 'priority' => '0.9'],
        ['loc' => '/billing-software', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/recurring-invoicing', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoice-automation', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/estimate-invoice-software', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/stripe-invoicing', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/small-business-invoicing', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-freelancers', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-agencies', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-consultants', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-contractors', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-small-businesses', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/invoicing-software-for-startups', 'freq' => 'monthly', 'priority' => '0.8'],
        // Free SEO tools
        ['loc' => '/tools/free-invoice-generator', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/tools/invoice-template', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/tools/invoice-number-generator', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/tools/estimate-generator', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/tools/late-fee-calculator', 'freq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/tools/invoice-calculator', 'freq' => 'monthly', 'priority' => '0.8'],
    ];

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
});

Route::bind('invoice', function ($value) {
    return Invoice::withoutGlobalScopes()->findOrFail($value);
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/register', [AuthController::class, 'register'])->name('submit.register');
    Route::post('/login', [AuthController::class, 'login'])->name('submit.login');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/respond/{invoice}', [InvoiceResponseController::class, 'respond'])->name('respond');
        Route::get('/{invoice}/rejected', [InvoiceResponseController::class, 'rejected'])->name('rejected');
        Route::get('/{invoice}/success', [InvoiceResponseController::class, 'success'])->name('success');
        Route::get('/{invoice}/public', [InvoiceController::class, 'public'])->name('public');
        Route::get('/{invoice}/cancel', [InvoiceResponseController::class, 'cancel'])->name('cancel');
        Route::get('/{invoice}/accept', [InvoiceResponseController::class, 'acceptPage'])->name('accept.page');
        Route::post('/{invoice}/pay', [InvoiceResponseController::class, 'createPaymentSession'])->name('pay');
        Route::get('/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('download');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkoutPage'])->name('subscription.checkout');
    Route::post('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout.process');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
});

/*
|--------------------------------------------------------------------------
| Platform Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('platform')->name('platform.')->group(function () {
    Route::get('/login', [App\Http\Controllers\PlatformAdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\PlatformAdminAuthController::class, 'login']);

    Route::middleware(['auth:platform'])->group(function () {
        Route::get('/', [App\Http\Controllers\PlatformAdminSettingController::class, 'index'])->name('settings');
        Route::get('/settings', [App\Http\Controllers\PlatformAdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\PlatformAdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/logout', [App\Http\Controllers\PlatformAdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/preview', [InvoiceController::class, 'preview'])->name('preview');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('search', [InvoiceController::class, 'search'])->name('search');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{invoice}/downloads', [InvoiceController::class, 'downloadPdf'])->name('downloads');
        Route::get('/{id}/send', [InvoiceController::class, 'sendInvoiceEmail'])->name('sendEmail');
        Route::post('/{invoice}/void', [InvoiceController::class, 'void'])->name('void');
    });

    Route::get('/reports', [InvoiceController::class, 'reports'])->name('reports');
    Route::post('/reports/export', [InvoiceController::class, 'exportCsv'])->name('reports.export');

    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/import', [CustomerController::class, 'import'])->name('customers.import');
        Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/create', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('list/fetching', [App\Http\Controllers\Ajax\CustomerController::class, 'fetch'])->name('customers.fetch');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });

    Route::get('/search', [SearchController::class, 'globalSearch'])->name('search');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/invite', [UserController::class, 'invite'])->name('users.invite');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::put('/users/{user}/revoke', [UserController::class, 'revoke'])->name('users.revoke');

    Route::post('products/import', [App\Http\Controllers\Ajax\ProductController::class, 'import'])->name('products.import');
    Route::get('/products/fetch', [App\Http\Controllers\Ajax\ProductController::class, 'fetch'])->name('products.fetch');
    Route::get('/products/search', [App\Http\Controllers\Ajax\ProductController::class, 'search'])->name('products.search');
    Route::resource('products', ProductController::class);

    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/organization', [SettingController::class, 'updateOrganization'])->name('settings.organization.update');
    Route::post('/settings/integration', [SettingController::class, 'updateIntegration'])->name('settings.integration.update');
    Route::post('/settings/mail', [SettingController::class, 'updateMail'])->name('settings.mail.update');
    Route::post('/settings/mail/test', [SettingController::class, 'sendTestMail'])->name('settings.mail.test');
    Route::post('/settings/invoice', [SettingController::class, 'updateInvoice'])->name('settings.invoice.update');
    Route::post('/settings/payment-gateway', [SettingController::class, 'togglePaymentGateway'])->name('settings.payment-gateway.toggle');

    Route::get('/help', function () {
        return view('help');
    })->name('help');

    Route::get('/settings/tutorial', function () {
        return view('settings.tutorial');
    })->name('settings.tutorial');

    // Invoice Templates
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [InvoiceTemplateController::class, 'index'])->name('index');
        Route::get('/{template}/preview', [InvoiceTemplateController::class, 'preview'])->name('preview');
        Route::get('/{template}/preview-embed', [InvoiceTemplateController::class, 'previewEmbed'])
            ->name('preview.embed');
        Route::post('/{template}/select', [InvoiceTemplateController::class, 'select'])->name('select');
        Route::post('/custom-css', [InvoiceTemplateController::class, 'customCss'])->name('customCss');
    });

    // Recurring Invoices
    Route::prefix('recurring')->name('recurring.')->group(function () {
        Route::get('/', [RecurringInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [RecurringInvoiceController::class, 'create'])->name('create');
        Route::post('/preview', [RecurringInvoiceController::class, 'preview'])->name('preview');
        Route::post('/', [RecurringInvoiceController::class, 'store'])->name('store');
        Route::get('/{recurring}', [RecurringInvoiceController::class, 'show'])->name('show');
        Route::get('/{recurring}/edit', [RecurringInvoiceController::class, 'edit'])->name('edit');
        Route::put('/{recurring}', [RecurringInvoiceController::class, 'update'])->name('update');
        Route::post('/{recurring}/pause', [RecurringInvoiceController::class, 'pause'])->name('pause');
        Route::post('/{recurring}/resume', [RecurringInvoiceController::class, 'resume'])->name('resume');
        Route::post('/{recurring}/clone', [RecurringInvoiceController::class, 'clone'])->name('clone');
        Route::delete('/{recurring}', [RecurringInvoiceController::class, 'destroy'])->name('destroy');
    });

    // Estimates & Quotes
    Route::prefix('estimates')->name('estimates.')->group(function () {
        Route::get('/', [EstimateController::class, 'index'])->name('index');
        Route::get('/create', [EstimateController::class, 'create'])->name('create');
        Route::post('/preview', [EstimateController::class, 'preview'])->name('preview');
        Route::post('/', [EstimateController::class, 'store'])->name('store');
        Route::get('/{estimate}', [EstimateController::class, 'show'])->name('show');
        Route::get('/{estimate}/edit', [EstimateController::class, 'edit'])->name('edit');
        Route::put('/{estimate}', [EstimateController::class, 'update'])->name('update');
        Route::post('/{estimate}/send', [EstimateController::class, 'send'])->name('send');
        Route::post('/{estimate}/convert', [EstimateController::class, 'convertToInvoice'])->name('convert');
        Route::delete('/{estimate}', [EstimateController::class, 'destroy'])->name('destroy');
    });

    Route::post('/settings/webhook/update', [App\Http\Controllers\WebhookSettingController::class, 'updateWebhooks'])
        ->name('settings.webhook.update');
    Route::post('/settings/security/update', [SettingController::class, 'updatePassword'])
        ->name('settings.security.update');
});

Route::get('/invitation/accept/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');

Route::post('/invitation/accept/{token}', [InvitationController::class, 'acceptSubmit'])
    ->name('invitation.accept.submit');

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/webhook/platform', [PlatformStripeWebhookController::class, 'handle']);

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/webhook/{identifier}', [App\Http\Controllers\OrganizationWebhookController::class, 'handle']);

// Public Estimate links (no auth)
Route::get('/estimate/{token}', [EstimateController::class, 'publicView'])->name('estimates.public');
Route::post('/estimate/{token}/approve', [EstimateController::class, 'approve'])->name('estimates.approve');
Route::post('/estimate/{token}/decline', [EstimateController::class, 'decline'])->name('estimates.decline');

Route::get('/auth/redirect', [MicrosoftController::class, 'redirectToMicrosoft'])->name('auth.redirect');
Route::get('/auth/callback', [MicrosoftController::class, 'handleCallback']);
