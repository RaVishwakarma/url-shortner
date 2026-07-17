<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $urls = ShortUrl::all();
        } elseif ($user->role === 'admin') {
            $urls = ShortUrl::where('company_id', $user->company_id)->get();
        } else {
            $urls = ShortUrl::where('user_id', $user->id)->get();
        }

        return response()->json($urls);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            abort(403, 'Super Admin cannot create short URLs.');
        }

        $request->validate([
            'original_url' => 'required|url',
        ]);

        $shortUrl = ShortUrl::create([
            'company_id'  => $user->company_id,
            'user_id'     => $user->id,
            'original_url'=> $request->original_url,
            'short_code'  => Str::random(6),
        ]);

        return response()->json($shortUrl, 201);
    }

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();

        return redirect($shortUrl->original_url);
    }
}