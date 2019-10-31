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
        DB::unprepared($this->delete_with_children_create());
        DB::unprepared($this->delete_without_children_create());
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
            DB::unprepared($this->delete_with_children_drop());
            DB::unprepared($this->delete_without_children_drop());

        }catch (\Illuminate\Database\QueryException $e){
            Log::debug("Drop procedure which does not exist".$e->getMessage());
        }
    }

    private function delete_without_children_create(){
        return "
            drop procedure if exists delete_without_children;
            CREATE PROCEDURE
            delete_without_children(IN node INT)
            BEGIN
            SELECT @parent:=parent_id from nodes where id=node limit 1;
            UPDATE nodes set parent_id = @parent where parent_id=node;
            DELETE FROM nodes WHERE id = node;
            END;
        ";
    }
    private function delete_without_children_drop(){
        return "
            drop procedure if exists delete_without_children;
        ";
    }


    private function delete_with_children_create(){
        return "
            drop procedure if exists delete_with_children;
            CREATE PROCEDURE
            delete_with_children(IN node INT)
            BEGIN
                WITH recursive subtree as(
                select id from nodes where id = node
                union all
                select child.id from nodes as child
                join subtree as parent on child.parent_id = parent.id)
                DELETE from nodes where id in (select * from subtree);

            END;
        ";
    }

    private function delete_with_children_drop(){
        return "Drop procedure if exists get_direct_children;";
    }

    private function get_direct_children_create(){
        return "
            Drop procedure if exists get_direct_children;
            CREATE PROCEDURE get_direct_children(IN parent INT, IN col VARCHAR(30), IN way VARCHAR(30))
            BEGIN
            SET @q = CONCAT('SELECT * from nodes where 
            nodes.parent_id = ',parent,' order by ',col,' ',way);
            PREPARE query from @q;
            EXECUTE query;
            DEALLOCATE PREPARE query;
            
            END;";
    }

    private function get_direct_children_drop(){
        return "DROP PROCEDURE if exists get_direct_children;";
    }
}
