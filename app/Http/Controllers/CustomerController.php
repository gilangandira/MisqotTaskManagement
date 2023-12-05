<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\Assets;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        // $data = Customer::paginate(10); // 10 adalah jumlah data per halaman
        // return response()->json($data);
        $data = Customer::query();
        if ($request->keyword) {
            $data = $data->where('customers_name', 'LIKE', '%' . $request->keyword . '%')->
                orWhere('location', 'LIKE', '%' . $request->keyword . '%')->
                orWhere('bandwith', 'LIKE', '%' . $request->keyword . '%')->
                orWhere('ap_ssid', 'LIKE', '%' . $request->keyword . '%');
        }
        $customers = $data->paginate(10);
        return response()->json($customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
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
                'location' => $request->input('location'),
                'start_dates' => $request->input('start_dates'),
            ]);

            if ($customer) {
                return ResponseFormatter::createApi(200, 'success', $customer);
            } else {
                return ResponseFormatter::createApi(201, 'failed');
            }
        } catch (Exception $error) {
            return ResponseFormatter::createApi(201, 'code error' . $error->getMessage());
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Temukan pengguna berdasarkan user_id
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                ], 404);
            }
            // Update informasi profil
            if ($request->has('customers_name')) {
                $customer->customers_name = $request->input('customers_name');
            }
            if ($request->has('ppoe_username')) {
                $customer->ppoe_username = $request->input('ppoe_username');
            }
            if ($request->has('ppoe_password')) {
                $customer->ppoe_password = $request->input('ppoe_password');
            }
            if ($request->has('ip_client')) {
                $customer->ip_client = $request->input('ip_client');
            }
            if ($request->has('ap_ssid')) {
                $customer->ap_ssid = $request->input('ap_ssid');
            }
            if ($request->has('channel_frequensy')) {
                $customer->channel_frequensy = $request->input('channel_frequensy');
            }
            if ($request->has('bandwith')) {
                $customer->bandwith = $request->input('bandwith');
            }
            if ($request->has('subscription_fee')) {
                $customer->subscription_fee = $request->input('subscription_fee');
            }
            if ($request->has('location')) {
                $customer->location = $request->input('location');
            }
            if ($request->has('start_dates')) {
                $customer->start_dates = $request->input('start_dates');
            }
            $customer->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data telah diperbarui',
                'data' => $customer
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

    public function updateImage(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                ], 404);
            }

            if ($request->file('image')) {
                if ($customer->image) {
                    Storage::delete($customer->image);
                }
                $path = $request->file('image')->store('customer-image');
                $customer->image = $path;
            }

            $customer->save();
            DB::commit();


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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json(['message' => 'Customer not found', 'data' => null], 404);
            }

            // Find assets associated with the customer
            $assets = Assets::where('customer_id', $id)->get();

            // Update customer_id in assets (set to 1, for example)
            foreach ($assets as $asset) {
                $asset->update([
                    'customer_id' => 1,
                ]);
            }

            // Delete customer
            $customer->delete();

            DB::commit();

            return response()->json(['message' => 'Success Delete', 'data' => null]);

        } catch (Exception $e) {
            // Log the error message
            Log::error('Error in destroy method: ' . $e->getMessage());

            // Rollback the transaction
            DB::rollback();

            return response()->json([
                'message' => 'Error in destroy method || ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }




    public function totalData()
    {
        $count = Customer::count();

        return response()->json(['total_count' => $count]);
    }

    public function totalBiaya()
    {
        $totalPembiayaan = Customer::sum('subscription_fee');

        return response()->json(['total_pembiayaan' => $totalPembiayaan]);
    }

    public function totalBandwith()
    {
        $totalBandwith = Customer::sum('bandwith');

        return response()->json(['total_bandwith' => $totalBandwith]);
    }
}