<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\ProductImage;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($size = null)
    {
        $user = auth()->user();

        if(!$user || !$user->hasPermissionTo(`View ProductImage`)){
            return $this->sendError();
        }

        if(!$size || $size > 100){
            $size = 100;
        }
        $data = ProductImage::paginate($size);
        return $this->sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if(!$user || !$user->hasPermissionTo(`Insert ProductImage`)){
            return $this->sendError();
        }

        $validator = Validator::make($request->all(),[
            //
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error',$validator->errors(),422);
        }
        ProductImage->create($validator->validated());

        return $this->sendResponse('Record Inserted Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if(!$user || !$user->hasPermissionTo(`View ProductImage`)){
            return $this->sendError();
        }
        $ProductImage = ProductImage::find($id);

        if(!$ProductImage){
            return $this->sendError('Record Not Found',404);
        }

        return $this->sendResponse($ProductImage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if(!$user || !$user->hasPermissionTo(`Update App\Models\ProductImage`)){
            return $this->sendError();
        }
        $ProductImage = ProductImage::find($id);

        if(!$ProductImage){
            return $this->sendError('Record Not Found',404);
        }

        $validator = Validator::make($request->all(),[
            //
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error',$validator->errors(),422);
        }

        $ProductImage->update($validator->validated());

        return $this->sendResponse('Record Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if(!$user || $user->hasPermissionTo(`Delete ProductImage`)){
            return $this->sendError();
        }
        $ProductImage=ProductImage::find($id);

        if($ProductImage){
            return $this->sendError('Record not found',404);
        }

        return $this->sendError($ProductImage);
        
    }
}
