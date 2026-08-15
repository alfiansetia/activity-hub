<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - {{ $activity->title }}</title>
    <style>
        @page {
            margin: 25mm 20mm 30mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #555;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
            font-size: 10.5pt;
        }

        .meta-table .label {
            width: 120px;
            font-weight: 700;
            color: #333;
        }

        .meta-table .separator {
            width: 20px;
            text-align: center;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #999;
            padding-bottom: 4px;
            margin-bottom: 8px;
            color: #333;
        }

        .section-content {
            padding: 5px 0 5px 10px;
            font-size: 10.5pt;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.7;
        }

        .attachments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .attachments-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 8px;
        }

        .attachment-item img {
            max-width: 100%;
            max-height: 220px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 3px;
            display: block;
            margin: 0 auto;
        }

        .attachment-caption {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 55%;
            float: right;
            text-align: center;
        }

        .signature-label {
            font-size: 10.5pt;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 80%;
            margin: 60px auto 8px auto;
        }

        .signature-name {
            font-size: 10pt;
            font-weight: 600;
        }

        .signature-role {
            font-size: 9pt;
            color: #666;
        }

        .signature-date {
            margin-top: 15px;
            font-size: 10pt;
        }

        .footer {
            clear: both;
            text-align: center;
            font-size: 8pt;
            color: #999;
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-accept {
            background: #dcfce7;
            color: #166534;
        }

        .status-reject {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <h1>Laporan Harian LI</h1>
        <div class="subtitle">{{ $activity->company->name ?? '' }}</div>
    </div>

    {{-- Meta Info --}}
    <table class="meta-table">
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ $activity->date?->format('d/m/Y') ?? '-' }}</td>
            <td class="label" style="text-align: right;">Hari</td>
            <td class="separator">:</td>
            <td>{{ $dayName }}</td>
        </tr>
        <tr>
            <td class="label">Aktiviti</td>
            <td class="separator">:</td>
            <td colspan="4">{{ $activity->title }}</td>
        </tr>
        <tr>
            <td class="label">Disemak oleh</td>
            <td class="separator">:</td>
            <td colspan="4">
                <span class="status-badge status-{{ $activity->status }}">
                    {{ ucfirst($activity->status) }}
                </span>
                @if ($activity->acceptor)
                    &mdash; {{ $activity->acceptor->name }}
                @elseif ($activity->rejector)
                    &mdash; {{ $activity->rejector->name }}
                @endif
            </td>
        </tr>
    </table>

    {{-- Tools --}}
    @if ($activity->tools)
        <div class="section">
            <div class="section-title">Tools</div>
            <div class="section-content">{{ $activity->tools }}</div>
        </div>
    @endif

    {{-- Tests --}}
    @if ($activity->tests)
        <div class="section">
            <div class="section-title">Test</div>
            <div class="section-content">{{ $activity->tests }}</div>
        </div>
    @endif

    {{-- Rules --}}
    @if ($activity->rules)
        <div class="section">
            <div class="section-title">Rules</div>
            <div class="section-content">{{ $activity->rules }}</div>
        </div>
    @endif

    {{-- Description --}}
    @if ($activity->descriptions)
        <div class="section">
            <div class="section-title">Description</div>
            <div class="section-content">{{ $activity->descriptions }}</div>
        </div>
    @endif

    {{-- Additional Location --}}
    @if ($activity->additional_location)
        <div class="section">
            <div class="section-title">Lokasi Tambahan</div>
            <div class="section-content">{{ $activity->additional_location }}</div>
        </div>
    @endif

    {{-- Attachments --}}
    @if ($activity->attachments->count())
        <div class="section">
            <div class="section-title">Attachment</div>
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

    {{-- Ulasan --}}
    @if ($activity->ulasan)
        <div class="section">
            <div class="section-title">Ulasan</div>
            <div class="section-content">{{ $activity->ulasan }}</div>
        </div>
    @endif

    {{-- Dosen Note --}}
    @if ($activity->dosen_note)
        <div class="section">
            <div class="section-title">Nota Dosen</div>
            <div class="section-content">{{ $activity->dosen_note }}</div>
        </div>
    @endif

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-label">Disemak oleh :</div>
            <div class="signature-line"></div>
            <div class="signature-name">
                @if ($activity->acceptor)
                    {{ $activity->acceptor->name }}
                @elseif ($activity->rejector)
                    {{ $activity->rejector->name }}
                @else
                    (.....................................)
                @endif
            </div>
            <div class="signature-role">
                @if ($activity->acceptor || $activity->rejector)
                    Dosen / Penyemak
                @else
                    &nbsp;
                @endif
            </div>
            <div class="signature-date">
                Tarikh :
                {{ $activity->accept_at?->format('d/m/Y') ?? ($activity->reject_at?->format('d/m/Y') ?? '...............') }}
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Dijana secara automatik oleh ActivityHub &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
