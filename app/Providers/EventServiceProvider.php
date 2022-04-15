<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
		SocialiteWasCalled::class => [
			'SocialiteProviders\\VKontakte\\VKontakteExtendSocialite@handle',
			'SocialiteProviders\\Facebook\\FacebookExtendSocialite@handle',
			'SocialiteProviders\\Google\\GoogleExtendSocialite@handle',
		],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
