@extends('layouts.base')

@section('title', 'スタッフ一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('/css/admin/staff_list.css') }}">
@endsection

@section('content')
    <div class="staff-container">
        <h1 class="page-title">スタッフ一覧</h1>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>

                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('admin.attendance_staff', $user->id) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
@endsection
