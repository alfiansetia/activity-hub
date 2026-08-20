@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'Activities', 'url' => route('activities.index'), 'icon' => 'bi bi-calendar-check'],
            ['label' => Str::limit($activity->title, 30), 'url' => route('activities.show', $activity)],
            ['label' => 'Edit Activity'],
        ],
    ])

    @if ($activity->is_reject)
        <span class="badge bg-danger mb-3">Previously Rejected</span>
    @endif

    @if ($activity->is_reject && $activity->reject_reason)
        <div class="alert alert-danger border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Rejection Reason:</strong> {{ $activity->reject_reason }}
        </div>
    @endif

    <form method="POST" action="{{ route('activities.update', $activity) }}" id="activityForm">
        @csrf
        @method('PUT')

        {{-- Main Info Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2 text-primary"></i> Activity Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Title --}}
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold">Tajuk Kerja / Projek <span
                                class="text-danger">*</span></label>
                        <input type="text" id="title" name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $activity->title) }}" placeholder="Masukkan tajuk kerja / projek"
                            required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Company --}}
                    @if (auth()->user()->is_admin)
                        <div class="col-md-6">
                            <label for="company_id" class="form-label fw-semibold">Company / Syarikat <span
                                    class="text-danger">*</span></label>
                            <select id="company_id" name="company_id"
                                class="form-select @error('company_id') is-invalid @enderror" required>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id', $activity->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempat / Lokasi (Syarikat)</label>
                            <input type="hidden" name="company_id"
                                value="{{ $activity->company_id ?? $defaultCompanyId }}">
                            <input type="text" class="form-control"
                                value="{{ $activity->company->name ?? $companies->first()?->name }}" readonly disabled>
                            <small class="text-muted">Company is automatically set from your account.</small>
                        </div>
                    @endif

                    {{-- Additional Location --}}
                    <div class="col-md-6">
                        <label for="additional_location" class="form-label fw-semibold">Additional Location / Lokasi
                            Tambahan</label>
                        <input type="text" id="additional_location" name="additional_location"
                            class="form-control @error('additional_location') is-invalid @enderror"
                            value="{{ old('additional_location', $activity->additional_location) }}"
                            placeholder="e.g. site a, Bengkel, Lab, dll.">
                        @error('additional_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tools --}}
                    <div class="col-md-6">
                        <label for="tools" class="form-label fw-semibold">Peralatan / Perisian / Dokumen yang
                            digunakan</label>
                        <textarea id="tools" name="tools" class="form-control @error('tools') is-invalid @enderror" rows="3"
                            placeholder="Senaraikan peralatan / perisian / dokumen yang digunakan...">{{ old('tools', $activity->tools) }}</textarea>
                        @error('tools')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tests --}}
                    <div class="col-md-6">
                        <label for="tests" class="form-label fw-semibold">Pengujian yang dijalankan (sekiranya
                            ada)</label>
                        <textarea id="tests" name="tests" class="form-control @error('tests') is-invalid @enderror" rows="3"
                            placeholder="Perincikan pengujian yang dijalankan...">{{ old('tests', $activity->tests) }}</textarea>
                        @error('tests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Rules --}}
                    <div class="col-md-6">
                        <label for="rules" class="form-label fw-semibold">Langkah-langkah Keselamatan (sekiranya
                            ada)</label>
                        <textarea id="rules" name="rules" class="form-control @error('rules') is-invalid @enderror" rows="3"
                            placeholder="Senaraikan langkah-langkah keselamatan...">{{ old('rules', $activity->rules) }}</textarea>
                        @error('rules')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ulasan --}}
                    <div class="col-md-6">
                        <label for="ulasan" class="form-label fw-semibold">Ulasan Penyelia Syarikat</label>
                        <textarea id="ulasan" name="ulasan" class="form-control @error('ulasan') is-invalid @enderror" rows="3"
                            placeholder="Ulasan penyelia syarikat (sekiranya ada)...">{{ old('ulasan', $activity->ulasan) }}</textarea>
                        @error('ulasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label for="descriptions" class="form-label fw-semibold">Perincian Kerja / Projek</label>
                        <small class="text-muted d-block mb-1" style="font-style: italic;">
                            (Langkah kerja, pengiraan, carta / jadual dan gambar rajah yang bersesuaian perlu disertakan)
                        </small>
                        <textarea id="descriptions" name="descriptions" class="form-control @error('descriptions') is-invalid @enderror"
                            rows="5" placeholder="Perincian langkah kerja / projek...">{{ old('descriptions', $activity->descriptions) }}</textarea>
                        @error('descriptions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Existing Attachments --}}
        @if ($activity->attachments->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-images me-2 text-primary"></i> Existing Attachments</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($activity->attachments as $att)
                            <div class="col-sm-6 col-md-4 col-lg-3" id="existing-att-{{ $att->id }}">
                                <div class="card border-0 shadow-sm h-100 attachment-card position-relative">
                                    <img src="{{ Storage::url($att->image_url) }}" class="attachment-img"
                                        alt="{{ $att->caption }}">
                                    <div class="card-body p-2">
                                        <small class="text-muted d-block mb-1">{{ $att->caption ?: 'No caption' }}</small>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_attachments[]"
                                                value="{{ $att->id }}" id="del-{{ $att->id }}">
                                            <label class="form-check-label small text-danger"
                                                for="del-{{ $att->id }}">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- New Attachments --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i> Add New Images</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" id="cameraBtn">
                        <i class="bi bi-camera-fill me-1"></i> Take Photo
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="addAttachmentBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Image
                    </button>
                </div>
            </div>
            <div class="card-body">
                {{-- Drop Zone --}}
                <div id="dropZone" class="border border-2 border-dashed rounded-3 text-center p-4 mb-3"
                    style="cursor: pointer; transition: all 0.2s;">
                    <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                    <p class="text-muted mb-1 mt-2">Drag & drop images here or click "Add Image"</p>
                    <small class="text-muted">Supports JPG, PNG, GIF, WEBP</small>
                </div>

                <div id="attachmentsContainer" class="row g-3"></div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('activities.show', $activity) }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-send me-1"></i> Update & Resubmit
            </button>
        </div>
    </form>

    {{-- Cropper Modal --}}
    <div class="modal fade" id="cropperModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow overflow-hidden">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title"><i class="bi bi-crop me-2"></i> Crop Image</h6>
                    <button type="button" class="btn-close btn-close-white" id="cropperClose"></button>
                </div>
                <div class="modal-body p-0 bg-dark" style="height: 60vh;">
                    <img id="cropperImage" src="" alt="" style="display: block; max-width: 100%;">
                </div>
                <div class="modal-footer bg-light flex-wrap gap-2">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Aspect ratio">
                        <button type="button" class="btn btn-outline-secondary ratio-btn active" data-ratio="free">
                            <i class="bi bi-aspect-ratio me-1"></i> Free
                        </button>
                        <button type="button" class="btn btn-outline-secondary ratio-btn" data-ratio="1">1:1</button>
                        <button type="button" class="btn btn-outline-secondary ratio-btn"
                            data-ratio="1.333">4:3</button>
                        <button type="button" class="btn btn-outline-secondary ratio-btn"
                            data-ratio="1.778">16:9</button>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="cropperCancel">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="cropperApply">
                            <i class="bi bi-check-lg me-1"></i> Apply Crop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Camera Modal --}}
    <div class="modal fade" id="cameraModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow overflow-hidden">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title"><i class="bi bi-camera-fill me-2"></i> Take Photo</h6>
                    <button type="button" class="btn-close btn-close-white" id="cameraClose"></button>
                </div>
                <div class="modal-body p-0 bg-dark position-relative" style="height: 60vh;">
                    <video id="cameraVideo" autoplay playsinline
                        style="width: 100%; height: 100%; object-fit: cover; display: block;"></video>
                    <canvas id="cameraCanvas" style="display: none;"></canvas>
                    <div id="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-white">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2 mb-0 small">Starting camera...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-light" id="switchCameraBtn" title="Switch Camera">
                        <i class="bi bi-arrow-repeat me-1"></i> Switch Camera
                    </button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="cameraCancel">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="cameraCapture">
                            <i class="bi bi-camera-fill me-1"></i> Capture
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="file" id="imageFileInput" accept="image/*" class="d-none" multiple>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <style>
        #dropZone.drag-over {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .attachment-card {
            overflow: hidden;
            border-radius: 0.75rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .attachment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .attachment-card .attachment-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .attachment-card .btn-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            padding: 0;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .attachment-card:hover .btn-remove {
            opacity: 1;
        }

        .attachment-card .btn-recrop {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            padding: 0;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .attachment-card:hover .btn-recrop {
            opacity: 1;
        }

        #switchCameraBtn {
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff;
        }

        #switchCameraBtn:hover {
            border-color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper = null;
            let currentSlot = null;
            let pendingSrc = null;
            let slotIndex = 0;
            const cropperEl = document.getElementById('cropperModal');
            const cropperModal = new bootstrap.Modal(cropperEl);
            const fileInput = document.getElementById('imageFileInput');
            const container = document.getElementById('attachmentsContainer');
            const dropZone = document.getElementById('dropZone');
            const cropperImg = document.getElementById('cropperImage');

            // Initialize cropper AFTER modal is fully shown (fixes desktop sizing)
            cropperEl.addEventListener('shown.bs.modal', function() {
                if (!pendingSrc) return;
                if (cropper) cropper.destroy();

                cropper = new Cropper(cropperImg, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    ready() {
                        document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove(
                            'active'));
                        document.querySelector('.ratio-btn[data-ratio="free"]').classList.add(
                            'active');
                    }
                });
            });

            // Cleanup on modal hide
            cropperEl.addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                pendingSrc = null;
            });

            // --- Add Image Button ---
            document.getElementById('addAttachmentBtn').addEventListener('click', () => {
                fileInput.value = '';
                fileInput.click();
            });

            // --- Drop Zone ---
            dropZone.addEventListener('click', () => fileInput.click());
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('drag-over');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('drag-over');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
                if (e.dataTransfer.files.length) {
                    handleFile(e.dataTransfer.files[0]);
                }
            });

            // --- File Input ---
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length) {
                    handleFile(e.target.files[0]);
                }
            });

            function handleFile(file) {
                if (!file || !file.type.startsWith('image/')) return;
                currentSlot = slotIndex++;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    pendingSrc = ev.target.result;
                    cropperImg.src = pendingSrc;
                    cropperModal.show();
                };
                reader.readAsDataURL(file);
            }

            // --- Aspect Ratio Buttons ---
            document.querySelectorAll('.ratio-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.ratio-btn').forEach(b => b.classList.remove(
                        'active'));
                    btn.classList.add('active');
                    const ratio = btn.dataset.ratio;
                    if (cropper) {
                        cropper.setAspectRatio(ratio === 'free' ? NaN : parseFloat(ratio));
                    }
                });
            });

            // --- Apply Crop ---
            document.getElementById('cropperApply').addEventListener('click', () => {
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    maxWidth: 1200,
                    maxHeight: 1200,
                });
                const base64 = canvas.toDataURL('image/jpeg', 0.9);

                addAttachmentSlot(currentSlot, base64);
                cropperModal.hide();
            });

            // --- Cancel / Close ---
            ['cropperCancel', 'cropperClose'].forEach(id => {
                document.getElementById(id).addEventListener('click', () => {
                    cropperModal.hide();
                });
            });

            // --- Add Attachment Slot ---
            function addAttachmentSlot(index, base64) {
                const col = document.createElement('div');
                col.className = 'col-sm-6 col-md-4 col-lg-3';
                col.setAttribute('data-slot', index);
                col.innerHTML = `
                    <div class="card attachment-card border-0 shadow-sm h-100">
                        <img src="${base64}" class="attachment-img" alt="Preview">
                        <button type="button" class="btn btn-danger btn-sm btn-remove" title="Remove">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm btn-recrop" title="Re-crop">
                            <i class="bi bi-crop"></i>
                        </button>
                        <div class="card-body p-2">
                            <input type="hidden" name="images[]" value="${base64}">
                            <input type="text" name="captions[]" class="form-control form-control-sm"
                                placeholder="Caption (optional)">
                        </div>
                    </div>
                `;

                container.appendChild(col);

                // Remove
                col.querySelector('.btn-remove').addEventListener('click', () => {
                    col.remove();
                });

                // Re-crop
                col.querySelector('.btn-recrop').addEventListener('click', () => {
                    currentSlot = index;
                    pendingSrc = base64;
                    cropperImg.src = base64;
                    cropperModal.show();
                    col.remove();
                });
            }

            // ===== Camera Capture =====
            const cameraModalEl = document.getElementById('cameraModal');
            const cameraModal = new bootstrap.Modal(cameraModalEl);
            const cameraVideo = document.getElementById('cameraVideo');
            const cameraCanvas = document.getElementById('cameraCanvas');
            const cameraLoading = document.getElementById('cameraLoading');
            let cameraStream = null;
            let facingMode = 'environment'; // Start with rear camera

            function startCamera() {
                // Stop any existing stream
                if (cameraStream) {
                    cameraStream.getTracks().forEach(t => t.stop());
                }
                cameraLoading.style.display = 'block';
                cameraVideo.style.display = 'block';

                // Polyfill navigator.mediaDevices for non-secure contexts
                if (navigator.mediaDevices === undefined) {
                    navigator.mediaDevices = {};
                }
                if (navigator.mediaDevices.getUserMedia === undefined) {
                    navigator.mediaDevices.getUserMedia = function(constraints) {
                        var getUserMedia = navigator.getUserMedia ||
                            navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia;
                        if (!getUserMedia) {
                            return Promise.reject(new Error(
                                'getUserMedia is not supported in this browser or requires HTTPS.'));
                        }
                        return new Promise(function(resolve, reject) {
                            getUserMedia.call(navigator, constraints, resolve, reject);
                        });
                    };
                }

                const constraints = {
                    video: {
                        facingMode: facingMode,
                        width: {
                            ideal: 1920
                        },
                        height: {
                            ideal: 1080
                        }
                    },
                    audio: false
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(stream => {
                        cameraStream = stream;
                        cameraVideo.srcObject = stream;
                        cameraVideo.onloadedmetadata = () => {
                            cameraVideo.play();
                            cameraLoading.style.display = 'none';
                        };
                    })
                    .catch(err => {
                        console.error('Camera error:', err);
                        let msg = 'Cannot access camera. Please allow camera permission.';
                        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location
                            .hostname !== '127.0.0.1') {
                            msg =
                                'Camera requires HTTPS or localhost. Please access this page via <strong>https://</strong> or <strong>localhost</strong>.';
                        }
                        cameraLoading.innerHTML =
                            '<i class="bi bi-camera-video-off fs-1"></i><p class="mt-2 mb-0">' + msg + '</p>';
                    });
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(t => t.stop());
                    cameraStream = null;
                }
                cameraVideo.srcObject = null;
            }

            // Helper: show alert above attachments card
            function showCameraAlert(message) {
                // Remove any existing camera alert
                const existing = document.getElementById('cameraAlert');
                if (existing) existing.remove();

                const alert = document.createElement('div');
                alert.id = 'cameraAlert';
                alert.className = 'alert alert-warning alert-dismissible fade show shadow-sm';
                alert.setAttribute('role', 'alert');
                alert.innerHTML =
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

                // Insert before the new attachments card
                const attachmentsCard = document.getElementById('dropZone').closest('.card');
                attachmentsCard.parentNode.insertBefore(alert, attachmentsCard);

                // Auto-dismiss after 8 seconds
                setTimeout(() => {
                    if (alert.parentNode) alert.remove();
                }, 8000);
            }

            // Open camera modal
            document.getElementById('cameraBtn').addEventListener('click', () => {
                // Check if camera API is available
                var hasMediaDevices = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
                var hasLegacyGetUserMedia = !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
                    navigator.mozGetUserMedia);

                if (!hasMediaDevices && !hasLegacyGetUserMedia) {
                    let msg = 'Camera is not supported in this browser.';
                    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location
                        .hostname !== '127.0.0.1') {
                        msg =
                            'Camera requires <strong>HTTPS</strong> or <strong>localhost</strong>. Please access via https:// or enable SSL in Laragon.';
                    }
                    showCameraAlert(msg);
                    return;
                }

                facingMode = 'environment';
                cameraModal.show();
                startCamera();
            });

            // Switch camera (front / rear)
            document.getElementById('switchCameraBtn').addEventListener('click', () => {
                facingMode = (facingMode === 'environment') ? 'user' : 'environment';
                startCamera();
            });

            // Capture photo
            document.getElementById('cameraCapture').addEventListener('click', () => {
                if (!cameraStream) return;

                const vw = cameraVideo.videoWidth;
                const vh = cameraVideo.videoHeight;
                cameraCanvas.width = vw;
                cameraCanvas.height = vh;
                const ctx = cameraCanvas.getContext('2d');

                // If using front camera, mirror the capture
                if (facingMode === 'user') {
                    ctx.translate(vw, 0);
                    ctx.scale(-1, 1);
                }

                ctx.drawImage(cameraVideo, 0, 0, vw, vh);

                const base64 = cameraCanvas.toDataURL('image/jpeg', 0.92);

                // Stop camera and close camera modal
                stopCamera();
                cameraModal.hide();

                // Open cropper with captured image
                currentSlot = slotIndex++;
                pendingSrc = base64;
                cropperImg.src = base64;
                cropperModal.show();
            });

            // Cancel / Close camera
            ['cameraCancel', 'cameraClose'].forEach(id => {
                document.getElementById(id).addEventListener('click', () => {
                    stopCamera();
                    cameraModal.hide();
                });
            });

            // Ensure camera stops if modal is closed by other means
            cameraModalEl.addEventListener('hidden.bs.modal', () => {
                stopCamera();
            });

            // Stop camera stream when page is about to unload
            window.addEventListener('beforeunload', stopCamera);
        });
    </script>
@endpush
