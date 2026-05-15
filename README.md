# OneKhusa Laravel Integration Reference

> **A complete, production-ready reference implementation of OneKhusa Payment Gateway integration using Laravel 11 & PHP 8.2+**

This project demonstrates best practices for integrating the OneKhusa payment gateway into a Laravel application using the Hosted Checkout flow with smart payment status polling.

---

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation Guide](#-installation-guide)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [Webhook Setup](#-webhook-setup-ngrok)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)

---

## 🎯 Features

✅ **Service-Oriented Architecture** – Clean separation of concerns with `OneKhusaService`  
✅ **Hosted Checkout Integration** – Seamless redirect to OneKhusa's managed payment page  
✅ **Smart Payment Polling** – Frontend-driven status verification using `fetch` API  
✅ **Webhook Support** – Secure endpoint for OneKhusa payment notifications  
✅ **Environment Configuration** – Fully configurable via `.env` file  
✅ **Error Handling** – Comprehensive error logging and user feedback  
✅ **CSRF Protected** – Laravel's built-in security with webhook exemptions

---

## 📦 Requirements

Before you begin, ensure you have the following installed:

| Requirement | Version | Download |
|---|---|---|
| **PHP** | 8.2 or higher | [php.net](https://www.php.net/downloads) |
| **Composer** | Latest | [getcomposer.org](https://getcomposer.org) |
| **Laravel** | 11.x | (Installed via Composer) |
| **Git** | Latest | [git-scm.com](https://git-scm.com) |
| **Node.js** (Optional) | 18+ | [nodejs.org](https://nodejs.org) |

**OneKhusa Account Requirements:**
- OneKhusa API Key
- OneKhusa API Secret
- Organization ID
- Merchant Number
- Webhook Secret

---

## 🚀 Installation Guide

Follow these steps carefully to set up the project:

### Step 1: Clone the Repository

```bash
git clone https://github.com/GarryBalala/OneKhusa-Laravel-Integration-Reference.git
cd OneKhusa-Laravel-Integration-Reference
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This will install all required PHP packages including Laravel and Guzzle HTTP client.

### Step 3: Create Environment Configuration File

```bash
cp .env.example .env
```

If `.env.example` doesn't exist, create a new `.env` file:

```bash
touch .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

This generates a unique encryption key for your Laravel application.

### Step 5: Configure Environment Variables

Open the `.env` file in your text editor and add your OneKhusa credentials:

```env
# ============================================
# Application Settings
# ============================================
APP_NAME="OneKhusa Integration"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ============================================
# OneKhusa Configuration
# ============================================

# Your OneKhusa API credentials (get from dashboard)
ONEKHUSA_API_KEY=your_api_key_here
ONEKHUSA_API_SECRET=your_api_secret_here

# Your organization and merchant details
ONEKHUSA_ORG_ID=0BBNREQ33RSX
ONEKHUSA_MERCHANT_NUMBER=79619974
ONEKHUSA_CAPTURED_BY=your-email@example.com

# Webhook secret for signature verification
ONEKHUSA_WEBHOOK_SECRET=your_webhook_secret_here

# API Endpoints (use sandbox for testing)
ONEKHUSA_BASE_URL=https://api.onekhusa.com/sandbox/v1
ONEKHUSA_CHECKOUT_URL=https://api.onekhusa.com/sandbox/v1/checkout/rtp/initiate

# Your public callback URL (important for webhooks)
PUBLIC_CALLBACK_URL=http://localhost:8000

# ============================================
# Optional Settings
# ============================================
PORT=8000
QUEUE_CONNECTION=sync
```

### Step 6: Set File Permissions (Linux/Mac)

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage bootstrap/cache/*
```

### Step 7: Verify Installation

Run the Laravel health check:

```bash
php artisan tinker
```

Then type:
```php
echo "Installation successful!";
```

Press `Ctrl+D` to exit.

---

## ⚙️ Configuration

### Environment Variables Explained

| Variable | Description | Example |
|---|---|---|
| `ONEKHUSA_API_KEY` | Your OneKhusa API key from dashboard | `sk_sandbox_...` |
| `ONEKHUSA_API_SECRET` | Secret for API authentication | Your secret key |
| `ONEKHUSA_ORG_ID` | Your organization identifier | `0BBNREQ33RSX` |
| `ONEKHUSA_MERCHANT_NUMBER` | Your merchant ID | `79619974` |
| `ONEKHUSA_CAPTURED_BY` | Email for payment capture | `merchant@example.com` |
| `ONEKHUSA_WEBHOOK_SECRET` | Secret to verify webhooks | Your webhook secret |
| `ONEKHUSA_BASE_URL` | OneKhusa API base endpoint | Sandbox or production URL |
| `ONEKHUSA_CHECKOUT_URL` | Hosted checkout endpoint | Complete checkout URL |
| `PUBLIC_CALLBACK_URL` | Your public URL for callbacks | Your domain or ngrok URL |

---

## 📂 Project Structure

```
OneKhusa-Laravel-Integration-Reference/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TicketController.php      # Purchase & status endpoints
│   │   │   └── WebhookController.php     # OneKhusa callback handler
│   │   └── Middleware/
│   │       └── VerifyWebhookSignature.php# Webhook verification
│   │
│   └── Services/
│       └── OneKhusaService.php           # Core integration logic
│
├── resources/
│   ├── views/
│   │   └── welcome.blade.php             # Dashboard UI
│   └── js/
│       └── payment-polling.js            # Frontend status checker
│
├── routes/
│   ├── web.php                           # Web routes (dashboard)
│   └── api.php                           # API routes (integration)
│
├── database/
│   └── migrations/                       # Database setup
│
├── .env                                  # Environment configuration
├── .gitignore                            # Git exclusions
├── composer.json                         # PHP dependencies
└── README.md                             # This file
```

---

## 💻 Usage

### Step 1: Start the Development Server

```bash
php artisan serve
```

The application will be available at **http://localhost:8000**

### Step 2: Access the Dashboard

Open your browser and navigate to:
```
http://localhost:8000
```

You'll see the payment dashboard where you can initiate transactions.

### Step 3: Initiate a Payment

1. Enter the payment amount
2. Enter a description
3. Click **"Buy Now"**
4. You'll be redirected to OneKhusa's hosted checkout page
5. Complete the payment following OneKhusa's flow

### Step 4: Verify Payment Status

The dashboard will automatically poll for payment status. Alternatively, you can:

```bash
# Check via API
curl http://localhost:8000/api/payment-status/{transaction_id}
```

---

## 🔌 API Endpoints

### Initiate Payment

**POST** `/api/tickets`

```bash
curl -X POST http://localhost:8000/api/tickets \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 10000,
    "description": "Purchase ticket",
    "customer_email": "customer@example.com"
  }'
```

**Response:**
```json
{
  "transaction_id": "txn_123456",
  "checkout_url": "https://api.onekhusa.com/sandbox/v1/checkout/...",
  "status": "pending"
}
```

### Check Payment Status

**GET** `/api/payment-status/{transaction_id}`

```bash
curl http://localhost:8000/api/payment-status/txn_123456
```

**Response:**
```json
{
  "transaction_id": "txn_123456",
  "status": "completed",
  "amount": 10000,
  "timestamp": "2026-05-15T12:30:00Z"
}
```

### Webhook Notification

**POST** `/api/webhooks/payments`

This endpoint receives payment notifications from OneKhusa. It automatically:
- Verifies the webhook signature
- Updates payment status in cache
- Logs the transaction

---

## 📡 Webhook Setup (NGrok)

To receive real-time payment notifications during development:

### Step 1: Install NGrok

Download from [ngrok.com](https://ngrok.com/download)

### Step 2: Start NGrok Tunnel

```bash
ngrok http 8000
```

You'll see output like:
```
Forwarding                    https://abc123xyz.ngrok-free.dev -> http://localhost:8000
```

Copy the HTTPS URL.

### Step 3: Update Environment

Edit your `.env` file:

```env
PUBLIC_CALLBACK_URL=https://abc123xyz.ngrok-free.dev
```

### Step 4: Register Webhook in OneKhusa Dashboard

1. Log in to OneKhusa Dashboard
2. Navigate to **Webhooks** or **Integrations**
3. Register the endpoint:
   ```
   https://abc123xyz.ngrok-free.dev/api/webhooks/payments
   ```
4. Select events: `payment.completed`, `payment.failed`

### Step 5: Test the Webhook

Complete a test payment and check:
- Your application logs
- NGrok inspector: http://127.0.0.1:4040
- OneKhusa Dashboard webhook logs

---

## 🔍 Troubleshooting

### Issue: Composer installation fails

**Solution:**
```bash
# Clear composer cache
composer clear-cache

# Try again
composer install
```

### Issue: `php artisan` command not found

**Solution:**
```bash
# Make sure you're in the project directory
cd OneKhusa-Laravel-Integration-Reference

# Try with PHP directly
php artisan serve
```

### Issue: Port 8000 already in use

**Solution:**
```bash
# Use a different port
php artisan serve --port=8001
```

### Issue: OneKhusa API authentication fails

**Checklist:**
- ✓ Verify API Key in `.env` is correct
- ✓ Verify API Secret in `.env` is correct
- ✓ Check you're using sandbox credentials for testing
- ✓ Ensure credentials haven't expired

### Issue: Webhooks not being received

**Checklist:**
- ✓ Verify `PUBLIC_CALLBACK_URL` in `.env` is correct
- ✓ NGrok tunnel is active and running
- ✓ Webhook URL is registered in OneKhusa Dashboard
- ✓ Check `ONEKHUSA_WEBHOOK_SECRET` matches in dashboard

### Issue: Payment polling not working

**Solution:**
```bash
# Check if cache driver is configured correctly
php artisan cache:clear

# Verify config/cache.php uses 'array' driver for development
```

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [OneKhusa API Documentation](https://onekhusa.com/docs)
- [NGrok Documentation](https://ngrok.com/docs)

---

## 💬 Support

For issues or questions:
- Check the [Troubleshooting](#-troubleshooting) section
- Review OneKhusa API documentation
- Open an issue on GitHub

---
