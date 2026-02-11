@extends('layouts.base')

@section('title', '申請一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/request_list.css') }}">
@endsection

@section('content')
    <div class="request-container">
        <h1 class="page-title">申請一覧</h1>

        <div class="request-tabs">
            <a href="?status=pending" class="tab {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
            <a href="?status=approved" class="tab {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
        </div>

        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <td>
                            {{ $request->status === 0 ? '承認待ち' : '承認済み' }}
                        </td>
                        <td>
                            {{ $request->user->name }}
                        </td>
                        <td>
                            {{ optional($request->attendance->work_date)->format('Y/m/d') }}
                        </td>
                        <td>
                            {{ $request->reason }}
                        </td>
                        <td>
                            {{ $request->created_at->format('Y/m/d') }}
                        </td>
                        <td>
                            <a
                                href="{{ route('attendance.detail', $request->attendance->work_date->format('Y-m-d')) }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
@endsection
