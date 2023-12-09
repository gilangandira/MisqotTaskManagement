<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;

class CheckerTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checker:statustask';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checker Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('dates', '!=', null)->get();

        foreach ($users as $user) {
            $tanggalUser = Carbon::parse($user->dates); // Menggunakan Carbon untuk memudahkan manipulasi tanggal

            // Periksa apakah tanggal sudah lewat
            if ($tanggalUser->isPast()) {
                // Update status_id sesuai kebutuhan Anda
                $user->update(['status_id' => 4]);
            }
        }
    }
}
