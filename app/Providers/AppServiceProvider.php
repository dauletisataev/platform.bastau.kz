<?php

namespace App\Providers;

use App\BusinessTrainer;
use App\Observers\BTrainerObserver;
use App\Group;
use App\Observers\GroupObserver;
use App\Observers\ParticipantObserver;
use App\Observers\UserObserver;
use App\User;
use App\Participant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use \Validator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        User::observe(new UserObserver());
        Group::observe(new GroupObserver());
        Participant::observe(ParticipantObserver::class);
        BusinessTrainer::observe(BTrainerObserver::class);

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            if (!in_array($format, $allow)) {
                return false;
            }

            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }
            return true;
        });


        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {

            $validator = \Validator::make(['image' => $value], [
                'image' => 'base64image'
            ]);

            if ($validator->fails()) return true;

            $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
            if (in_array($type, $parameters)) {
                return true;
            }
            return false;
        });

        Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
            return str_replace(':values',join(",",$parameters),$message);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
