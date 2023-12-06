<?php

namespace App\Http\Controllers;

use App\Notifications\DataTaskNotification;
use Exception;
use App\Models\SLA;
use App\Models\Task;
use App\Models\User;
use App\Models\Customer;
use App\Models\TimeTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\AddTaskNotification;
use App\Notifications\DataAddedNotification;

class TaskController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = Task::with(['sla', 'status', 'assets', 'condition', 'users', 'timetracker']);
        if($request->keyword) {
            $data = $data->where('name', 'LIKE', '%'.$request->keyword.'%')->
                orWhere('location', 'LIKE', '%'.$request->keyword.'%')->
                orWhereHas('status', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'LIKE', '%'.$request->keyword.'%');
                })->
                orWhereHas('timetracker', function ($userQuery) use ($request) {
                    $userQuery->where('due_dates', 'LIKE', '%'.$request->keyword.'%');
                });
        }
        // $task = $data->orderBy(function ($query) {
        //     $query->select('due_dates')
        //         ->from('timetracker')
        //         ->whereColumn('tasks.timetracker_id', 'timetracker.id');
        // }, 'asc')->paginate(10);
        $task = $data->orderBy('dates', 'asc')->paginate(10);
        return response()->json($task);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
            $request->validate([
                'name' => 'required',
                'assets_id' => 'required',
                'sla_id' => 'required',
                'description' => 'required',
                'location' => 'required',
            ]);
            $now = Carbon::now();
            $sla = SLA::find($request->input('sla_id'));
            $time = TimeTracker::create([
                'due_dates' => $now->addDay($sla->waktu),
            ]);
            $task = Task::create([
                'name' => $request->input('name'),
                'assets_id' => $request->input('assets_id'),
                'sla_id' => $request->input('sla_id'),
                'timetracker_id' => $time->id,
                'description' => $request->input('description'),
                'location' => $request->input('location'),
            ]);

            $job = Task::find($task->id);
            $users = $request->json('user_id');

            $job->users()->attach($users);
            $notificationData = [
                'body' => 'Your notification body here', // Replace with the actual body data
            ];
            $users = User::whereIn('id', $users)->where('fcm_token', '!=', null)->get();
            foreach($users as $user) {
                $user->notify(new DataTaskNotification);
            }
            if($task) {
                return ResponseFormatter::createApi(200, 'success', $task, $job);
            } else {
                return ResponseFormatter::createApi(201, 'failed');
            }
        } catch (Exception $error) {
            return ResponseFormatter::createApi(201, 'code error'.$error->getMessage());
        }
    }


    public function update(Request $request, $id) {
        try {

            $task = Task::findOrFail($id);

            // Update informasi Task
            if($request->has('name')) {
                $task->name = $request->input('name');
            }
            if($request->has('sla_id')) {
                $task->sla_id = $request->input('sla_id');
            }
            if($request->has('description')) {
                $task->description = $request->input('description');
            }
            if($request->has('location')) {
                $task->location = $request->input('location');
            }

            $task->save();
            DB::commit();
            $job = Task::find($task->id);
            if($request->has('user_id')) {
                $users = $request->json('user_id');
                $job->users()->sync($users);
            }
            $data = Task::where('id', '=', $task->id)->get();
            if($task) {
                return ResponseFormatter::createApi(200, 'success', $data);
            } else {
                return ResponseFormatter::createApi(201, 'failed');
            }
        } catch (Exception $error) {
            return ResponseFormatter::createApi(404, 'code error'.$error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */public function destroy($id) {
        // try {
        //     $task = Customer::findOrFail($id);
        //     $task->delete();

        //     return ResponseFormatter::createApi(200, 'success destroy data');
        // } catch (Exception $error) {
        //     return ResponseFormatter::createApi(202, 'failed ' . $error->getMessage());
        // }

        $task = Task::find($id);
        $job = Task::find($task->id);
        $job->users()->detach();
        $task->delete();
        return response()->json(['message' => 'Success Delete', 'data' => null]);
    }


    public function do (Request $request, $id) {
        try {
            $task = Task::findOrFail($id);
            $time = TimeTracker::findOrFail($task->timetracker_id);
            $now = Carbon::now();
            if($request->has('status_id')) {
                $task->status_id = $request->input('status_id');
            }
            if($task->status_id == 4) {
                $time->update([
                    'due_dates' => $now->addDay(30),
                ]);
            } else if($task->status_id == 2) {
                $time->update([
                    'due_dates' => $now->addDay($task->SLA->waktu),
                ]);
            } else {
                $time->update([
                    'due_dates' => $now->addDay($now),
                ]);
            }
            $task->save();
            DB::commit();
            ////////Duration////////
            // Konversi ke integer (timestamp) dalam satuan detik
            $timestamp = $time->due_dates->diffInSeconds(Carbon::create(now()));
            $time->update([
                'timer' => $timestamp
            ]);
            if($task) {
                return ResponseFormatter::createApi(200, 'success', $task);
            } else {
                return ResponseFormatter::createApi(201, 'failed');
            }
        } catch (Exception $error) {
            return ResponseFormatter::createApi(201, 'code error'.$error->getMessage());
        }
    }
    public function start(Request $request, $id) {
        $task = TimeTracker::find($id);
        // Mendefinisikan tanggal dan waktu
        $tanggalWaktu = Carbon::create($task->due_dates);
        // Konversi ke integer (timestamp) dalam satuan detik
        $timestamp = $tanggalWaktu->diffInSeconds(Carbon::create(now()));
        $task->update([
            'timer' => $timestamp
        ]);

        return ResponseFormatter::createApi(200, 'success', $task);

    }
    public function end(Request $request, $id) {
        $task = TimeTracker::find($id);

        if(!$task) {
            return ResponseFormatter::createApi(404, 'Task not found', null);
        }

        $startTime = Carbon::parse($task->start_time);
        $currentTime = now();
        $durationInSeconds = $startTime->diffInSeconds($currentTime);

        $task->update([
            'seconds' => $task->seconds + $durationInSeconds,
            'runing_time?' => false,
            'end_time' => now()
        ]);



        return ResponseFormatter::createApi(200, 'success', $task);
    }


    public function jobuser($taskId) {
        $users = Task::find($taskId)->users;
        return response()->json($users);
    }
    public function sla() {
        $data = SLA::all();
        return response()->json($data);
    }

    public function performance() {
        // Ambil semua pengguna
        $users = User::all();

        // Array untuk menyimpan jumlah tugas yang diselesaikan oleh setiap pengguna berdasarkan status
        $tasksCompletedByUser = [];

        // Loop melalui setiap pengguna
        foreach($users as $user) {
            // Hitung jumlah tugas yang diselesaikan oleh pengguna berdasarkan status
            $tasksCompleted = $user->tasks()->where('status_id', '=', 1)->count(); // Gantilah '1' dengan ID status yang sesuai

            // Tambahkan hasil ke array
            $tasksCompletedByUser[$user->name] = $tasksCompleted;
        }

        // Sekarang, $tasksCompletedByUser berisi jumlah tugas yang diselesaikan oleh setiap pengguna berdasarkan status
        return ResponseFormatter::createApi(200, 'success', $tasksCompletedByUser);
    }
}