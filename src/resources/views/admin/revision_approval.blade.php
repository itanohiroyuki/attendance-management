@extends('layouts.base')

@section('title', '修正申請承認画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/admin/revision_approval.css') }}">
@endsection

@section('content')
    <div class="attendance-detail-container">
        <h1 class="page-title">勤怠詳細</h1>

        <form action="{{ route('admin.correction-request.approve', $attendanceCorrection->id) }}" method="POST">
            @csrf
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
                            <span class="time-text">
                                {{ $attendanceCorrection->requested_start_time ? substr($attendanceCorrection->requested_start_time, 0, 5) : '—' }}
                            </span>
                            <span>〜</span>
                            <span class="time-text">
                                {{ $attendanceCorrection->requested_end_time ? substr($attendanceCorrection->requested_end_time, 0, 5) : '—' }}
                            </span>

                            <input type="hidden" name="start_time"
                                value="{{ $attendanceCorrection->requested_start_time }}">
                            <input type="hidden" name="end_time" value="{{ $attendanceCorrection->requested_end_time }}">
                        </td>
                    </tr>

                    <tr>
                        <th>休憩</th>
                        <td class="time-range">
                            <span class="time-text">
                                {{ $attendanceCorrection->requested_break1_start_time ? substr($attendanceCorrection->requested_break1_start_time, 0, 5) : '—' }}
                            </span>
                            <span>〜</span>
                            <span class="time-text">
                                {{ $attendanceCorrection->requested_break1_end_time ? substr($attendanceCorrection->requested_break1_end_time, 0, 5) : '—' }}
                            </span>

                            <input type="hidden" name="break1_start_time"
                                value="{{ $attendanceCorrection->requested_break1_start_time }}">
                            <input type="hidden" name="break1_end_time"
                                value="{{ $attendanceCorrection->requested_break1_end_time }}">
                        </td>
                    </tr>
                    <tr>
                        <th>休憩2</th>
                        <td class="time-range">
                            @if ($attendanceCorrection->requested_break2_start_time || $attendanceCorrection->requested_break2_end_time)
                                <span class="time-text">
                                    {{ substr($attendanceCorrection->requested_break2_start_time, 0, 5) }}
                                </span>
                                <span>〜</span>
                                <span class="time-text">
                                    {{ substr($attendanceCorrection->requested_break2_end_time, 0, 5) }}
                                </span>
                            @endif

                            <input type="hidden" name="break2_start_time"
                                value="{{ $attendanceCorrection->requested_break2_start_time }}">
                            <input type="hidden" name="break2_end_time"
                                value="{{ $attendanceCorrection->requested_break2_end_time }}">
                        </td>
                    </tr>

                    <tr class="no-border">
                        <th>備考</th>
                        <td class="remarks-column">
                            <div class="remarks-text">
                                {{ $attendanceCorrection->reason ?? '-' }}
                            </div>
                            <input type="hidden" name="note" value="{{ $attendanceCorrection->reason }}">
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
                        <button class="approve-btn" type="submit">承認</button>
                    @else
                        <span class="approved">承認済み</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
