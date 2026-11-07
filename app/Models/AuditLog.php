<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'method', 'route', 'description', 'ip_address', 'user_agent', 'response_status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
