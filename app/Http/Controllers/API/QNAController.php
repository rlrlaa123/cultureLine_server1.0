<?php

namespace App\Http\Controllers\API;

use App\Answer;
use App\Category;
use App\CategoryQuestion;
use App\Comment;
use App\Question;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;

class QNAController extends Controller
{
    public function __construct( ) {
        $this->middleware('jwt.auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::orderby('updated_at', 'desc')->paginate(10);

        foreach ($questions as $question) {
            $categories = DB::table('category_question')
                ->select('category_id')
                ->where('question_id', $question->id)
                ->get();

            $cate = '';

            foreach ($categories as $i => $category) {
                if ($i + 1 == count($categories)) {
                    $cate = $cate . $category->category_id;
                } else {
                    $cate = $cate . $category->category_id . ',';
                }
            }

            $question->categories = $cate;

            $answers = Answer::where('question_id', $question->id)->orderby('created_at')->get();

            foreach ($answers as $answer) {
                $answer->author = $answer->author->name;

                $answer->comments = Comment::where('answer_id', $answer->id)->orderby('created_at')->get();
            }

            $question->answers = $answers;
            $question->author = $question->author->name;
        }

        return response($questions, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required',
            'title' => 'required',
            'contents' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $categories = explode(',', $request->categories);

        $question = new Question;

        $question->author_id = auth()->user()->id;

        $question->title = $request->title;
        $question->contents = $request->contents;

        $question->save();

        $cate = '';

        foreach ($categories as $i => $category) {
            $question->categories()->attach($category);

            if ($i + 1 == count($categories)) {
                $cate = $cate . $category;
            } else {
                $cate = $cate . $category . ',';
            }
        }

        $question->answers = [];
        $question->author = $question->author->name;
        $question->categories = $cate;

        return response($question);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::find($id);

        $answers = Answer::where('question_id', $question->id)->get();

        foreach ($answers as $answer) {
            $answer->author = $answer->author->name;

            $answer->comments = Comment::where('answer_id', $answer->id)->orderby('updated_at', 'desc')->get();
        }

        $question->answers = $answers;
        $question->author = $question->author->name;

        $categories = DB::table('category_question')
            ->select('category_id')
            ->where('question_id', $question->id)
            ->get();

        $cate = '';

        foreach ($categories as $i => $category) {
            if ($i + 1 == count($categories)) {
                $cate = $cate . $category->category_id;
            } else {
                $cate = $cate . $category->category_id . ',';
            }
        }

        return response($question, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'contents' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        Question::where('id', $id)->update([
            'title' => $request->title,
            'contents' => $request->contents,
        ]);

        $question = Question::find($id);

        DB::table('category_question')
            ->where('question_id', $question->id)->get();

        $answers = Answer::where('question_id', $question->id)->get();

        foreach ($answers as $answer) {
            $answer->author = $answer->author->name;
        }

        $question->answers = $answers;
        $question->author = $question->author->name;

        $categories = DB::table('category_question')
            ->select('category_id')
            ->where('question_id', $question->id)
            ->get();

        $cate = '';

        foreach ($categories as $i => $category) {
            if ($i + 1 == count($categories)) {
                $cate = $cate . $category->category_id;
            } else {
                $cate = $cate . $category->category_id . ',';
            }
        }

        $question->categories = $cate;

        return response($question, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = Question::find($id);
//        return $id;
//        return DB::table('category_question')->where('question_id', $id)->get();
        DB::table('category_question')->where('question_id', $id)->delete();

        $question->delete();

        return response('success', 200);
    }

    public function categorySearch($id)
    {
        $category_ids = explode(',', $id);

        $relationships = DB::table('category_question');
        foreach ($category_ids as $category_id) {
            $relationships->orWhere('category_id', '=', $category_id);
        }

        $question_ids = [];

        foreach ($relationships->get() as $relationship) {
            array_push($question_ids, $relationship->question_id);
        }

        $question_id_list = [];

        foreach (array_unique($question_ids) as $question_id) {
            array_push($question_id_list, $question_id);
        }

        $questions = Question::whereIn('id', $question_id_list)->orderby('updated_at', 'desc')->paginate(10);

        foreach ($questions as $question) {
            $categories = DB::table('category_question')
                ->select('category_id')
                ->where('question_id', $question->id)
                ->get();

            $cate = '';

            foreach ($categories as $i => $category) {
                if ($i + 1 == count($categories)) {
                    $cate = $cate . $category->category_id;
                } else {
                    $cate = $cate . $category->category_id . ',';
                }
            }

            $question->categories = $cate;

            $answers = Answer::where('question_id', $question->id)->orderby('updated_at','desc')->get();

            foreach ($answers as $answer) {
                $answer->author = $answer->author->name;

                $answer->comments = Comment::where('answer_id', $answer->id)->orderby('updated_at', 'desc')->get();
            }

            $question->answers = $answers;
            $question->author = $question->author->name;
        }

        return response($questions, 200);
    }
}
