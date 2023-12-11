<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DataAddedNotification;
use Illuminate\Console\Command;
use App\Notifications\DailyNotification;

class DailyNotificationMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('fcm_token', '!=', null)->get();
        foreach ($users as $user) {
            $user->notify(new DataAddedNotification);
        }
    }
}
