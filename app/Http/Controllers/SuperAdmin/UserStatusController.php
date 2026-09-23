<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ArchivedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatusController extends Controller
{

public function activeUsers(Request $request)
{
    $this->authorizeSuperadmin($request);

    return response()->json(
        User::where('status', 'approved')
            ->with('approvedBy:id,name')
            ->orderBy('name')
            ->get()
    );
}

    public function suspend(Request $request, User $user)
{
    $this->authorizeSuperadmin($request);

    $validated = $request->validate([
        'reason' => 'required|string',
    ]);

    $user->update([
        'account_status' => 'suspended',
        'suspended_at' => now(),
        'suspended_by' => $request->user()->id,
        'suspension_reason' => $validated['reason'],
    ]);

    return response()->json(['message' => 'User suspended.', 'user' => $user->fresh()]);
}

public function reactivate(Request $request, User $user)
{
    $this->authorizeSuperadmin($request);

    $user->update([
        'account_status' => 'active',
        'suspended_at' => null,
        'suspended_by' => null,
        'approved_by' => $request->user()->id,
        'approved_at' => now(),
        'suspension_reason' => null,
    ]);

    return response()->json(['message' => 'User reactivated.', 'user' => $user->fresh()]);
}

    public function terminate(Request $request, User $user)
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
            ArchivedUser::create([
                'original_user_id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'birthdate' => $user->birthdate,
                'gender' => $user->gender,
                'address' => $user->address,
                'role' => $user->role,
                'archive_type' => 'terminated',
                'reason' => $validated['reason'],
                'actioned_by' => $request->user()->id,
                'actioned_at' => now(),
            ]);

            $user->delete();
        });

        return response()->json(['message' => 'User terminated and archived. They may register again.']);
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        return response()->json(
            ArchivedUser::with('actionedBy')->orderByDesc('actioned_at')->get()
        );
    }

    protected function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'superadmin', 403, 'Unauthorized.');
    }
}
