<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
}