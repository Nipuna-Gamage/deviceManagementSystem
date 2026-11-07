# AssetFlow

AssetFlow is a Laravel 11 device and asset management system for managing
devices, employees, departments, assignments, maintenance work, reports, and
workspace settings.

## Features

- Authenticated dashboard with live inventory statistics
- Device registration, search, filtering, details, editing, and deletion
- Device statuses: available, assigned, maintenance, and retired
- Device conditions, warranty dates, notes, serial numbers, and asset tags
- Employee and department directories
- Device assignment and return workflow with assignment history
- Maintenance requests and maintenance status tracking
- Reports dashboard with device and assignment CSV exports
- Recent activity feed for registrations, assignments, returns, and maintenance
- Admin user management and audit logs
- Responsive AssetFlow dashboard, sidebar, charts, and pagination UI
- Settings Center for profile, organization, notifications, appearance, security,
  and system preferences
- Profile and organization image uploads using Laravel Storage
- Role-based access for `admin`, `manager`, and `staff` users
- Warranty and asset alert notifications

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- MySQL or MariaDB

On Windows, XAMPP can provide PHP and MySQL. This project currently uses the
MySQL client at `C:\xampp\mysql\bin\mysql.exe`.

## Installation

Open PowerShell in the project directory:

```powershell
cd C:\Users\nipun\Desktop\deviceManagementSystem
```

Install PHP dependencies:

```powershell
composer install
```

Create the environment file and application key:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Install frontend dependencies:

```powershell
npm install
```

## Database configuration

Create a MySQL or MariaDB database named `assetflow` and configure `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assetflow
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations for a new or previously unmigrated database:

```powershell
php artisan config:clear
php artisan migrate
```

Do not commit `.env` or database credentials.

## Email configuration

To send real email notifications, configure SMTP in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=assetflow@example.com
MAIL_FROM_NAME="AssetFlow"
```

Then clear the cached configuration:

```powershell
php artisan config:clear
```

## Running the application

Use two PowerShell terminals.

Terminal 1:

```powershell
php artisan serve
```

Terminal 2:

```powershell
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

For a production frontend build:

```powershell
npm run build
```

For uploaded profile and organization images, create the public storage link:

```powershell
php artisan storage:link
```

## Main pages

| Page | URL | Access |
| --- | --- | --- |
| Dashboard | `/` | Authenticated users |
| Device inventory | `/devices` | Authenticated users |
| Register device | `/devices/register` | Authenticated users; write access for admin/manager |
| Assignments | `/assignments` | Authenticated users; write access for admin/manager |
| Employees | `/employees` | Authenticated users |
| Departments | `/departments` | Authenticated users |
| Maintenance | `/maintenance` | Authenticated users |
| Reports | `/reports` | Authenticated users |
| Settings | `/settings` | Authenticated users |
| User management | `/users` | Admin users |
| Audit logs | `/audit-logs` | Admin users |

## Roles and permissions

- `admin`: full administration, user management, audit logs, organization and
  system settings
- `manager`: operational write access for devices, employees, departments,
  assignments, and maintenance
- `staff`: authenticated read access according to the current route rules

All authenticated routes use active-account checks. Write requests are also
recorded by the audit middleware where applicable.

## Demo data

The project includes a safe, repeatable demo-data command that inserts fictional
records directly into the configured `assetflow` database. It does not create a
separate database and does not overwrite existing records.

Insert demo data:

```powershell
php artisan assetflow:demo --seed
```

The seed creates:

- 4 users
- 6 departments
- 30 employees
- 50 devices
- 20 assignments
- 12 maintenance records
- 36 audit log entries
- 18 notifications
- 4 user preference records

Demo records are identified by dedicated markers such as
`@demo.assetflow.test`, `DEMO-`, `DEMO-EMP-`, and `DEMO-AST-`.
Re-running the command skips insertion when marked demo data already exists.

Demo users use this password:

```text
AssetFlowDemo!2026
```

Example login:

```text
demo.user1@demo.assetflow.test
```

Remove only marked demo records:

```powershell
php artisan assetflow:demo --remove
```

The cleanup command does not remove existing non-demo users, employees,
departments, devices, assignments, maintenance records, audit logs, or
notifications.

## Useful commands

```powershell
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
php artisan assetflow:send-alerts
php artisan assetflow:make-admin
```

## Testing

Run the complete test suite:

```powershell
php artisan test
```

The suite covers authentication, authorization, device CRUD, assignments,
maintenance, reports, dashboard states, notifications, and security headers.

## Project structure

- `app/Console/Commands/` - Artisan commands
- `app/Http/Controllers/` - Application controllers
- `app/Http/Middleware/` - Authentication, roles, activity, and security middleware
- `app/Models/` - Eloquent models
- `database/migrations/` - Database schema changes
- `database/seeders/` - Database seeders, including demo data
- `resources/views/` - Blade templates
- `resources/css/` and `resources/js/` - Frontend source
- `routes/web.php` - Web routes
- `tests/` - Feature and unit tests

## Current limitations

- Two-factor authentication is not implemented.
- Maintenance, reports, and settings workflows require an authenticated account.
- The application is intended for local or internal use until production
  deployment hardening, HTTPS, backups, and complete access-policy review are
  configured.

## License

This project is built with the Laravel framework and is intended for internal
AssetFlow device-management use.
