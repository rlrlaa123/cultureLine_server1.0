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
        $category->name = '기획';

        $category->save();

        $category = new Category;
        $category->name = '창작';

        $category->save();

        $category = new Category;
        $category->name = '문화기관';

        $category->save();

        $category = new Category;
        $category->name = '마케팅';

        $category->save();

        $category = new Category;
        $category->name = '기타';

        $category->save();
    }
}
