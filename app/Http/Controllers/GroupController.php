<?php

namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'admin_P_ID' => 'required',
            'name' => 'required|unique:gruppen'
        ]);

        $group = new Group();

        $group->passwort    = Hash::make($request->passwort);
        $group->hinweis     = $request->hinweis;
        $group->admin_P_ID  = $request->admin_P_ID;
        $group->name        = $request->name;

        $checkGroupCreated = $group->save();

        if($checkGroupCreated){
            $group = Group::where('name', '=', $request->name)
                            ->where('hinweis', '=', $request->hinweis)
                            ->where('admin_P_ID', '=', $request->admin_P_ID)->first();

            if($group->G_ID){
                $membership = new Membership();

                $membership->P_ID    = $request->admin_P_ID;
                $membership->G_ID    = $group->G_ID;
    
                $checkMembership = $membership->save();

                if($checkMembership){
                    return response()->json("Group Successfully Created!", 200);
                }else{
                    $group = Group::where('name', '=', $request->name)
                                    ->where('hinweis', '=', $request->hinweis)
                                    ->where('admin_P_ID', '=', $request->admin_P_ID)->first();
                    $group->delete();
                    return response()->json("Creating the group failed!", 500);
                }
            }else{
                $group = Group::where('name', '=', $request->name)
                            ->where('hinweis', '=', $request->hinweis)
                            ->where('admin_P_ID', '=', $request->admin_P_ID)->first();
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
            $this->validate($request, [
                'passwort' => 'required',
                'hinweis' => 'required'
            ]);

            $group->passwort    = Hash::make($request->passwort);
            $group->hinweis     = $request->hinweis;

            $check = $group->save();

            if($check){
                return response()->json($group, 200);
            }else{
                return response()->json('Updating the group failed!', 500);
            }
        }else{
            return response()->json("Group doesn't exist.", 404);
        }
    }

    public function delete($G_ID){
        $group = Group::find($G_ID);

        if($group){
            $check = $group->delete();

            if($check){
                return response()->json('Group Successfully Deleted!', 200);
            }else{
                return response()->json('Deleting the group failed!', 500);
            }
        }else{
            return response()->json("Group doesn't exist.", 404);
        }
    }

    public function join(Request $request){
        $this->validate($request, [
            'P_ID' => 'required',
            'G_ID' => 'required'
        ]);

        $membership = new Membership();

        $membership->P_ID    = $request->P_ID;
        $membership->G_ID    = $request->G_ID;

        $check = $membership->save();

        if($check){
            return response()->json('Group successfully joined!', 200);
        }else{
            return response()->json('Joining the group failed!', 500);
        }
    }
}