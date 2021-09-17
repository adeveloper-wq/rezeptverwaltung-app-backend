<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientName;
use App\Models\Receipt;
use App\Models\Membership;
use App\Models\Steps;
use App\Models\Unit;
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

    public function getIngredients(Request $request){
        $this->validate($request, [
            'R_ID' => 'required'
        ]);

        $receipt = Receipt::find($request->R_ID);
        if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $receipt->G_ID]])->first()){
            $ingredients = Ingredient::where('R_ID', '=', $request->R_ID)->get();
            if($ingredients && count($ingredients) > 0){
                return response()->json($ingredients, 200);
            }else{
                response()->json("Es existieren keine Zutaten zu diesem Rezept.", 404);
            }
        }else{
            return response()->json("Unauthorisiert.", 401);
        }
    }

    public function getIngredientNames(Request $request){
        $this->validate($request, [
            'Z_IDs' => 'required|array|min:1',
            'Z_IDs.*' => 'required|string|distinct'
        ]);

        $names = array();

        $Z_IDs = $request->Z_IDs;

        foreach ($Z_IDs as &$Z_ID) {
            array_push($names, IngredientName::where('Z_ID', '=', $Z_ID)->first());
        }

        if($names && count($names) > 0){
            return response()->json($names, 200);
        }else{
            response()->json("Fehler beim Laden der Zutaten.", 500);
        }
    }

    public function getUnitNames(Request $request){
        $this->validate($request, [
            'E_IDs' => 'required|array|min:1',
            'E_IDs.*' => 'required|string|distinct'
        ]);

        $names = array();

        $E_IDs = $request->E_IDs;

        foreach ($E_IDs as &$E_ID) {
            array_push($names, Unit::where('E_ID', '=', $E_ID)->first());
        }

        if($names && count($names) > 0){
            return response()->json($names, 200);
        }else{
            response()->json("Fehler beim Laden der Einheiten.", 500);
        }
    }
}