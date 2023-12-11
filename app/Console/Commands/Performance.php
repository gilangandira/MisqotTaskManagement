<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Performance;
use Illuminate\Console\Command;

class PerformanceMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:performance';

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
        $users = User::all();
        $month = Carbon::now()->month;
        // Loop melalui setiap pengguna
        foreach ($users as $user) {
            // Hitung jumlah tugas yang diselesaikan oleh pengguna berdasarkan status
            $tasksCompleted = $user->tasks()
                ->where('status_id', '=', 1) // Gantilah '1' dengan ID status yang sesuai
                ->whereMonth('dates', '=', $month) // Gantilah $bulan dengan bulan yang diinginkan (misalnya, 1 untuk Januari, 2 untuk Februari, dst.)
                ->count(); // Gantilah '1' dengan ID status yang sesuai

            // Simpan data ke dalam tabel 'performance'



            $task = Performance::create([
                'user_id' => $user->id,
                'month' => $month, // Gunakan bulan saat ini sebagai contoh, Anda mungkin ingin menyesuaikannya
                'total' => $tasksCompleted,
            ]);

            echo $task + "Has Created";
        }

    }
}
