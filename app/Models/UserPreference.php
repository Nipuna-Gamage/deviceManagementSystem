<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_notifications',
        'warranty_alerts',
        'maintenance_reminders',
        'assignment_notifications',
        'return_notifications',
        'theme',
        'accent_color',
        'sidebar_collapsed',
        'date_format',
        'timezone',
        'currency',
        'asset_tag_prefix',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'warranty_alerts' => 'boolean',
            'maintenance_reminders' => 'boolean',
            'assignment_notifications' => 'boolean',
            'return_notifications' => 'boolean',
            'sidebar_collapsed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
