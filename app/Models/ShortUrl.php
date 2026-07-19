<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'original_url',
        'short_code',
    ];

    public function getAllShortUrlsData($user)
    {
        $shortUrls = self::query()
            ->join('companies', 'short_urls.company_id', '=', 'companies.id')
            ->join('users', 'short_urls.user_id', '=', 'users.id')
            ->select([
                'short_urls.*',
                'companies.name as company_name',
                'users.name as user_name',
            ]);

        if ($user->role === 'admin') {
            $shortUrls->where('short_urls.company_id', $user->company_id);
        } elseif ($user->role === 'member') {
            $shortUrls->where('short_urls.user_id', $user->id);
        }

        return $shortUrls->orderByDesc('short_urls.created_at');
    }
}
