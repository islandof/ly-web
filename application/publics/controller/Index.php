<?php

namespace app\publics\controller;

use app\extra\push\WokerAPI;
use think\Controller;
use app\publics\model\User;

header('Access-Control-Allow-Origin:*');

class Index extends Controller
{

    /**
     * 发送消息
     * @return string
     */
    public function sendmessage()
    {
        $post = $this->request->post();


        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();

        if ($app) {
            $gagword = User::table("gagwords")->where("app_id", $app['id'])->select();

            if ($gagword) {

                $search = [];
                foreach ($gagword as $vd) {
                    $search[] = $vd['data'];
                }

                $word = $post['content'];

                $newword = str_replace($search, "***", $word);

                $post['content'] = $newword;
            }

            $option = array('host' => Api_Host, "port" => Api_port);

            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);

            $post["mine"] = false;

            $appkey = $post['appkey'];
            unset($post["appkey"]);
            $post['timestamp'] = microtime(true) * 1000;

            if ($post['type'] == 'group') {
                $post['id'] = $post['toid'];
            } else {
                $post['id'] = $post['fromid'];
            }
            $data = json_encode($post);
            // 组合数据
            $arr = [
                "app_key" => $appkey,
                "from" => $post["fromid"],
                "to" => $post["toid"],
                "data" => $data,
                "type" => $post["type"],
                "timestamp" => time()
            ];


            $white = User::table('white_list')->where(['app_id' => $app['id'], 'userid' => $post["fromid"]])->find();

            if (!$white) {
                if ($app["state"] == 'forbidden_all_chat') {
                    $arr["code"] = -1;
                    $arr['msg'] = "禁止私聊和群聊";
                    return json_encode($arr);
                }

            }


            $toid = $post['toid'];

            if ($post["type"] == "group") {

                if (!$white) {
                    if ($app["state"] == 'forbidden_group_chat') {
                        $arr["code"] = -1;
                        $arr['msg'] = "禁止群聊";
                        return json_encode($arr);
                    }

                    $groupgaglist = User::table("gaglist")->where(['app_id' => $app['id'], 'gid' => $post['toid'], 'userid' => ""])->find();


                    $gaglist = User::table("gaglist")->where('gid', ['=', 0], ['=', $post['toid']], 'or')->where(['app_id' => $app['id'], 'userid' => $post['fromid']])->find();


                    if ($groupgaglist) {
                        $arr["code"] = -1;
                        $arr["msg"] = "本群现在禁止群聊！";
                        return json_encode($arr);
                    } else if ($gaglist) {
                        $arr["code"] = -1;
                        $arr["msg"] = "已经被禁言！";
                        return json_encode($arr);

                    } else {

                        unset($post["toid"]);
                        $woker->emit('group' . $toid, 'getmessage', array('message' => $post));
                    }

                } else {
                    unset($post["toid"]);
                    $woker->emit('group' . $toid, 'getmessage', array('message' => $post));

                }


            } else {

                if (!$white) {

                    if ($app["state"] == 'forbidden_private_chat') {
                        $arr["code"] = -1;
                        $arr['msg'] = "禁止私聊";
                        return json_encode($arr);
                    }

                    $gag = User::table("gaglist")->where(['app_id' => $app['id'], 'userid' => $post["fromid"]])->find();

                    if ($gag && $gag['gid'] == 0) {
                        $arr["code"] = -1;
                        $arr['msg'] = "已经被禁言！";
                        return json_encode($arr);
                    }
                }


                $woker->emit('user' . $toid, 'getmessage', array('message' => $post));
            }


            $message = User::table("message")->insert($arr);
        } else {

            $arr["code"] = -1;
            $arr['msg'] = "非发消息！";

            return json_encode($arr);
        }
    }

    /**
     * 添加好友
     * @return string
     */
    public function add_friend()
    {

        $post = $this->request->post();
        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();

        if ($app) {
            $option = array('host' => Api_Host, "port" => Api_port);
            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);
            $post["mine"]["type"] = "friend";

            $woker->emit('user' . $post['addfriend']['id'], "addfriend", array('message' => $post['mine']));
        } else {
            $arr["code"] = -1;
            $arr["msg"] = "非法消息！";

            return json_encode($arr);
        }

    }

    /**
     * 删除好友
     * @return string
     */
    public function remove_friend()
    {
        $post = $this->request->post();
        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();

        if ($app) {


            $option = array('host' => Api_Host, "port" => Api_port);

            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);
            $post["mine"]["type"] = "friend";
            $woker->emit("user" . $post['removefriend']["id"], "removefriend", array("message" => $post['mine']));
        } else {
            $arr["code"] = -1;
            $arr["msg"] = "非法消息！";

            return json_encode($arr);
        }
    }

    /**
     * 添加群
     * @return string
     */
    public function join_group()
    {
        $post = $this->request->post();
        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();
        if ($app) {

            $option = array('host' => Api_Host, "port" => Api_port);

            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);
            unset($post["addgroup"]["type"]);
            $woker->emit("user" . $post["mine"]['id'], "addgroup", array("message" => $post["addgroup"]));
        } else {
            $arr["code"] = -1;
            $arr["msg"] = "非法消息！";
            return json_encode($arr);
        }
    }

    /**
     * 删除群
     * @return string
     */
    public function leave_group()
    {
        $post = $this->request->post();
        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();
        if ($app) {

            $option = array('host' => Api_Host, "port" => Api_port);
            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);
            $woker->emit("user" . $post["mine"]['id'], "livegroup", array("message" => $post["removegroup"]));
        } else {
            $arr["code"] = -1;
            $arr["msg"] = "非法消息！";
            return json_encode($arr);
        }
    }


    /**
     * 上传图片
     * @return string
     */
    public function upload_image()
    {


        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $file = $this->request->file("file");

        $size = ($_FILES["file"]["size"]) / 1024;

        if ($size > 10240) {
            $code = 1;
            $error = "文件太大！";
            $imgpath = "";

            $data = [
                "code" => $code,
                "msg" => $error,
                "data" => [
                    "src" => $imgpath
                ]
            ];

            return json_encode($data);
        }
        $newpaths = ROOT_PATH . "/public/assets/upload/images/";
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg,JPG,JPEG,PNG,GIF'])->move($newpaths, bin2hex(pack('N', time()) . pack('n', rand(1, 65535))));

        if ($info) {
            $code = 0;
            $imgname = $info->getFilename();
            $imgpath = $http_type . $_SERVER['HTTP_HOST'] . "/assets/upload/images/" . $imgname;
            $error = "";
        } else {
            $code = 1;
            $error = $file->getError();
            $imgpath = "";
        }

        $data = [
            "code" => $code,
            "msg" => $error,
            "data" => [
                "src" => $imgpath
            ]
        ];

        return json_encode($data);
        // 返回数据格式
    }

    /**
     * 上传文件
     * @return string
     */
    public function upload_file()
    {

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $file = $this->request->file("file");

        $newpaths = ROOT_PATH . "/public/assets/upload/files/";
        $name = $_FILES["file"]["name"];
        $arr = explode(".", $name);
        $ext = $arr[1];
        if ($ext == 'html' || $ext == 'htm' || $ext == 'jsp' || $ext == 'php' || $ext == 'js') {
            $code = 1;
            $error = "不支持该文件格式！";
            $imgpath = "";

            $data = [
                "code" => $code,
                "msg" => $error,
                "data" => [
                    "src" => $imgpath
                    , "name" => $name
                ]
            ];

            return json_encode($data);
        }

        $size = ($_FILES["file"]["size"]) / 1024;
        if ($size > 10240) {
            $code = 1;
            $error = "文件太大！";
            $imgpath = "";

            $data = [
                "code" => $code,
                "msg" => $error,
                "data" => [
                    "src" => $imgpath
                    , "name" => $name
                ]
            ];

            return json_encode($data);
        }

        $info = $file->move($newpaths, bin2hex(pack('N', time()) . pack('n', rand(1, 65535))));
        if ($info) {
            $code = 0;
            $imgname = $info->getFilename();
            $imgpath = $http_type . $_SERVER['HTTP_HOST'] . "/assets/upload/files/" . $imgname;
            $error = "";
        } else {
            $code = 1;
            $error = $file->getError();
            $imgpath = "";
        }

        $data = [
            "code" => $code,
            "msg" => $error,
            "data" => [
                "src" => $imgpath
                , "name" => $name
            ]
        ];

        return json_encode($data);

    }

    /**
     * 查看群成员接口
     * @return string
     */
    public function members()
    {
        $data = [
            "code" => 0
            , "msg" => ""
            , "data" => [
                "list" => []
            ]
        ];
        return json_encode($data);
    }

    /**
     * 视频申请
     * @return string
     */
    public function apply()
    {
        $post = $this->request->post();
        $app_key = $post['appkey'];
        $app = User::table("app")->where("app_key", $app_key)->find();
        if ($app) {
            $option = array('host' => Api_Host, "port" => Api_port);
            $user = User::table('user')->where(['uid' => $post['id'], 'app_key' => $post['appkey']])->find();
            $type = $user['status'];

            if ($type == 'offline') {
                return "对方不在线！";
            }

            $woker = new WokerAPI($app_key, $app['app_secret'], $app['id'], $option);

            $woker->emit("user" . $post['id'], "video", array("message" => "申请视频连接", "channel" => $post['channel'], "avatar" => $post['avatar'], 'username' => $post['name'], "mine" => $post['mine']));
        } else {
            header("Status: 401 Not authenticated");
        }

    }

    /**
     * 储存音频文件
     * @return bool|string
     */
    public function saveVoice()
    {

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $file = $this->request->file('file');

        if ($file) {
            $newpath = ROOT_PATH . "/public/assets/upload/voices/";
            $info = $file->move($newpath, time() . ".wav");

            if ($info) {
                $imgname = $info->getFilename();

                $imgpath = $http_type . $_SERVER['HTTP_HOST'] . "/assets/upload/voices/" . $imgname;
                $arr = [
                    'data' => [
                        'src' => $imgpath
                    ]
                ];
                return json_encode($arr);
            } else {
                return false;
            }
        }
    }

    /**
     *
     * [refuse description]
     * @return [type] [description]
     */
    public function refuse()
    {
        $post = $this->request->post();
        $appkey = $post['appkey'];
        $app = User::table("app")->where("app_key", $appkey)->find();
        if ($app) {
            $option = array('host' => Api_Host, "port" => Api_port);
            $woker = new WokerAPI($appkey, $app['app_secret'], $app['id'], $option);

            $woker->emit('user' . $post['id'], 'video-refuse', array("message" => '对方拒绝视频连接！'));

        } else {
            header("Status: 401 Not authenticated");
        }
    }
}
