<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Assets;
use App\Models\Vendor;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CategoryAssets;
use App\Models\ConditionAssets;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\Fcm\FcmChannel;
use App\Notifications\DataAddedNotification;

class AssetsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = Assets::with(['condition', 'user', 'vendor', 'customer', 'category']);
        if($request->keyword) {
            $data = $data->where('nama_aset', 'LIKE', '%'.$request->keyword.'%')->
                orWhere('location', 'LIKE', '%'.$request->keyword.'%')->
                orWhereHas('condition', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'LIKE', '%'.$request->keyword.'%');
                })->
                orWhereHas('vendor', function ($vendorQuery) use ($request) {
                    $vendorQuery->where('name', 'LIKE', '%'.$request->keyword.'%');
                })->orWhereHas('category', function ($vendorQuery) use ($request) {
                    $vendorQuery->where('name', 'LIKE', '%'.$request->keyword.'%');
                })->orWhereHas('customer', function ($vendorQuery) use ($request) {
                    $vendorQuery->where('customers_name', 'LIKE', '%'.$request->keyword.'%');
                });
        }
        $assets = $data->paginate(10);

        return response()->json($assets);
    }

    public function condition() {
        $data = ConditionAssets::all();
        return response()->json($data);
    }
    public function category() {
        $data = CategoryAssets::all();
        return response()->json($data);
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
            $request->validate(
                [
                    'category_id' => 'required',
                    'condition_id' => 'required',
                    'vendor_id' => 'required',
                    'nama_aset' => 'required',
                    'description' => 'required',
                    'location' => 'required',
                    'serial_number' => 'required',
                    'serial_assets' => 'required',
                    'price' => 'required',
                    'date_buyed' => 'required',
                ]
            );
            $user = Auth::user();
            $customer = false;
            $customer = $request->input('newCustomer');
            if($customer === 'true') {
                $request->validate([
                    'customers_name' => 'required',
                    'ppoe_username' => 'required',
                    'ppoe_password' => 'required',
                    'ip_client' => 'required',
                    'ap_ssid' => 'required',
                    'channel_frequensy' => 'required',
                    'bandwith' => 'required',
                    'subscription_fee' => 'required',
                    'location' => 'required',
                    'start_dates' => 'required',
                ]);
                $customer = Customer::create([
                    'customers_name' => $request->input('customers_name'),
                    'ppoe_username' => $request->input('ppoe_username'),
                    'ppoe_password' => $request->input('ppoe_password'),
                    'ip_client' => $request->input('ip_client'),
                    'ap_ssid' => $request->input('ap_ssid'),
                    'channel_frequensy' => $request->input('channel_frequensy'),
                    'bandwith' => $request->input('bandwith'),
                    'subscription_fee' => $request->input('subscription_fee'),
                    'location' => $request->input('cuslocation'),
                    'start_dates' => $request->input('start_dates'),
                ]);
                $customer_id = $customer->id;
            } else {
                $customer_id = 1;
            }


            $data = [
                'user_id' => $user->id,
                'category_id' => $request->input('category_id'),
                'condition_id' => $request->input('condition_id'),
                'customer_id' => $customer_id,
                'vendor_id' => $request->input('vendor_id'),
                'nama_aset' => $request->input('nama_aset'),
                'description' => $request->input('description'),
                'location' => $request->input('location'),
                'serial_number' => $request->input('serial_number'),
                'serial_assets' => $request->input('serial_assets'),
                'price' => $request->input('price'),
                'date_buyed' => $request->input('date_buyed'),
            ];

            Assets::create($data);
            // Kirim notifikasi setelah data berhasil ditambahkan
            $users = User::where('fcm_token', '!=', null)->get();
            foreach($users as $user) {
                $user->notify(new DataAddedNotification);
            }
            return response()->json([
                'success' => true,
                'message' => 'Data telah di tambahkan',
                'data' => $data,
                'customer' => $customer
            ]);

        } catch (\Exception $e) {
            // Jika terjadi kesalahan, catat pesan kesalahan ke log Laravel
            Log::error('Kesalahan dalam metode Menambah Data: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam metode Penambahan Data || '.$e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();
        try {
            // Temukan pengguna berdasarkan user_id
            $assets = Assets::find($id);
            $user = Auth::user();
            if(!$assets) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                ], 404);
            }

            $customer = 1;
            $customer = $request->input('newCustomer');
            if($customer == 1) {
                $customer = Customer::find($assets->customer_id);
                if(!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pengguna tidak ditemukan',
                    ], 404);
                }
                // Update informasi Customers
                if($request->has('customers_name')) {
                    $customer->customers_name = $request->input('customers_name');
                }
                if($request->has('ppoe_username')) {
                    $customer->ppoe_username = $request->input('ppoe_username');
                }
                if($request->has('ppoe_password')) {
                    $customer->ppoe_password = $request->input('ppoe_password');
                }
                if($request->has('ip_client')) {
                    $customer->ip_client = $request->input('ip_client');
                }
                if($request->has('ap_ssid')) {
                    $customer->ap_ssid = $request->input('ap_ssid');
                }
                if($request->has('channel_frequensy')) {
                    $customer->channel_frequensy = $request->input('channel_frequensy');
                }
                if($request->has('bandwith')) {
                    $customer->bandwith = $request->input('bandwith');
                }
                if($request->has('subscription_fee')) {
                    $customer->subscription_fee = $request->input('subscription_fee');
                }
                if($request->has('location')) {
                    $customer->location = $request->input('location');
                }
                if($request->has('start_dates')) {
                    $customer->start_dates = $request->input('start_dates');
                }
                $customer->save();
                DB::commit();
            } else if($customer == 2) {
                $request->validate([
                    'customers_name' => 'required',
                    'ppoe_username' => 'required',
                    'ppoe_password' => 'required',
                    'ip_client' => 'required',
                    'ap_ssid' => 'required',
                    'channel_frequensy' => 'required',
                    'bandwith' => 'required',
                    'subscription_fee' => 'required',
                    'location' => 'required',
                    'start_dates' => 'required',
                ]);
                $customer = Customer::create([
                    'customers_name' => $request->input('customers_name'),
                    'ppoe_username' => $request->input('ppoe_username'),
                    'ppoe_password' => $request->input('ppoe_password'),
                    'ip_client' => $request->input('ip_client'),
                    'ap_ssid' => $request->input('ap_ssid'),
                    'channel_frequensy' => $request->input('channel_frequensy'),
                    'bandwith' => $request->input('bandwith'),
                    'subscription_fee' => $request->input('subscription_fee'),
                    'location' => $request->input('cuslocation'),
                    'start_dates' => $request->input('start_dates'),
                ]);
                $assets->customer_id = $customer->id;
            } else {
                if($request->has('customer_id')) {
                    $assets->customer_id = $request->input('customer_id');
                }
            }
            // Update informasi Assets
            $assets->user_id = $user->id;
            if($request->has('category_id')) {
                $assets->category_id = $request->input('category_id');
            }
            if($request->has('condition_id')) {
                $assets->condition_id = $request->input('condition_id');
            }
            if($request->has('vendor_id')) {
                $assets->vendor_id = $request->input('vendor_id');
            }
            if($request->has('nama_aset')) {
                $assets->nama_aset = $request->input('nama_aset');
            }
            if($request->has('description')) {
                $assets->description = $request->input('description');
            }
            if($request->has('location')) {
                $assets->location = $request->input('location');
            }
            if($request->has('serial_number')) {
                $assets->serial_number = $request->input('serial_number');
            }
            if($request->has('serial_assets')) {
                $assets->serial_assets = $request->input('serial_assets');
            }
            if($request->has('price')) {
                $assets->price = $request->input('price');
            }
            if($request->has('date_buyed')) {
                $assets->date_buyed = $request->input('date_buyed');
            }
            $assets->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data telah diperbarui',
                'data' => [$assets, $customer]
            ]);


        } catch (Exception $e) {
            // Jika terjadi kesalahan, catat pesan kesalahan ke log Laravel
            Log::error('Kesalahan dalam metode update: '.$e->getMessage());

            // Rollback transaksi
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam metode update || '.$e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $assets = Assets::find($id);
        $assets->delete();
        return response()->json(['message' => 'Success Delete', 'data' => null]);
    }
    public function totalAssets() {
        $assets = Assets::all();

        foreach($assets as $asset) {
            $total = $asset->where('status_id', '=', 3)->count();

            // $total = $asset->where('price');
        }

        return response()->json(['message' => 'Success Delete', 'data' => $total]);
    }



}