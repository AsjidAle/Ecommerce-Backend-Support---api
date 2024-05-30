<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Category`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Category::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Category`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        Category::create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Category`)) {
            return $this->sendError();
        }
        $Category = Category::find($id);

        if (!$Category) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($Category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update Category`)) {
            return $this->sendError();
        }
        $Category = Category::find($id);

        if (!$Category) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $Category->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $Category->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Category`)) {
            return $this->sendError();
        }
        $Category = Category::find($id);

        if ($Category) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($Category);

    }
}
