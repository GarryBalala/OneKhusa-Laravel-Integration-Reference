# OneKhusa Laravel Integration Reference

A professional reference implementation of the **OneKhusa Payment Gateway** using **PHP 8.2+** and **Laravel 11**. This project demonstrates the **Hosted Checkout** flow with an automated status-polling strategy.

## 🚀 Key Features
- **Service-Oriented Architecture**: Clean separation of logic in `App\Services\OneKhusaService`.
- **Hosted Checkout**: Seamless redirection to OneKhusa’s managed payment page.
- **Smart Polling**: A frontend strategy using `fetch` to verify payment status against the Laravel Cache.
- **Webhook Integration**: Secure endpoint for OneKhusa notifications with CSRF exemption.
- **Environment Driven**: Fully configurable via `.env`.

## 📂 Project Structure
```text
onekhusa-laravel-integration/
├── app/
│   ├── Http/Controllers/
│   │   ├── TicketController.php   # Buy logic & Status checks
│   │   └── WebhookController.php  # Handles OneKhusa callbacks
│   └── Services/
│       └── OneKhusaService.php    # API logic (Guzzle/Http)
├── resources/views/
│   └── welcome.blade.php          # Fintech Dashboard (Frontend)
├── routes/
│   ├── web.php                    # Dashboard route
│   └── api.php                    # Integration endpoints
├── .env                           # The Brain (Secrets)
└── .gitignore                     # Security
🛠️ Setup & Installation
Clone and Install:
code
Bash
git clone https://github.com/GarryBalala/OneKhusa-Laravel-Integration-Reference.git
cd OneKhusa-Laravel-Integration-Reference
composer install
Configure Environment:
Create a .env file and add your credentials:
code
Env
ONEKHUSA_API_KEY=
ONEKHUSA_API_SECRET=
ONEKHUSA_ORG_ID=0BBNREQ33RSX
ONEKHUSA_MERCHANT_NUMBER=79619974
ONEKHUSA_CAPTURED_BY=example@gmail.com
ONEKHUSA_WEBHOOK_SECRET=
ONEKHUSA_BASE_URL=https://api.onekhusa.com/sandbox/v1
ONEKHUSA_CHECKOUT_URL=https://api.onekhusa.com/sandbox/v1/checkout/rtp/initiate

# Update this with your current NGrok URL
PUBLIC_CALLBACK_URL=https://zena-unjudgeable-renita.ngrok-free.dev

PORT=8080
Start the Server:
code
Bash
php artisan serve
📡 Webhook Setup (NGrok)
Run ngrok http 8000.
Update PUBLIC_CALLBACK_URL in your .env.
Register https://your-id.ngrok-free.dev/api/webhooks/payments in the OneKhusa Portal.
code
Code
---

