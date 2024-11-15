<?php

namespace App\Http\Controllers\Api\Role;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResponseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::when(request()->search, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->search . '%');
        })->with('permissions')->latest()->paginate(5);

        $roles->appends(['search' => request()->search]);

        return new BaseResponseResource(true, 'List Data Roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        if ($role) {
            return new BaseResponseResource(true, 'Data Role Berhasil Disimpan!', $role);
        }

        return new BaseResponseResource(false, 'Data Role Gagal Disimpan!', null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role) {
            return new BaseResponseResource(true, 'Detail Data Role!', $role);
        }

        return new BaseResponseResource(false, 'Detail Data Role Tidak Ditemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        /**
         * validate request
         */
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($role) {
            return new BaseResponseResource(true, 'Data Role Berhasil Diupdate!', $role);
        }

        return new BaseResponseResource(false, 'Data Role Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->delete()) {
            return new BaseResponseResource(true, 'Data Role Berhasil Dihapus!', null);
        }

        return new BaseResponseResource(false, 'Data Role Gagal Dihapus!', null);
    }

    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        $roles = Role::latest()->get();

        return new BaseResponseResource(true, 'List Data Roles', $roles);
    }
}
