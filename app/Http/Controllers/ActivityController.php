<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $selectedCompanyId = $request->company_id;

        // Dosen: show company selection when no company filter
        if ($user->is_dosen && !$selectedCompanyId) {
            $companies = \App\Models\Company::withCount([
                'activities',
                'activities as pending_count' => fn($q) => $q->where('status', 'pending'),
                'activities as accept_count' => fn($q) => $q->where('status', 'accept'),
                'activities as reject_count' => fn($q) => $q->where('status', 'reject'),
            ])->orderBy('name')->get();

            return view('activities.index', compact('companies'));
        }

        $activities = Activity::with(['user', 'company'])
            ->when($user->is_user, fn($q) => $q->where('company_id', $user->company_id))
            ->when($selectedCompanyId, fn($q) => $q->where('company_id', $selectedCompanyId))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $selectedCompany = $selectedCompanyId ? \App\Models\Company::find($selectedCompanyId) : null;

        return view('activities.index', compact('activities', 'selectedCompany'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->is_user) {
            return back()->with('error', 'Only regular users can create activities.');
        }

        if ($user->company_status !== 'accept' || !$user->company_id) {
            return back()->with('error', 'Your account is not yet approved or you have no company assigned.');
        }

        $companies = $user->is_admin
            ? \App\Models\Company::orderBy('name')->get()
            : collect([$user->company]);

        $defaultCompanyId = $user->company_id;

        return view('activities.create', compact('companies', 'defaultCompanyId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_user) {
            return back()->with('error', 'Only regular users can create activities.');
        }

        if ($user->company_status !== 'accept' || !$user->company_id) {
            return back()->with('error', 'Your account is not yet approved or you have no company assigned.');
        }

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'descriptions' => ['nullable', 'string'],
            'rules'        => ['nullable', 'string'],
            'tools'        => ['nullable', 'string'],
            'company_id'   => ['nullable', 'exists:companies,id'],
            'images'       => ['nullable', 'array'],
            'images.*'     => ['string'], // base64
            'captions'     => ['nullable', 'array'],
            'captions.*'   => ['nullable', 'string', 'max:255'],
        ]);

        // Auto-assign defaults from backend
        $validated['date'] = now()->format('Y-m-d H:i:s');
        $validated['company_id'] = $validated['company_id'] ?? $user->company_id;

        DB::transaction(function () use ($validated) {
            $activity = Activity::create([
                'title'        => $validated['title'],
                'date'         => $validated['date'],
                'descriptions' => $validated['descriptions'] ?? null,
                'rules'        => $validated['rules'] ?? null,
                'tools'        => $validated['tools'] ?? null,
                'company_id'   => $validated['company_id'],
                'user_id'      => auth()->id(),
                'status'       => 'pending',
            ]);

            $this->saveAttachments($activity, $validated['images'] ?? [], $validated['captions'] ?? []);
        });

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['user', 'company', 'attachments', 'acceptor', 'rejector']);
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $user = auth()->user();

        if ($activity->status === 'accept') {
            return back()->with('error', 'Accepted activities cannot be edited.');
        }

        // Only creator, same-company user, or admin can edit
        if ($user->is_user && $activity->user_id !== $user->id && $activity->company_id !== $user->company_id) {
            abort(403);
        }

        $activity->load('attachments');

        $companies = $user->is_admin
            ? \App\Models\Company::orderBy('name')->get()
            : collect([$user->company]);

        $defaultCompanyId = $user->company_id;

        return view('activities.edit', compact('activity', 'companies', 'defaultCompanyId'));
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->status === 'accept') {
            return back()->with('error', 'Accepted activities cannot be edited.');
        }

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'descriptions' => ['nullable', 'string'],
            'rules'        => ['nullable', 'string'],
            'tools'        => ['nullable', 'string'],
            'company_id'   => ['nullable', 'exists:companies,id'],
            'images'       => ['nullable', 'array'],
            'images.*'     => ['string'],
            'captions'     => ['nullable', 'array'],
            'captions.*'   => ['nullable', 'string', 'max:255'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:attachments,id'],
        ]);

        // Keep existing date, auto-fill company if not provided
        $validated['company_id'] = $validated['company_id'] ?? $activity->company_id;

        DB::transaction(function () use ($validated, $activity) {
            $wasRejected = $activity->status === 'reject';

            $activity->update([
                'title'        => $validated['title'],
                'descriptions' => $validated['descriptions'] ?? null,
                'rules'        => $validated['rules'] ?? null,
                'tools'        => $validated['tools'] ?? null,
                'company_id'   => $validated['company_id'],
                'status'       => 'pending',
                'reject_by'    => null,
                'reject_reason' => null,
                'reject_at'    => null,
                're_submit_at' => $wasRejected ? now() : null,
            ]);

            // Delete selected attachments
            if (!empty($validated['delete_attachments'])) {
                $toDelete = Attachment::whereIn('id', $validated['delete_attachments'])->where('activity_id', $activity->id)->get();
                foreach ($toDelete as $att) {
                    Storage::disk('public')->delete($att->image_url);
                    $att->delete();
                }
            }

            $this->saveAttachments($activity, $validated['images'] ?? [], $validated['captions'] ?? []);
        });

        return redirect()->route('activities.show', $activity)->with('success', 'Activity updated and resubmitted for review.');
    }

    public function destroy(Activity $activity)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $activity->attachments()->each(function ($att) {
            Storage::disk('public')->delete($att->image_url);
        });

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted.');
    }

    // Dosen: accept activity
    public function accept(Request $request, Activity $activity)
    {
        if (auth()->user()->role !== 'dosen') abort(403);

        $validated = $request->validate([
            'dosen_note' => ['nullable', 'string'],
        ]);

        $activity->update([
            'status'     => 'accept',
            'accept_by'  => auth()->id(),
            'accept_at'  => now(),
            'dosen_note' => $validated['dosen_note'] ?? null,
        ]);

        return back()->with('success', 'Activity accepted.');
    }

    // Dosen: reject activity
    public function reject(Request $request, Activity $activity)
    {
        if (auth()->user()->role !== 'dosen') abort(403);

        $request->validate(['reject_reason' => 'required|string']);

        $activity->update([
            'status'        => 'reject',
            'reject_by'     => auth()->id(),
            'reject_at'     => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'Activity rejected.');
    }

    private function saveAttachments(Activity $activity, array $images, array $captions): void
    {
        foreach ($images as $i => $base64) {
            if (empty($base64)) continue;

            // Decode base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $ext = strtolower($type[1]);
                if ($ext === 'jpeg') $ext = 'jpg';
            } else {
                $ext = 'png';
            }

            $data = base64_decode($base64);
            $filename = 'activities/' . Str::uuid() . '.' . $ext;

            Storage::disk('public')->put($filename, $data);

            Attachment::create([
                'activity_id' => $activity->id,
                'caption'     => $captions[$i] ?? '',
                'image_url'   => $filename,
            ]);
        }
    }
}
