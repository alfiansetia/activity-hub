@extends('layouts.app')

@section('title', 'Send Notification')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'Notifications', 'url' => route('admin.notifications.index')],
            ['label' => 'Send Notification'],
        ],
    ])

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-bell me-2"></i>Send Notification to All Users
                    </h6>
                </div>
                <form method="POST" action="{{ route('admin.notifications.store') }}">
                    @csrf
                    <div class="card-body">
                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">
                                Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                placeholder="Enter notification title" required autofocus maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="5"
                                placeholder="Enter notification message" required maxlength="5000">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 5000 characters.</div>
                        </div>

                        {{-- Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                                required>
                                <option value="info" {{ old('type', 'info') === 'info' ? 'selected' : '' }}>
                                    🔵 Info</option>
                                <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>
                                    🟢 Success</option>
                                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>
                                    🟡 Warning</option>
                                <option value="danger" {{ old('type') === 'danger' ? 'selected' : '' }}>
                                    🔴 Danger</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="card bg-light border">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div id="preview-icon"
                                            class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">
                                            <i class="bi bi-info-circle-fill"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-semibold" id="preview-title">Notification title</div>
                                            <div class="text-muted small" id="preview-message">Notification message will
                                                appear here...</div>
                                            <div class="text-muted small mt-1">
                                                <i class="bi bi-clock me-1"></i><span id="preview-time">Just now</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            This notification will be sent to <strong>all users</strong> in the system (except yourself).
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Live preview
            const titleInput = document.getElementById('title');
            const messageInput = document.getElementById('message');
            const typeSelect = document.getElementById('type');
            const previewTitle = document.getElementById('preview-title');
            const previewMessage = document.getElementById('preview-message');
            const previewIcon = document.getElementById('preview-icon');

            const typeConfig = {
                info: {
                    bg: 'bg-info',
                    icon: 'bi-info-circle-fill'
                },
                success: {
                    bg: 'bg-success',
                    icon: 'bi-check-circle-fill'
                },
                warning: {
                    bg: 'bg-warning',
                    icon: 'bi-exclamation-triangle-fill'
                },
                danger: {
                    bg: 'bg-danger',
                    icon: 'bi-x-circle-fill'
                },
            };

            function updatePreview() {
                previewTitle.textContent = titleInput.value || 'Notification title';
                previewMessage.textContent = messageInput.value || 'Notification message will appear here...';

                const config = typeConfig[typeSelect.value] || typeConfig.info;
                previewIcon.className = config.bg +
                    ' text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0';
                previewIcon.style.width = '36px';
                previewIcon.style.height = '36px';
                previewIcon.innerHTML = '<i class="bi ' + config.icon + '"></i>';
            }

            titleInput.addEventListener('input', updatePreview);
            messageInput.addEventListener('input', updatePreview);
            typeSelect.addEventListener('change', updatePreview);
        </script>
    @endpush
@endsection
