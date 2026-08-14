@extends('layouts.app')

@section('title', 'Manage Companies')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'Company Management'],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 fade-up">
        <div>
            <h4 class="fw-semibold mb-1">Company Management</h4>
            <p class="text-muted small mb-0">Create, edit, and manage companies.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
            <i class="bi bi-plus-lg me-1"></i> New Company
        </button>
    </div>

    {{-- Search --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search company name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Companies Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th class="d-none d-sm-table-cell">Activities</th>
                            <th class="d-none d-md-table-cell">Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td class="text-muted">{{ $company->id }}</td>
                                <td class="fw-semibold">{{ $company->name }}</td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $company->activities_count }} activities
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell text-muted small">
                                    {{ $company->created_at?->format('d M Y') }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#editCompanyModal{{ $company->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                                            onsubmit="return confirm('Delete this company?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.companies.update', $company) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title">Edit Company</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label fw-semibold">Company Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $company->name }}" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-building fs-2 d-block mb-2"></i>
                                    No companies found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($companies->hasPages())
            <div class="card-footer bg-white">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.companies.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="bi bi-building me-2"></i> New Company</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="e.g. PT Maju Bersama" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
