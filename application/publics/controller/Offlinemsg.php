<?php

namespace app\publics\controller;

use think\Controller;
use app\extra\push\WokerAPI;
use app\publics\model\User;

header('Access-Control-Allow-Origin:*');

Class Offlinemsg extends Controller
{
    /**
     * 登录显示未读消息
     * @return string
     */
    public function index()
    {
        $post = $this->request->post();

        $app_key = $post["app_key"];

        $app = User::table("app")->where("app_key", $app_key)->find();

        if ($app) {

            // 自己的id,群id
            $mine = $post["id"];
            $user = User::table('user')->where('uid', $mine)->find();

            $arr=[];
            $chatdata = [];

            if ($user) {
                $logtime = $user['logout_timestamp'];

                $re = User::table('user')->where('uid', $mine)->update(['status' => 'online', 'logout_timestamp' => time()]);
            } else {
                $logtime = 0;
                $arr = [
                    "app_key" => $app_key,
                    "uid" => $mine,
                    "logout_timestamp" => time(),
                    "status" => 'online'
                ];
                $re = User::table("user")->insert($arr);
            }


            $data =User::table('message')->where(['to'=>$mine,'type'=>'friend','app_key'=>$app_key])->where('timestamp','>',$logtime)->order('mid desc')->limit(20)->select();

            if ($data) {
                $result = array_reverse($data);
                
                foreach ($result as $value) {
                    $chatdata[] = $value['data'];
                }
                
            }

            if(isset($post['group']) && $post['group'] != 'false' &&  $post['group'] != ''){
                  $group = $post['group'];
              }else{
                  $group =fasle;
              }
            
            if ($group) {
                  $gid= [];
                foreach ($group as $v) {
                    $gid[] = $v['id'];
                }

                $gdata =User::table('message')->where('to','in',$gid)->where(['type'=>'group','app_key'=>$app_key])->where('timestamp','>',$logtime)->order('mid desc')->limit(20)->select();

                if($gdata){
                     $result = array_reverse($gdata);
                     foreach ($result as $value) {
                        $chatdata[] = $value['data'];
                     }
            
                }
               
            }

             $arr['code'] = 0;
             $arr['data'] =$chatdata;
             return json_encode($arr);

        } else {
            header("Status: 401 Not authenticated");
        }
    }
}
