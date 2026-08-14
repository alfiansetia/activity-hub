<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $companies = Company::orderBy('name')->get();

        $user = $request->user()->load(['companyAcceptBy', 'companyRejectBy']);

        return view('profile.edit', [
            'user' => $user,
            'companies' => $companies,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function requestCompany(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        // Validate: cannot select the same company
        if ($user->company_id && $user->company_id == $validated['company_id']) {
            return back()->with('error', 'You are already a member of this company. Please select a different company.');
        }

        $user->update([
            'company_id' => $validated['company_id'],
            'company_status' => 'pending',
            'company_reject_reason' => null,
            'company_reject_at' => null,
            'company_reject_by' => null,
            'company_accept_at' => null,
            'company_accept_by' => null,
        ]);

        if ($user->wasChanged('company_id') && $user->getOriginal('company_id')) {
            return back()->with('success', 'Company change request submitted. Please wait for admin approval.');
        }

        return back()->with('success', 'Company join request submitted. Please wait for admin approval.');
    }
}
