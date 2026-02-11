<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class StaffController extends Controller
{
    public function list()
    {
        $users = User::where('is_admin', false)->get();

        return view('admin.staff_list', compact('users'));
    }

    public function attendanceList(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $month = $request->input('month', now()->format('Y-m'));

        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn($attendances) => $attendances->work_date->format('Y-m-d'));

        $dates = CarbonPeriod::create($startOfMonth, $endOfMonth);

        return view('admin.staff_attendance_list', compact(
            'user',
            'attendances',
            'month',
            'startOfMonth',
            'endOfMonth',
            'dates'
        ));
    }
}
