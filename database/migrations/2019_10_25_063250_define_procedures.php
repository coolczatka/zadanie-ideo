<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefineProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared($this->get_direct_children_create());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::unprepared($this->get_direct_children_drop());

        }catch (\Illuminate\Database\QueryException $e){
            Log::debug("Drop procedure which does not exist".$e->getMessage());
        }
    }



    private function get_direct_children_create(){
        return "
            Drop procedure if exists get_direct_children;
            CREATE PROCEDURE 
            get_direct_children(IN parent INT)
            BEGIN
            SELECT * from nodes where 
            nodes.parent_id = parent;
            END;";
    }

    private function get_direct_children_drop(){
        return "DROP PROCEDURE get_direct_children;";
    }
}
