@extends('layouts.app')

@section('title', 'Manage Users')

@push('styles')
    <style>
        .reject-reason-field {
            display: none;
        }

        .reject-reason-field.show {
            display: block;
        }
    </style>
@endpush

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'User Management'],
        ],
    ])

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search name or email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accept" {{ request('status') === 'accept' ? 'selected' : '' }}>Accepted</option>
                        <option value="reject" {{ request('status') === 'reject' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Role</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; font-size: 0.75rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <div class="d-md-none text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell text-muted small">{{ $user->email }}</td>
                                <td>
                                    @if ($user->is_admin)
                                        <span class="badge bg-primary">Admin</span>
                                    @elseif ($user->is_dosen)
                                        <span class="badge bg-info text-dark">Dosen</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small">{{ $user->company->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if ($user->company_status === 'accept')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($user->company_status === 'reject')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        {{-- Edit Button --}}
                                        <button type="button" class="btn btn-outline-primary btn-sm" title="Edit User"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Approve/Reject --}}
                                        @if ($user->company_status === 'pending')
                                            <form method="POST" action="{{ route('admin.users.approve', $user) }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm"
                                                    title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Reject"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif

                                        {{-- Delete --}}
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Delete this user?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit User Modal --}}
                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header">
                                                <h6 class="modal-title">
                                                    <i class="bi bi-pencil-square me-2"></i>Edit User
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{-- User Info --}}
                                                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; font-size: 0.9rem;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>

                                                {{-- Role --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Role</label>
                                                    <select name="role" class="form-select">
                                                        <option value="user" {{ $user->is_user ? 'selected' : '' }}>User
                                                        </option>
                                                        <option value="dosen" {{ $user->is_dosen ? 'selected' : '' }}>
                                                            Dosen</option>
                                                        <option value="admin" {{ $user->is_admin ? 'selected' : '' }}>
                                                            Admin</option>
                                                    </select>
                                                </div>

                                                {{-- Company --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Company</label>
                                                    <select name="company_id" class="form-select">
                                                        <option value="">No Company</option>
                                                        @foreach ($companies as $company)
                                                            <option value="{{ $company->id }}"
                                                                {{ $user->company_id == $company->id ? 'selected' : '' }}>
                                                                {{ $company->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Company Status --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Company Status</label>
                                                    <select name="company_status" class="form-select"
                                                        onchange="toggleRejectReason{{ $user->id }}(this.value)">
                                                        <option value="pending"
                                                            {{ $user->company_status === 'pending' ? 'selected' : '' }}>
                                                            Pending</option>
                                                        <option value="accept"
                                                            {{ $user->company_status === 'accept' ? 'selected' : '' }}>
                                                            Accepted</option>
                                                        <option value="reject"
                                                            {{ $user->company_status === 'reject' ? 'selected' : '' }}>
                                                            Rejected</option>
                                                    </select>
                                                </div>

                                                {{-- Reject Reason (conditional) --}}
                                                <div class="mb-0 reject-reason-field {{ $user->company_status === 'reject' ? 'show' : '' }}"
                                                    id="rejectReasonField{{ $user->id }}">
                                                    <label class="form-label fw-semibold">
                                                        Rejection Reason
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="company_reject_reason" class="form-control" rows="3"
                                                        placeholder="Explain why this user is being rejected...">{{ $user->company_reject_reason }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-lg me-1"></i> Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                function toggleRejectReason{{ $user->id }}(value) {
                                    const field = document.getElementById('rejectReasonField{{ $user->id }}');
                                    if (value === 'reject') {
                                        field.classList.add('show');
                                    } else {
                                        field.classList.remove('show');
                                    }
                                }
                            </script>

                            {{-- Reject Modal (quick reject from table) --}}
                            @if ($user->company_status === 'pending')
                                <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                                            @csrf
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Reject User: {{ $user->name }}</h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label fw-semibold">Rejection Reason <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="company_reject_reason" class="form-control" rows="3"
                                                        placeholder="Explain why this user is being rejected..." required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-lg me-1"></i> Reject User
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
