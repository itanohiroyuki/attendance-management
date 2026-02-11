<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $users = User::where('is_admin', false)->orderBy('id')->get();

        $attendances = Attendance::where('work_date', $date)
            ->get()
            ->keyBy('user_id');

        return view('admin.attendance_list', compact(
            'date',
            'users',
            'attendances'
        ));
    }

    public function detail($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);

        $pendingCorrection = $attendance->pendingCorrection;

        $hasPendingCorrection = (bool) $pendingCorrection;

        $showBreak2 =
            ! $hasPendingCorrection
            || $attendance?->break2_start_time
            || $attendance?->break2_end_time
            || $pendingCorrection?->requested_break2_start_time
            || $pendingCorrection?->requested_break2_end_time;

        return view('admin.attendance_detail', compact(
            'attendance',
            'pendingCorrection',
            'hasPendingCorrection',
            'showBreak2'
        ));
    }

    public function update(AdminAttendanceUpdateRequest $request, Attendance $attendance)
    {
        $attendance->update($request->only([
            'start_time',
            'end_time',
            'break1_start_time',
            'break1_end_time',
            'break2_start_time',
            'break2_end_time',
            'note',
        ]));

        return redirect()
            ->route('admin.attendance.detail', $attendance->id)
            ->with('success', '勤怠を更新しました');
    }

    public function exportCsv(Request $request, $id)
    {
        $month = $request->input('month', now()->format('Y-m-d'));

        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $user = User::findOrFail($id);

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')
            ->get();
        $fileName = "{$user->name}_{$month}_attendance.csv";
        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩時間',
                '勤怠時間',
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->work_date->format('Y-m-d'),
                    optional($attendance->start_time)->format('H-i'),
                    optional($attendance->end_time)->format('H-i'),
                    gmdate('H:i', $attendance->break_minutes * 60),
                    gmdate('H:i', $attendance->work_minutes * 60),
                ]);
            }
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
