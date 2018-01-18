<?php
/**
 * Created by PhpStorm.
 * User: chenrui
 * Date: 2017/9/22
 * Time: 11:12
 */

namespace app\publics\controller;

use think\Controller;
use app\publics\model\User;
use think\config;

header('Access-Control-Allow-Origin:*');

class Set extends Controller
{
    /**
     * 读取app接口
     * @return string
     */
    public function index()
    {
        $secret = Config::get('secert');//秘钥

        $get = $this->request->get();
        $timestamp = $get['timestamp'];
        $token = $get['token'];

        if ($timestamp <= time() - 300 || $timestamp >= time() + 300) {//时间戳在五分钟外
            return '拒绝访问！';
        }
        $compare_token = md5($timestamp . $secret);
        if ($token !== $compare_token) {
            return '秘钥不正确！';
        }

        $result = User::table('app')->select();
        $arr = [];
        foreach ($result as $v) {
            $arr[$v['app_key']] = [
                'id' => $v['id'],
                'app_key' => $v['app_key'],
                'app_secret' => $v['app_secret'],
                'p_end_time' => $v['end_time'],
                'max_connect' => $v['links'],
                'max_message' => 50000000,
                'channel_hook' => 'http://api.laychat.net/publics/event'
            ];
        }
        return json_encode($arr);
    }

    public function uploadimg(){

         $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
       
         $image_base = trim($_POST['imageContent']);
         $img = str_replace('data:image/png;base64','',$image_base); 
         $img = str_replace('','+',$img);
         $data_img = base64_decode($img);

         $url =ROOT.'/assets/upload/images/'.time().'.jpg';
         $res = file_put_contents($url,$data_img);
        
        if($res){
            $data = [
            "code" => 0,
            "msg" => '',
            "data" => [
                "src" =>$http_type.$_SERVER['HTTP_HOST']."/assets/upload/images/".time().'.jpg'
                ]
            ];

         return  json_encode($data);
        }
        
       }

}

