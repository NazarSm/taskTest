<?php

namespace App\Http\Controllers\Auth;

use App\Http\Logger\UserRegistrationLogger;
use App\Http\Sender\EmailSender;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    const FROM = 'nazarsommelier@gmail.com';
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
    public $emailSender;
    public $userRegistrationLog;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EmailSender $emailSender, UserRegistrationLogger $userRegistrationLog)
    {
        $this->middleware('guest');
        $this->emailSender = $emailSender;
        $this->userRegistrationLog = $userRegistrationLog;
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        $from = self::FROM;
        $to = $user->email;
        $subject = 'Підтвердження email';
        $text = 'Будь ласка,перейдіть за посиланням :' . url("register/confirm/{$user->token}");

        $this->emailSender->send($from, $to, $subject, $text);
        $this->userRegistrationLog->timeUserRegistration($user);

        $request->session()->flash('message', 'На вашу адреу було відправлене повідомлення для підвтердження');
        return back();
    }

    public function confirmEmail(Request $request, $token)
    {

        User::whereToken($token)->firstOrFail()->confirmEmail();
        $request->session()->flash('message', 'Email підтверджено. Зайдіть під своїм іменем');
        return redirect('login');
    }

}
