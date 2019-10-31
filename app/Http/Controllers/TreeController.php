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

    public function detail(Int $tree_id){
        $tree = Tree::findOrFail($tree_id);
        if(!auth()->guest())
            $is_owner = $tree->user->id == auth()->user()->id ? true : false;
        else
            $is_owner = false;
        return view('detail',['is_owner'=>$is_owner,'start_node'=>0,'tree_id'=>$tree_id]);
    }

    public function part_of_tree(Int $tree_id, Int $node_id){
        $tree = Tree::findOrFail($tree_id);
        if(!auth()->guest())
            $is_owner = $tree->user->id == auth()->user()->id ? true : false;
        else
            $is_owner = false;
        return view('detail',['is_owner'=>$is_owner,'start_node'=>$node_id,'tree_id'=>$tree_id]);
    }

    public function getChildren(Request $request, Int $tree_id)
    {
        $id = $request->get('id');
        if (!auth()->guest())
            $is_owner = (Tree::find($tree_id)->user_id == auth()->user()->id) ? true : false;
        else
            $is_owner = false;

        $r = response()->json(['children' => Node::get_direct_children_s($id,$tree_id,
            ($request->sort_column ?? 'id')), 'owner' => $is_owner]);
        return $r;
    }

    public function getFullTree(Int $tree_id){
        $tree = Tree::findOrFail($tree_id);
        if(!auth()->guest())
            $is_owner = $tree->user->id == auth()->user()->id ? true : false;
        else
            $is_owner = false;
        return view('whole_tree')->with('tree',$tree)->with(['is_owner'=>$is_owner]);
    }

    public function create_node(Request $request){
        $request->parent_id = $request->parent_id==0 ? null : $request->parent_id;
        $validator = Validator::make($request->all(),[
                'name' => 'required',
                'parent_id' => 'required:exists:nodes,id',
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
                if ($request->with == 'with') {
                    $node->delete_with_children();
                }
                if ($request->with == 'without') {
                    $node->delete_without_children();
                }
                return response()->json(['del' => $request->all()]);
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
        if($this->permissionToNode($request,'tree_id','id')){
            if(!$validator->fails()){
                $node = Node::findOrFail($request->id);
                $node->parent_id = $request->parent_id ?? $node->parent_id;
                $node->name = $request->name ?? $node->name;
                $node->save();
                return $node;
            }
            else
                response()->json(['error' => $validator->errors()]);
        }
        else
            return response()->json(['error'=>'Permission error']);
    }

    public function mytrees(){
        if(!auth()->guest()){
            $trees = Tree::all()->where('user_id',auth()->user()->id);
            return view('home')->with('trees',$trees ?? []);
        }
        redirect()->route('login');
    }

    public function add_tree(Request $request){

            $tree = new Tree();
            $tree->name = $request->name;
            $tree->user_id = auth()->user()->id;
            $tree->save();
            return redirect()->route('detail',['id'=>$tree->id]);

    }

    private function permissionToNode(Request $request, String $tree_name, String $node_name)
    {
        try {
            if(auth()->user()->id != Tree::find($request->post($tree_name))->user_id ||
                ($request->post($node_name)!= 0 &&
                auth()->user()->id != Tree::find(Node::find($request->post($node_name))->tree_id)->user_id))
                return false;
            else
                return true;
        }catch (\Exception $e){
            return false;
        }
    }

}