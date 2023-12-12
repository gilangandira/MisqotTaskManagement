<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\DataAddedNotification;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class AuthController extends Controller
{

    private $response = [
        'message' => null,
        'data' => null,
    ];

    public function index()
    {
        $data = User::all();

        if ($data) {
            return ResponseFormatter::createApi(200, 'succes', $data);

        } else {
            return ResponseFormatter::createApi(201, 'failed');
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada kesalahan',
                'data' => $validator->errors()
            ]);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['image'] = 'profile-image\1077114.png';
        if ($request->has('role')) {
            $input['role'] = $request->input('role');
        }
        $user = User::create($input);

        // $success['token'] = $user->createToken('auth_token')->plainTextToken;
        // $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'message' => 'Sukses Mendaftar',
            'data' => $input
        ]);
    }


    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if ($request->filled('fcm_token')) {
                Auth::user()->update(['fcm_token' => $request->fcm_token]);
            }

            $auth = Auth::user();
            $success = [
                'token' => $auth->createToken($request->email)->plainTextToken,
                'id' => $auth->id,
                'name' => $auth->name,
                'email' => $auth->email,
                'role' => $auth->role,
                'image' => $auth->image,
                'kelamin' => $auth->kelamin,
                'agama' => $auth->agama,
                'jabatan' => $auth->jabatan,
                'alamat' => $auth->alamat,

            ];
            $auth->notify(new DataAddedNotification());
            return response()->json([
                'success' => true,
                'message' => 'Login sukses',
                'data' => $success
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Salah',
                'data' => null
            ], 201);
        }
    }

    public function me()
    {
        $user = Auth::user();

        $this->response['message'] = 'success';
        $this->response['data'] = $user;

        return response()->json($this->response, 200);
    }

    public function logout()
    {
        auth()->user()->CurrentAccessToken()->delete();
        $this->response['message'] = 'Success';
        return response()->json($this->response, 200);
    }

    /////////////////////CRUD USER//////////////////

    public function show($id)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tidak ditemukan',
                'data' => $id,
            ], 404);
        }

        $data = $user->toArray();
        $data['token'] = $user->api_token;

        return response()->json([
            'success' => true,
            'message' => 'Profil ditemukan',
            'data' => $data,
        ]);
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Temukan pengguna berdasarkan user_id
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                ], 404);
            }
            // Update informasi profil
            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->has('email')) {
                $user->email = $request->input('email');
            }
            if ($request->has('role')) {
                $user->role = $request->input('role');
            }
            if ($request->has('password')) {
                $newPassword = $request->input('password');
                $hashedPassword = bcrypt($newPassword);

                $user->password = $hashedPassword;
                auth()->user()->CurrentAccessToken()->delete();
            }
            if ($request->has('kelamin')) {
                $user->kelamin = $request->input('kelamin');
            }
            if ($request->has('agama')) {
                $user->agama = $request->input('agama');
            }
            if ($request->has('jabatan')) {
                $user->jabatan = $request->input('jabatan');
            }
            if ($request->has('alamat')) {
                $user->alamat = $request->input('alamat');
            }

            if ($request->file('image')) {
                if ($user->image) {
                    Storage::delete($user->image);
                }
                $path = $request->file('image')->store('public/profile-image');
                $user->image = basename($path);
            }


            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data telah diperbarui',
                'data' => $user
            ]);
        } catch (Exception $e) {
            // Jika terjadi kesalahan, catat pesan kesalahan ke log Laravel
            Log::error('Kesalahan dalam metode update: ' . $e->getMessage());

            // Rollback transaksi
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam metode update || ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }



    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'Success Delete', 'data' => null]);
    }

    public function getUserImage($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ResponseFormatter::createApi(404, 'User not found');
        }
        $imagePath = "storage/{$user->image}";
        return response()->file(public_path($imagePath));
    }


}