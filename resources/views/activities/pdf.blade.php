<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - {{ $activity->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5pt;
            color: #1a1a1a;
            line-height: 1.6;
        }

        @if (!empty($isPreview))
            /* Browser Screen Preview Mode */
            @page {
                size: A4 portrait;
                margin: 15mm 18mm;
            }

            body {
                background-color: #f1f5f9;
                padding: 0;
                margin: 0;
            }

            /* Action bar for browser preview */
            .preview-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 210mm;
                margin: 20px auto 15px auto;
                padding: 12px 20px;
                background: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                font-family: system-ui, -apple-system, sans-serif;
            }

            .preview-actions-title {
                font-weight: 600;
                font-size: 15px;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .preview-btn-group {
                display: flex;
                gap: 10px;
            }

            .preview-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 14px;
                font-size: 13px;
                font-weight: 500;
                border-radius: 6px;
                text-decoration: none;
                cursor: pointer;
                border: 1px solid #cbd5e1;
                background: #ffffff;
                color: #334155;
                transition: all 0.15s ease;
            }

            .preview-btn:hover {
                background: #f8fafc;
                border-color: #94a3b8;
                color: #0f172a;
            }

            .preview-btn-primary {
                background: #2563eb;
                color: #ffffff;
                border-color: #2563eb;
            }

            .preview-btn-primary:hover {
                background: #1d4ed8;
                border-color: #1d4ed8;
                color: #ffffff;
            }

            /* A4 Sheet Container */
            .a4-page {
                background: #ffffff;
                width: 210mm;
                min-height: 297mm;
                padding: 18mm 18mm 22mm 18mm;
                margin: 0 auto 40px auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 1px 4px rgba(0, 0, 0, 0.06);
                border-radius: 2px;
                position: relative;
            }
        @else
            /* DomPDF Export Mode */
            @page {
                size: A4 portrait;
                margin: 15mm 18mm 20mm 18mm;
            }

            body {
                background-color: #ffffff;
                padding: 15mm 18mm 20mm 18mm;
                margin: 0;
            }
        @endif

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 22px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 5px 4px;
            vertical-align: top;
            font-size: 10pt;
        }

        .meta-table .label {
            width: 150px;
            font-weight: 700;
            color: #222;
        }

        .meta-table .separator {
            width: 15px;
            text-align: center;
            font-weight: 700;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: 700;
            color: #111;
            margin-bottom: 4px;
        }

        .section-subtitle {
            font-size: 9pt;
            font-style: italic;
            color: #555;
            margin-bottom: 6px;
        }

        .section-content {
            padding: 6px 10px;
            font-size: 10pt;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
            min-height: 24px;
            border-bottom: 1px dotted #ccc;
        }

        .attachments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .attachments-table td {
            width: 50%;
            vertical-align: top;
            padding: 8px;
        }

        .attachment-item {
            text-align: center;
        }

        .attachment-item img {
            max-width: 100%;
            max-height: 220px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 3px;
            display: block;
            margin: 0 auto;
        }

        .attachment-caption {
            font-size: 9pt;
            color: #444;
            margin-top: 6px;
            text-align: center;
            font-weight: 500;
        }

        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-title {
            font-size: 10pt;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
        }

        .signature-company {
            font-size: 10.5pt;
            font-weight: 700;
            color: #111;
            margin-bottom: 10px;
        }

        .signature-space {
            height: 60px;
        }

        .signature-line {
            border-bottom: 1px dashed #444;
            width: 320px;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 9.5pt;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }

        .signature-date {
            font-size: 9.5pt;
            color: #333;
        }

        .footer {
            clear: both;
            text-align: center;
            font-size: 8pt;
            color: #888;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        /* Print Specific Reset for Browser Print */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 18mm 20mm 18mm;
            }

            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .preview-actions {
                display: none !important;
            }

            .a4-page {
                width: 100% !important;
                min-height: auto !important;
                padding: 15mm 18mm 20mm 18mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>

<body>

    @if (!empty($isPreview))
        {{-- Browser Preview Actions Bar --}}
        <div class="preview-actions">
            <div class="preview-actions-title">
                📄 Pratinjau Laporan A4
            </div>
            <div class="preview-btn-group">
                <a href="{{ route('activities.show', $activity) }}" class="preview-btn">
                    &larr; Kembali
                </a>
                <button onclick="window.print()" class="preview-btn">
                    🖨️ Cetak
                </button>
                <a href="{{ route('activities.pdf', $activity) }}" class="preview-btn preview-btn-primary">
                    📥 Muat Turun PDF
                </a>
            </div>
        </div>

        {{-- A4 Page Sheet Wrapper --}}
        <div class="a4-page">
    @endif

    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN HARIAN LI</h1>
    </div>

    {{-- Meta Info --}}
    <table class="meta-table">
        <tr>
            <td class="label">Tarikh</td>
            <td class="separator">:</td>
            <td style="width: 30%;">{{ $activity->date?->format('d/m/Y') ?? '-' }}</td>
            <td class="label" style="width: 60px; text-align: right;">Hari</td>
            <td class="separator">:</td>
            <td>{{ $dayName }}</td>
        </tr>
        <tr>
            <td class="label">Tempat / Lokasi</td>
            <td class="separator">:</td>
            <td colspan="4">
                {{ $activity->company->name ?? '-' }}{{ $activity->additional_location ? ' (' . $activity->additional_location . ')' : '' }}
            </td>
        </tr>
        <tr>
            <td class="label">Tajuk Kerja / Projek</td>
            <td class="separator">:</td>
            <td colspan="4">{{ $activity->title }}</td>
        </tr>
    </table>

    {{-- 1. Peralatan / Perisian / Dokumen yang digunakan --}}
    <div class="section">
        <div class="section-title">Peralatan / Perisian / Dokumen yang digunakan :</div>
        <div class="section-content">{{ $activity->tools ?: '-' }}</div>
    </div>

    {{-- 2. Pengujian yang dijalankan (sekiranya ada) --}}
    <div class="section">
        <div class="section-title">Pengujian yang dijalankan (sekiranya ada) :</div>
        <div class="section-content">{{ $activity->tests ?: '-' }}</div>
    </div>

    {{-- 3. Langkah-langkah Keselamatan (sekiranya ada) --}}
    <div class="section">
        <div class="section-title">Langkah-langkah Keselamatan (sekiranya ada) :</div>
        <div class="section-content">{{ $activity->rules ?: '-' }}</div>
    </div>

    {{-- 4. Perincian Kerja / Projek --}}
    <div class="section">
        <div class="section-title">Perincian Kerja / Projek :</div>
        <div class="section-subtitle">(Langkah kerja, pengiraan, carta / jadual dan gambar rajah yang bersesuaian
            perlu
            disertakan)</div>
        <div class="section-content">{{ $activity->descriptions ?: '-' }}</div>
    </div>

    {{-- 5. Lampiran Perincian Kerja / Projek (sekiranya ada) --}}
    @if ($activity->attachments->count())
        <div class="section">
            <div class="section-title">Lampiran Perincian Kerja / Projek (sekiranya ada) :</div>
            @php
                $attachments = $activity->attachments->toArray();
                $chunks = array_chunk($attachments, 2);
            @endphp
            <table class="attachments-table">
                @foreach ($chunks as $chunk)
                    <tr>
                        @foreach ($chunk as $attData)
                            <td>
                                <div class="attachment-item">
                                    @php
                                        $imagePath = public_path('storage/' . $attData['image_url']);
                                        $imageData = file_exists($imagePath)
                                            ? 'data:image/' .
                                                pathinfo($imagePath, PATHINFO_EXTENSION) .
                                                ';base64,' .
                                                base64_encode(file_get_contents($imagePath))
                                            : null;
                                    @endphp
                                    @if ($imageData)
                                        <img src="{{ $imageData }}"
                                            alt="{{ $attData['caption'] ?? 'Attachment' }}">
                                    @endif
                                    <div class="attachment-caption">
                                        {{ $attData['caption'] ?: 'Tiada keterangan' }}
                                    </div>
                                </div>
                            </td>
                        @endforeach
                        {{-- Fill empty cell if odd count --}}
                        @if (count($chunk) < 2)
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    {{-- 6. Ulasan Penyelia Syarikat --}}
    <div class="section">
        <div class="section-title">Ulasan Penyelia Syarikat :</div>
        <div class="section-content">{{ $activity->ulasan ?: '-' }}</div>
    </div>

    {{-- 7. Signature Section --}}
    <div class="signature-section">
        <div class="signature-title">Disemak Oleh:</div>
        <div class="signature-company">{{ $activity->company->name ?? '-' }}</div>
        <div class="signature-space"></div>
        <div class="signature-line"></div>
        <div class="signature-label">Tandatangan, nama &amp; Cop Penyelia Syarikat</div>
        <div class="signature-date">Tarikh : ...................................</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Dijana secara automatik oleh ActivityHub &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

    @if (!empty($isPreview))
        </div>
    @endif

</body>

</html>
