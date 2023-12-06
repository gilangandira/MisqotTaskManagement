<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Performance;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class PerformanceController extends Controller {



    public function index(Request $request) {
        $perform = Performance::query();

        if($request->has('keyword')) {
            $perform->where('user_id', 'LIKE', '%'.$request->keyword.'%');
        }

        $perform = $perform->orderBy('month', 'asc')->get();

        return ResponseFormatter::createApi(200, 'success', $perform);
    }
    public function store(Request $request) {
        $users = User::all();

        $month = $request->input('month');
        // Loop melalui setiap pengguna
        foreach($users as $user) {
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

        }

        return ResponseFormatter::createApi(200, 'success', $task);
    }

}
