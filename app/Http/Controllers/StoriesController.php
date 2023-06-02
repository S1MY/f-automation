<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoriesController extends Controller
{
    public function storiesCreate(Request $request)
    {   
        $request->validate([
            'name' => 'required|string|max:20',
            'accType' => 'required|string|max:5',
            'accountId' => 'required|number',
            'file' => 'required|file|size:10240|mimetypes:video/avi,video/mp4|mimes:jpg,jpeg'
        ]);
        return response()->json([
            'status' => 'success',
            'title' => 'Крутяк',
            'message' => $request->file
            ]);
    }
}
