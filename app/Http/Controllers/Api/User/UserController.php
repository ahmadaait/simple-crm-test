<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::when(request()->search, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        $users->appends(['search' => request()->search]);

        return new BaseResponseResource(true, 'List Data Users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        $user->assignRole($request->roles);

        if ($user) {
            return new BaseResponseResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        return new BaseResponseResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $user = User::with('roles')->whereId($id)->first();

        if ($user) {
            return new BaseResponseResource(true, 'Detail Data User!', $user);
        }

        return new BaseResponseResource(false, 'Detail Data User Tidak Ditemukan!', null);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,' . $user->id,
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "" || $request->password == null) {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);
        } else {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);
        }

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        if ($user) {
            return new BaseResponseResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        return new BaseResponseResource(false, 'Data User Gagal Diupdate!', null);
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return new BaseResponseResource(true, 'Data User Berhasil Dihapus!', null);
        }

        return new BaseResponseResource(false, 'Data User Gagal Dihapus!', null);
    }
}
