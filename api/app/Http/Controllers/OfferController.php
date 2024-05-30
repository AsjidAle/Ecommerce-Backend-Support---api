<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfferController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Offer`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Offer::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Offer`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'discount' => 'required|integer|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        Offer::create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Offer`)) {
            return $this->sendError();
        }
        $Offer = Offer::find($id);

        if (!$Offer) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($Offer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update Offer`)) {
            return $this->sendError();
        }
        $Offer = Offer::find($id);

        if (!$Offer) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            //
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $Offer->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Offer`)) {
            return $this->sendError();
        }
        $Offer = Offer::find($id);

        if ($Offer) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($Offer);

    }
}
