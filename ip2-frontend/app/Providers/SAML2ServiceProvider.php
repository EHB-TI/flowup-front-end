<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use \Illuminate\Http\Request;

class SAML2ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
            $messageId = $event->getSaml2Auth()->getLastMessageId();
            // Add your own code preventing reuse of a $messageId to stop replay attacks

            $samlUser = $event->getSaml2User();
            $attributes = $samlUser->getAttributes();
            $email_var = $attributes["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"][0];
            $email = explode("@", $email_var)[0] . "@desideriushogeschool.be";

            // $laravelUser = //find user by ID or attribute
            //if it does not exist create it and go on  or show an error message

            $user = User::where('email', $email)->first();

            $credentials = [
                'email' => $user->email,
                'password' => $user->birthday . $user->email
            ];

            if (Auth::attempt($credentials)) {
                $request = new \Illuminate\Http\Request();
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            Auth::logout();
            Session::save();
        });
    }
}
