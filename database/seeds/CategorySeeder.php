<?php

use Illuminate\Database\Seeder;
use \App\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new Category;
        $category->name = '카테고리1';

        $category->save();

        $category = new Category;
        $category->name = '카테고리2';

        $category->save();

        $category = new Category;
        $category->name = '카테고리3';

        $category->save();
    }
}
