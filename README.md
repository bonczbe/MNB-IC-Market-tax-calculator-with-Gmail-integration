# MNB-IC-Market-tax-calculator-with-Gmail-integration

A locally-hosted Laravel application for tracking daily broker account statistics via email,
calculating profit/loss, and automatically computing yearly tax obligations —
built specifically for IC Markets users with MNB (Magyar Nemzeti Bank) exchange rates (e.g. USD → HUF),
but adaptable to other brokers and currencies with minor code modifications.

---

## What It Does

- Parses daily balance emails from IC Markets (Gmail) and stores them as daily statuses
- Fetches official MNB exchange rates on weekdays at 23:00
- Calculates daily profit/loss per broker account in HUF
- Tracks deposits and withdrawals to accurately separate profit from capital movements
- Computes yearly taxable income with loss carry-forward support
- Automatically upserts yearly tax calculations on December 31st
- Multi-user support: each user sees only their own accounts (admins see all)
- Admin can create users and generate/reset passwords

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL
- A Gmail account with IMAP enabled (for email parsing)

---

## Installation

### 1. Clone & install dependencies

```bash
git clone [<repo-url>](https://github.com/bonczbe/MNB-IC-Market-tax-calculator-with-Gmail-integration.git)
cd MNB-IC-Market-tax-calculator-with-Gmail-integration

composer install
npm install
npm run build
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Fill in `.env` with your database credentials, mail settings, and tax configuration:

```env
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

TAX_VOLUME=0.15           # e.g. 15% tax rate
TAX_BASE_CURRENCY=HUF
TAX_BASE_BROKER_CURRENCY=USD
```

Configure IMAP settings in `config/imap.php` for your Gmail account.

### 3. Migrate & seed

```bash
php artisan migrate --seed
```

The seeder creates a default admin user and a dummy broker account:

| Field    | Value     |
| -------- | --------- |
| Email    | admin@a.a |
| Password | password  |

---

## Running the Application

Three processes need to run simultaneously (use separate terminal windows):

```bash
# 1. Web server
php artisan serve

# 2. Queue worker (processes email extraction and rate fetching jobs)
php artisan queue:work

# 3. Scheduler (triggers daily/yearly automated jobs)
php artisan schedule:work
```

---

## Scheduled Jobs

Jobs can be individually toggled in `.env` via `config/schedule.php`:

| Job                                     | Schedule             | Description                                   |
| --------------------------------------- | -------------------- | --------------------------------------------- |
| `app:fetch-mnb-rate`                    | Weekdays at 23:00    | Fetches MNB exchange rates                    |
| `app:email-extract`                     | Weekdays at 23:50    | Parses broker emails and saves daily statuses |
| `app:calculate-tax-by-account-for-year` | December 31 at 23:57 | Calculates and stores yearly tax per account  |

All jobs run in the `Europe/Budapest` timezone.

---

## IC Markets Setup

If you use IC Markets as your broker, the filter configuration is already set correctly.
You only need to update the dummy broker account with your actual data:

- **Account Number** — your IC Markets account number
- **Filter Number** — replace `52776665` with your own account identifier in the text
- **Starting Balance** — your initial deposit amount

Or simply create a new broker account record with your own values.

---

## User Management

- **Admin** can create new users via the admin panel
- **Admin** can generate a new password for any user directly from the user edit form
- **Regular users** see only their own broker accounts and related data
- **Admin** can toggle between "all accounts" and "only mine" view using the filter toggle

---

## Adapting to Other Brokers / Currencies

The application is built around IC Markets and MNB rates, but can be adapted:

- **Different broker**: modify the email parsing logic in the email extract job and adjust the filter fields
- **Different exchange rates**: replace or extend the `app:fetch-mnb-rate` command with your preferred rate source
- **Different currency pair**: update `TAX_BASE_CURRENCY` and `TAX_BASE_BROKER_CURRENCY` in `.env`
- **Different tax rules**: update `TAX_VOLUME` in `.env` and loss carry-forward logic in `TaxCalculatorService`

> ⚠️ The email parser was written specifically for Gmail IMAP. Other providers may require
> modifications in `config/imap.php` and the email extract job.

---

## Notes

- This application is **designed for local use only** and has not been hardened for public deployment
- Exchange rates are fetched from the official MNB (Hungarian National Bank) API
- Tax calculations use a simple flat-rate model with loss carry-forward from the previous year

---

## Contributing

Found a bug or have an idea for improvement? Issues and pull requests are welcome.
This project is a work in progress and open to further development.
