<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        return [
            'tenant' => tenant(),
            'user' => User::get(),
        ];
    }

    // Create Tenant
    public function createTenant(Request $request)
    {
        $request->validate([
            'companyName' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Create Tenant Database
        $tenant = Tenant::create(['id' => $request->companyName]);

        // Create Tenant Domain
        $tenant->domains()->create([
            'domain' => $request->companyName . '.' .env('APP_DOMAIN'),
        ]);
        $tenant->run(function() use ($request){
            // Create User
            User::create([
                'name' => $request->companyName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        });
        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => $tenant,
        ]);
    }
}
