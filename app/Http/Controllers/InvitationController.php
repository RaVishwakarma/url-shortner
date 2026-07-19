<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Invitation;
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,member',
            'company_name' => $user->role === 'super_admin'
                ? 'required|string|max:255'
                : 'nullable|string|max:255',
        ]);

        if ($user->role === 'super_admin' && $validated['role'] !== 'admin') {
            return response('Super Admin can only invite an Admin.', 403);
        }

        if ($user->role === 'super_admin') {
            $company = Company::create([
                'name' => $validated['company_name'],
            ]);
        } else {
            if (empty($user->company_id)) {
                return response('An Admin must belong to a company.', 422);
            }

            $company = Company::find($user->company_id);
        }

        if (empty($company)) {
            return response('Company not found.', 422);
        }

        Invitation::where('email', $validated['email'])->delete();

        $role = $validated['role'];
        if ($user->role === 'super_admin') {
            $role = 'admin';
        }

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'email' => $validated['email'],
            'role' => $role,
            'token' => Str::random(40),
            'expires_at' => now()->addDays(7),
        ]);

        $invitation->company_name = $company->name;

        Notification::route('mail', $invitation->email)
            ->notify(new InvitationCreated($invitation));

        return back()
            ->with('success', 'Invitation created successfully.')
            ->with('invitation_url', URL::route('invitations.accept', $invitation->token));
    }

    public function accept($token)
    {
        $invitation = $this->model->getValidInvitation($token);

        if (empty($invitation)) {
            return response('Invitation not found.', 404);
        }

        return view('auth.accept-invite', ['invitation' => $invitation]);
    }

    public function register(Request $request, $token)
    {
        $invitation = $this->model->getValidInvitation($token);

        if (empty($invitation)) {
            return response('Invitation not found.', 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|confirmed|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => $validated['password'],
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
        ]);

        Invitation::where('id', $invitation->id)->delete();

        return to_route('login')
            ->with('success', 'Account created. Please log in.');
    }
}
