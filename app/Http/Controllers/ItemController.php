<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
 
class ItemController extends Controller
{
    private $items;
 
    public function __construct() {
        $this->middleware('auth:api');

        $this->items = array();
        for($i = 0; $i<10; $i++) {
            $item = array(
                'id' => $i,
                'name' => "item-" . $i
            );
            $this->items[] = $item;
        }
         
    }
 
    public function all()
    {
        return response()->json($this->items);
    }
     
    public function get($id)
    {
        $found_key = array_search($id, array_column($this->items, 'id'));
        return response()->json($this->items[$found_key]);
    }
}