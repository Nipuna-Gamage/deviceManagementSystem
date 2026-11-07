<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'manufacturer', 'model', 'serial_number',
        'asset_tag', 'status', 'condition', 'purchase_date',
        'warranty_expiry', 'notes',
    ];

    public function assignments(): HasMany { return $this->hasMany(DeviceAssignment::class); }

    public function activeAssignment() { return $this->hasOne(DeviceAssignment::class)->whereNull('returned_at')->latestOfMany(); }

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
        ];
    }
}
