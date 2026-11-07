<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'assetflow:make-admin {email?}';
    protected $description = 'Create or promote an AssetFlow administrator account';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Administrator email');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A valid email address is required.');
            return self::FAILURE;
        }
        $user = User::where('email', $email)->first();
        $name = $user?->name ?: $this->ask('Administrator name');
        $password = $this->secret('Password (minimum 10 characters)');
        if (! is_string($password) || strlen($password) < 10) {
            $this->error('Password must contain at least 10 characters.');
            return self::FAILURE;
        }
        $user ??= new User();
        $user->fill(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'admin', 'is_active' => true]);
        $user->save();
        $this->info('Administrator account is ready.');
        return self::SUCCESS;
    }
}
