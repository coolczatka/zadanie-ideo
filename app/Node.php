<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Node extends Model
{
    protected $fillable = ['name','parent_id'];

    public function tree(){
        return $this->belongsTo(Tree::class);
    }

    public function get_direct_children(){
        return DB::select('call get_direct_children(?)',[$this->id]);
    }

}
