<?php

namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller{
    public function __construct() {
        $this->middleware('auth:api');         
    }


    public function index(){
        $groups = Group::all();

        if($groups){
            return response()->json($groups, 200);
        }else{
            return response()->json("Gruppen können nicht geladen werden.", 500);
        }
    }

    public function get($name){
        $name = str_replace('+', ' ', $name);
        $group = Group::where('name', '=', $name)->first();;

        if($group){
            return response()->json($group, 200);
        }else{
            return response()->json("Gruppe existiert nicht.", 404);
        }
    }

    public function create(Request $request){
        $this->validate($request, [
            'passwort' => 'required',
            'hinweis' => 'required',
            'name' => 'required|unique:gruppen'
        ], ['name.unique' => 'Der Name ist schon vergeben.']);

        $group = new Group();

        $HG_Bildpfad = '';

        if($request->HG_Bildpfad){
            $HG_Bildpfad = $request->HG_Bildpfad;
        }

        $menüfarbe = '';

        if($request->menüfarbe){
            $HG_Bildpfad = $request->menüfarbe;
        }

        $group->passwort    = Hash::make($request->passwort);
        $group->hinweis     = $request->hinweis;
        $group->admin_P_ID  = Auth::user()->P_ID;
        $group->name        = $request->name;
        $group->HG_Bildpfad = $HG_Bildpfad;
        $group->menüfarbe   = $menüfarbe;

        $checkGroupCreated = $group->save();

        if($checkGroupCreated){
            $group = Group::where('name', '=', $request->name)
                            ->where('hinweis', '=', $request->hinweis)
                            ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();

            if($group->G_ID){
                $membership = new Membership();

                $membership->P_ID    = Auth::user()->P_ID;
                $membership->G_ID    = $group->G_ID;
    
                $checkMembership = $membership->save();

                if($checkMembership){
                    return response()->json("Gruppe erfolgreich erstellt!", 200);
                }else{
                    $group = Group::where('name', '=', $request->name)
                                    ->where('hinweis', '=', $request->hinweis)
                                    ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                    $group->delete();
                    return response()->json("Gruppe konnte nicht erstellt werden! (Beitritt in neue Gruppe fehlgeschlagen)", 500);
                }
            }else{
                $group = Group::where('name', '=', $request->name)
                            ->where('hinweis', '=', $request->hinweis)
                            ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                $group->delete();
                return response()->json("Gruppe konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
            }
        }else{
            return response()->json("Gruppe konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
        }
    }

    public function update(Request $request, $G_ID){
        $group = Group::find($G_ID);

        if($group){
            if(Auth::user()->P_ID == $group->admin_P_ID){
                $this->validate($request, [
                    'hinweis' => 'required'
                ]);
    
                $group->hinweis     = $request->hinweis;
    
                $check = $group->save();
    
                if($check){
                    return response()->json($group, 200);
                }else{
                    return response()->json('Aktualisieren der Gruppe fehlgeschlagen', 500);
                }
            }else{
                return response()->json("Nur Admins können die Gruppe bearbeiten.", 401);
            }
        }else{
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function delete($G_ID){
        $group = Group::find($G_ID);

        if($group){
            if(Auth::user()->P_ID == $group->admin_P_ID){
                $check = $group->delete();

                $memberships = Membership::where('G_ID', '=', $G_ID);
                $memberships->delete();
    
                if($check){
                    return response()->json('Gruppe erfolgreich gelöscht!', 200);
                }else{
                    return response()->json('Löschen der Gruppe fehlgeschlagen!', 500);
                }
            }else{
                return response()->json("Nur Admins können die Gruppe löschen.", 401);
            }
        }else{
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function join(Request $request){
        $this->validate($request, [
            'G_ID' => 'required',
            'passwort' => 'required'
        ]);

        $group = Group::find($request->G_ID);
        if($group){
            if(Hash::make($request->passwort) == $group->passwort){
                $membership = new Membership();

                $membership->P_ID    = Auth::user()->P_ID;
                $membership->G_ID    = $request->G_ID;

                $check = $membership->save();

                if($check){
                    return response()->json('Gruppe erfolgreich beigetreten!', 200);
                }else{
                    return response()->json('Der Gruppe konnte nicht beigetreten werden!', 500);
                }
            }else{
                return response()->json("Falsches Passwort.", 401);
            }
        }else{
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }
}