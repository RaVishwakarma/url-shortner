<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        abort_unless($user instanceof User, 401);

        if ($user->role === 'super_admin') {
            $urls = ShortUrl::with(['company', 'user'])->latest()->get();
        } elseif ($user->role === 'admin') {
            $urls = ShortUrl::with(['company', 'user'])
                ->where('company_id', $user->company_id)
                ->latest()
                ->get();
        } else {
            $urls = ShortUrl::with(['company', 'user'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('dashboard', compact('urls'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        if ($user->role === 'super_admin') {
            abort(403, 'Super Admin cannot create short URLs.');
        }

        $request->validate([
            'original_url' => 'required|url:http,https|max:2048',
        ]);

        abort_if($user->company_id === null, 422, 'A user must belong to a company.');

        do {
            $shortCode = Str::random(8);
        } while (ShortUrl::where('short_code', $shortCode)->exists());

        ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $request->string('original_url'),
            'short_code' => $shortCode,
        ]);

        return to_route('dashboard')->with('success', 'Short URL created successfully.');
    }

    public function redirect(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();

        return redirect($shortUrl->original_url);
    }
}
