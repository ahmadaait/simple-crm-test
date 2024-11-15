<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResponseResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $permissions = Permission::when(request()->search, function ($permissions) {
            $permissions = $permissions->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $permissions->appends(['search' => request()->search]);

        return new BaseResponseResource(true, 'List Data Permissions', $permissions);
    }

    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        $permissions = Permission::latest()->get();

        return new BaseResponseResource(true, 'List Data Permissions', $permissions);
    }
}
