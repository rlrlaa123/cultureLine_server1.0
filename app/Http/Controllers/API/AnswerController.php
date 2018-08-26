<?php

namespace App\Http\Controllers\API;

use App\Answer;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;

class AnswerController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'contents' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $answer = new Answer;

        $question = Question::find($id);

        $answer->question_id = $question->id;
        $answer->author_id = auth()->user()->id;

        $answer->contents = $request->contents;

        $question->updated_at = Carbon::now();

        $question->save();

        $answer->save();

        $answer->like = 0;
        $answer->author = auth()->user();

        if (DB::table('answer_like')->where('user_id', auth()->user()->id)->first()) {
            $answer->liked = 1;
        }
        else {
            $answer->like = 0;
        }

        return response($answer, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $question_id, $id)
    {
        $validator = Validator::make($request->all(), [
            'contents' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        Answer::where('id', $id)->update([
            'contents' => $request->contents,
        ]);

        return response('success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($question_id, $id)
    {
        $answer = Answer::find($id);

        $answer->delete();

        return response('success', 200);
    }

    public function like($question_id, $id)
    {
        $answer = Answer::find($id);
        $like = DB::table('answer_like')->where('user_id', auth()->user()->id);

        if ($like->first()) {
//            return json_encode($like);
            $like->delete();
            $answer->like -= 1;
            $answer->save();

        }
        else {
            $answer->like += 1;
            $answer->save();

            DB::table('answer_like')->insert([
                'user_id' => auth()->user()->id,
                'answer_id' => $answer->id
            ]);
        }

        return response('success', 200);
    }

    public function select($question_id, $id)
    {
        $answer = Answer::find($id);

        if (!$answer->selected) {
            $answer->selected = true;
            $answer->save();

            $question = Question::find($answer->question_id);

            $question->selected = true;
            $question->save();
        }
        else {
            $answer->selected = false;
            $answer->save();

            $question = Question::find($answer->question_id);

            $question->selected = false;
            $question->save();
        }

        return response('success', 200);
    }
}
