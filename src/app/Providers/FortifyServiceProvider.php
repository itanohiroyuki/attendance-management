<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            fn() =>
            new class implements LoginResponse {
                public function toResponse($request)
                {
                    if (auth()->user()->is_admin) {
                        return redirect('/admin/attendance/list');
                    }
                    return redirect('/attendance');
                }
            }
        );

        $this->app->singleton(
            RegisterResponse::class,
            fn() =>
            new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect('/email/verify');
                }
            }
        );
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function (Request $request) {
            if ($request->is('admin/*')) {
                return view('admin.login');
            }

            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        $this->app->bind(
            FortifyLoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );
    }
}
