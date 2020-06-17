<?php

namespace App\Listeners;

use App\Events\NewUserChat;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserChatListener
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
     * @param  NewUserChat  $event
     * @return void
     */
    public function handle(NewUserChat $event)
    {
        //
    }
}
