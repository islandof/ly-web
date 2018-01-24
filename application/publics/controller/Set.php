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

    public function uploadimg()
    {

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $image_base = trim($_POST['imageContent']);
        $img = str_replace('data:image/png;base64', '', $image_base);
        $img = str_replace('', '+', $img);
        $data_img = base64_decode($img);

        $url = ROOT . '/assets/upload/images/' . time() . '.jpg';
        $res = file_put_contents($url, $data_img);

        if ($res) {
            $data = [
                "code" => 0,
                "msg" => '',
                "data" => [
                    "src" => $http_type . $_SERVER['HTTP_HOST'] . "/assets/upload/images/" . time() . '.jpg'
                ]
            ];

            return json_encode($data);
        }

    }

    /**
     * .添加敏感词
     * [addwords description]
     * @return [type] [description]
     */
    public function addwords()
    {
        $post = $_POST;
        $data = User::table('gagwords')->insert($post);

        if ($data) {
            return '添加成功！';
        } else {
            return '添加失败！';
        }

    }

    /**
     * .删除敏感词
     * [deletewords description]
     * @return [type] [description]
     */
    public function deletewords()
    {
        $post = $_POST;
        $data = User::table('gagwords')->where('id', $post["id"])->delete();
        if ($data) {
            return '删除成功！';
        } else {
            return '删除失败！';
        }
    }


    public function deletewhite()
    {
        $post = $_POST;
        $data = User::table('white_list')->where('id', $post["id"])->delete();
        if ($data) {
            return '删除成功！';
        } else {
            return '删除失败！';
        }
    }

    /**
     * .添加被禁人
     * [addgag description]
     * @return [type] [description]
     */
    public function addgag()
    {
        $post = $_POST;
        $wlist = User::table('white_list')->where('userid', $post['userid'])->find();

        if ($wlist) {
            return '该成员已被设为白名单，不能执行该操作！';
        }


        $data = User::table('gaglist')->insert($post);

        if ($data) {
            User::table('message')->where('from', $post["userid"])->delete();
            return '添加成功';
        } else {
            return '添加失败';
        }

    }

    /**
     * .开放被禁人
     * [deletelist description]
     * @return [type] [description]
     */
    public function deletelist()
    {
        $post = $_POST;
        $data = User::table('gaglist')->where('userid', $post["userid"])->delete();
        if ($data) {
            return '删除成功！';
        } else {
            return '删除失败！';
        }
    }

    public function getGroupMessage()
    {
        $post = $_POST;
        if (isset($post["username"])) {
            $data = User::table('message')->where("to", $post["groupid"])->where("username", $post["username"])->field("mid,data")->order('mid', 'desc')->limit(50)->select();
        } else {
            $data = User::table('message')->where("to", $post["groupid"])->field("mid,data")->order('mid', 'desc')->limit(50)->select();
        }

        $result = json_encode($data);
        return $result;

    }

    public function deleteMessage()
    {
        $post = $_POST;
        $data = User::table('message')->where('mid', $post["mid"])->delete();
        if ($data) {
            return '删除成功！';
        } else {
            return '删除失败！';
        }

    }

}

