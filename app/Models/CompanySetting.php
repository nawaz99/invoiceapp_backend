<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'gstin',
        'email',
        'logo_path',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
