<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\OfferProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View OfferProduct`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = OfferProduct::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert OfferProduct`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            //
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        OfferProduct->create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View OfferProduct`)) {
            return $this->sendError();
        }
        $OfferProduct = OfferProduct::find($id);

        if (!$OfferProduct) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($OfferProduct);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update OfferProduct`)) {
            return $this->sendError();
        }
        $OfferProduct = OfferProduct::find($id);

        if (!$OfferProduct) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            //
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $OfferProduct->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete OfferProduct`)) {
            return $this->sendError();
        }
        $OfferProduct = OfferProduct::find($id);

        if ($OfferProduct) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($OfferProduct);

    }
}
