<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use Illuminate\Http\Request;
use App\Models\ConditionAssets;

class ConditionAssetsAssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            view(
                'ConditionAssets.index_ConditionAssets',
                [
                    "title" => "List ConditionAssets",
                    "asset" => Assets::all(),
                    "categories" => ConditionAssets::all()
                ]
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('CRUD.create_asset', [
            'title' => 'Tambah Asset',
            // 'active' => 'create_asset'
            'categories' => ConditionAssets::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
            ]
        );

        $data = [
            'name' => $request->input('name'),
        ];

        ConditionAssets::create($data);
        if ($data) {
            // Jika data berhasil disimpan
            echo '<script>alert("Data berhasil disimpan");</script>';
        } else {
            // Jika terjadi kesalahan saat menyimpan data
            echo '<script>alert("Terjadi kesalahan saat menyimpan data");</script>';
        }
        return redirect('/categories')->with('Succes');
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

    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ConditionAssets::where('id', $id)->delete();
        return response()->redirect('/categories');

    }
}