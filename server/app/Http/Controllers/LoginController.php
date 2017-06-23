<?php

namespace App\Http\Controllers;

use App\Counter;
use App\Jobs\GetCourse;
use App\StudentJobs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * 插件入口
     * @author hteen
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $jar = new CookieJar();
        foreach ($request->input('cookies') as $item) {

            $cookie = new SetCookie();
            $cookie->setName($item['name']);
            $cookie->setValue($item['value']);
            $cookie->setDomain($item['domain']);
            $cookie->setPath($item['path']);

            $jar->setCookie($cookie);
        }

        $options = [
            'cookies' => $jar,
            'headers' => [
                'Referer' => 'http://www.scude.cc/'
            ]
        ];

        $client = new Client();

        // 学生中心地址
        $url = "http://www.scude.cc/wangluo/studentSpace/courseStudy/courseStudy.jsp";

        try {
            $response = $client->request('GET', $url, $options);
        } catch (Exception\RequestException $exception) {
            return $this->responseJsonError('cookie无效, 请登录学生中心后再试');
        }

        $body = $response->getBody();

        // 川大编码是GBK, 需要转码
        $body = mb_convert_encoding($body, 'utf-8', 'gbk');
        preg_match('/学号：([a-zA-Z0-9]+)/', $body, $mc);

        $username = $mc[1] ?? false;

        if (!$username)
            return $this->responseJsonError('学号获取错误');

        $is_counter = Counter::where('student_id', $username)->count();

        // 首次使用
        if ( !$is_counter ) {

            $in_jobs = StudentJobs::where('student_id', $username)->count();

            if ($in_jobs)
                return $this->responseJsonError('首次拉取任务进行中, 请稍后再打开插件2...');

            // 异步执行拉取所有课程
            $job = new GetCourse($body, $username, $options);
            $job_id = dispatch($job);

            $student_job = new StudentJobs();
            $student_job->student_id = $username;
            $student_job->jobs_id = $job_id;
            $student_job->save();

            return $this->responseJsonError('首次拉取任务进行中, 请稍后再次打开插件...');
        }

        $data = Counter::where('student_id', $username)->get();

        return $this->responseJsonSuccess($data);
    }

    public function login($cookies){

        $jar = new CookieJar();
        foreach ($cookies as $item) {

            $cookie = new SetCookie();
            $cookie->setName($item['name']);
            $cookie->setValue($item['value']);
            $cookie->setDomain($item['domain']);
            $cookie->setPath($item['path']);

            $jar->setCookie($cookie);
        }

        $options = [
            'cookies' => $jar,
            'headers' => [
                'Referer' => 'http://www.scude.cc/'
            ]
        ];

        $client = new Client();

        // 学生中心地址
        $url = "http://www.scude.cc/wangluo/studentSpace/courseStudy/courseStudy.jsp";

        try {
            $response = $client->request('GET', $url, $options);
        } catch (Exception\RequestException $exception) {
            return $this->responseJsonError('cookie无效, 请先登录学生中心');
        }

        $body = $response->getBody();
        $body = mb_convert_encoding($body, 'utf-8', 'gbk');
        preg_match('/学号：([a-zA-Z0-9]+)/', $body, $mc);

        return $mc[1] ?? false;
    }

    /**
     * 增加访问次数
     * @author hteen
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function incr(Request $request) {
        $sc = Counter::where('student_id', $request->input('student_id'))
            ->where('counter_id', $request->input('counter_id'))
            ->first();

        if (!$sc)
            return $this->responseJsonError('参数错误');

        $sc->counter_num =  $sc->counter_num+1;
        $sc->save();

        return $this->responseJsonSuccess($sc->counter_num);
    }

    /**
     * 增加访问分钟数
     * @author hteen
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function minutes(Request $request)
    {
        $student_id = $this->login($request->input('cookies'));

        if (!$student_id)
            return $this->responseJsonError('学号错误');

        $urls = [];
        foreach ($request->input('urls') as $tab) {
            $urls[] = $tab['url'];
        }

        if ($urls) {
            DB::table('counter')
                ->where('student_id', $student_id)
                ->whereIn('resource_url', $urls)
                ->increment('counter_minutes');
        }

        return $this->responseJsonSuccess($student_id);
    }
}
