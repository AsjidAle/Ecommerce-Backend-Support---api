<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     */
    public function upload(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo(`Insert File`)) {
            return $this->sendError();
        }

        $validator = Validator::make($request->all(), [
            'folder' => 'required|string',
            'path' => 'required|string',
            'attachment' => 'required|mimes:' . $request->input('folder') . '|max:128', // 128kb
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        File::create($validator->validated());

        return $this->sendResponse('File uploaded Successfully');
    }
}
