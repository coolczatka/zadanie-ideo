<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNodeRequest;
use App\Tree;
use Illuminate\Http\Request;
use App\Node;
use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        return view('detail')->with('is_owner',$is_owner);
    }

    public function getChildren(Request $request, Int $tree_id){
        $id = $request->get('id');
        if(!auth()->guest())
            $is_owner = (Tree::find($tree_id)->user_id == auth()->user()->id) ? true : false;
        else
            $is_owner = false;
        if ($id == 0)
            return response()->json(['children' =>Node::where('parent_id', null)->where('tree_id',$tree_id)->get(),'owner'=>$is_owner]);
        else
            $node = Node::findOrFail($id);
        return response()->json(['children' => $node->get_direct_children(),'owner'=>$is_owner]);
    }

    public function getFullTree(Int $tree_id){
        $tree = Tree::findOrFail($tree_id);
        return view('full_tree')->with('tree',$tree);
    }

    public function create_node(Request $request){
        $validator = Validator::make($request->all(),[
                'name' => 'required',
                'parent_id' => 'required|exists:nodes,id',
                'tree_id' => 'required|exists:trees,id'
        ]);

        if(!$validator->fails()) {
            try {
                if (auth()->guest() || !$this->permissionToNode($request,'tree_id','parent_id') ||
                    $request->post('parent_id') == $request->post('id'))
                    return response()->json(['error' => 'You are no granted to add that node']);
            }catch (\Exception $e){
                return response()->json(['error' => 'You are no granted to add that node']);
            }
            $node = new Node();
            $node->name = $request->post('name');
            $node->parent_id = $request->post('parent_id') == 0 ? null : $request->post('parent_id');
            $node->tree_id = $request->post('tree_id');
            $node->save();
            return $node;
        }
        else
            return response()->json(['error'=>$validator->errors()]);
    }



    public function delete_node(Request $request){

        if (!auth()->guest() && $this->permissionToNode($request,'tree_id','id') ) {
            $node = Node::find($request->id);
            if ($node != null) {
                if ($request->with == 1) {
                    $node->delete_with_children();
                }
                if ($request->with == 0) {
                    $node->delete_without_children();
                }
                return response()->json(['del' => true]);
            }
            return response()->json(['error'=>'bad id']);
        }
    }

    public function patch(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'parent_id' => 'exists:nodes,id',
            'tree_id' => 'exists:trees,id'
        ]);
    }


    public function mytrees(){

        return view('home');
    }



    private function permissionToNode(Request $request, String $tree_name, String $node_name)
    {
        try {
            if(auth()->user()->id != Tree::find($request->post($tree_name))->user_id ||
                auth()->user()->id != Tree::find(Node::find($request->post($node_name))->tree_id)->user_id)
                return false;
            else
                return true;
        }catch (\Exception $e){
            return false;
        }
    }

}