<?php

use Illuminate\Database\Seeder;
use \App\Answer;
use \App\Question;
use \App\User;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answer = new Answer;

        $answer->question_id = Question::where('id', 1)->first()->id;
        $answer->author_id = User::where('name', '삼성SDS')->first()->id;

        $answer->contents = '토익점수 높네요. IT 기업에 관심있다면 전산하는 친구랑 같이 사이트를 만드는 프로젝트를 해보면 도움이 될듯합니다.';
        $answer->like = 3;

        $answer->save();

        $answer = new Answer;

        $answer->question_id = Question::where('id', 1)->first()->id;
        $answer->author_id = User::where('name', '한국청소년정책연구원')->first()->id;

        $answer->contents = '현재 네이버 합격자들의 평균 스펙은 1. 평균 학점 (85% 보유): 3.64점 2. 토익 보유자 평균 (36% 보유): 800점';
        $answer->like = 2;

        $answer->save();

        $answer = new Answer;

        $answer->question_id = Question::where('id', 1)->first()->id;
        $answer->author_id = User::where('name', '카카오')->first()->id;

        $answer->contents = '네이버나 카카오 같은 IT 기업의 기획에는 전략 기획과 비즈니스 기획, 서비스 기획이 있습니다.';

        $answer->save();

        $answer = new Answer;

        $answer->question_id = Question::where('id', 2)->first()->id;
        $answer->author_id = User::where('name', '포스코')->first()->id;

        $answer->contents = '실력만 괜찮으시다면 충분히 가능성은 있습니다. 다만 부족해보이는 스펙이 문제가 되겠네요.지금 회사 다니면서 차근히 스펙을 올리셔야 할 것 같습니다. 최소 기사자격증은 보유해야하고, 영어점수도준비하셔야 겠네요... 자소서를';

        $answer->save();

        $answer = new Answer;

        $answer->question_id = Question::where('id', 2)->first()->id;
        $answer->author_id = User::where('name', '현대모비스')->first()->id;

        $answer->contents = '저는 이직 추천드립니다. 왜냐하면 IT분야이기 때문입니다. 특히 카카오 같은 경우 시험만으로 경력직을 채용합니다.';

        $answer->save();

        $answer = new Answer;

        $answer->question_id = Question::where('id', 3)->first()->id;
        $answer->author_id = User::where('name', '컴투스')->first()->id;

        $answer->contents = '안녕하세요, 전 다음 카카오 서비스 기획자입니다. 냉정하게 조언을 드리자면결론적으로 지금 스펙으로는 네이버에 지원하기는 어려울 듯 싶습니다.';

        $answer->save();
    }
}
