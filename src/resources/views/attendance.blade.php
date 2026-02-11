@extends('layouts.base')

@section('title', '勤怠登録')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/attendance.css') }}">
@endsection

@section('content')
    <div class="attendance-content">
        <div class="attendance-status">
            {{ $attendance?->status_label ?? '勤務外' }}
        </div>
        <div class="date" id="current-date">
            日付
        </div>
        <div class="time" id="current-time">
            時間
        </div>
        <div class="attendance_button">
            @if (!$attendance)
                <form action="/attendance/start" method="POST">
                    @csrf
                    <button class="btn1" type="submit">出勤</button>
                </form>
            @endif
            @if ($attendance?->isWorking() && !$attendance->isOnBreak())
                <form action="/attendance/end" method="POST">
                    @csrf
                    <button class="btn1" type="submit">退勤</button>
                </form>
                <form action="{{ route('attendance.break.start') }}" method="POST">
                    @csrf
                    <button class="btn2" type="submit">休憩入</button>
                </form>
            @endif
            @if ($attendance?->isOnBreak())
                <form action="/attendance/break/end" method="POST">
                    @csrf
                    <button class="btn2" type="submit">休憩戻</button>
                </form>
            @endif
            @if ($attendance?->isFinished())
                <p class="p">お疲れ様でした。</p>
            @endif
        </div>
    </div>
    <script>
        function updateDateTime() {
            const now = new Date();

            const date = now.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'short'
            });

            const time = now.toLocaleTimeString('ja-JP', {
                hour: '2-digit',
                minute: '2-digit'
            });

            document.getElementById('current-date').textContent = date;
            document.getElementById('current-time').textContent = time;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
@endsection
