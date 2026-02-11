@extends('layouts.base')

@section('title', '勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/admin/attendance_list.css') }}">
@endsection

@section('content')
    <div class="attendance-container">
        <h1 class="page-title">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}の勤怠</h1>
        <div class="date-switch">
            <a href="?date={{ \Carbon\Carbon::parse($date)->subDay()->toDateString() }}"><img class="arrow left"
                    src="{{ asset('img/arrow.png') }}" alt="矢印"> 前日</a>
            <form method="GET" action="{{ route('admin.attendance.list') }}">
                <div class="date-picker">
                    <span class="date-text"><img class="calendar" src="{{ asset('img/calendar.png') }}" alt="カレンダー">
                        {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
                    </span>
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                        class="date-input">
                </div>
            </form>
            <a href="?date={{ \Carbon\Carbon::parse($date)->addDay()->toDateString() }}">翌日<img class="arrow right"
                    src="{{ asset('img/arrow.png') }}" alt="矢印"> </a>
        </div>

        <div class="attendance-table-wrapper">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        @php
                            $attendance = $attendances->get($user->id);
                        @endphp

                        <tr>
                            <td>{{ $user->name }}</td>

                            <td>{{ optional($attendance?->start_time)->format('H:i') }}</td>
                            <td>{{ optional($attendance?->end_time)->format('H:i') }}</td>

                            <td>
                                {{ $attendance ? gmdate('H:i', $attendance->break_minutes * 60) : '' }}
                            </td>

                            <td>
                                {{ $attendance ? gmdate('H:i', $attendance->work_minutes * 60) : '' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.attendance.detail', $attendance ? $attendance->id : $date) }}">
                                    詳細
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
