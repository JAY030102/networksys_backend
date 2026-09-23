<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    protected array $allowedRoles = ['admin', 'staff', 'superadmin']; // adjust to your actual roles

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile_number' => 'required|string|max:255|unique:users,mobile_number',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'role' => ['required', Rule::in($this->allowedRoles)],
            'password' => 'required|string|min:8|max:72|confirmed',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'username' => $validated['username'],
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'birthdate' => $validated['birthdate'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',        // requires superadmin approval
            'account_status' => 'active',
        ]);

        return response()->json([
            'message' => 'Registration submitted. Your account is pending approval from an administrator.',
            'user' => $user,
        ], 201);
    }
}
