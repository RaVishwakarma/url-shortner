# URL Shortener

A multi-company URL shortening service built with Laravel 12. Users are invited into a company as an Admin or Member, while a seeded SuperAdmin manages companies and can inspect every short URL.

Repository: https://github.com/RaVishwakarma/url-shortner

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite or MySQL

## Local setup

```bash
git clone https://github.com/RaVishwakarma/url-shortner.git
cd url-shortener
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

The default `.env.example` uses SQLite. To use MySQL, change `DB_CONNECTION` and add the corresponding `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` values before running the migrations.

Open `http://127.0.0.1:8000`.

## Seeded accounts

| Role | Email | Password |
| --- | --- | --- |
| SuperAdmin | `superadmin@example.com` | `test123` |
| Admin | `admin@example.com` | `password` |
| Member | `member@example.com` | `password` |

Change these development passwords before using the application outside a local test environment.

## Roles and permissions

- SuperAdmin can create a company by inviting its first Admin, and can see short URLs from every company. A SuperAdmin cannot create short URLs.
- Admin can invite Admins or Members into their own company, create short URLs, and see every short URL in their company.
- Member can create short URLs and see only the URLs they created.
- Short URLs are public and redirect visitors to their original HTTP or HTTPS URL.
- Public registration is disabled; Admin and Member accounts are created through invitation links.

Invitations expire after seven days. With the default `MAIL_MAILER=log`, invitation emails are written to `storage/logs/laravel.log`. The generated invitation link is also shown after submitting the invitation form for convenient local testing.

## Running tests

```bash
php artisan test
```

To run all configured quality checks:

```bash
composer test
```

The feature tests cover URL creation, role-based URL visibility, public redirects, invitation authorization, invitation acceptance, and invitation expiry.

## AI usage disclosure

OpenAI Codex was used to review the requirements, identify authorization and test coverage gaps, assist with Laravel validation and notification syntax, and implement and verify the requested fixes. The application structure, requirements interpretation, and final behavior were reviewed as part of the development workflow.
