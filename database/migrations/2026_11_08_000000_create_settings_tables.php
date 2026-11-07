<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('email');
        });

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('warranty_alerts')->default(true);
            $table->boolean('maintenance_reminders')->default(true);
            $table->boolean('assignment_notifications')->default(true);
            $table->boolean('return_notifications')->default(true);
            $table->string('theme', 20)->default('light');
            $table->string('accent_color', 7)->default('#536df0');
            $table->boolean('sidebar_collapsed')->default(false);
            $table->string('date_format', 20)->default('Y-m-d');
            $table->string('timezone', 100)->default('UTC');
            $table->char('currency', 3)->default('USD');
            $table->string('asset_tag_prefix', 20)->default('AST-');
            $table->timestamps();
        });

        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name')->default('AssetFlow');
            $table->string('logo_path')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('date_format', 20)->default('Y-m-d');
            $table->string('timezone', 100)->default('UTC');
            $table->char('currency', 3)->default('USD');
            $table->string('asset_tag_prefix', 20)->default('AST-');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_settings');
        Schema::dropIfExists('user_preferences');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });
    }
};
