<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNodeRequest;
use App\Tree;
use Illuminate\Http\Request;
use App\Node;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TreeController extends Controller
{
    public function index(){
        $trees =Tree::all();

        return view('index',['trees' => $trees]);
    }

    public function detail(Int $id){
        $tree = Tree::findOrFail($id);
        if(!auth()->guest())
            $is_owner = $tree->user->id == auth()->user()->id ? true : false;
        else
            $is_owner = false;
        return view('detail')->with('tree',$tree)->with('is_owner',$is_owner);
    }

    public function getChildren(Request $request){
        $id = $request->post('id');
        if($id==0)
            return response()->json(Node::where('parent_id',null)->get());
        else
            $node = Node::findOrFail($id);
        return response()->json($node->get_direct_children());
    }

    public function create(CreateNodeRequest $request){

    }
}