<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new ShortUrl;
    }

    public function index()
    {
        $user = request()->user();
        $urls = $this->model->getAllShortUrlsData($user)->get();

        return view('dashboard', ['urls' => $urls]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return response('Super Admin cannot create short URLs.', 403);
        }

        $request->validate([
            'original_url' => 'required|url:http,https|max:2048',
        ]);

        if ($user->company_id === null) {
            return response('A user must belong to a company.', 422);
        }

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

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (empty($shortUrl)) {
            return response('Short URL not found.', 404);
        }

        return redirect($shortUrl->original_url);
    }
}
