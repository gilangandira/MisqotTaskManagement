<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Performance;
use Illuminate\Console\Command;
use App\Helpers\ResponseFormatter;

class PerformanceCounter extends Command
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

        foreach ($users as $user) {
            $tasksCompleted = $user->tasks()
                ->where('status_id', '=', 1)
                ->whereMonth('dates', '=', $month)
                ->count();

            Performance::create([
                'user_id' => $user->id,
                'month' => $month,
                'total' => $tasksCompleted,
            ]);
        }

        $this->info('Performance data has been updated successfully.');

        // Alternatively, you can log the information
        // \Log::info('Performance data has been updated successfully.');
    }
}
