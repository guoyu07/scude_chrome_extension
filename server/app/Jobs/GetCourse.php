<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class GetCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $body;
    public $username;
    public $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($body, $username, $options)
    {
        $this->body = $body;
        $this->username = $username;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $html = $this->body;
        $username = $this->username;
        $options = $this->options;

        $p = '/href="([\S]+)"/';

        preg_match_all($p, $html, $matchs);
        $course_links = [];

        $insert_data = [];

        foreach ($matchs[1] as $url) {

            if (strpos($url, 'course_code') !== false && strpos($url, 'username') !== false) {

                $url = explode('coursename=', $url);
                $new = explode('&semester', $url[1]);
                $course_name = $new[0];
                $new[0] = urlencode(iconv("utf-8", "gbk", $new[0]));
                $url[1] = implode('&semester', $new);
                $new_url = 'http://www.scude.cc/wangluo/studentSpace/courseStudy/' . implode('coursename=', $url);

                $course_links[md5($new_url, 1)] = [
                    'link' => $new_url,
                    'name' => $course_name
                ];

            }

        }

        $course_links = array_values($course_links);
        foreach ($course_links as $course) {

            $client = new Client();
            $response = $client->request('GET', $course['link'], $options);

            $body = $response->getBody();
            $body = mb_convert_encoding($body, 'utf-8', 'gbk');

            $p = '/href="(counter.jsp[\S]+)"/';
            preg_match_all($p, $body, $m);

            $desc = [
                'dagang' => '教学大纲',
                'jiaoxue' => '教学课件',
                'fudao' => '辅导课件',
                'xuexi' => '学习指导',
                'moni' => '模拟题',
            ];

            foreach ($m[1] as $item) {

                $temp = explode('&type=', $item);

                $resource_url = str_replace('counter.jsp?path=','', $item);
                $resource_url = substr($resource_url,0, strpos($resource_url, '&'));

                $insert_data[] = [
                    'student_id' => $username,
                    'course_name' => $course['name'],
                    'counter_name' => $desc[$temp[1]] ?? '未知',
                    'counter_url' => 'http://www.scude.cc/wangluo/studentSpace/courseStudy/' . $item,
                    'resource_url' => $resource_url,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

        }

        DB::table('counter')->insert($insert_data);

    }
}
