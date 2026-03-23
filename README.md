# MNB-IC-Market-tax-calculator-with-Gmail-integration

A locally-hosted Laravel application for tracking daily broker account statistics via email,
calculating profit/loss, and automatically computing yearly tax obligations —
built specifically for IC Markets users with MNB (Magyar Nemzeti Bank) exchange rates (e.g. USD → HUF),
but adaptable to other brokers and currencies with minor code modifications.

***

## What It Does

- Parses daily balance emails from IC Markets via per-user IMAP configuration and stores them as daily statuses
- Fetches official MNB exchange rates on weekdays at 23:00
- Calculates daily profit/loss per broker account in HUF
- Tracks deposits and withdrawals to accurately separate profit from capital movements
- Computes yearly taxable income with loss carry-forward support
- Automatically upserts yearly tax calculations on December 31st
- Multi-user support: each user sees only their own accounts (admins see all)
- Admin can create users and generate/reset passwords
- Two-factor authentication (2FA) support via Laravel Fortify
- Policy-based authorization on all resources

***

## Known Limitations

> **IMAP credentials required:** Each user must configure their own IMAP settings (host, port, username, password) in their profile before email extraction will work. The password is stored encrypted in the database.

***

## Requirements

- Docker
- A Gmail account with IMAP enabled (for email parsing)

***

## Installation

### 1. Clone The Project

```
git clone https://github.com/bonczbe/MNB-IC-Market-tax-calculator-with-Gmail-integration.git
cd MNB-IC-Market-tax-calculator-with-Gmail-integration
```

If needed, you can run `composer install` and the frontend build commands inside your app container (for example: `docker compose exec app composer install`), depending on your local workflow.

### 2. Environment setup

```
cp .env.example .env
```

Fill in `.env` with your database credentials, mail settings, and tax configuration:

```
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

TAX_VOLUME=0.15           # e.g. 15% tax rate
TAX_BASE_CURRENCY=HUF
TAX_BASE_BROKER_CURRENCY=USD
```

### 3. Running the Application

```
docker compose up -d --build
```

The seeder creates a default admin user and a dummy broker account:

| Field    | Value     |
| -------- | --------- |
| Email    | admin@a.a |
| Password | password  |

> ⚠️ Change the default admin password immediately after first login.

After logging in, go to the user profile and configure your IMAP settings (host, port, encryption, username, password) to enable email extraction.

***

## Deploy new version of the Application

Run `deploy.bat` or `deploy.sh` depending on your operating system.

***

## Scheduled Jobs

Jobs can be individually toggled in `.env` via `config/schedule.php`:

| Job                                     | Schedule             | Description                                   |
| --------------------------------------- | -------------------- | --------------------------------------------- |
| `app:fetch-mnb-rate`                    | Weekdays at 23:00    | Fetches MNB exchange rates                    |
| `app:email-extract`                     | Weekdays at 23:50    | Parses broker emails and saves daily statuses |
| `app:calculate-tax-by-account-for-year` | December 31 at 23:57 | Calculates and stores yearly tax per account  |

All jobs run in the `Europe/Budapest` timezone.

***

## Docker Services

The application runs as 4 Docker containers:

| Service     | Description                              |
| ----------- | ---------------------------------------- |
| `app`       | Laravel application server (port 8000)   |
| `db`        | MariaDB database                         |
| `queue`     | Laravel queue worker for background jobs |
| `scheduler` | Laravel scheduler for cron-like jobs     |

- All services use `restart: unless-stopped`
- Health checks are configured on the `app` service
- `queue` and `scheduler` wait for `app` to be healthy before starting
- Database credentials are read from `.env` (not hardcoded)

***

## IC Markets Setup

If you use IC Markets as your broker, the filter configuration is already set correctly.
You only need to update the dummy broker account with your actual data:

- **Account Number** — your IC Markets account number
- **Filter Number** — replace with your own account identifier in the text
- **Starting Balance** — your initial deposit amount

Or simply create a new broker account record with your own values.

***

## User Management

- **Admin** can create new users via the admin panel
- **Admin** can generate a new password for any user directly from the user edit form
- **Regular users** see only their own broker accounts and related data
- **Admin** can toggle between "all accounts" and "only mine" view using the filter toggle
- All resources are protected by Laravel Policies (ownership-based access control)

***

## Adapting to Other Brokers / Currencies

The application is built around IC Markets and MNB rates, but can be adapted:

- **Different broker**: modify the email parsing logic in the email extract job and adjust the filter fields
- **Different exchange rates**: replace or extend the `app:fetch-mnb-rate` command with your preferred rate source
- **Different currency pair**: update `TAX_BASE_CURRENCY` and `TAX_BASE_BROKER_CURRENCY` in `.env`
- **Different tax rules**: update `TAX_VOLUME` in `.env` and loss carry-forward logic in `TaxCalculatorService`

> ⚠️ The email parser defaults to Gmail IMAP settings (imap.gmail.com, port 993, SSL). Other providers
> can be configured per user via the admin panel IMAP settings.

***

## Notes

- This application is **designed for internal/local use only** and has not been hardened for public deployment
- Exchange rates are fetched from the official MNB (Hungarian National Bank) API
- Tax calculations use a simple flat-rate model with loss carry-forward from the previous year

***

## Contributing

Found a bug or have an idea for improvement? Issues and pull requests are welcome.
This project is a work in progress and open to further development.
