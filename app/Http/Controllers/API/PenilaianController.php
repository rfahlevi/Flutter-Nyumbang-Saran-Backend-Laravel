<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $penilaian = Penilaian::with(['saran','saran.user' => function($query) {
            $query->select('id', 'name', 'departemen_id', 'jenis_kelamin', 'tanggal_lahir', 'email');
        }])->select('penilaian.id', 'penilaian.saran_id', 'penilaian.status', 'penilaian.poin')
                    ->get();
       

        if($penilaian->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada penilaian'
            ]);
        }

        return response()->json([
            'data' => $penilaian,
            'message' => 'Berhasil mendapatkan semua data penilaian',
            'success' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'saran_id' => 'required',
            'status' => 'required',
            'poin' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        //Create Penilaian
        $penilaian = Penilaian::create([
            'saran_id' => $request->saran_id,
            'status' => $request->status,
            'poin' => $request->poin
        ]);

        return response()->json([
            'data' => $penilaian,
            'message' => 'Penilaian berhasil dibuat',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $penilaian = Penilaian::with(['saran','saran.user' => function($query) {
            $query->select('id', 'name', 'departemen_id', 'jenis_kelamin', 'tanggal_lahir', 'email');
        }])->select('penilaian.id', 'penilaian.saran_id', 'penilaian.status', 'penilaian.poin')->find($id);

        if(is_null($penilaian)) {
            return response()->json([
                'message' => 'Penilaian tidak ditemukan',
                'success' => false
            ]);
        }

        return response()->json([
            'data' => $penilaian,
            'message' => 'Berhasil mendapatkan semua data penilaian',
            'success' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Penilaian $penilaian)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'poin' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $penilaian->status = $request->status;
        $penilaian->poin = $request->poin;
        $penilaian->save();

        return response()->json([
            'data' => $penilaian,
            'message' => 'Penilaian berhasil di update',
            'success' => true
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penilaian $penilaian)
    {
        $penilaian->delete();

        return response()->json([
            'message' => 'Penilaian berhasil di hapus'
        ]);
    }
}
