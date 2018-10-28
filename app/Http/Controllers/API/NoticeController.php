<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Goutte\Client;


class NoticeController extends Controller
{
    public function __construct( )
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
        $client = new Client();

        $crawler = $client->request('GET', 'http://dept.inha.ac.kr/user/indexMain.do?siteId=culturecm&idpchk=true');

        // get contents
        $contents = $crawler->filter('.tab_list ul li ul li a')->each(function ($node) {
            // erase tabs
            $text = trim(preg_replace('/\t+/', '', $node->text()));

            // exclude 더보기
            if ($text != '더보기') {
                return $text;
            }
        });

        // get links
        $links = $crawler->filter('.tab_list ul li ul li a')->each(function ($node) {
            return $node->attr('href');
        });

        // get created_at
        $created_at = $crawler->filter('.tab_list ul li ul li span')->each(function($node) {
            // erase tabs
            $text = trim(preg_replace('/\t+/', '', $node->text()));

            return $text;
        });

        // find null index and exclude null attributes from arrays
        $index = [];

        for ($i = 0; $i < count($contents); $i++) {
            if ($contents[$i] == null) {
                array_push($index, $i);
            }
        }

        for ($i = 0; $i < count($index); $i++) {
            unset($contents[$index[$i]]);
            unset($links[$index[$i]]);
        }

        // recreate contents and links array to set ascending keys
        $contents_temp = [];
        $links_temp = [];

        foreach ($contents as $content) {
            array_push($contents_temp, $content);
        }

        foreach ($links as $link) {
            array_push($links_temp, $link);
        }

        // push notice objects into notices
        $notices = [];

        for ($i = 0; $i < count($contents_temp); $i++) {
            $content = (object) array();
            $content->contents = $contents_temp[$i];
            $content->links = "http://dept.inha.ac.kr" . $links_temp[$i];
            $content->created_at = $created_at[$i];

            array_push($notices, $content);
        }

        return response($notices, 200);
    }
}
