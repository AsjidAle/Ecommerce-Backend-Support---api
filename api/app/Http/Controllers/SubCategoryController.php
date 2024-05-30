<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View SubCategory`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = SubCategory::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert SubCategory`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|exist:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        SubCategory->create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View SubCategory`)) {
            return $this->sendError();
        }
        $SubCategory = SubCategory::find($id);

        if (!$SubCategory) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($SubCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update SubCategory`)) {
            return $this->sendError();
        }
        $SubCategory = SubCategory::find($id);

        if (!$SubCategory) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|exist:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $SubCategory->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete SubCategory`)) {
            return $this->sendError();
        }
        $SubCategory = SubCategory::find($id);

        if ($SubCategory) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($SubCategory);

    }
}
