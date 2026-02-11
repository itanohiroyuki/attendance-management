@extends('layouts.base')

@section('title', 'メール確認画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
    <div class="email-content">
        <p class="message">
            登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p class="message">メール認証を完了してください
        </p>
        <a class="access-btn" href="http://localhost:8025/">認証はこちらから</a>
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button class="resend-btn">認証メールを再送する</button>
        </form>
        @if (session('status'))
            <p class="status-message">{{ session('status') }}</p>
        @endif
    </div>
@endsection
