<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    protected $fillable = [
        'organization_name',
        'logo_path',
        'contact_email',
        'contact_phone',
        'address',
        'date_format',
        'timezone',
        'currency',
        'asset_tag_prefix',
    ];
}
