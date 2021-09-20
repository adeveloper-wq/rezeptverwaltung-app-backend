<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function get(Request $request, $path)
    {
        /* if (!Storage::disk('local')->exists($path)) {
            response()->json("Die Datei existiert nicht, bitte den Pfad kontrollieren.", 404);
        } else {
            response()->Storage::disk('local')->get($path);
        } */
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:png,jpg,jpeg'
        ]);


        if ($request->hasFile('image')) {
            $allowedfileExtension=['jpeg','jpg','png'];
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $check = in_array($extension, $allowedfileExtension);

            if($check){
                $name = time() . "-" . uniqid() . "-" . Auth::user()->P_ID . "-" . $file->getClientOriginalName();
                $path = $file->move(storage_path('images' . DIRECTORY_SEPARATOR . 'user'), $name);
                return response()->json("Erfolgreich:" . $path, 200);
            }
            /* $fileExtension = $request->file('image')->getClientOriginalName();
            $file = pathinfo($fileExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileStore = uniqid() . '_' . $file . '_' . time() . '.' . $extension; */
            //$path = $request->file('image')->storeAs('images', "blabla");
            //$request->file('image')->move(storage_path('images' . DIRECTORY_SEPARATOR . 'user'), "blbalba.png");
            
        }
    }
}
