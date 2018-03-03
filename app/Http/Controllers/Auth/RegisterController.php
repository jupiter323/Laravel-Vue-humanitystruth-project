<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcome;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|integer|min:10|unique:users',
            'alias' => 'nullable|string|max:32|unique:users',
            'password' => 'required|string|min:6|confirmed'
            // 'g-recaptcha-response' => ['required', new Recaptcha],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        session()->flash('warning', "You're almost done! Please check your email for a verification link..");
        $user = User::create([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'alias' => $data['alias'],
            'password' => bcrypt($data['password']),
        ]);
        
        Mail::to($data['email'])->send(new Welcome($user));
        
        return $user;
    }
}
