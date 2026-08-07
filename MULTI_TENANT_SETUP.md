# Inveqi Multi-Tenant Setup

This repository contains two Laravel 12 applications sharing one MySQL database:

| App | Path | Purpose | URL (local) |
|-----|------|---------|-------------|
| **Tenant App** | `invoice-generate/` | Landing, registration, invoices, subscriptions | `http://localhost/invoice-generate/public` |
| **Admin App** | `../invoice-admin/` | Platform admin — orgs, plans, subscriptions | `http://localhost/invoice-admin/public` |

## Quick Start

### 1. Tenant App (`invoice-generate`)

```bash
cd invoice-generate
composer install
cp .env.example .env   # configure DB + Stripe keys
php artisan key:generate
php artisan migrate --seed
```

### 2. Admin App (`invoice-admin`)

```bash
cd ../invoice-admin
composer install
cp .env.example .env   # same DB as tenant app
php artisan key:generate
# Do NOT run migrate — tables are managed by invoice-generate
```

## Default Credentials

**Platform Admin** (admin app at `/login`):
- Email: `admin@inveqi.com`
- Password: `Admin@123`

**Demo Tenant** (tenant app at `/login`):
- Email: `admin@example.com`
- Password: `Admin@123`

## Features Added

- **Multi-tenancy**: Each organization has isolated customers, products, invoices, and settings
- **Landing page** at `/` with pricing
- **Registration** creates organization + 14-day trial subscription
- **Subscription plans**: Starter (free), Professional, Business
- **Payment gateway toggle** per tenant (plan-gated) in Settings → Integrations
- **Platform admin** to manage organizations, plans, and subscriptions

## Stripe Configuration

**Tenant invoice payments** (per organization):
- Configure in Settings → Integrations (`stripe_public_key`, `stripe_secret_key`)

**Platform subscriptions** (billing tenants):
```env
STRIPE_PLATFORM_PUBLIC_KEY=pk_...
STRIPE_PLATFORM_SECRET_KEY=sk_...
STRIPE_PLATFORM_WEBHOOK_SECRET=whsec_...
```
Webhook endpoint: `POST /webhook/platform`

## Dependencies Updated

- Regenerated `composer.lock` with Laravel 12.60, Stripe PHP 18.2, DomPDF 3.1, etc.
- Admin app: fresh Laravel 12.12 install
