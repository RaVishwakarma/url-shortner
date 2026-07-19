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
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function getValidInvitation($token)
    {
        $invitation = self::query()
            ->join('companies', 'invitations.company_id', '=', 'companies.id')
            ->select([
                'invitations.*',
                'companies.name as company_name',
            ])
            ->where('invitations.token', $token)
            ->first();

        if (empty($invitation)) {
            return null;
        }

        if (! empty($invitation->expires_at) && $invitation->expires_at <= now()) {
            return null;
        }

        return $invitation;
    }
}
