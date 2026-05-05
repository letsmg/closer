<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Get all reports (admin only)
     */
    public function index(Request $request)
    {
        $this->authorize('manage-reports');
        
        $reports = Report::with(['reporter', 'reported'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->reason, function ($query, $reason) {
                $query->where('reason', $reason);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reports);
    }

    /**
     * Create a new report
     */
    public function store(Request $request)
    {
        $request->validate([
            'reported_id' => 'required|exists:users,id',
            'reason' => 'required|in:harassment,disrespect,fake_profile,other',
            'description' => 'nullable|string|max:1000'
        ]);

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reported_id' => $request->reported_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return response()->json($report, 201);
    }

    /**
     * Update report status (admin only)
     */
    public function update(Request $request, Report $report)
    {
        $this->authorize('manage-reports');
        
        $request->validate([
            'status' => 'required|in:analyzed,resolved',
            'analysis_notes' => 'nullable|string|max:1000'
        ]);

        $report->status = $request->status;
        $report->analyzed_by = Auth::id();
        $report->analyzed_at = now();
        
        if ($request->has('analysis_notes')) {
            $report->analysis_notes = $request->analysis_notes;
        }
        
        $report->save();

        return response()->json($report);
    }

    /**
     * Get report details
     */
    public function show(Report $report)
    {
        $this->authorize('view-reports');
        
        $report->load(['reporter', 'reported', 'analyzedBy']);
        
        return response()->json($report);
    }
}
