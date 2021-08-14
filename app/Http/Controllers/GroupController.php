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
            return response()->json("Can't get groups.", 404);
        }
    }

    public function get($name){
        $name = str_replace('+', ' ', $name);
        $group = Group::where('name', '=', $name)->first();;

        if($group){
            return response()->json($group, 200);
        }else{
            return response()->json("Group doesn't exist.", 404);
        }
    }

    public function create(Request $request){
        $this->validate($request, [
            'passwort' => 'required',
            'hinweis' => 'required',
            'name' => 'required|unique:gruppen'
        ]);

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
                    return response()->json("Group Successfully Created!", 200);
                }else{
                    $group = Group::where('name', '=', $request->name)
                                    ->where('hinweis', '=', $request->hinweis)
                                    ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                    $group->delete();
                    return response()->json("Creating the group failed!", 500);
                }
            }else{
                $group = Group::where('name', '=', $request->name)
                            ->where('hinweis', '=', $request->hinweis)
                            ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                $group->delete();
                return response()->json("Creating the group failed!", 500);
            }
        }else{
            return response()->json("Creating the group failed!", 500);
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
                    return response()->json('Updating the group failed!', 500);
                }
            }else{
                return response()->json("Not authorized to update the group.", 401);
            }
        }else{
            return response()->json("Group doesn't exist.", 404);
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
                    return response()->json('Group Successfully Deleted!', 200);
                }else{
                    return response()->json('Deleting the group failed!', 500);
                }
            }else{
                return response()->json("Not authorized to delete the group.", 401);
            }
        }else{
            return response()->json("Group doesn't exist.", 404);
        }
    }

    public function join(Request $request){
        $this->validate($request, [
            'P_ID' => 'required',
            'G_ID' => 'required',
            'passwort' => 'required'
        ]);

        if(Auth::user()->P_ID == $request->P_ID){
            $group = Group::find($request->G_ID);
            if($group){
                if(Hash::make($request->passwort) == $group->passwort){
                    $membership = new Membership();
    
                    $membership->P_ID    = $request->P_ID;
                    $membership->G_ID    = $request->G_ID;
    
                    $check = $membership->save();
    
                    if($check){
                        return response()->json('Group successfully joined!', 200);
                    }else{
                        return response()->json('Joining the group failed!', 500);
                    }
                }else{
                    return response()->json("Wrong password.", 401);
                }
            }else{
                return response()->json("Group doesn't exist.", 404);
            }
        }else{
            return response()->json("Trying to add a non-authicated user to a group.", 401);
        }
    }
}