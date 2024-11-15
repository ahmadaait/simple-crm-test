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
        $perPage = request()->perPage ?: 2;
        $sort = request()->sort ?: 'created_at';
        $order = request()->order ?: 'desc';

        $permissions = Permission::when(request()->search, function ($permissions) {
            $permissions = $permissions->where('name', 'like', '%' . request()->search . '%');
        });

        $permissions = $permissions->orderBy($sort, $order)->paginate($perPage);

        $permissions->appends([
            'search' => request()->search,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order
        ]);

        return new BaseResponseResource(true, 'List Data Permissions', $permissions, 200);
    }

    /**
     * all
     *
     * @return void
     */
    public function all()
    {
        $permissions = Permission::latest()->get();

        return new BaseResponseResource(true, 'List Data Permissions', $permissions, 200);
    }
}
