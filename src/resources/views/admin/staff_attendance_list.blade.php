@extends('layouts.base')

@section('title', 'スタッフ別勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/admin/staff_attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-container">
        <h1 class="page-title">{{ $user->name }}さんの勤怠</h1>
        <div class="month-switch">
            <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}"><img class="arrow left"
                    src="{{ asset('img/arrow.png') }}" alt="矢印"> 前月</a>
            <form method="GET">
                <div class="month-picker">
                    <span class="month-text">
                        <img class="calendar" src="{{ asset('img/calendar.png') }}" alt="カレンダー">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('Y/m') }}
                    </span>
                    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                        class="month-input">
                </div>
            </form>
            <a href="?month={{ \Carbon\Carbon::parse($month)->addMonth()->format('Y-m') }}">翌月 <img class="arrow right"
                    src="{{ asset('img/arrow.png') }}" alt="矢印"></a>
        </div>

        <div class="attendance-table-wrapper">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($dates as $date)
                        @php
                            $workDate = $date->format('Y-m-d');
                            $attendance = $attendances->get($workDate);
                        @endphp

                        <tr>
                            <td>
                                {{ $date->locale('ja')->isoFormat('MM/DD(ddd)') }}
                            </td>

                            <td>
                                {{ optional($attendance?->start_time)->format('H:i') }}
                            </td>

                            <td>
                                {{ optional($attendance?->end_time)->format('H:i') }}
                            </td>

                            <td>
                                {{ $attendance ? gmdate('H:i', $attendance->break_minutes * 60) : '' }}
                            </td>

                            <td>
                                {{ $attendance ? gmdate('H:i', $attendance->work_minutes * 60) : '' }}
                            </td>

                            <td>
                                @if ($attendance)
                                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
                                @else
                                    <a href="{{ route('admin.attendance.detail', $workDate) }}">詳細</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="btn-area">
            <form method="GET" action="{{ route('admin.attendance.staff.csv', $user->id) }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="csv-btn">CSV出力</button>
            </form>
        </div>
    </div>
    <script>
        let currentDate = new Date();

        const monthText = document.querySelector('.current-month');

        function updateMonth() {
            const y = currentDate.getFullYear();
            const m = String(currentDate.getMonth() + 1).padStart(2, '0');
            monthText.textContent = `${y}/${m}`;
        }

        document.querySelectorAll('.month-btn')[0].onclick = () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateMonth();
        };

        document.querySelectorAll('.month-btn')[1].onclick = () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateMonth();
        };

        updateMonth();
    </script>
@endsection
