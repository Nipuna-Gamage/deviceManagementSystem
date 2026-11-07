# AssetFlow

AssetFlow is a Laravel-based device and asset management system. It provides a
central workspace for registering devices, managing employees and departments,
assigning devices, and tracking device returns.

## Features

- Dashboard with live inventory totals
- Device registration, listing, search, filtering, details, editing, and deletion
- Automatic asset tags in the format `AST-00001`
- Device status and condition tracking
- Purchase date, warranty expiry, and notes
- Employee and department management
- Device assignment and return workflow
- Assignment history with handover and return details
- Responsive interface powered by Laravel Blade and Vite

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB

For Windows development, XAMPP can be used to provide PHP and MySQL.

## Installation

Clone or download the project, then open PowerShell in the project directory:

```powershell
cd C:\path\to\deviceManagementSystem
```

Install the backend dependencies:

```powershell
composer install
```

Create the environment file and application key:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Install the frontend dependencies:

```powershell
npm install
```

## Database configuration

Create a MySQL or MariaDB database named `assetflow`. Then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assetflow
DB_USERNAME=root
DB_PASSWORD=
```

Use the username and password configured for your local MySQL installation.
Do not commit `.env` or database credentials.

Run the migrations:

```powershell
php artisan config:clear
php artisan migrate
```

## Running the application

Start the Laravel server:

```powershell
php artisan serve
```

In a second PowerShell terminal, start Vite:

```powershell
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in a browser.

For a production frontend build, use:

```powershell
npm run build
```

## Main pages

| Page | URL |
| --- | --- |
| Dashboard | `/` |
| Device inventory | `/devices` |
| Register a device | `/devices/register` |
| Assignments | `/assignments` |
| Employees | `/employees` |
| Departments | `/departments` |

Maintenance, reports, and settings pages are currently placeholders for future
work.

## Demo data

After configuring the database, demo records can be imported from an SQL dump
using the MySQL client:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -p assetflow < C:\path\to\assetflow.sql
```

If the MySQL root account has no password, press Enter when prompted. Adjust
the MySQL executable and SQL file paths for your installation.

## Testing

Run the test suite with:

```powershell
php artisan test
```

## Useful commands

```powershell
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
```

## Project structure

- `app/` - Models, controllers, and application services
- `database/migrations/` - Database schema changes
- `resources/views/` - Blade templates
- `resources/css/` and `resources/js/` - Frontend source files
- `routes/web.php` - Web routes
- `public/` - Public entry point and compiled assets
- `tests/` - Feature and unit tests

## Security note

Authentication and authorization are not currently implemented. Do not expose
this development application publicly until access control has been added.

## License

This project is based on the Laravel framework and is intended for local
development and internal asset-management use.
