<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Tree;
class TreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Tree::class,20)->create();
    }
}
