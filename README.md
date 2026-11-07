<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## AssetFlow redesign — Phase 1

The first redesign phase introduces a responsive AssetFlow workspace shell, navigation, dashboard preview, and device-registration screen. Dashboard metrics and recent-device rows are illustrative sample content only; they are not read from the database yet.

### Frontend assets

Install the frontend dependencies and build the Vite assets:

```bash
npm install
npm run build
```

For local development, run `npm run dev` alongside the Laravel development server.

### Next phase

Phase 2 will add the MySQL device schema, Eloquent models, validated create/update/delete workflows, and real dashboard queries. The registration screen is intentionally preview-only until those backend workflows are implemented.

## Phase 2 — MySQL device inventory

The device inventory now supports database-backed registration, listing, searching, filtering, viewing, editing and deleting.

### Configure MySQL

1. Create a MySQL database named `assetflow` (or choose another name).
2. Copy `.env.example` to `.env` if you do not already have an `.env` file.
3. Set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in `.env` to match your local MySQL account. Never commit `.env`.
4. Run:

```bash
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

### Device inventory features

- Unique serial number validation.
- Automatically generated asset tags (`AST-00001`, etc.).
- Category, status and text search filters.
- Pagination, device detail, edit and delete workflows.
- Purchase date, warranty expiry, condition and notes fields.
- Dashboard totals and recent devices read from the database.

### Tests

Run `php artisan test` after installing Composer dependencies. Feature tests cover inventory loading, registration, serial number uniqueness, updates and deletion.


## Phase 3 — Employees, departments and assignments

Phase 3 adds relational MySQL tables for departments, employees and device assignment history. It includes employee and department directories, assignment creation, device return recording, assignment search/filtering, and automatic device status changes.

### Apply Phase 3 migrations

Back up your database first, then run the following from the project directory. These are additive migrations and do not require dropping the existing `devices` table:

```bash
php artisan migrate
php artisan route:list
php artisan test
```

### Workflow rules

- Department names and codes are unique. Departments with employees cannot be deleted; employees are retained and can be deactivated.
- Employee codes and email addresses are unique. Employees with assignment history cannot be deleted.
- Only active employees and devices with `available` status can receive a new assignment.
- Assignment creation locks the device row in a database transaction, records the handover and changes device status to `assigned`.
- Returning an active assignment records the return date and notes and changes the device status back to `available`.
- Assignment records are retained as history; they are not deleted when a device is returned.

### Notes

The app currently has no authentication/authorization workflow in this phase. `assigned_by` is nullable until user authentication and role permissions are implemented. Do not expose this deployment publicly until authentication and authorization are added.
