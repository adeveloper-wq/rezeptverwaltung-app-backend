<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index()
    {
        $memberships = Membership::where('P_ID', '=', Auth::user()->P_ID)->get();
        //error_log(print_r($memberships, TRUE)); 
        if (count($memberships) > 0) {
            $groups = array();
            foreach ($memberships as &$membership) {
                array_push($groups, Group::where('G_ID', '=', $membership->G_ID)->first());
            }
            if (count($groups) > 0) {
                return response()->json($groups, 200);
            } else {
                return response()->json("Fehler beim Laden der Gruppen.", 500);
            }
        } else {
            return response()->json("Noch keinen Gruppen beigetreten.", 404);
        }
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $name = $request->name;
        //$name = str_replace('+', ' ', $name);
        $group = Group::where('name', '=', $name)->first();

        if ($group) {
            return response()->json($group, 200);
        } else {
            return response()->json("Gruppe existiert nicht.", 404);
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'hinweis' => 'required',
            'name' => 'required|unique:gruppen'
        ], ['name.unique' => 'Der Name ist schon vergeben.']);

        $group = new Group();

        $HG_Bildpfad = '';

        if ($request->HG_Bildpfad) {
            $HG_Bildpfad = $request->HG_Bildpfad;
        }

        $menüfarbe = '';

        if ($request->menüfarbe) {
            $HG_Bildpfad = $request->menüfarbe;
        }

        $group->passwort    = Hash::make($request->password);
        $group->hinweis     = $request->hinweis;
        $group->admin_P_ID  = Auth::user()->P_ID;
        $group->name        = $request->name;
        $group->HG_Bildpfad = $HG_Bildpfad;
        $group->menüfarbe   = $menüfarbe;

        $checkGroupCreated = $group->save();

        if ($checkGroupCreated) {
            $group = Group::where('name', '=', $request->name)
                ->where('hinweis', '=', $request->hinweis)
                ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();

            if ($group->G_ID) {
                $membership = new Membership();

                $membership->P_ID    = Auth::user()->P_ID;
                $membership->G_ID    = $group->G_ID;

                $checkMembership = $membership->save();

                if ($checkMembership) {
                    return response()->json("Gruppe erfolgreich erstellt!", 200);
                } else {
                    $group = Group::where('name', '=', $request->name)
                        ->where('hinweis', '=', $request->hinweis)
                        ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                    $group->delete();
                    return response()->json("Gruppe konnte nicht erstellt werden! (Beitritt in neue Gruppe fehlgeschlagen)", 500);
                }
            } else {
                $group = Group::where('name', '=', $request->name)
                    ->where('hinweis', '=', $request->hinweis)
                    ->where('admin_P_ID', '=', Auth::user()->P_ID)->first();
                $group->delete();
                return response()->json("Gruppe konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
            }
        } else {
            return response()->json("Gruppe konnte nicht erstellt werden! (Fehler bei Erstellung)", 500);
        }
    }

    public function update(Request $request, $G_ID)
    {
        $group = Group::find($G_ID);

        if ($group) {
            if (Auth::user()->P_ID == $group->admin_P_ID) {
                $this->validate($request, [
                    'hinweis' => 'required'
                ]);

                $group->hinweis = $request->hinweis;

                $check = $group->save();

                if ($check) {
                    return response()->json($group, 200);
                } else {
                    return response()->json('Aktualisieren der Gruppe fehlgeschlagen', 500);
                }
            } else {
                return response()->json("Nur Admins können die Gruppe bearbeiten.", 401);
            }
        } else {
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function updateAdmin(Request $request, $G_ID)
    {
        $group = Group::find($G_ID);

        if ($group) {
            if (Auth::user()->P_ID == $group->admin_P_ID) {
                $this->validate($request, [
                    'admin_P_ID' => 'required'
                ]);

                $membership = Membership::where([['P_ID', '=', $request->admin_P_ID], ['G_ID', '=', $G_ID]])->first();
                if ($membership) {
                    $group->admin_P_ID = $request->admin_P_ID;

                    $check = $group->save();

                    if ($check) {
                        return response()->json('Admin der Gruppe wurde aktualisiert.', 200);
                    } else {
                        return response()->json('Aktualisieren des Admins fehlgeschlagen', 500);
                    }
                } else {
                    return response()->json("Fehlgeschlagen! Der neue Admin ist kein Mitglied der Gruppe.", 401);
                }
            } else {
                return response()->json("Nur Admins können die Gruppe bearbeiten.", 401);
            }
        } else {
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function delete($G_ID)
    {
        $group = Group::find($G_ID);

        if ($group) {
            if (Auth::user()->P_ID == $group->admin_P_ID) {
                $check = $group->delete();

                $memberships = Membership::where('G_ID', '=', $G_ID);
                $memberships->delete();

                if ($check) {
                    return response()->json('Gruppe erfolgreich gelöscht!', 200);
                } else {
                    return response()->json('Löschen der Gruppe fehlgeschlagen!', 500);
                }
            } else {
                return response()->json("Nur Admins können die Gruppe löschen.", 401);
            }
        } else {
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function join(Request $request, $G_ID)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);

        $group = Group::find($G_ID);
        if ($group) {
            $membership = Membership::where([['P_ID', '=', Auth::user()->P_ID], ['G_ID', '=', $G_ID]])->first();
            if ($membership) {
                return response()->json("Schon beigetreten.", 200);
            } else {
                if (Hash::check($request->password, $group->passwort)) {
                    $membership = new Membership();

                    $membership->P_ID    = Auth::user()->P_ID;
                    $membership->G_ID    = $G_ID;

                    $check = $membership->save();

                    if ($check) {
                        return response()->json('Gruppe erfolgreich beigetreten!', 200);
                    } else {
                        return response()->json('Der Gruppe konnte nicht beigetreten werden!', 500);
                    }
                } else {
                    return response()->json("Falsches Passwort.", 401);
                }
            }
        } else {
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }

    public function leave(Request $request, $G_ID)
    {
        $group = Group::find($G_ID);
        if ($group) {

            // KICK USER
            if ($request->P_ID) {
                $this->validate($request, [
                    'P_ID' => 'required'
                ]);

                if($request->P_ID == Auth::user()->P_ID){
                    return response()->json('Zum eigenen Verlassen der Gruppe die Methode ohne den Body aufrufen.', 400);
                }else{
                    if ($group->admin_P_ID == Auth::user()->P_ID) {
                        $memberships = Membership::where([['P_ID', '=', $request->P_ID], ['G_ID', '=', $G_ID]]);
                        
                        if ($memberships) {
                            $check = $memberships->delete();
                            if ($check) {
                                return response()->json('User erfolgreich aus der Gruppe entfernt.', 200);
                            } else {
                                return response()->json('Entfernen des Users aus der Gruppe fehlgeschlagen!', 500);
                            }
                        } else {
                            return response()->json("Angegebene User ist kein Mitglied der Gruppe.", 404);
                        }
                    } else {
                        return response()->json("Nur Admins können andere User aus der Gruppe entfernen oder sie bannen.", 401);
                    }
                }
            // LEAVE GROUP
            } else {
                $memberships = Membership::where([['P_ID', '=', Auth::user()->P_ID], ['G_ID', '=', $G_ID]]);
                if ($memberships) {
                    if ($group->admin_P_ID == Auth::user()->P_ID) {
                        return response()->json("Gruppe kann als Admin nicht verlassen werden.", 409);
                    } else {
                        $check = $memberships->delete();

                        if ($check) {
                            return response()->json('Erfolgreich aus der Gruppe ausgetreten.', 200);
                        } else {
                            return response()->json('Austreten aus der Gruppe fehlgeschlagen!', 500);
                        }
                    }
                } else {
                    return response()->json("Kein Mitglied in der Gruppe.", 404);
                }
            }
        } else {
            return response()->json("Die Gruppe existiert nicht.", 404);
        }
    }
}
