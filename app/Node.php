<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Node extends Model
{
    protected $fillable = ['name','parent_id'];
    public $timestamps = false;

    public function tree(){
        return $this->belongsTo(Tree::class);
    }

    public function get_direct_children(String $order='id', String $way='asc'){
        return DB::select('call get_direct_children(?,?,?)',[$this->id,$order,$way]);
    }

    public static function get_direct_children_s(Int $id, Int $tree_id, String $order='id', String $way='asc'){
        if($id==0)
            return self::where('parent_id', null)->where('tree_id',$tree_id)
                ->orderBy($order,$way)->get();
        return DB::select('call get_direct_children(?,?,?)',[$id,$order,$way]);
    }

    public function delete_with_children(){
        return DB::select('call delete_with_children(?)',[$this->id]);
    }

    public function delete_without_children(){
        return DB::select('call delete_without_children(?)',[$this->id]);
    }

}
