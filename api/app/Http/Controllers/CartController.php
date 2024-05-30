<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Cart`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Cart::where('user', $user->id)->paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Cart`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'product' => 'required|exists,products,id',
            'qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        if ($Cart->product == $request->product && $Cart->user == $user->id) {
            $Cart->product = $Cart->product + $request->qty;
            $Cart->save();
        } else {
            $data = $validator->validated();
            $data['user'] = $user->id;
            Cart::create($data);
        }
        return $this->sendResponse('Added to Cart Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Cart`)) {
            return $this->sendError();
        }
        $Cart = Cart::find($id);

        if (!$Cart) {
            return $this->sendError('Record Not Found', 404);
        }

        if ($Cart->user != $user->id) {
            return $this->sendError();
        }

        return $this->sendResponse($Cart);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update Cart`)) {
            return $this->sendError();
        }

        $Cart = Cart::find($id);

        if (!$Cart || $Cart->user != $user->id) {
            return $this->sendError('Record Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'product' => 'required|exists,products,id',
            'qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $Cart->update($validator->validated());
        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Cart`)) {
            return $this->sendError();
        }
        $Cart = Cart::find($id);

        if ($Cart) {
            return $this->sendError('Record not found', 404);
        }
        if ($Cart->user != $user->id) {
            return $this->sendError();
        }

        return $this->sendError($Cart);

    }
}
