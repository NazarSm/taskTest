<?php


namespace App\Http\Logger;


use App\User;
use Illuminate\Support\Facades\Storage;

class UserRegistrationLogger
{

    public function timeUserRegistration(User $user)
    {
        $file = sprintf('%s/%s', User::PATH_REGISTRATION_FILE, User::NAME_REGISTRATION_FILE);
        $content = sprintf('Користувач - %s.  Був зареєстрований: %s', $user->name, $user->created_at );

        if(Storage::exists($file)){
            Storage::prepend($file, $content);
        }else {
            Storage::put($file, $content);
        }
    }

}
