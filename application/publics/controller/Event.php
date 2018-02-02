<?php

namespace app\publics\controller;

use think\Controller;
use app\extra\push\WokerAPI;
use app\publics\model\User;

header('Access-Control-Allow-Origin:*');

Class Event extends Controller
{
    public function index()
    {
        $app_key = $_SERVER['HTTP_X_WOKER_KEY'];
//        $app = User::table("app")->where("app_key", $app_key)->find();
        $app = "b054014693241bcd9c20";

        if ($app_key == $app) {
            $app_secret = $app["app_secret"];

            $option = array('host' => Api_Host, "port" => Api_port);

            $woker = new WokerAPI($app_key, $app_secret, $app['id'], $option);

            $webhook_signature = $_SERVER ['HTTP_X_WOKER_SIGNATURE'];

            $body = file_get_contents('php://input');

            $expected_signature = hash_hmac('sha256', $body, $app_secret, false);

            if ($webhook_signature == $expected_signature) {

                $payload = json_decode($body, true);
                foreach ($payload['events'] as $event) {

                    // 通知离线
                    if ($event['name'] == 'channel_removed') {

                        if (strpos($event['channel'], 'user') === 0) {
                            $id = str_replace('user', '', $event['channel']);
                            $user = User::table('user')->where('uid', $id)->find();
                            if (!$user) {
                                $arr = [];
                                $arr['uid'] = $id;
                                $arr['logout_timestamp'] = time();
                                $arr['app_key'] = $app_key;
                                $arr['status'] = 'offline';
                                $res = User::table('user')->insert($arr);
                            }

                            $re = User::table('user')->where('uid', $id)->update(['logout_timestamp' => time(), 'status' => 'offline']);
                        }

                    }

                    // if($event['name'] == 'channel_added'){
                    //     if(strpos($event['channel'],'user')=== 0){
                    //          $id = str_replace('user', '', $event['channel']);
                    //          $user = User::table('user')->where('uid', $id)->find();
                    //          if(!$user){
                    //             $logtime =0;
                    //          }else{
                    //             $logtime =$user['logout_timestamp'];
                    //          }
                    //          $data = User::query("select * from message  where `to` = '{$id}' and `timestamp` > {$logtime} and type='friend'  order by `mid` desc limit 20");
                             
                    //     }
                    // }

                
                header("Status: 200 OK");
            } 

            } else {
                header("Status: 401 Not authenticated");
            }
     }
  }

}  
