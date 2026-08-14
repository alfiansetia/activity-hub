@extends('layouts.app')

@section('title', 'Activity Calendar')

@section('content')
    {{-- Page Header --}}
    <div class="mb-4 fade-up">
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em;">
                    <i class="bi bi-calendar3 me-2" style="color: var(--primary);"></i>Activity Calendar
                </h4>
                <p class="mb-0" style="color: var(--text-muted); font-size: 0.875rem;">
                    Monitor your activities by date. Click on a day to see activity details.
                </p>
            </div>
            @if (auth()->user()->is_user)
                <a href="{{ route('activities.create') }}" class="btn btn-primary text-nowrap">
                    <i class="bi bi-plus-lg me-1"></i> New Activity
                </a>
            @endif
        </div>
    </div>

    {{-- Month Summary Stats --}}
    <div class="row g-3 mb-4 fade-up">
        <div class="col-6 col-lg-3">
            <div class="card-stat p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $monthTotal }}</div>
                        <div class="stat-label">Total Activities</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-stat p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $monthPending }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-stat p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $monthAccept }}</div>
                        <div class="stat-label">Accepted</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-stat p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $monthReject }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="card fade-up">
        <div class="card-body p-0">
            {{-- Calendar Header: Navigation --}}
            <div class="calendar-header">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                        class="calendar-nav-btn" title="Previous Month">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <button type="button" class="calendar-month-title" data-bs-toggle="modal"
                        data-bs-target="#monthYearPicker" title="Click to select month & year">
                        {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem; opacity: 0.5;"></i>
                    </button>
                    @if ($isNextDisabled)
                        <span class="calendar-nav-btn calendar-nav-btn--disabled" title="Cannot go beyond current month">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @else
                        <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                            class="calendar-nav-btn" title="Next Month">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                </div>
                <div class="text-center mt-2">
                    <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-calendar-event me-1"></i> Today
                    </a>
                </div>
            </div>

            {{-- Calendar Grid --}}
            <div class="calendar-grid-wrapper">
                {{-- Day Headers --}}
                <div class="calendar-day-headers">
                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                        <div class="calendar-day-header">{{ $dayName }}</div>
                    @endforeach
                </div>

                {{-- Weeks --}}
                @foreach ($weeks as $week)
                    <div class="calendar-week">
                        @foreach ($week as $day)
                            <div class="calendar-day {{ !$day['is_current_month'] ? 'calendar-day--other-month' : '' }} {{ $day['is_today'] ? 'calendar-day--today' : '' }} {{ $day['total'] > 0 ? 'calendar-day--has-activity' : '' }}"
                                @if ($day['total'] > 0 && $day['is_current_month']) style="cursor: pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#dayModal-{{ $day['date'] }}" @endif>
                                <div
                                    class="calendar-day-number {{ $day['is_today'] ? 'calendar-day-number--today' : '' }}">
                                    {{ $day['day'] }}
                                </div>
                                @if ($day['total'] > 0 && $day['is_current_month'])
                                    <div class="calendar-day-activities">
                                        <span class="calendar-activity-badge" title="{{ $day['total'] }} activities">
                                            {{ $day['total'] }}
                                        </span>
                                        <div class="calendar-activity-dots">
                                            @if ($day['accept'] > 0)
                                                <span class="calendar-dot calendar-dot--accept"></span>
                                            @endif
                                            @if ($day['pending'] > 0)
                                                <span class="calendar-dot calendar-dot--pending"></span>
                                            @endif
                                            @if ($day['reject'] > 0)
                                                <span class="calendar-dot calendar-dot--reject"></span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="calendar-legend">
                <div class="calendar-legend-item">
                    <span class="calendar-dot calendar-dot--accept"></span>
                    <span>Accepted</span>
                </div>
                <div class="calendar-legend-item">
                    <span class="calendar-dot calendar-dot--pending"></span>
                    <span>Pending</span>
                </div>
                <div class="calendar-legend-item">
                    <span class="calendar-dot calendar-dot--reject"></span>
                    <span>Rejected</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Month/Year Picker Modal --}}
    <div class="modal fade" id="monthYearPicker" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
                <div class="modal-header"
                    style="background: var(--gradient-primary); color: #fff; border-radius: var(--radius-xl) var(--radius-xl) 0 0; border: none;">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-calendar3 me-2"></i>Select Month & Year
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <form id="monthYearForm" method="GET" action="{{ route('calendar.index') }}">
                        <div class="mb-3">
                            <label class="form-label">Month</label>
                            <select name="month" id="monthSelect" class="form-select">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select name="year" id="yearSelect" class="form-select">
                                @foreach (range($maxYear, $minYear) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> Go
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Day Detail Modals --}}
    @foreach ($weeks as $week)
        @foreach ($week as $day)
            @if ($day['total'] > 0 && $day['is_current_month'])
                <div class="modal fade" id="dayModal-{{ $day['date'] }}" tabindex="-1"
                    aria-labelledby="dayModalLabel-{{ $day['date'] }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content"
                            style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
                            {{-- Modal Header --}}
                            <div class="modal-header"
                                style="background: var(--gradient-primary); color: #fff; border-radius: var(--radius-xl) var(--radius-xl) 0 0; border: none;">
                                <h6 class="modal-title fw-bold" id="dayModalLabel-{{ $day['date'] }}">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    {{ \Carbon\Carbon::parse($day['date'])->format('l, d F Y') }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            {{-- Modal Body --}}
                            <div class="modal-body p-0">
                                {{-- Summary bar --}}
                                <div class="d-flex align-items-center gap-3 px-3 py-2"
                                    style="background: var(--bg-body); border-bottom: 1px solid var(--border-light);">
                                    <span class="badge badge-soft-primary">{{ $day['total'] }} activities</span>
                                    @if ($day['accept'] > 0)
                                        <span class="badge badge-soft-success">{{ $day['accept'] }} accepted</span>
                                    @endif
                                    @if ($day['pending'] > 0)
                                        <span class="badge badge-soft-warning">{{ $day['pending'] }} pending</span>
                                    @endif
                                    @if ($day['reject'] > 0)
                                        <span class="badge badge-soft-danger">{{ $day['reject'] }} rejected</span>
                                    @endif
                                </div>

                                {{-- Activity List --}}
                                <div class="list-group list-group-flush">
                                    @foreach ($day['activities'] as $activity)
                                        <a href="{{ $activity['url'] }}"
                                            class="list-group-item list-group-item-action d-flex align-items-start gap-3 px-3 py-3"
                                            style="border-left: 3px solid
                                                @if ($activity['status'] === 'accept') var(--success)
                                                @elseif($activity['status'] === 'pending') var(--warning)
                                                @else var(--danger) @endif;">
                                            {{-- Status Icon --}}
                                            <div class="flex-shrink-0 mt-1">
                                                @if ($activity['status'] === 'accept')
                                                    <i class="bi bi-check-circle-fill"
                                                        style="color: var(--success); font-size: 1.1rem;"></i>
                                                @elseif($activity['status'] === 'pending')
                                                    <i class="bi bi-hourglass-split"
                                                        style="color: var(--warning); font-size: 1.1rem;"></i>
                                                @else
                                                    <i class="bi bi-x-circle-fill"
                                                        style="color: var(--danger); font-size: 1.1rem;"></i>
                                                @endif
                                            </div>
                                            {{-- Details --}}
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="fw-semibold"
                                                    style="font-size: 0.875rem; color: var(--text-primary);">
                                                    {{ $activity['title'] }}
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                                        <i class="bi bi-clock me-1"></i>{{ $activity['date'] }}
                                                    </span>
                                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                                        <i class="bi bi-person me-1"></i>{{ $activity['user'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- Status Badge --}}
                                            <div class="flex-shrink-0">
                                                @if ($activity['status'] === 'accept')
                                                    <span class="badge badge-soft-success">Accepted</span>
                                                @elseif($activity['status'] === 'pending')
                                                    <span class="badge badge-soft-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-soft-danger">Rejected</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Modal Footer --}}
                            <div class="modal-footer"
                                style="border-top: 1px solid var(--border-light); border-radius: 0 0 var(--radius-xl) var(--radius-xl);">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
@endsection

@push('styles')
    <style>
        /* ============================================
                                                                       CALENDAR COMPONENT
                                                                       ============================================ */

        /* Calendar Header */
        .calendar-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
        }

        .calendar-month-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
            background: none;
            border: 1px solid transparent;
            cursor: pointer;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
        }

        .calendar-month-title:hover {
            background: var(--bg-input);
            border-color: var(--border-color);
        }

        .calendar-nav-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-secondary);
            text-decoration: none;
            border: 1px solid var(--border-color);
            transition: all var(--transition-fast);
            font-size: 0.9rem;
        }

        .calendar-nav-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 2px 8px var(--primary-glow);
        }

        .calendar-nav-btn--disabled {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-muted);
            border: 1px solid var(--border-light);
            font-size: 0.9rem;
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Calendar Grid Wrapper */
        .calendar-grid-wrapper {
            padding: 0.75rem 1rem 1rem;
        }

        /* Day Headers (Mon, Tue, ...) */
        .calendar-day-headers {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .calendar-day-header {
            text-align: center;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            padding: 0.5rem 0;
        }

        /* Weeks */
        .calendar-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        /* Day Cell */
        .calendar-day {
            position: relative;
            aspect-ratio: 1 / 1;
            border-radius: var(--radius-md);
            padding: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            transition: all var(--transition-fast);
            cursor: default;
            background: transparent;
            min-height: 70px;
        }

        .calendar-day:hover:not(.calendar-day--other-month) {
            background: rgba(99, 102, 241, 0.04);
        }

        .calendar-day--other-month {
            opacity: 0.3;
        }

        .calendar-day--today {
            background: rgba(99, 102, 241, 0.06) !important;
            border: 1.5px solid var(--primary-light);
        }

        .calendar-day--has-activity {
            background: rgba(99, 102, 241, 0.03);
        }

        .calendar-day--has-activity:not(.calendar-day--other-month):hover {
            background: rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
        }

        /* Day Number */
        .calendar-day-number {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
        }

        .calendar-day-number--today {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: var(--radius-full);
            background: var(--primary);
            color: #fff;
            font-size: 0.75rem;
        }

        .calendar-day--other-month .calendar-day-number {
            color: var(--text-muted);
        }

        /* Activity Badge */
        .calendar-day-activities {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .calendar-activity-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 20px;
            padding: 0 5px;
            border-radius: var(--radius-full);
            background: var(--gradient-primary);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1;
            box-shadow: 0 1px 4px var(--primary-glow);
        }

        /* Activity Dots */
        .calendar-activity-dots {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .calendar-dot {
            width: 6px;
            height: 6px;
            border-radius: var(--radius-full);
            flex-shrink: 0;
        }

        .calendar-dot--accept {
            background: var(--success);
        }

        .calendar-dot--pending {
            background: var(--warning);
        }

        .calendar-dot--reject {
            background: var(--danger);
        }

        /* Legend */
        .calendar-legend {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            padding: 0.875rem 1.5rem;
            border-top: 1px solid var(--border-light);
            flex-wrap: wrap;
        }

        .calendar-legend-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        /* ============================================
                                                                       RESPONSIVE: TABLET (max-width: 768px)
                                                                       ============================================ */
        @media (max-width: 768px) {
            .calendar-grid-wrapper {
                padding: 0.5rem 0.5rem 0.75rem;
            }

            .calendar-day {
                min-height: 56px;
                padding: 4px;
            }

            .calendar-day-number {
                font-size: 0.7rem;
            }

            .calendar-day-number--today {
                width: 22px;
                height: 22px;
                font-size: 0.65rem;
            }

            .calendar-activity-badge {
                min-width: 18px;
                height: 16px;
                font-size: 0.55rem;
                padding: 0 4px;
            }

            .calendar-dot {
                width: 5px;
                height: 5px;
            }

            .calendar-month-title {
                font-size: 1rem;
            }

            .calendar-header {
                padding: 1rem;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .stat-label {
                font-size: 0.7rem;
            }
        }

        /* ============================================
                                                                       RESPONSIVE: MOBILE (max-width: 480px)
                                                                       ============================================ */
        @media (max-width: 480px) {
            .calendar-grid-wrapper {
                padding: 0.35rem 0.25rem 0.5rem;
            }

            .calendar-day-headers {
                gap: 2px;
            }

            .calendar-week {
                gap: 2px;
            }

            .calendar-day {
                min-height: 44px;
                padding: 3px;
                aspect-ratio: auto;
                border-radius: var(--radius-sm);
            }

            .calendar-day-header {
                font-size: 0.6rem;
                padding: 0.35rem 0;
            }

            .calendar-day-number {
                font-size: 0.65rem;
                margin-bottom: 2px;
            }

            .calendar-day-number--today {
                width: 20px;
                height: 20px;
                font-size: 0.6rem;
            }

            .calendar-activity-badge {
                min-width: 16px;
                height: 14px;
                font-size: 0.5rem;
                padding: 0 3px;
                box-shadow: none;
            }

            .calendar-activity-dots {
                gap: 2px;
            }

            .calendar-dot {
                width: 4px;
                height: 4px;
            }

            .calendar-month-title {
                font-size: 0.9rem;
                padding: 0.15rem 0.4rem;
            }

            .calendar-nav-btn,
            .calendar-nav-btn--disabled {
                width: 30px;
                height: 30px;
                font-size: 0.75rem;
            }

            .calendar-header {
                padding: 0.75rem;
            }

            .calendar-legend {
                gap: 0.75rem;
                padding: 0.65rem 1rem;
            }

            .calendar-legend-item {
                font-size: 0.65rem;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            .stat-label {
                font-size: 0.65rem;
            }
        }

        /* ============================================
                                                                       RESPONSIVE: VERY SMALL (max-width: 360px)
                                                                       ============================================ */
        @media (max-width: 360px) {
            .calendar-day {
                min-height: 38px;
                padding: 2px;
            }

            .calendar-day-header {
                font-size: 0.5rem;
                letter-spacing: 0;
            }

            .calendar-day-number {
                font-size: 0.55rem;
            }

            .calendar-day-number--today {
                width: 18px;
                height: 18px;
                font-size: 0.5rem;
            }

            .calendar-activity-badge {
                display: none;
            }

            .calendar-activity-dots {
                gap: 1.5px;
            }

            .calendar-dot {
                width: 4px;
                height: 4px;
            }

            .calendar-month-title {
                font-size: 0.8rem;
                padding: 0.1rem 0.3rem;
            }

            .calendar-nav-btn,
            .calendar-nav-btn--disabled {
                width: 26px;
                height: 26px;
                font-size: 0.65rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1;

            function limitMonths() {
                if (!monthSelect || !yearSelect) return;
                const selectedYear = parseInt(yearSelect.value);
                const options = monthSelect.options;

                for (let i = 0; i < options.length; i++) {
                    const monthVal = parseInt(options[i].value);
                    if (selectedYear === currentYear && monthVal > currentMonth) {
                        options[i].disabled = true;
                        options[i].style.color = '#ccc';
                    } else {
                        options[i].disabled = false;
                        options[i].style.color = '';
                    }
                }

                // If currently selected month is beyond limit, reset to current month
                if (selectedYear === currentYear && parseInt(monthSelect.value) > currentMonth) {
                    monthSelect.value = currentMonth;
                }
            }

            if (yearSelect) {
                yearSelect.addEventListener('change', limitMonths);
                limitMonths(); // run on load
            }
        });
    </script>
@endpush
