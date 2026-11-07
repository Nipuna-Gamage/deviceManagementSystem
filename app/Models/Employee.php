<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['employee_code', 'first_name', 'last_name', 'email', 'phone', 'job_title', 'department_id', 'is_active'];

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function assignments(): HasMany { return $this->hasMany(DeviceAssignment::class); }
    public function getFullNameAttribute(): string { return trim($this->first_name . ' ' . $this->last_name); }
}
