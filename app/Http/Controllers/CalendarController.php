<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now();

        // Year limits: current year back to 5 years ago
        $maxYear = $now->year;
        $minYear = $maxYear - 5;

        // Validate request parameters — redirect to calendar with defaults if invalid
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:' . $minYear . '|max:' . $maxYear,
        ]);

        if ($validator->fails()) {
            return redirect()->route('calendar.index');
        }

        $month = (int) $request->input('month', $now->month);
        $year = (int) $request->input('year', $now->year);

        // Clamp month if viewing current year (cannot exceed current month)
        if ($year === $maxYear && $month > $now->month) {
            $month = $now->month;
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // Next month — disabled if current month is the max
        $isNextDisabled = ($year === $maxYear && $month === $now->month);
        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        // Query activities for the month
        $activitiesQuery = Activity::query()
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);

        // Role-based filtering: user sees only own activities
        if ($user->is_user) {
            $activitiesQuery->where('user_id', $user->id);
        }
        // Admin and dosen can see all activities

        // Get activity counts grouped by date
        $activityCounts = (clone $activitiesQuery)
            ->selectRaw('DATE(date) as activity_date, COUNT(*) as total')
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date')
            ->toArray();

        $pendingCounts = (clone $activitiesQuery)
            ->where('status', 'pending')
            ->selectRaw('DATE(date) as activity_date, COUNT(*) as total')
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date')
            ->toArray();

        $acceptCounts = (clone $activitiesQuery)
            ->where('status', 'accept')
            ->selectRaw('DATE(date) as activity_date, COUNT(*) as total')
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date')
            ->toArray();

        $rejectCounts = (clone $activitiesQuery)
            ->where('status', 'reject')
            ->selectRaw('DATE(date) as activity_date, COUNT(*) as total')
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date')
            ->toArray();

        // Get detailed activities per date for the modal
        $activitiesByDate = (clone $activitiesQuery)
            ->with('user')
            ->orderBy('date')
            ->get()
            ->groupBy(fn($activity) => $activity->date->toDateString())
            ->map(fn($group) => $group->map(fn($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'date' => $a->date->format('Y-m-d H:i'),
                'status' => $a->status,
                'user' => $a->user->name ?? '-',
                'url' => route('activities.show', $a->id),
            ]))
            ->toArray();

        // Build calendar weeks
        $weeks = [];
        $current = $startOfCalendar->copy();
        while ($current->lte($endOfCalendar)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $current->toDateString();
                $isCurrentMonth = $current->month === $month;

                $week[] = [
                    'date' => $dateStr,
                    'day' => $current->day,
                    'is_current_month' => $isCurrentMonth,
                    'is_today' => $current->isToday(),
                    'total' => $activityCounts[$dateStr] ?? 0,
                    'pending' => $pendingCounts[$dateStr] ?? 0,
                    'accept' => $acceptCounts[$dateStr] ?? 0,
                    'reject' => $rejectCounts[$dateStr] ?? 0,
                    'activities' => $activitiesByDate[$dateStr] ?? [],
                ];

                $current->addDay();
            }
            $weeks[] = $week;
        }

        // Summary stats for the month
        $monthTotal = array_sum($activityCounts);
        $monthPending = array_sum($pendingCounts);
        $monthAccept = array_sum($acceptCounts);
        $monthReject = array_sum($rejectCounts);

        return view('calendar.index', compact(
            'weeks',
            'month',
            'year',
            'prevMonth',
            'nextMonth',
            'isNextDisabled',
            'monthTotal',
            'monthPending',
            'monthAccept',
            'monthReject',
            'maxYear',
            'minYear',
        ));
    }
}
