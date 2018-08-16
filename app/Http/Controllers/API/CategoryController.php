<?php

namespace App\Http\Controllers\API;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function __construct( ) {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
        $categories = Category::all();

        return response($categories, 200);
    }
}
