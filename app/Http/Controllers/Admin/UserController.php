<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('company')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn($q) => $q->where('company_status', $request->status))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $companies = Company::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'companies'));
    }

    public function approve(User $user)
    {
        $user->update([
            'company_status'    => 'accept',
            'company_accept_at' => now(),
            'company_accept_by' => auth()->id(),
        ]);

        return back()->with('success', "User {$user->name} approved.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['company_reject_reason' => ['required', 'string', 'max:65535']]);

        $user->update([
            'company_status'        => 'reject',
            'company_reject_at'     => now(),
            'company_reject_by'     => auth()->id(),
            'company_reject_reason' => $request->company_reject_reason,
        ]);

        return back()->with('success', "User {$user->name} rejected.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,dosen,user']);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role updated to {$request->role}.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'max:255', 'in:admin,dosen,user'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'company_status' => ['required', 'string', 'max:255', 'in:pending,accept,reject'],
            'company_reject_reason' => ['nullable', 'string', 'max:65535'],
        ]);

        $updateData = [
            'role' => $validated['role'],
            'company_id' => $validated['company_id'],
            'company_status' => $validated['company_status'],
        ];

        // Handle status-specific fields
        if ($validated['company_status'] === 'reject') {
            $updateData['company_reject_reason'] = $validated['company_reject_reason'] ?? null;
            $updateData['company_reject_at'] = now();
            $updateData['company_reject_by'] = auth()->id();
            $updateData['company_accept_at'] = null;
            $updateData['company_accept_by'] = null;
        } elseif ($validated['company_status'] === 'accept') {
            $updateData['company_accept_at'] = now();
            $updateData['company_accept_by'] = auth()->id();
            $updateData['company_reject_reason'] = null;
            $updateData['company_reject_at'] = null;
            $updateData['company_reject_by'] = null;
        } else {
            // pending - clear both
            $updateData['company_reject_reason'] = null;
            $updateData['company_reject_at'] = null;
            $updateData['company_reject_by'] = null;
            $updateData['company_accept_at'] = null;
            $updateData['company_accept_by'] = null;
        }

        $user->update($updateData);

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
