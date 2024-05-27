<?php

namespace App\Listeners;

use App\Events\NewCustomerRegisterEvent;
use App\Models\User;
use App\Notifications\NewCustomerRegisterNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewCustomerRegisterListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NewCustomerRegisterEvent $event)
    {
        User::get()->each(function($admin) use ($event){
            $admin->notify(new NewCustomerRegisterNotification($event->data));
        });
    }
}
