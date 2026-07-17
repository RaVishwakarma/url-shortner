<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class InvitationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,member',
            'company_name' => 'nullable|string'
        ]);


        $user = auth()->user();


        /*
         | SuperAdmin flow
         | Create new company and invite Admin
         */

        if ($user->role === 'super_admin') {

            $company = Company::create([
                'name' => $request->company_name
            ]);


            Invitation::create([
                'company_id' => $company->id,
                'email' => $request->email,
                'role' => 'admin',
                'token' => Str::random(40),
            ]);


            return response()->json([
                'message' => 'Admin invitation created'
            ]);
        }



        /*
         | Admin flow
         | Invite inside own company
         */

        if ($user->role === 'admin') {


            Invitation::create([
                'company_id' => $user->company_id,
                'email' => $request->email,
                'role' => $request->role,
                'token' => Str::random(40),
            ]);


            return response()->json([
                'message' => 'Invitation created'
            ]);
        }
        abort(403);
    }

    public function accept($token)
    {
        // dd($token);
        $invitation = Invitation::where('token',$token)->firstOrFail();
        // dd( $invitation);

        return view('auth.accept-invite', compact('invitation'));
    }

    public function register(Request $request, $token)
    {
        $invitation = Invitation::where('token',$token)->firstOrFail();


        $request->validate([
            'name'=>'required',
            'password'=>'required|min:6'
        ]);


        User::create([
            'name'=>$request->name,
            'email'=>$invitation->email,
            'password'=>$request->password,
            'company_id'=>$invitation->company_id,
            'role'=>$invitation->role
        ]);


        $invitation->delete();


        return redirect('/login')
            ->with('success','Account created. Please login.');
    }
}