<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Product`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }

        if (!$size) {
            $data = Product::all();
        } else {
            $data = Product::paginate($size);
        }
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Product`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|strring',
            'price' => 'required|integer|min:0.01',
            'stock' => 'required|integer|min:1',
            'subCategory' => 'required|exist:in,subcategories,id',
            'brand' => 'required|exist:brands,id',
            'source' => 'required|string',
            'sourcePrice' => 'required|integer',
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $product = Product::create($validator->validated());

        ProductImage::create(['product' => $product->id, 'image' => $request->image]);

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Product`)) {
            return $this->sendError();
        }
        $Product = Product::find($id);

        if (!$Product) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($Product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update App\Models\Product`)) {
            return $this->sendError();
        }
        $Product = Product::find($id);

        if (!$Product) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|strring',
            'price' => 'required|integer|min:0.01',
            'stock' => 'required|integer|min:1',
            'subCategory' => 'required|exist:in,subcategories,id',
            'brand' => 'required|exist:brands,id',
            'source' => 'required|string',
            'sourcePrice' => 'required|integer',
            'image' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $Product->update($validator->validated());

        if ($request->image) {
            ProductImage::create(['product' => $product->id, 'image' => $request->image]);
        }
        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Product`)) {
            return $this->sendError();
        }
        $Product = Product::find($id);

        if ($Product) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($Product);

    }
}
