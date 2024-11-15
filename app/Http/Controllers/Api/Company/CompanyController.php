<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResponseResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::when(request()->search, function ($companies) {
            $companies = $companies->where('name', 'like', '%' . request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        $companies->appends(['search' => request()->search]);

        return new BaseResponseResource(true, 'List Data Companies', $companies);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:companies',
            'logo'     => 'required',
            'address'  => 'required',
            'phone'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'logo'      => $request->logo,
                'address'   => $request->address,
                'phone'     => $request->phone,
            ]);

            if ($company) {
                $companyName = str_replace(' ', '', $request->name);
                $managerEmail = strtolower($companyName) . '.manager@gmail.com';
                $employeeEmail = strtolower($companyName) . '.employee@gmail.com';
                $password = bcrypt('password');

                $manager = $company->users()->create([
                    'name'      => $request->name . ' Manager',
                    'email'     => $managerEmail,
                    'password'  => $password,
                    'company_id' => $company->id,
                    'role'      => 'manager'
                ]);

                $employee = $company->users()->create([
                    'name'      => $request->name . ' Employee',
                    'email'     => $employeeEmail,
                    'password'  => $password,
                    'company_id' => $company->id,
                    'role'      => 'employee'
                ]);

                DB::commit();
                return new BaseResponseResource(true, 'Data Company Berhasil Disimpan!', $company);
            }

            DB::rollBack();
            return new BaseResponseResource(false, 'Data Company Gagal Disimpan!', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $company = Company::whereId($id)->first();

        if ($company) {
            return new BaseResponseResource(true, 'Detail Data Company!', $company);
        }

        return new BaseResponseResource(false, 'Detail Data Company Tidak Ditemukan!', null);
    }

    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:companies,email,' . $company->id,
            'logo'     => 'required',
            'address'  => 'required',
            'phone'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $company->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'logo'      => $request->logo,
            'address'   => $request->address,
            'phone'     => $request->phone,
        ]);

        if ($company) {
            return new BaseResponseResource(true, 'Data Company Berhasil Diupdate!', $company);
        }

        return new BaseResponseResource(false, 'Data Company Gagal Diupdate!', null);
    }

    public function destroy(Company $company)
    {
        if ($company->delete()) {
            return new BaseResponseResource(true, 'Data Company Berhasil Dihapus!', null);
        }

        return new BaseResponseResource(false, 'Data Company Gagal Dihapus!', null);
    }
}
