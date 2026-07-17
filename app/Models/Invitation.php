<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'company_id',
        'email',
        'role',
        'token',
        'expires_at'
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}