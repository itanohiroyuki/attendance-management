<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::todayForUser(auth()->id());
        return view('attendance', compact('attendance'));
    }

    public function start()
    {
        $userId = auth()->id();

        if (Attendance::existsTodayForUser($userId)) {
            return redirect()->route('attendance');
        }

        Attendance::create([
            'user_id'    => $userId,
            'work_date'  => today(),
            'start_time' => now(),
        ]);

        return redirect()->route('attendance');
    }

    public function breakStart()
    {
        $attendance = Attendance::workingTodayForUser(auth()->id());

        if (!$attendance || $attendance->end_time) {
            abort(400);
        }

        if (!$attendance->break1_start_time) {
            $attendance->update([
                'break1_start_time' => now(),
            ]);
            return redirect()->route('attendance');
        }

        if (
            $attendance->break1_start_time &&
            $attendance->break1_end_time &&
            !$attendance->break2_start_time
        ) {
            $attendance->update([
                'break2_start_time' => now(),
            ]);
            return redirect()->route('attendance');
        }

        abort(400);
    }

    public function breakEnd()
    {
        $attendance = Attendance::workingTodayForUser(auth()->id());

        if (!$attendance) {
            abort(400);
        }

        if (
            $attendance->break1_start_time &&
            !$attendance->break1_end_time
        ) {
            $attendance->update([
                'break1_end_time' => now(),
            ]);
            return redirect()->route('attendance');
        }

        if (
            $attendance->break2_start_time &&
            !$attendance->break2_end_time
        ) {
            $attendance->update([
                'break2_end_time' => now(),
            ]);
            return redirect()->route('attendance');
        }

        abort(400);
    }

    public function end()
    {
        $attendance = Attendance::workingTodayForUser(auth()->id());

        if (!$attendance || $attendance->end_time) {
            return redirect()->route('attendance');
        }

        $attendance->update([
            'end_time' => now(),
        ]);

        return redirect()->route('attendance');
    }

    public function list(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $baseMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $startOfMonth = $baseMonth->copy();
        $endOfMonth   = $baseMonth->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', auth()->id())
            ->whereBetween('work_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->get()
            ->keyBy(fn($a) => $a->work_date->format('Y-m-d'));

        $dates = CarbonPeriod::create($startOfMonth, $endOfMonth);

        return view('attendance_list', compact(
            'attendances',
            'month',
            'baseMonth',
            'startOfMonth',
            'endOfMonth',
            'dates'
        ));
    }

    public function detail(string $date)
    {
        $attendance = Attendance::with('user')
            ->where('user_id', auth()->id())
            ->whereDate('work_date', $date)
            ->first();

        $pendingCorrection = null;

        if ($attendance) {
            $pendingCorrection = AttendanceCorrection::where('attendance_id', $attendance->id)
                ->where('status', 0)
                ->latest()
                ->first();
        }

        $hasPendingCorrection = (bool) $pendingCorrection;

        $showBreak2 =
            ! $hasPendingCorrection
            || $attendance?->break2_start_time
            || $attendance?->break2_end_time
            || $pendingCorrection?->requested_break2_start_time
            || $pendingCorrection?->requested_break2_end_time;

        return view('attendance_detail', compact(
            'attendance',
            'date',
            'hasPendingCorrection',
            'pendingCorrection',
            'showBreak2'
        ));
    }
}
