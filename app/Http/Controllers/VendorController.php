<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Vendor;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateVendorRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Vendor::paginate(10); // 10 adalah jumlah data per halaman
        return response()->json($data);
    }
    public function Listvendor()
    {
        // $data = Vendor::all(); // 10 adalah jumlah data per halaman
        $data = Vendor::paginate(10);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required',
                    'brand' => 'required',
                    'cpu' => 'required',
                    'cpu_core' => 'required',
                    'ram' => 'required',
                    'lan_ports' => 'required',
                    'lan_speed' => 'required',
                    'wireless_standards' => 'required',
                    'guest_network' => 'required',
                    'power' => 'required',
                ]
            );
            $data = Vendor::create([
                'name' => $request->input('name'),
                'brand' => $request->input('brand'),
                'cpu' => $request->input('cpu'),
                'cpu_core' => $request->input('cpu_core'),
                'ram' => $request->input('ram'),
                'lan_ports' => $request->input('lan_ports'),
                'lan_speed' => $request->input('lan_speed'),
                'wireless_standards' => $request->input('wireless_standards'),
                'guest_network' => $request->input('guest_network'),
                'power' => $request->input('power'),
            ]);

            if ($data) {
                return ResponseFormatter::createApi(200, 'success', $data);
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
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        //
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Temukan pengguna berdasarkan user_id
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan',
                ], 404);
            }
            // Update informasi profil
            if ($request->has('name')) {
                $vendor->name = $request->input('name');
            }
            if ($request->has('brand')) {
                $vendor->brand = $request->input('brand');
            }
            if ($request->has('cpu')) {
                $vendor->cpu = $request->input('cpu');
            }
            if ($request->has('cpu_core')) {
                $vendor->cpu_core = $request->input('cpu_core');
            }
            if ($request->has('ram')) {
                $vendor->ram = $request->input('ram');
            }
            if ($request->has('lan_ports')) {
                $vendor->lan_ports = $request->input('lan_ports');
            }
            if ($request->has('lan_speed')) {
                $vendor->lan_speed = $request->input('lan_speed');
            }
            if ($request->has('wireless_standards')) {
                $vendor->wireless_standards = $request->input('wireless_standards');
            }
            if ($request->has('guest_network')) {
                $vendor->guest_network = $request->input('guest_network');
            }
            if ($request->has('power')) {
                $vendor->power = $request->input('power');
            }

            $vendor->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data telah diperbarui',
                'data' => $vendor
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
        DB::beginTransaction();

        try {
            $customer = Vendor::find($id);

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
}
