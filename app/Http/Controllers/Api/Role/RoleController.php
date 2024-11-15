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
        $perPage = request()->perPage ?: 2;
        $sort = request()->sort ?: 'created_at';
        $order = request()->order ?: 'desc';

        $roles = Role::when(request()->search, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->search . '%');
        })->with('permissions');

        $roles = $roles->orderBy($sort, $order)->paginate($perPage);

        $roles->appends([
            'search' => request()->search,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order
        ]);

        return new BaseResponseResource(true, 'List Data Roles', $roles, 200);
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
            return new BaseResponseResource(true, 'Data Role successfully saved!', $role, 200);
        }

        return new BaseResponseResource(false, 'Data Role failed to save!', null, 400);
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
            return new BaseResponseResource(true, 'Detail Data Role!', $role, 200);
        }

        return new BaseResponseResource(false, 'Detail Data Role Not Found!', null, 404);
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
            return new BaseResponseResource(true, 'Data Role updated successfully!', $role, 200);
        }

        return new BaseResponseResource(false, 'Data Role failed to update!', null, 400);
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
            return new BaseResponseResource(true, 'Data Role successfully deleted!', null, 200);
        }

        return new BaseResponseResource(false, 'Data Role failed to delete!', null, 400);
    }

    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        $roles = Role::latest()->get();

        return new BaseResponseResource(true, 'List Data Roles', $roles, 200);
    }
}
