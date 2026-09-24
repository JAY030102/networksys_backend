<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ArchivedUser;

class UserApprovalController extends Controller
{
    public function pending(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        return response()->json(
            User::where('status', 'pending')->orderBy('created_at')->get()
        );
    }

    public function approve(Request $request, User $user)
    {
        $this->authorizeSuperAdmin($request);

        $user->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'User approved.', 'user' => $user]);
    }

    public function reject(Request $request, User $user)
    {
        $this->authorizeSuperadmin($request);

        $validated = $request->validate([
            'reason' => 'nullable|string',
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
                'archive_type' => 'rejected',
                'reason' => $validated['reason'] ?? 'Registration rejected.',
                'actioned_by' => $request->user()->id,
                'actioned_at' => now(),
            ]);

            $user->delete();
        });

        return response()->json(['message' => 'User rejected and archived. They may register again.']);
    }

    protected function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'superadmin', 403, 'Unauthorized.');
    }
}
