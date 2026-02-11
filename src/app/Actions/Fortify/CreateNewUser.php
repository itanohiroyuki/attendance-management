<?php

namespace App\Actions\Fortify;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CreateNewUser
{
    public function create(array $input): User
    {
        // Fortifyの配列を「Request」に変換
        $request = Request::create('/register', 'POST', $input);

        // FormRequestを手動実行（←ここが必須）
        $register = RegisterRequest::createFrom($request);
        $register->setContainer(app())->setRedirector(app('redirect'));
        $register->validateResolved(); // ★ unique がここで走る

        $data = $register->validated();

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
