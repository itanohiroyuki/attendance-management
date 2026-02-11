<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use App\Models\Attendance;
use App\Http\Requests\AttendanceCorrectionRequest;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function store(AttendanceCorrectionRequest $request, Attendance $attendance)
    {
        AttendanceCorrection::create([
            'user_id' => auth()->id(),
            'attendance_id' => $attendance->id,
            'status' => AttendanceCorrection::STATUS_PENDING,
            'reason' => $request->reason,
            'requested_start_time' => $request->start_time,
            'requested_end_time' => $request->end_time,
            'requested_break1_start_time' => $request->break1_start_time,
            'requested_break1_end_time' => $request->break1_end_time,
            'requested_break2_start_time' => $request->break2_start_time,
            'requested_break2_end_time' => $request->break2_end_time,
        ]);
        return redirect()
            ->route('attendance.detail', $attendance->work_date)
            ->with('message', '修正申請を送信しました');
    }

    public function userList(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = AttendanceCorrection::with(['attendance'])
            ->where('user_id', auth()->id())
            ->when($status === 'pending', fn($q) => $q->where('status', AttendanceCorrection::STATUS_PENDING))
            ->when($status === 'approved', fn($q) => $q->where('status', AttendanceCorrection::STATUS_APPROVED))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('request_list', compact('requests', 'status'));
    }

    public function adminList(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = AttendanceCorrection::with(['user', 'attendance'])
            ->when($status === 'pending', fn($q) => $q->where('status', AttendanceCorrection::STATUS_PENDING))
            ->when($status === 'approved', fn($q) => $q->where('status', AttendanceCorrection::STATUS_APPROVED))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.request_list', compact('requests', 'status'));
    }

    public function approveShow(AttendanceCorrection $attendanceCorrection)
    {
        $attendance = $attendanceCorrection->attendance;
        $hasPendingCorrection = $attendanceCorrection->status === AttendanceCorrection::STATUS_PENDING;

        return view('admin.revision_approval', compact(
            'attendance',
            'attendanceCorrection',
            'hasPendingCorrection',
        ));
    }

    public function approve(AttendanceCorrection $attendanceCorrection)
    {
        $attendance = $attendanceCorrection->attendance;

        $attendance->update([
            'start_time' => $attendanceCorrection->requested_start_time,
            'end_time' => $attendanceCorrection->requested_end_time,
            'break1_start_time' => $attendanceCorrection->requested_break1_start_time,
            'break1_end_time' => $attendanceCorrection->requested_break1_end_time,
            'break2_start_time' => $attendanceCorrection->requested_break2_start_time,
            'break2_end_time' => $attendanceCorrection->requested_break2_end_time,
            'note' => $attendanceCorrection->reason,
        ]);

        $attendanceCorrection->update([
            'status' => AttendanceCorrection::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('message', '申請を承認しました');
    }
}
