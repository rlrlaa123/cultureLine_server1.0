<?php

use Illuminate\Database\Seeder;
use \App\Question;
use \App\Category;
use \App\User;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $question = new Question;

        $question->author_id = User::where('name', '김동현')->first()->id;

        $question->title = '문과계열이 네이버 카카오 들어가려면 스펙 어느정도 돼야할까요?';
        $question->contents = '현재 2학년 콘텐츠과를 다니고 있습니다. 토익940학점 3.71, 목표가 네이버 카카오같은 기업인데 제가 노릴만한 자리가 많이 적을까요?';

        $question->save();

        $question->categories()->attach(Category::where('name', '기획')->first());

        $question = new Question;

        $question->author_id = User::where('name', '김동현')->first()->id;

        $question->title = 'UX분야, 서비스 기획 관련 분야 취업 질문드리고 싶습니다.';
        $question->contents = '안녕하세요 IT에 관심많은 대학생 000입니다. 내년 2월 졸업예정을 앞두고 있는4학년이라 취업 준비를 하고있는데요. 저는 UX 분야, 서비스 기획에 관심이 있습니다. (특히 네이버에관심이 많습니다.)';

        $question->save();

        $question->categories()->attach(Category::where('name', '디자인')->first());
        $question->categories()->attach(Category::where('name', '기획')->first());

        $question = new Question;

        $question->author_id = User::where('name', '김동현')->first()->id;

        $question->title = 'IT 직무 재취업 희망';
        $question->contents = '중소기업에서 개발자로 1년반이상 근무중인 25세 여성입니다. 학교는 서울 하위권 4년제 컴공 졸업 3.5/4.3 입니다. 영어점수는 없고, 자격증은 ocjp 하나 있습니다. 현재 대기업혁력으로 tv 검색 및 추천서비스 관련업무중입니다. 직무 관련새선, 코딩실력이 띄어난 것은 아니지만 제게 주어진 개발은';

        $question->save();

        $question->categories()->attach(Category::where('name', '기타')->first());
    }
}
