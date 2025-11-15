<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceResponseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (only accessible when not logged in)
|--------------------------------------------------------------------------
*/

Route::get('/invoice/respond/{invoice}', [InvoiceResponseController::class, 'respond'])
    ->name('invoice.respond');

Route::get('/invoice/{invoice}/rejected', [InvoiceResponseController::class, 'rejected'])
    ->name('invoices.rejected');

Route::get('/invoice/{invoice}/success', [InvoiceResponseController::class, 'success'])
    ->name('invoices.success');

Route::get('/invoice/{invoice}/cancel', [InvoiceResponseController::class, 'cancel'])
    ->name('invoices.cancel');

Route::middleware('guest')->group(function () {


    // Login & Register Views
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    // Forgot & Reset Password Views
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    // Email Verification View

    // Authentication Actions
    Route::post('/register', [AuthController::class, 'register'])->name('submit.register');
    Route::post('/login', [AuthController::class, 'login'])->name('submit.login');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('search', [App\Http\Controllers\InvoiceController::class, 'search'])->name('search');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('download');
        Route::get('/{id}/send', [InvoiceController::class, 'sendInvoiceEmail'])->name('sendEmail');       // Send email
        Route::post('/{invoice}/void', [InvoiceController::class, 'void'])->name('void');
        Route::get('/{invoice}/accept', [InvoiceResponseController::class, 'acceptPage'])->name('accept.page');
        Route::post('/{invoice}/pay', [InvoiceResponseController::class, 'createPaymentSession'])->name('pay');

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

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/invite', [UserController::class, 'invite'])->name('users.invite');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');


    // AJAX product search and fetch
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
    Route::get('/help', function () {
        return view('help');
    })->name('help');

    Route::post('/settings/webhook/update', [App\Http\Controllers\WebhookSettingController::class, 'updateWebhooks'])
        ->name('settings.webhook.update');
    Route::post('/settings/security/update', [\App\Http\Controllers\SettingController::class, 'updatePassword'])
        ->name('settings.security.update');


});
Route::get('/invitation/accept/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');

Route::post('/invitation/accept/{token}', [InvitationController::class, 'acceptSubmit'])
    ->name('invitation.accept.submit');


Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->post('/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

/*
|--------------------------------------------------------------------------
| Default / Landing Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});
