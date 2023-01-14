<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Saran;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saran = Saran::with('user.departemen')
            ->get();

        if ($saran->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada saran'
            ]);
        }

        return response()->json([
            'data' => $saran,
            'message' => 'Berhasil menampilkan semua data saran',
            'success' => true
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
            'no_saran' => 'required',
            'user_id' => 'required',
            // 'departemen_id' => 'required',
            'kondisi_awal' => 'required',
            'usulan' => 'required',
            'file_pendukung' => 'required|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        //Upload file
        $file = $request->file('file_pendukung');
        $file->storeAs('file-pendukung', $file->hashName());


        //Create saran
        $saran = Saran::create([
            'no_saran' => $request->no_saran,
            'user_id' => $request->user_id,
            // 'departemen_id' => $request->departemen_id,
            'kondisi_awal' => $request->kondisi_awal,
            'usulan' => $request->usulan,
            'file_pendukung' => $file->hashName()
        ]);

        return response()->json([
            'data' => $saran,
            'message' => 'Saran berhasil dibuat',
            'success' => true,
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
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message' => 'Saran tidak ditemukan',
                'success' => false
            ]);
        }

        $saran = Saran::with('user.departemen')->get();

        return response()->json([
            'data' => $saran,
            'message' => 'Berhasil mendapatkan saran',
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
    public function update(Request $request, Saran $saran)
    {
        Validator::make($request->all(), [
            'kondisi_awal' => 'required',
            'usulan' => 'required',
            'file_pendukung' => 'required|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $item = Saran::findOrFail($saran->id);

        //check if file is uploaded
        if ($request->hasFile('file_pendukung')) {
            //upload new file
            $file = $request->file('file_pendukung');
            $file->storeAs('file-pendukung', $file->hashName());

            //delete old file
            Storage::delete('file-pendukung/' . $saran->file_pendukung);

            $item->update([
                'kondisi_awal' => $request->kondisi_awal,
                'usulan' => $request->usulan,
                'file_pendukung' => $file->hashName(),
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'Saran berhasil di update beserta file',
                'success' => true
            ]);
        } else {
            $item->update([
                'kondisi_awal' => $request->kondisi_awal,
                'usulan' => $request->usulan,
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'Saran berhasil di update',
                'success' => true
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Saran $saran)
    {
        //Delete File
        Storage::delete('file-pendukung/' . $saran->file_pendukung);

        $saran->delete();

        return response()->json([
            'message' => 'Saran berhasil dihapus',
            'success' => true
        ]);
    }
}
