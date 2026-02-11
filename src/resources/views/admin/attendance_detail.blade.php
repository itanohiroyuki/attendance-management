@extends('layouts.base')

@section('title', '勤怠詳細(管理者)')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')
    <div class="attendance-detail-container">
        <h1 class="page-title">勤怠詳細</h1>
        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="detail-card">
                <table class="detail-table">
                    <tr>
                        <th>名前</th>
                        <td class="date-name">{{ optional($attendance?->user)->name ?? '—' }}
                        </td>
                    </tr>

                    <tr>
                        <th>日付</th>
                        @php
                            $carbonDate = \Carbon\Carbon::parse($attendance->work_date);
                        @endphp
                        <td class="date-display">
                            <span class="date-year">{{ $carbonDate->format('Y年') }}</span>
                            <span class="date-md">{{ $carbonDate->format('n月j日') }}</span>
                        </td>
                    </tr>

                    <tr>
                        <th>出勤・退勤</th>
                        <td class="time-range">
                            <input type="text" name="start_time"
                                value="{{ old('start_time', optional($attendance?->start_time)->format('H:i')) }}"
                                class="time-input" {{ $hasPendingCorrection ? 'readonly' : '' }}>
                            <span class="time-separator">〜</span>
                            <input type="text" name="end_time"
                                value="{{ old('end_time', optional($attendance?->end_time)->format('H:i')) }}"class="time-input"
                                {{ $hasPendingCorrection ? 'readonly' : '' }}>
                        </td>
                    </tr>

                    <tr>
                        <th>休憩</th>
                        <td class="time-range">
                            <input type="text" name="break1_start_time"
                                value="{{ old('break1_start_time', $attendance?->break1_start_time ? substr($attendance->break1_start_time, 0, 5) : '') }}"
                                class="time-input" {{ $hasPendingCorrection ? 'readonly' : '' }}>
                            <span class="time-separator">〜</span>
                            <input type="text" name="break1_end_time"
                                value="{{ old('break1_end_time', $attendance?->break1_end_time ? substr($attendance->break1_end_time, 0, 5) : '') }}"
                                class="time-input" {{ $hasPendingCorrection ? 'readonly' : '' }}>
                        </td>
                    </tr>
                    @if ($showBreak2)
                        <tr>
                            <th>休憩2</th>
                            <td class="time-range">
                                <input type="text" name="break2_start_time"
                                    value="{{ old('break2_start_time', $attendance?->break2_start_time ? substr($attendance->break2_start_time, 0, 5) : '') }}"
                                    class="time-input" {{ $hasPendingCorrection ? 'readonly' : '' }}>
                                <span class="time-separator">〜</span>
                                <input type="text" name="break2_end_time"
                                    value="{{ old('break2_end_time', $attendance?->break2_end_time ? substr($attendance->break2_end_time, 0, 5) : '') }}"
                                    class="time-input" {{ $hasPendingCorrection ? 'readonly' : '' }}>
                            </td>
                        </tr>
                    @endif

                    <tr class="no-border">
                        <th>備考</th>
                        <td class="remarks-column">
                            @if ($hasPendingCorrection)
                                <div class="readonly-text">
                                    {{ $pendingCorrection->reason }}
                                </div>
                            @else
                                <textarea name="note">{{ old('note', $attendance->note) }}</textarea>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="btn-area">
                <div class="action-slot">
                    @if ($hasPendingCorrection)
                        <span class="pending-message">
                            *承認待ちのため修正はできません。
                        </span>
                    @else
                        <button class="edit-btn" type="submit">修正</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
