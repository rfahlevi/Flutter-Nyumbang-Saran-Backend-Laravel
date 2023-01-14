<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
          ]);
        
          if($validator->fails()) {
            return response()->json(
                $validator->errors(), 500
            );
          }

          User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
          ]);

          $user = User::where('email', $request->email)->first();

          $tokenResult = $user->createToken('token')->plainTextToken;

          return response()->json([
            'token_type' => 'Bearer',
            'token' => $tokenResult,
            'user' => $user,
          ]);

    }

    public function login(Request $request) 
    {        
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau Password salah'
            ], 500);
        }

        $user = User::where('email', $request->email)->first();

        if(! Hash::check($request->password, $user->password, [])) {
            throw new \Exception('Invalid Credentials');
        }

        $tokenResult = $user->createToken('token')->plainTextToken;

          return response()->json([
            'token_type' => 'Bearer',
            'token' => $tokenResult,
            'user' => $user,
          ]);

    }

    public function fetch(Request $request) 
    {
        $user = User::with('departemen')
        ->where('id', Auth::user()->id)
        ->get();

        return response()->json([
            'user' => $user,
            'message' => "Data user berhasil di ambil",
            'status' => true
        ]);
    }

    public function updateProfile(Request $request) 
    {
       
        $user = Auth::user();

        $item = User::findOrFail($user->id);

        //check if foto profil is uploaded
        if($request->isNotFilled('foto_profil')) {
            //upload new file
            $foto_profil = $request->file('foto_profil');
            $foto_profil->storeAs('foto-profil', $foto_profil->hashName());

            //delete old file
            Storage::delete('foto-profil/'. $user->foto_profil);

            $item->update([
                'foto_profil' => $foto_profil->hashName(),
                'name' => $request->name,
                'tanggal_lahir' => date('Y-m-d', strtotime($request->input('tanggal_lahir'))),
                'jenis_kelamin' => $request->jenis_kelamin,
                'departemen_id' => $request->departemen_id,
                'nik' => $request->nik,
                'email' => $request->email,
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'User berhasil diupdate beserta foto profil',
                'success' => true,
            ]);
        } else {
            $item->update([
                'name' => $request->name,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'departemen_id' => $request->departemen_id,
                'nik' => $request->nik,
                'email' => $request->email,
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'User berhasil diupdate',
                'success' => true,
            ]);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->currentAccessToken()->delete();
    
        return response()->json([
           'status' => $token,
            'message' => 'Token dihapus, berhasil logout'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $users = User::join('departemen', 'users.departemen_id', '=', 'departemen.id')
                    ->select('users.id', 'users.foto_profil', 'users.name', 'users.nik', 'users.email', 'departemen.dept_name', 'users.tanggal_lahir', 'users.jenis_kelamin', 'users.roles')
                    ->get();

    return response()->json([
        'data' => $users, 
        'message' => 'Berhasil menampilkan semua data user', 
        'success' => true
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
        $validator =Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;
        
        return response()->json(['data' => $user, 'token_type' => 'Bearrer', 'token' => $token]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $user = User::join('departemen', 'users.departemen_id', '=' ,'departemen.id')
        ->select('users.id', 'users.foto_profil', 'users.name', 'users.nik', 'departemen.dept_name', 'users.tanggal_lahir', 'users.jenis_kelamin', 'users.email', 'users.roles')->find($id);
        if(is_null($user)) {
            return response()->json([
                'message' => 'User tidak ditemukan',
                'status' => 'Failed'
            ]);
        }

        return response()->json(
            $user
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
       Validator::make($request->all(),[
            'nik' => 'required|unique:users',
            'departemen_id' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'foto_profil' => 'required|mimes:jpeg,jpg,png,gif,svg|max:1028',
        ]);

        $item = User::findOrFail($user->id);

        //check if foto profil is uploaded
        if($request->isNotFilled('foto_profil')) {
            //upload new file
            $foto_profil = $request->file('foto_profil');
            $foto_profil->storeAs('foto-profil', $foto_profil->hashName());

            //delete old file
            Storage::delete('foto-profil/'. $user->foto_profil);

            $item->update([
                'nik' => $request->nik,
                'departemen_id' => $request->departemen_id,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'foto_profil' => $foto_profil->hashName(),
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'User berhasil diupdate beserta foto profil',
                'success' => true,
            ]);
        } else {
            $item->update([
                'nik' => $request->nik,
                'departemen' => $request->departemen_id,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
            ]);

            return response()->json([
                'data' => $item,
                'message' => 'User berhasil diupdate',
                'success' => true,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User berhasil di hapus']);
    }
}
