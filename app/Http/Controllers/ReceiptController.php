<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientName;
use App\Models\Receipt;
use App\Models\Membership;
use App\Models\Step;
use App\Models\Tag;
use App\Models\ReceiptTag;
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

    public function createReceipt(Request $request) {
        $this->validate($request, [
            'groupId' => 'required',
            'name' => 'required',
            'portions' => 'required',
            'workingTime' => 'required',
            'cookingTime' => 'required',
            'restTime' => 'required',
            'tagIds' => 'required|array',
            'tagIds.*' => 'required|distinct',
            'ingredients' => 'required',
            'steps' => 'required'
        ]);

        if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $request->groupId]])->first()){
            $receipt = new Receipt();

            $receipt->titel = $request->name;
            $receipt->portionen = $request->portions;
            $receipt->arbeitszeit = $request->workingTime;
            $receipt->kochzeit = $request->cookingTime;
            $receipt->ruhezeit = $request->restTime;

            $receipt->G_ID = $request->groupId;
            $receipt->P_ID = Auth::user()->P_ID;

            $time = time();

            $receipt->erstell_dat = $time;

            $checkReceiptCreated = $receipt->save();

            if($checkReceiptCreated){
                $createdReceipt = Receipt::where('titel', '=', $request->name)
                                ->where('G_ID', '=', $request->groupId)
                                ->where('P_ID', '=', Auth::user()->P_ID)
                                ->where('erstell_dat', '=', $time)->first();

                if($createdReceipt->R_ID){
                    $tagIds = $request->tagIds;

                    foreach ($tagIds as &$tagId) {
                        $tagReceipt = new ReceiptTag();

                        $tagReceipt->R_ID = $createdReceipt->R_ID;
                        $tagReceipt->T_ID = $tagId;

                        $tagReceipt->save();
                    }

                    $jsonStringSteps = $request->steps;

                    $decodedSteps = json_decode($jsonStringSteps, false);

                    foreach ($decodedSteps as &$decodedStep) {
                        $step = new Step();

                        $step->R_ID = $createdReceipt->R_ID;
                        $step->schritt_nr = $decodedStep->stepNumber;
                        $step->anweisung = $decodedStep->instruction;

                        $step->save();
                    }

                    $jsonStringIngredients = $request->ingredients;

                    $decodedIngredients = json_decode($jsonStringIngredients, false);

                    foreach($decodedIngredients as &$decodedIngredient){
                        $ingredient = new Ingredient();

                        $ingredient->E_ID = $decodedIngredient->unitId;
                        $ingredient->name = $decodedIngredient->name;
                        $ingredient->menge = $decodedIngredient->amount;;
                        $ingredient->R_ID = $createdReceipt->R_ID;

                        $ingredient->save();
                    }
                    return response()->json($createdReceipt, 200);
                }else{
                    $createdReceipt =   Receipt::where('titel', '=', $request->name)
                                        ->where('G_ID', '=', $request->groupId)
                                        ->where('P_ID', '=', Auth::user()->P_ID)
                                        ->where('erstell_dat', '=', $time)->first();
                    $createdReceipt->delete();
                    return response()->json("Rezept konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
                }
            }else{
                return response()->json("Rezept konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
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
        if($receipt){
            if(Membership::where([['P_ID', '=', Auth::user()->P_ID],['G_ID', '=', $receipt->G_ID]])->first()){
                $steps = Step::where('R_ID', '=', $request->R_ID)->get();
                if($steps && count($steps) > 0){
                    return response()->json($steps, 200);
                }else{
                    response()->json("Es existieren keine Schritte zu diesem Rezept.", 404);
                }
            }else{
                return response()->json("Unauthorisiert.", 401);
            }
        }else{
            return response()->json("Das Rezept existiert nicht.", 404);
        }
    }

    public function getIngredients(Request $request){
        $this->validate($request, [
            'R_ID' => 'required'
        ]);

        $receipt = Receipt::find($request->R_ID);
        if($receipt){
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
        }else{
            return response()->json("Das Rezept existiert nicht.", 404);
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

    public function getAllUnits(){
        $units = Unit::all();

        if($units){
            return response()->json($units, 200);
        }else{
            response()->json("Es existieren keine Einheiten.", 404);
        }
    }

    public function getAllTags(){
        $tags = Tag::all();

        if($tags){
            return response()->json($tags, 200);
        }else{
            response()->json("Es existieren keine Tags.", 404);
        }
    }
}