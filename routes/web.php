<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
    Route::post('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
});

Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
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
    Route::post('/settings/invoice', [SettingController::class, 'updateInvoice'])->name('settings.invoice.update');
    Route::post('/settings/payment-gateway', [SettingController::class, 'togglePaymentGateway'])->name('settings.payment-gateway.toggle');

    Route::get('/help', function () {
        return view('help');
    })->name('help');

    // Invoice Templates
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', [InvoiceTemplateController::class, 'index'])->name('index');
        Route::get('/{template}/preview', [InvoiceTemplateController::class, 'preview'])->name('preview');
        Route::post('/{template}/select', [InvoiceTemplateController::class, 'select'])->name('select');
        Route::post('/custom-css', [InvoiceTemplateController::class, 'customCss'])->name('customCss');
    });

    // Recurring Invoices
    Route::prefix('recurring')->name('recurring.')->group(function () {
        Route::get('/', [RecurringInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [RecurringInvoiceController::class, 'create'])->name('create');
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

// Public Estimate links (no auth)
Route::get('/estimate/{token}', [EstimateController::class, 'publicView'])->name('estimates.public');
Route::post('/estimate/{token}/approve', [EstimateController::class, 'approve'])->name('estimates.approve');
Route::post('/estimate/{token}/decline', [EstimateController::class, 'decline'])->name('estimates.decline');

Route::get('/auth/redirect', [MicrosoftController::class, 'redirectToMicrosoft']);
Route::get('/auth/callback', [MicrosoftController::class, 'handleCallback']);
