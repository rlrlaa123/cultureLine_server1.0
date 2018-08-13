<?php

use Illuminate\Database\Seeder;
use \App\User;
use \Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;

        $user->email = 'kimdonghyun3366@gmail.com';
        $user->name = '김동현';
        $user->password = Hash::make('ehehdd009~!');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'samsung@culture.com';
        $user->name = '삼성SDS';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'youngresearch@culture.com';
        $user->name = '한국청소년정책연구원';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'kakao@culture.com';
        $user->name = '카카오';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'posco@culture.com';
        $user->name = '포스코';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'hyundaimobis@culture.com';
        $user->name = '현대모비스';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();

        $user = new User;

        $user->email = 'comtus@culture.com';
        $user->name = '컴투스';
        $user->password = Hash::make('secret');
        $user->stu_id = '2014005378';
        $user->major = '문화콘텐츠학과';

        $user->save();
    }
}
