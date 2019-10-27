<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('nodes')->insert(['tree_id'=>3,'name'=>'lolwut12','parent_id'=>null]);
        DB::table('nodes')->insert(['tree_id'=>3,'name'=>'lolwut11','parent_id'=>null]);
        DB::table('nodes')->insert(['tree_id'=>3,'name'=>'lolwut21','parent_id'=>1]);
        DB::table('nodes')->insert(['tree_id'=>3,'name'=>'lolwut13','parent_id'=>1]);
        DB::table('nodes')->insert(['tree_id'=>3,'name'=>'lolwut14','parent_id'=>3]);
    }
}
