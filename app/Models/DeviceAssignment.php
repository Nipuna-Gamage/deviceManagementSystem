<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'employee_id', 'assigned_by', 'assigned_at', 'returned_at', 'assignment_notes', 'return_notes'];

    protected function casts(): array { return ['assigned_at' => 'datetime', 'returned_at' => 'datetime']; }

    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function getIsActiveAttribute(): bool { return $this->returned_at === null; }
}
