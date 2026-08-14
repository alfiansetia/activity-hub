<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::withCount('activities')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:companies']);

        Company::create(['name' => $request->name]);

        return back()->with('success', 'Company created.');
    }

    public function update(Request $request, Company $company)
    {
        $request->validate(['name' => 'required|string|max:255|unique:companies,name,' . $company->id]);

        $company->update(['name' => $request->name]);

        return back()->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        if ($company->activities()->exists()) {
            return back()->with('error', 'Cannot delete company with existing activities.');
        }

        $company->delete();

        return back()->with('success', 'Company deleted.');
    }
}
