<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ReCaptcha;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/app';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'g-recaptcha-response' => ['required', new ReCaptcha()],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $token = md5(bcrypt($data['password']) . time() . $data['name'] . rand(0, 9999999));
        
        Mail::send('emails.signup', ['name' => $data['name'], 'token' => $token], function ($message) use ($data) {
            if (env('APP_DEBUG')) {
                $emailTo = env('EMAIL_DEBUG');
            } else {
                $emailTo = $data['email'];
            }

            $message->to($emailTo, $data['name'])->subject('Wellcome to the Coinmonkey! Please, activate your account.');
        });
        
        return User::create([
            'name' => $data['name'],
            'confirm_token' => $token,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
