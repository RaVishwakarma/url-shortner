# URL Shortener

A multi-company URL shortening service built with Laravel 12. Users are invited into a company as an Admin or Member, while a seeded SuperAdmin manages companies and can see every short URL.

Repository: https://github.com/RaVishwakarma/url-shortner

# Requirements

- PHP 8.3 or newer
- Composer
- Node.js 20.19.x or 22.12+ and npm
- MySQL

# Local setup

```bash
git clone https://github.com/RaVishwakarma/url-shortner.git
cd url-shortner
cp .env.example .env
composer install
```

# setup database then run

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

# Seeded accounts

 Role        Email                       Password 
 SuperAdmin  `superadmin@example.com`    `test123` 
 Admin       `admin@example.com`         `password`
 Member      `member@example.com`        `password` 

# Roles and permissions

- SuperAdmin can create a company by inviting its first Admin, and can see short URLs from every company. A SuperAdmin cannot create short URLs.
- Admin can invite Admins or Members into their own company.
- Member can create and manage only their own URLs.
- Short URLs are redirected to their original URL.

