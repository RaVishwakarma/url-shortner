<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,member',
            'company_name' => $user->role === 'super_admin'
                ? 'required|string|max:255'
                : 'nullable|string|max:255',
        ]);

        if ($user->role === 'super_admin' && $validated['role'] !== 'admin') {
            abort(403, 'Super Admin can only invite an Admin.');
        }

        $invitation = DB::transaction(function () use ($user, $validated): Invitation {
            $company = $user->role === 'super_admin'
                ? Company::create(['name' => $validated['company_name']])
                : Company::find($user->company_id);

            abort_unless($company instanceof Company, 422, 'An Admin must belong to a company.');

            Invitation::where('email', $validated['email'])->delete();

            return Invitation::create([
                'company_id' => $company->id,
                'email' => $validated['email'],
                'role' => $user->role === 'super_admin' ? 'admin' : $validated['role'],
                'token' => Str::random(40),
                'expires_at' => now()->addDays(7),
            ]);
        });

        Notification::route('mail', $invitation->email)
            ->notify(new InvitationCreated($invitation));

        return back()
            ->with('success', 'Invitation created successfully.')
            ->with('invitation_url', URL::route('invitations.accept', $invitation->token));
    }

    public function accept(string $token): View
    {
        $invitation = $this->validInvitation($token);

        return view('auth.accept-invite', compact('invitation'));
    }

    public function register(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->validInvitation($token);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($invitation, $validated): void {
            User::create([
                'name' => $validated['name'],
                'email' => $invitation->email,
                'password' => $validated['password'],
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]);

            $invitation->delete();
        });

        return to_route('login')
            ->with('success', 'Account created. Please log in.');
    }

    private function validInvitation(string $token): Invitation
    {
        return Invitation::query()
            ->where('token', $token)
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
    }
}
