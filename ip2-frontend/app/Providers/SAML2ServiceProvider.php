<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Arr;

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

            $user = $event->getSaml2User();
            $attributes = $user->getAttributes();
            var_dump($attributes["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"][0]);
            $email = $attributes["http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"][0];

            // $laravelUser = //find user by ID or attribute
            //if it does not exist create it and go on  or show an error message
            $frontUser = User::where('email', $email)->first();
            Auth::login($frontUser);
        });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            Auth::logout();
            Session::save();
        });
    }
}
