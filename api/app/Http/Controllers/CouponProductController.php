<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\CouponProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View CouponProduct`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = CouponProduct::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert CouponProduct`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string|unique:coupon_products,code',
            'discount' => 'required|min:0.01',
            'valid_till' => 'required|date|after:now',
            'product' => 'required|exist:products,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        CouponProduct::create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View CouponProduct`)) {
            return $this->sendError();
        }
        $CouponProduct = CouponProduct::find($id);

        if (!$CouponProduct) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($CouponProduct);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update CouponProduct`)) {
            return $this->sendError();
        }
        $CouponProduct = CouponProduct::find($id);

        if (!$CouponProduct) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string|unique:coupon_products,code|except:' . $CouponProduct->id,
            'discount' => 'required|min:0.01',
            'valid_till' => 'required|date|after:now',
            'product' => 'required|exist:products,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $CouponProduct->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete CouponProduct`)) {
            return $this->sendError();
        }
        $CouponProduct = CouponProduct::find($id);

        if ($CouponProduct) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($CouponProduct);

    }
}
