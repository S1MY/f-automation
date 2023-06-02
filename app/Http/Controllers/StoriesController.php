<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoriesController extends Controller
{
    public function storiesCreate(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'title' => 'Крутяк',
            'message' => $request
            ]);
    }
}
