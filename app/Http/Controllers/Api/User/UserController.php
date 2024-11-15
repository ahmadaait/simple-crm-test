<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $loggedInUser;

    public function __construct()
    {
        $this->loggedInUser = auth()->user();
    }

    public function index()
    {
        $perPage = request()->perPage ?: 2;
        $sort = request()->sort ?: 'created_at';
        $order = request()->order ?: 'desc';

        $users = User::when(request()->search, function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->with('roles');

        if ($this->loggedInUser->hasRole('manager')) {
            $users = $users->where('company_id', $this->loggedInUser->company_id)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['manager', 'employee']);
                });
        } else if ($this->loggedInUser->hasRole('employee')) {
            $users = $users->where('company_id', $this->loggedInUser->company_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'employee');
                });
        }

        $users = $users->orderBy($sort, $order)->paginate($perPage);

        $users->appends([
            'search' => request()->search,
            'perPage' => $perPage,
            'sort' => $sort,
            'order' => $order
        ]);

        return new UserResponseResource(true, 'List Data Users', $users, 200);
    }

    public function store(Request $request)
    {
        $company_id = $request->company_id ?: $this->loggedInUser->company_id;

        if ($this->loggedInUser->hasRole('manager')) {
            if (in_array('manager', $request->roles)) {
                return new UserResponseResource(false, 'Managers cannot create users with the role of manager!', null, 403);
            }

            if ($this->loggedInUser->company_id !== $company_id) {
                return new UserResponseResource(false, 'Managers can only create users within the same company!', null, 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed',
            'company_id' => 'nullable',
            'roles'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'company_id' => $company_id
        ]);

        $user->assignRole($request->roles);

        if ($user) {
            return new UserResponseResource(true, 'Data User Berhasil Disimpan!', $user, 201);
        }

        return new UserResponseResource(false, 'Data User Gagal Disimpan!', null, 400);
    }

    public function show($id)
    {
        $user = User::with('roles')->whereId($id)->first();

        if (!$user) {
            return new UserResponseResource(false, 'Data User Tidak Ditemukan!', null, 404);
        }

        if ($this->loggedInUser->hasRole('manager')) {
            if (
                $this->loggedInUser->company_id !== $user->company_id ||
                (!$user->hasRole('manager') && !$user->hasRole('employee') && $this->loggedInUser->id !== $user->id)
            ) {
                return new UserResponseResource(false, 'Managers can only view their own information, other managers, and employees within the same company.', null, 403);
            }
        } elseif ($this->loggedInUser->hasRole('employee')) {
            if (
                $this->loggedInUser->company_id !== $user->company_id ||
                (!$user->hasRole('employee') && $this->loggedInUser->id !== $user->id)
            ) {
                return new UserResponseResource(false, 'Employees can only view their own information and other employees within the same company.', null, 403);
            }
        }

        return new UserResponseResource(true, 'Detail Data User!', $user, 200);
    }

    public function update(Request $request, User $user)
    {
        if ($this->loggedInUser->hasRole('manager')) {
            if ($this->loggedInUser->company_id !== $user->company_id || ($this->loggedInUser->id !== $user->id && $user->hasRole('manager'))) {
                return new UserResponseResource(false, 'Managers can only update their own information or employees\' information within the same company.', null, 403);
            }
        }

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
            return new UserResponseResource(true, 'Data User Berhasil Diupdate!', $user, 200);
        }

        return new UserResponseResource(false, 'Data User Gagal Diupdate!', null, 400);
    }

    public function destroy(User $user)
    {
        if ($this->loggedInUser->hasRole('manager')) {
            if ($this->loggedInUser->company_id !== $user->company_id || ($this->loggedInUser->id !== $user->id && $user->hasRole('manager'))) {
                return new UserResponseResource(false, 'Managers can only delete their own information or employees\' information within the same company.', null, 403);
            }
        }

        if ($user->delete()) {
            return new UserResponseResource(true, 'Data User Berhasil Dihapus!', null, 200);
        }

        return new UserResponseResource(false, 'Data User Gagal Dihapus!', null, 400);
    }
}
