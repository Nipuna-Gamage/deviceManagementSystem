<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Employee;
use App\Models\MaintenanceRecord;
use App\Models\OrganizationSetting;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AssetFlowDemoSeeder extends Seeder
{
    public const USER_MARKER = '@demo.assetflow.test';
    public const DEPARTMENT_MARKER = 'DEMO-';
    public const EMPLOYEE_MARKER = 'DEMO-EMP-';
    public const DEVICE_MARKER = 'DEMO-AST-';

    public function run(): array
    {
        return DB::transaction(function (): array {
            if ($this->alreadySeeded()) {
                return ['status' => 'skipped'];
            }

            $users = $this->createUsers();
            $departments = $this->createDepartments();
            $employees = $this->createEmployees($departments);
            $devices = $this->createDevices();

            $this->createAssignments($users, $employees, $devices);
            $this->createMaintenance($users, $devices);
            $this->createAuditLogs($users);
            $this->createNotifications($users);
            $this->createPreferences($users);

            return [
                'status' => 'seeded',
                'users' => $users->count(),
                'departments' => $departments->count(),
                'employees' => $employees->count(),
                'devices' => $devices->count(),
                'assignments' => DeviceAssignment::whereIn('device_id', $devices->pluck('id'))->count(),
                'maintenance' => MaintenanceRecord::whereIn('device_id', $devices->pluck('id'))->count(),
                'audits' => AuditLog::whereIn('user_id', $users->pluck('id'))->count(),
                'notifications' => DB::table('notifications')->where('notifiable_type', User::class)->whereIn('notifiable_id', $users->pluck('id'))->count(),
            ];
        });
    }

    public function remove(): array
    {
        return DB::transaction(function (): array {
            $userIds = User::where('email', 'like', '%' . self::USER_MARKER)->pluck('id');
            $departmentIds = Department::where('code', 'like', self::DEPARTMENT_MARKER . '%')->pluck('id');
            $employeeIds = Employee::where('employee_code', 'like', self::EMPLOYEE_MARKER . '%')->pluck('id');
            $deviceIds = Device::where('asset_tag', 'like', self::DEVICE_MARKER . '%')->pluck('id');

            DB::table('notifications')->where('notifiable_type', User::class)->whereIn('notifiable_id', $userIds)->delete();
            AuditLog::whereIn('user_id', $userIds)->delete();
            DeviceAssignment::whereIn('device_id', $deviceIds)->orWhereIn('employee_id', $employeeIds)->delete();
            MaintenanceRecord::whereIn('device_id', $deviceIds)->delete();
            UserPreference::whereIn('user_id', $userIds)->delete();
            Employee::whereIn('id', $employeeIds)->delete();
            Device::whereIn('id', $deviceIds)->delete();
            Department::whereIn('id', $departmentIds)->delete();
            User::whereIn('id', $userIds)->delete();

            return [
                'users' => $userIds->count(),
                'departments' => $departmentIds->count(),
                'employees' => $employeeIds->count(),
                'devices' => $deviceIds->count(),
            ];
        });
    }

    private function alreadySeeded(): bool
    {
        return User::where('email', 'like', '%' . self::USER_MARKER)->exists()
            || Department::where('code', 'like', self::DEPARTMENT_MARKER . '%')->exists()
            || Device::where('asset_tag', 'like', self::DEVICE_MARKER . '%')->exists();
    }

    private function createUsers()
    {
        $definitions = [
            ['name' => 'Maya Perera', 'role' => 'admin'],
            ['name' => 'Daniel Fernando', 'role' => 'manager'],
            ['name' => 'Nisha Wijesinghe', 'role' => 'manager'],
            ['name' => 'Ravi Jayawardena', 'role' => 'staff'],
        ];

        return collect($definitions)->map(fn (array $definition, int $index) => User::create([
            'name' => $definition['name'],
            'email' => 'demo.user' . ($index + 1) . self::USER_MARKER,
            'password' => Hash::make('AssetFlowDemo!2026'),
            'role' => $definition['role'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]));
    }

    private function createDepartments()
    {
        $names = [
            ['Technology', 'Technology and infrastructure'],
            ['Finance', 'Finance and accounting'],
            ['Human Resources', 'People operations'],
            ['Operations', 'Daily business operations'],
            ['Marketing', 'Brand and communications'],
            ['Customer Success', 'Customer support and success'],
        ];

        return collect($names)->map(fn (array $department, int $index) => Department::create([
            'name' => 'Demo ' . $department[0],
            'code' => self::DEPARTMENT_MARKER . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'description' => $department[1] . ' demo department.',
            'is_active' => true,
        ]));
    }

    private function createEmployees($departments)
    {
        $names = [
            ['Amaya', 'Silva', 'Software Engineer'], ['Kasun', 'Perera', 'Systems Analyst'],
            ['Liam', 'Fernando', 'Support Engineer'], ['Sana', 'Wijeratne', 'Product Manager'],
            ['Dilan', 'Rajapaksha', 'Finance Executive'], ['Mihiri', 'Senanayake', 'Accountant'],
            ['Noah', 'Dias', 'HR Coordinator'], ['Tharushi', 'Gunawardena', 'People Partner'],
            ['Ethan', 'De Alwis', 'Operations Lead'], ['Ishara', 'Bandara', 'Operations Executive'],
            ['Aiden', 'Peiris', 'Marketing Manager'], ['Nethmi', 'Herath', 'Content Specialist'],
            ['Oliver', 'Peris', 'Customer Success Lead'], ['Dinuka', 'Maduranga', 'Support Specialist'],
            ['Ayesha', 'Karunaratne', 'QA Engineer'], ['Ruwan', 'Kumara', 'DevOps Engineer'],
            ['Zara', 'Mendis', 'UX Designer'], ['Sahan', 'Wickramasinghe', 'Data Analyst'],
            ['Emma', 'Abeysekara', 'Recruiter'], ['Janith', 'Ekanayake', 'Procurement Officer'],
            ['Maya', 'Rathnayake', 'Business Analyst'], ['Kavindu', 'Ariyaratne', 'Network Engineer'],
            ['Sithum', 'Liyanage', 'Sales Executive'], ['Hiruni', 'Amarasinghe', 'Account Manager'],
            ['Leo', 'Hettiarachchi', 'Frontend Developer'], ['Piumi', 'Samarasinghe', 'Office Manager'],
            ['Theo', 'Nawaratne', 'Security Analyst'], ['Anuki', 'Weerasinghe', 'Project Coordinator'],
            ['Arjun', 'Balasubramaniam', 'IT Technician'], ['Chamara', 'Dissanayake', 'Facilities Officer'],
        ];

        return collect($names)->map(fn (array $employee, int $index) => Employee::create([
            'employee_code' => self::EMPLOYEE_MARKER . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            'first_name' => $employee[0],
            'last_name' => $employee[1],
            'email' => 'demo.employee' . ($index + 1) . self::USER_MARKER,
            'phone' => '+94 11 555 ' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            'job_title' => $employee[2],
            'department_id' => $departments[$index % $departments->count()]->id,
            'is_active' => $index !== 28,
        ]));
    }

    private function createDevices()
    {
        $catalog = [
            ['Laptop', 'Dell', 'Latitude 5440'],
            ['Laptop', 'Lenovo', 'ThinkPad T14'],
            ['Desktop', 'HP', 'ProDesk 600'],
            ['Monitor', 'LG', 'UltraFine 27'],
            ['Phone', 'Apple', 'iPhone 15'],
            ['Tablet', 'Samsung', 'Galaxy Tab S9'],
            ['Network', 'Cisco', 'Meraki MR46'],
            ['Printer', 'Brother', 'HL-L5210'],
        ];
        $statuses = array_merge(array_fill(0, 12, 'assigned'), array_fill(0, 8, 'available'), array_fill(0, 12, 'maintenance'), array_fill(0, 6, 'retired'), array_fill(0, 12, 'available'));
        $conditions = ['excellent', 'good', 'good', 'fair', 'poor'];

        return collect(range(1, 50))->map(function (int $number) use ($catalog, $statuses, $conditions) {
            [$category, $manufacturer, $model] = $catalog[($number - 1) % count($catalog)];
            $status = $statuses[$number - 1];

            return Device::create([
                'name' => 'Demo ' . $manufacturer . ' ' . $category . ' ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'category' => $category,
                'manufacturer' => $manufacturer,
                'model' => $model,
                'serial_number' => 'DEMO-SN-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                'asset_tag' => self::DEVICE_MARKER . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'status' => $status,
                'condition' => $conditions[($number - 1) % count($conditions)],
                'purchase_date' => now()->subMonths(3 + $number)->toDateString(),
                'warranty_expiry' => $number % 5 === 0 ? now()->subDays(15)->toDateString() : now()->addMonths(6 + ($number % 18))->toDateString(),
                'notes' => $number % 5 === 0 ? 'Demo asset with an expired warranty scenario.' : 'Fictional AssetFlow demonstration device.',
            ]);
        });
    }

    private function createAssignments($users, $employees, $devices): void
    {
        collect(range(1, 20))->each(function (int $number) use ($users, $employees, $devices): void {
            $active = $number <= 12;
            DeviceAssignment::create([
                'device_id' => $devices[$number - 1]->id,
                'employee_id' => $employees[($number - 1) % $employees->count()]->id,
                'assigned_by' => $users[($number - 1) % $users->count()]->id,
                'assigned_at' => now()->subDays(45 - $number),
                'returned_at' => $active ? null : now()->subDays(5 + $number),
                'assignment_notes' => 'Demo assignment for AssetFlow workflow testing.',
                'return_notes' => $active ? null : 'Demo device returned after project completion.',
            ]);
        });
    }

    private function createMaintenance($users, $devices): void
    {
        collect(range(1, 12))->each(function (int $number) use ($users, $devices): void {
            $status = ['open', 'in_progress', 'completed', 'cancelled'][$number % 4];
            MaintenanceRecord::create([
                'device_id' => $devices[20 + $number - 1]->id,
                'reported_by' => $users[($number - 1) % $users->count()]->id,
                'title' => 'Demo ' . ['Battery replacement', 'Preventive inspection', 'Display repair', 'Network diagnostics'][$number % 4],
                'description' => 'Fictional maintenance record for dashboard and workflow testing.',
                'type' => ['repair', 'preventive', 'inspection'][$number % 3],
                'priority' => ['low', 'medium', 'high', 'critical'][$number % 4],
                'status' => $status,
                'scheduled_date' => now()->addDays($number)->toDateString(),
                'completed_date' => $status === 'completed' ? now()->subDays($number)->toDateString() : null,
                'cost' => 75 + ($number * 42.50),
                'resolution' => $status === 'completed' ? 'Demo maintenance completed successfully.' : null,
            ]);
        });
    }

    private function createAuditLogs($users): void
    {
        collect(range(1, 36))->each(function (int $number) use ($users): void {
            AuditLog::create([
                'user_id' => $users[($number - 1) % $users->count()]->id,
                'action' => ['create', 'update', 'assign', 'return', 'login'][$number % 5],
                'method' => $number % 5 === 4 ? 'POST' : 'PUT',
                'route' => 'demo.assetflow',
                'description' => 'Demo audit event ' . $number . ' for reporting validation.',
                'ip_address' => '192.0.2.' . (($number % 40) + 1),
                'user_agent' => 'AssetFlow Demo Seeder',
                'response_status' => 200,
                'created_at' => now()->subHours($number),
                'updated_at' => now()->subHours($number),
            ]);
        });
    }

    private function createNotifications($users): void
    {
        collect(range(1, 18))->each(function (int $number) use ($users): void {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\AssetAlertNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $users[($number - 1) % $users->count()]->id,
                'data' => json_encode(['subject' => 'Demo asset alert', 'message' => 'Demo notification ' . $number . ' for AssetFlow testing.', 'demo_marker' => true], JSON_THROW_ON_ERROR),
                'read_at' => $number % 3 === 0 ? now()->subDays(1) : null,
                'created_at' => now()->subHours($number),
                'updated_at' => now()->subHours($number),
            ]);
        });
    }

    private function createPreferences($users): void
    {
        $users->each(fn (User $user) => UserPreference::create([
            'user_id' => $user->id,
            'theme' => 'light',
            'accent_color' => '#536df0',
            'timezone' => 'Asia/Colombo',
            'currency' => 'LKR',
            'asset_tag_prefix' => self::DEVICE_MARKER,
        ]));
    }
}
