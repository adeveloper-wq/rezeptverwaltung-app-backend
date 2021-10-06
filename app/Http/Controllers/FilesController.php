<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Receipt;
use App\Models\Membership;

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

    public function uploadReceiptImages(Request $request){
        $this->validate($request, [
            'image' => 'required|array',
            'image.*' => 'required|distinct|image|mimes:png,jpg,jpeg',
            'receiptId' => 'required'
        ]);

        //https://stackoverflow.com/questions/48003164/how-to-upload-multiple-files-using-lumen-multiple-file-upload
        $receipt = Receipt::find($request->receiptId);
        if($receipt){
            if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $receipt->G_ID]])->first()){
                $file_count = count($request->file('image') );
                $a=$request->file('image');
                $finalArray = array();
                for ($i=0; $i<$file_count; $i++) {
                    $allowedfileExtension=['jpeg','jpg','png'];
                    $file = $a[$i];
                    $extension = $file->getClientOriginalExtension();
                    $check = in_array($extension, $allowedfileExtension);
    
                    if($check){
                        $name = time() . "-" . uniqid() . "-" . Auth::user()->P_ID . "-" . $receipt->G_ID . "-" . $request->receiptId . "-" . $file->getClientOriginalName();
                        $file->move(storage_path('images' . DIRECTORY_SEPARATOR . 'receipt' . DIRECTORY_SEPARATOR . $request->receiptId), $name);
                        $path = storage_path('images' . DIRECTORY_SEPARATOR . 'receipt' . DIRECTORY_SEPARATOR . $request->receiptId) . $name;
                        $finalArray[$i]['image']="Erfolgreich hochgeladen.";
                    }else{
                        return response()->json("Bild " . $i . " ist kein jpeg, jpg oder png.", 400);
                    }
                }
                return response()->json($finalArray, 200);
            }else{
                return response()->json("Unauthorisiert.", 401);
            }
        }else{
            return response()->json("Das Rezept existiert nicht.", 404);
        }
        
    }
}
