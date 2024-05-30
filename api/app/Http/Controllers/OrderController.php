<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Order`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Order::where('user', $user->id)->paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Order`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'product' => 'required|exist,products,id',
            'qty' => 'required|integer|min:0',
            'price' => 'required|integer',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        $data = $validator->validated();
        $data['user'] = $user->id;
        $data['state'] = 'PENDING';
        Order::create($data);

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Order`)) {
            return $this->sendError();
        }
        $Order = Order::find($id);

        if (!$Order) {
            return $this->sendError('Order Not Found', 404);
        }

        return $this->sendResponse($Order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Update Order`)) {
            return $this->sendError();
        }
        $Order = Order::find($id);

        if (!$Order) {
            return $this->sendError('Order Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'product' => 'required|exist,products,id',
            'qty' => 'required|integer|min:0',
            'price' => 'required|integer',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $product = Product::find($request->product);
        $offerProduct = OfferProduct::where('product', $request->product)->get();
        $offer = Offer::where('product', $request->product)->get();

        $Order->update($validator->validated());

        return $this->sendResponse('Order Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->hasPermissionTo(`Delete Order`)) {
            return $this->sendError();
        }
        $Order = Order::find($id);

        if ($Order) {
            return $this->sendError('Record not found', 404);
        }

        if ($Order->user != $user->id) {
            return $this->sendError("Can't delete other's order!");
        }
        return $this->sendError($Order);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo('Update Order status')) {
            return $this->sendError("You can't update the order status", 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'in:PENDING,PROCESSING,FULFILED,DELIVERED',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $order = Order::find($id);

        if (!$order) {
            return $this->sendError('Order does not exist');
        }

        $data = $validator->validated();
        $order->update(['status' => $data['status']]);

        return $this->sendResponse('Order status successfully udpated!');
    }
}
