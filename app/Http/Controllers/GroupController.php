<?php

namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\Membership;
use Illuminate\Http\Request;

class GroupController extends Controller{
    public function index(){
        $groups = Group::all();
        return response()->json($groups);
    }

    public function get($name){
        $group = Group::find($name);
        return response()->json($group);
    }

    public function create(Request $request){
        $group = new Group();

        $group->passwort    = $request->passwort;
        $group->hinweis     = $request->hinweis;
        $group->admin_P_ID  = $request->admin_P_ID;

        $group->save();

        return response()->json("Group Successfully Created!");
    }

    public function update(Request $request, $G_ID){
        $group = Group::find($G_ID);

        $group->passwort    = $request->passwort;
        $group->hinweis     = $request->hinweis;

        $group->save();

        return response()->json($group);
    }

    public function delete($G_ID){
        $group = Group::find($G_ID);
        $group->delete();

        return response()->json("Group Successfully Deleted!");
    }

    public function join(Request $request){
        $membership = new Membership();

        $membership->P_ID    = $request->P_ID;
        $membership->G_ID    = $request->G_ID;

        $membership->save();

        return response()->json("Group Successfully Joined!");
    }
}