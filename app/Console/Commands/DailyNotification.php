<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\DailyNotification;

class DailyNotificationScedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification Daily';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('fcm_token', '!=', null)->get();
        foreach ($users as $user) {
            $user->notify(new DailyNotification);
            echo "Daily Notifiaction Already Sended to" + $user->name;
        }
    }
}
