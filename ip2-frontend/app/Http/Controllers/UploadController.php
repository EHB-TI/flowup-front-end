<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    //

    public function handle(Request $request)
    {
        $extension = $request->file('image')->extension();

        $pathToFile = $request->file('image')->storeAs('public/images', $request->user . '.' . $extension);



        

        return $extension;
    }
}
