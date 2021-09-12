<?php

namespace App\Http\Controllers;
use App\Models\Receipt;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller{
    public function __construct() {
        $this->middleware('auth:api');         
    }

    public function get(Request $request){
        $this->validate($request, [
            'G_ID' => 'required'
        ]);

        if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $request->G_ID]])){
            $receipts = Receipt::where('G_ID', '=', $request->G_ID)->get();

            if($receipts){
                return response()->json($receipts, 200);
            }else{
                return response()->json("Für diese Gruppe existieren noch keine Rezepte.", 404);
            }
        }else{
            return response()->json("Unauthorisiert.", 401);
        }
    }
}