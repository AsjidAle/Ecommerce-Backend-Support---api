<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Brand`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Brand::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Brand`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:brands,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        Brand::create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Brand`)) {
            return $this->sendError();
        }
        $Brand = Brand::find($id);

        if (!$Brand) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($Brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update Brand`)) {
            return $this->sendError();
        }
        $Brand = Brand::find($id);

        if (!$Brand) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:brands,name|except:' . $id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $Brand->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Brand`)) {
            return $this->sendError();
        }
        $Brand = Brand::find($id);

        if ($Brand) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($Brand);

    }
}
