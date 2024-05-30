<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\CouponTotal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponTotalController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View CouponTotal`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = CouponTotal::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert CouponTotal`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string|unique:coupon_products,code|unique:coupon_total,code',
            'code' => 'required|string|unique:coupon_products,code|unique:coupon_totals,code',
            'discount' => 'required|min:0.01',
            'valid_till' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        CouponTotal->create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View CouponTotal`)) {
            return $this->sendError();
        }
        $CouponTotal = CouponTotal::find($id);

        if (!$CouponTotal) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($CouponTotal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update CouponTotal`)) {
            return $this->sendError();
        }
        $CouponTotal = CouponTotal::find($id);

        if (!$CouponTotal) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string|unique:coupon_products,code|unique:coupon_totals,code,' . $CouponTotal->id,
            'discount' => 'required|min:0.01',
            'valid_till' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $CouponTotal->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete CouponTotal`)) {
            return $this->sendError();
        }
        $CouponTotal = CouponTotal::find($id);

        if ($CouponTotal) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($CouponTotal);

    }
}
