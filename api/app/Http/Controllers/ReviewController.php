<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Review`)) {
            return $this->sendError();
        }

        if (!$size || $size > 100) {
            $size = 100;
        }
        $data = Review::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert Review`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'discription' => 'required|string',
            'rating' => 'required|in:[1,2,3,4,5]',
            'order' => 'required|exist:orders,id|order->status:NOT-FUlFiled',
            'unknownUser' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        $data = $validator->validated();
        $data['user'] = $user->id;
        Review::create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`View Review`)) {
            return $this->sendError();
        }

        $Review = Review::find($id);

        if (!$Review && ($Review->user != $user->id || $user->hasRole('Admin') || $user->hasRole('Executive'))) {
            return $this->sendError('Record Not Found', 404);
        }

        return $this->sendResponse($Review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $user = auth()->user();

        // if (!$user || !$user->hasPermissionTo(`Update Review`)) {
        //     return $this->sendError();
        // }
        // $Review = Review::find($id);

        // if (!$Review) {
        //     return $this->sendError('Record Not Found', 404);
        // }

        // $validator = Validator::make($request->all(), [
        //     //
        // ]);

        // if ($validator->fails()) {
        //     return $this->sendError('Validation Error', $validator->errors(), 422);
        // }

        // $Review->update($validator->validated());

        return $this->sendResponse("Review can't be Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $Review = Review::find($id);

        if (!$Review && (!$user || $Review->user != $user->id || $user->hasPermissionTo(`Delete Review`))) {
            return $this->sendError('Record not found', 404);
        }

        return $this->sendError($Review);

    }
}
