<?php

namespace App\Http\Controllers;
use App\Models\Receipt;
use App\Models\Membership;
use App\Models\Steps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller{
    public function __construct() {
        $this->middleware('auth:api');         
    }

    public function get(Request $request){
        $this->validate($request, [
            'G_ID' => 'required'
        ]);

        if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $request->G_ID]])->first()){
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

    public function getSteps(Request $request){
        $this->validate($request, [
            'R_ID' => 'required'
        ]);

        $receipt = Receipt::find($request->R_ID);
        if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $receipt->G_ID]])->first()){
            $steps = Steps::where('R_ID', '=', $request->R_ID)->get();
            if($steps && count($steps) > 0){
                return response()->json($steps, 200);
            }else{
                response()->json("Es existieren keine Schritte zu diesem Rezept.", 404);
            }
        }else{
            return response()->json("Unauthorisiert.", 401);
        }
    }
}