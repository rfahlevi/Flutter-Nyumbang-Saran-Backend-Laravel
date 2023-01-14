<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departemen = Departemen::latest()->get();

        return response()->json(['data' => $departemen]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dept_name' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $departemen = Departemen::create([
            'dept_name' => $request->dept_name,
        ]);

        return response()->json(['data' => $departemen]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departemen = Departemen::find($id);
        if(is_null($departemen)) {
            return response()->json([
                'message' => 'Departemen tidak ditemukan',
                'success' => false
            ]);
        }

        return response()->json(
            ['data' => $departemen,
            'message' => 'Berhasil mendapatkan saran',]
        );
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
    public function update(Request $request, Departemen $departeman)
    {
        $validator = Validator::make($request->all(),[
            'dept_name' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $departeman->dept_name = $request->dept_name;
        $departeman->save();

        return response()->json(['data' => $departeman, 'message' => 'Departemen berhasil di update']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Departemen $departeman)
    {
        $departeman->delete();

        return response()->json(['message' => 'Departemen berhasil dihapus']);
    }
}
