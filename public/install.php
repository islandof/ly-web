<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/shanghai");
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
set_time_limit(0);
ob_end_clean();
ob_implicit_flush(1);
define('WLIVE_ROOT', str_replace('\\', '/', substr(dirname(__FILE__), 0, -7)));
session_start();

$sqlFile = '../install/laychat.sql';
$PHP_SELF = addslashes(htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
@extract($_POST);
@extract($_GET);

/**
 * @param $var
 * @return bool
 */
function writable($var)
{
    $writeable = false;
    $var = WLIVE_ROOT . $var;
    // var_dump($var);exit;
    if (is_writable($var)) {
        $writeable = true;
    }
    return $writeable;
}

$dirarray = array(

    "/config",
    "/runtime",
    "/public"

);

$writeable = array();
$quit = false;
foreach ($dirarray as $key => $dir) {
    $writeable[$key]['name'] = $dir;
    if (writable($dir)) {
        $writeable[$key]['status'] = 'OK';
    } else {
        $writeable[$key]['status'] = 'False';
        $quit = true;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="iiSNS install">
    <meta name="author" content="Shiyang">
    <title>LayChat installer</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- Custom styles for this template -->
    <link href="assets/css/main.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>

    <![endif]-->
    <style>
        .red {
            color: red;
        }

        .green {
            color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header clearfix">
        <h3 class="text-muted">LayChat 云插件安装向导</h3>
    </div>
    <div class="row marketing">
        <?php if (!file_exists('index.php')): ?>
        <?php if (!@$step): ?>
            <div class="progress" style="height:30px;font-weight: bold;">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
                     aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                     style="width: 33.3%;border-right: 2px solid #fff;font-size: 16px;padding: 6px;">
                    第一步
                </div>
                <div class="progress-bar " role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                     style="width: 33.3%;border-right: 2px solid #fff;background: #dddddd;font-size: 16px;padding: 6px;">
                    第二步
                </div>
                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                     style="width: 33.4%;background: #ddd;font-size: 16px;padding: 6px;">
                    第三步
                </div>

            </div>
            <h2>环境检测</h2>

            <table class="table table-hover">
                <caption>php扩展检测</caption>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Current server</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <td>PHP Version</td>
                    <td><?php echo PHP_VERSION; ?></td>
                    <?php
                    if (PHP_VERSION >= '5.4') {
                        ?>
                        <td class="green">OK</td>
                        <?php
                    } else {
                        ?>
                        <td class='red'>False</td>
                        <?php
                        $quit = true;
                    }
                    ?>

                </tr>
                </tr>

                <tr>
                    <td>Mbstring</td>
                    <td><?php echo extension_loaded('mbstring'); ?></td>
                    <?php
                    if (extension_loaded('mbstring')) {
                        ?>
                        <td class="green">OK</td>
                        <?php
                    } else {
                        ?>
                        <td class='red'>False</td>
                        <?php
                        $quit = true;
                    }
                    ?>

                </tr>

                <tr>
                    <td>GD</td>
                    <td><?php echo extension_loaded('gd'); ?></td>

                    <?php
                    if (extension_loaded('gd')) {
                        ?>
                        <td class="green">OK</td>
                        <?php
                    } else {
                        ?>
                        <td class='red'>False</td>
                        <?php
                        $quit = true;
                    }
                    ?>
                </tr>

                <tr>
                    <td>curl</td>
                    <td><?php echo extension_loaded('mbstring'); ?></td>
                    <?php
                    if (extension_loaded('curl')) {
                        ?>
                        <td class="green">OK</td>
                        <?php
                    } else {
                        ?>
                        <td class='red'>False</td>
                        <?php
                        $quit = true;
                    }
                    ?>

                </tr>

                <tr>
                    <td>mysqli</td>
                    <td><?php echo extension_loaded('mysqli'); ?></td>

                    <?php
                    if (extension_loaded('mysqli')) {
                        ?>
                        <td class="green">OK</td>
                        <?php
                    } else {
                        ?>
                        <td class='red'>False</td>
                        <?php
                        $quit = true;
                    }
                    ?>
                </tr>


                </tbody>
            </table>
            <table class="table table-hover">
                <caption>文件可写检测</caption>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($writeable as $item): ?>
                    <tr class="<?php echo ($item['status'] == 'OK') ? 'success' : 'danger'; ?>">
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($quit): ?>
                <center style="margin-bottom: 20px;">
                    <a href="/install.php" style="display: inline-block;width: 90px;" class="btn btn-danger">重新检测</a>
                </center>
            <?php else: ?>
                <center style="margin-bottom: 20px;">
                    <a href="install.php?step=2" style="display: inline-block;width: 90px;"
                       class="btn btn-success">下一步</a>
                </center>
            <?php endif; ?>

        <?php elseif ($step == 2): ?>
        <div class="progress" style="height:30px;font-weight: bold;">
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60"
                 aria-valuemin="0" aria-valuemax="100"
                 style="width: 33.3%;border-right: 2px solid #fff;font-size: 16px;padding: 6px;">
                第一步
            </div>
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60"
                 aria-valuemin="0" aria-valuemax="100"
                 style="width: 33.3%;border-right: 2px solid #fff;font-size: 16px;padding: 6px;">
                第二步
            </div>
            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                 style="width: 33.4%;background: #ddd;font-size: 16px;padding: 6px;">
                第三步
            </div>

        </div>
        <form method="post" action="install.php?step=3">
            <div class="col-md-6">
                <fieldset>
                    <legend>
                        <small>填写数据库信息</small>
                    </legend>
                    <div class="form-group">
                        <label class="control-label" for="dbHost">Host</label>
                        <input type="text" class="form-control" id="dbHost" name="dbHost"
                               value="<?php if (isset($_POST['dbHost'])) echo $_POST['dbHost']; else echo 'localhost'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="dbName">Database name</label>
                        <input type="text" class="form-control" id="dbName" name="dbName"
                               value="<?php if (isset($_POST['dbName'])) echo $_POST['dbName']; ?>"
                               placeholder="Database Name">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="dbUser">Username</label>
                        <input type="text" class="form-control" id="dbUser" name="dbUser"
                               value="<?php if (isset($_POST['dbUser'])) echo $_POST['dbUser']; ?>"
                               placeholder="Database Username">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="dbPass">Password</label>
                        <input type="text" class="form-control" id="dbPass" name="dbPass"
                               value="<?php if (isset($_POST['dbPass'])) echo $_POST['dbPass']; ?>"
                               placeholder="Database Password">
                    </div>


                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend>
                        <small>填写用户数据</small>
                    </legend>
                    <div class="form-group">
                        <label class="control-label" for="user">管理员名称</label>
                        <input type="text" class="form-control" id="user" name="user"
                               value="<?php if (isset($_POST['user'])) echo $_POST['user']; ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="password">密码</label>
                        <input type="text" class="form-control" id="password" name="password"
                               value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>"
                               placeholder="">
                    </div>
                    <?php if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' ){ ?>
             

                    <div class="form-group">
                        <label class="control-label" for="host">当前服务器ip</label>
                        <input type="text" class="form-control" id="host" name="host"
                               value="<?php if (isset($_POST['host'])) echo $_POST['host']; ?>"
                               placeholder="请填写当前服务器ip或域名，便于通讯">
                    </div>
                    <?php }else{ ?>
                      <div class="form-group hide">
                        <label class="control-label" for="host">当前服务器ip</label>
                        <input type="text" class="form-control" id="host" name="host"
                               value="<?php  echo $_SERVER['SERVER_NAME']; ?>"
                               placeholder="请填写当前服务器ip或域名，便于通讯">
                    </div>
                    <?php } ?>
                </fieldset>
            </div>
            <center>
                <div class="form-actions" style="margin-bottom:20px;">
                    <button type="submit" style="width: 90px;" class=" btn btn-success">下一步</button>
                </div>
            </center>
    </div>
    </form>
    <?php elseif ($step == 3): ?>
        <div class="progress" style="height:30px;font-weight: bold;">
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60"
                 aria-valuemin="0" aria-valuemax="100"
                 style="width: 33.3%;border-right: 2px solid #fff;font-size: 16px;">
                第一步
            </div>
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60"
                 aria-valuemin="0" aria-valuemax="100"
                 style="width: 33.3%;border-right: 2px solid #fff;font-size: 16px;">
                第二步
            </div>
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60"
                 aria-valuemin="0" aria-valuemax="100" style="width: 33.4%;font-size: 16px;">
                第三步
            </div>

        </div>


        <?php

        if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($dbPass) || empty($user) || empty($password) || empty($host)): ?>

            <div class="alert alert-danger" role="alert"><strong>错误.</strong> 请把表单数据填完</div>

            <center style="margin-bottom: 20px;">
                <a href="javascript:history.go(-1)" style="display: inline-block;width: 90px;" class="btn btn-default">返回上一步</a>
            </center>

        <?php else: ?>


            <?php

           
            $link = mysqli_connect($dbHost, $dbUser, $dbPass);
            if (mysqli_connect_errno()) {
                echo '<div class="alert alert-danger" role="alert">Connection failed: ' . mysqli_connect_error() . '</div>';
                echo "<br>";
                echo '<a href="javascript:history.go(-1)" class="btn btn-default">返回上一步</a>';
                exit();
            }

            if (mysqli_select_db($link, $dbName) === false) {
                mysqli_query($link, "CREATE DATABASE {$dbName}  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
                mysqli_select_db($link, $dbName);
            }

            $sql =file_get_contents($sqlFile);
          

            foreach (explode(";", trim($sql)) as $query) {
                mysqli_select_db($link, $dbName);
                @mysqli_query($link, trim($query));  
            }


            mysqli_select_db($link, $dbName);

            $pass = md5($user . "laychat" . $password);
            $time=time();
            $app_key ='b054014693241bcd9c20';
            $secret='44166c9e7acafe44a320';
            $res =mysqli_query($link,"insert into admin(id,username,password) values(1,'{$user}','{$pass}')");
            $result = mysqli_query($link, "insert into app(app_key,app_secret,orderid,create_time,buy_time) values('{$app_key}','{$secret}',1,'{$time}','{$time}')");

            if (!$result) {

                echo '<div class="alert alert-danger" role="alert">Connection failed: ' . mysqli_error($link) . '</div>';
                echo "<br>";
                echo '<a href="javascript:history.go(-1)" class="btn btn-default">返回上一步</a>';
                exit();

            }

            mysqli_close($link);

            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

            if($http_type == 'https://'){
                $ws_type ='wss://';
            }else{
                $ws_type ='ws://';
            }

            $api =  $http_type . $host;

            $aport = '2080';

             $_SESSION['user'] = null;

            ?>

            <div class="alert alert-success"><strong>数据已经导入!</strong>安装已经成功!</div>

            <div>
                <p>ws:<b><?php echo  $ws_type . $host . ':9090'; ?></b></p>
                <p>api:<b><?php echo  $http_type . $host . ':2080'; ?></b></p>
                <p></p>
            </div>

            <?php sleep(2); ?>

            <center style="margin-bottom: 20px;">
                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>" style="display:inline-block;width: 90px;"
                   class="btn btn-success">完成</a>
            </center>
            <?php


            file_put_contents("../config/database.php", "<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库调试模式
    'debug'          => true,
    // 是否严格检查字段是否存在
    'fields_strict'  => true,
    // 是否自动写入时间戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false,
    // 数据库类型
    'type'           => 'mysql',
    // 服务器地址
    'hostname'       => '{$dbHost}',
    // 数据库名
    'database'       => '{$dbName}',
    // 用户名
    'username'       => '{$dbUser}',
    // 密码
    'password'       => '{$dbPass}',
    // 端口
    'hostport'       => '',
    // 数据库表前缀
    'prefix'         => '',
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库连接参数
    'params'         => [],
];
");


            $url = $http_type . $_SERVER['HTTP_HOST'];
            $ws  =  $ws_type . $host;

            file_put_contents('laychat.js', " layui.config({
    base: '{$url}/assets/libs/'
}).extend({
    'woker.min': 'woker/woker.min',
    'swfobject': 'websocket/swfobject',
    'web_socket': 'websocket/web_socket',
    'recorder': 'webrtc/recorder'
});

if(typeof $ != 'function') {
       layui.use(['jquery'], function () {
        laychat.jquery = layui.jquery;
    });
}

layui.link('{$url}/assets/libs/layui/css/modules/demo.css');
var laychat = {
    appName: 'LayChat',
    websocketAddress: '{$ws}:9090',
    membersUrl: '{$url}/publics/members',
    sendMessageUrl: '{$url}/publics/sendmessage',
    uploadImageUrl: '{$url}/publics/upload_image',
    uploadFileUrl: '{$url}/publics/upload_file',
    addFriendUrl: '{$url}/publics/add_friend',
    removeFriendUrl: '{$url}/publics/remove_friend',
    addGroupUrl: '{$url}/publics/join_group',
    leaveGroupUrl: '{$url}/publics/leave_group',
    chatlog: '{$url}/publics/chatlog/mine/',
    noreadMsg: '{$url}/publics/Offlinemsg',
    uploadVoiceUrl: '{$url}/publics/saveVoice',
    initUrl: null,
    brief: false,
    setmin: false,
    right: '0px',
    minRight: null,
    initSkin: '',
    isAudio: false,
    isVideo: false,
    notice: false,
    maxLength: 3000,
    skin: null,
    copyright: false,
    isMobile: false,
    setMin: false,
    enableNewFriend:false,
    enableAudio: false,
    enableVideo: true,
    enableGroup: true,
    enableFriend: true,
    friendList:'',
    groupList:'',
    voice: 'default.mp3',
    inited: 0,
    socket: null,
    open: function () {

        laychat.Listenpaste();

        if (this.inited) return;
        if (this.isIE6or7()) return;

        if (laychat.isMobile) {
            layui.link('{$url}/assets/libs/layui/css/layui.mobile.css');
        } else {
            layui.link('{$url}/assets/libs/layui/css/layui.css');

        }

        if (typeof WebSocket != 'function') {

            layui.use(['jquery', 'swfobject', 'web_socket', 'woker.min'], function () {
                WEB_SOCKET_SWF_LOCATION = '{$url}/assets/libs/swf/WebSocketMain.swf';
                WEB_SOCKET_DEBUG = true;
                WEB_SOCKET_SUPPRESS_CROSS_DOMAIN_SWF_ERROR = true;


                laychat.jquery = layui.jquery;
                if (laychat.initUrl != null) {
                    laychat.jquery = layui.jquery;
                    laychat.jquery.ajax({
                        url: laychat.initUrl,
                        type: 'get',
                        dataType: 'json',
                        success: function (res) {

                            if (res.code == 0) {
                                var data = res.data;
                                laychat.mine =data.mine;
                                laychat.friendList =data.friend;
                                laychat.groupList  =data.group;
                                
                                laychat.initIM(data);
                                laychat.connect(data);

                            }
                        }
                    });
                } else {
                   
                    var data = {
                        'mine': laychat.mine,
                        'friend': laychat.friendList,
                        'group': laychat.groupList
                    };
                    laychat.initIM(data);
                    laychat.connect(data);
                }


            });
        } else {
            layui.use(['jquery', 'woker.min'], function () {
                laychat.jquery = layui.jquery;


                if (laychat.initUrl != null) {
                    laychat.jquery = layui.jquery;
                    laychat.jquery.ajax({
                        url: laychat.initUrl,
                        type: 'get',
                        dataType: 'json',
                        success: function (res) {

                            if (res.code == 0) {
                                var data = res.data;
                                laychat.mine=data.mine;
                                laychat.friendList=data.friend;
                                laychat.groupList=data.group;
                              

                                laychat.initIM(data);
                                laychat.connect(data);

                            }
                        }
                    });
                } else {
                     
                    var data = {
                        'mine': laychat.mine,
                        'friend': laychat.friendList,
                        'group': laychat.groupList
                    };
                    laychat.initIM(data);
                    laychat.connect(data);
                }

            });
        }

    },
    isIE6or7: function () {
        var b = document.createElement('b');
        b.innerHTML = '<!--[if IE 5]><i></i><![endif]--><!--[if IE 6]><i></i><![endif]--><!--[if IE 7]><i></i><![endif]-->';
        return b.getElementsByTagName('i').length === 1;
    },
    connect: function (res) {

        var falgstr = this.websocketAddress;
        var arr = falgstr.split(':');
        var host = arr[1].replace('//', '');
        if (arr[0] == 'wss') {
            type = 'wss';
            var woker = new Woker(laychat.app_key, {
                encrypted: true
                , enabledTransports: ['wss']
                , wsHost: host
                , wssPort: arr[2]
            });

        } else {
            var woker = new Woker(laychat.app_key, {
                encrypted: false
                , enabledTransports: ['ws']
                , wsHost: host
                , wsPort: arr[2]
            });
        }

        var channel = woker.subscribe('user' + res.mine.id);

        channel.on('woker:subscription_succeeded', function () {

             var mine =res.mine.id;

                layui.use(['jquery','layim'], function () {
                    laychat.jquery = layui.jquery;
                    laychat.jquery.ajax({
                        url: laychat.noreadMsg,
                        type: 'post',
                        data: {id: res.mine.id, group: res.group, app_key: laychat.app_key},
                        dataType:'json',
                        success: function (res) {
                              if (res.code ==  0) {
                                 layui.each(res.data,function(k,v){
                                     var obj = laychat.jquery.parseJSON(v);
                                     if(obj.type == 'friend'){
                                        layui.layim.getMessage(obj);
                                    }else{
                                        if(obj.fromid != mine){
                                            layui.layim.getMessage(obj);
                                        }
                                    }
                                 });
                             }
                        }
                    });
                });

        });

        // 接受消息
        channel.on('getmessage', function (data) {

            layui.layim.getMessage(data.message);
        });

        channel.on('addfriend', function (data) {
            data.message.groupid =res.friend[0].id;
            layui.layim.addList(data.message);
        });

        channel.on('removefriend', function (data) {
            layui.layim.removeList(data.message);
        });

        channel.on('video', function (data) {

            layui.use('layer', function () {

                var msg = data.message;
                var cha = data.channel;
                var avatar =data.avatar;
                var username =data.username;
                var userid =data.mine;

                var layer = layui.layer;
                layer.open({
                    type: 1,
                    title: '申请框',
                    area: ['260px', '180px'],
                    shade: 0.01,
                    fixed: true,
                    btn: ['接受', '拒绝'],
                    content: \"<div style='position: absolute;left:20px;top:15px;'><img src='\"+avatar+\"' width='40px' height='40px' style='border-radius:40px;position:absolute;left:5px;top:5px;'><span style='width:100px;position:absolute;left:70px;top:5px;font-size:13px;overflow-x: hidden;'>\"+username+\"</span><div style='width:90px;height:20px;position:absolute;left:70px;top:26px;'>\"+msg+\"</div></div>\",
                    yes: function () {
                         layer.close(layer.index);
                      
                            var iWidth = 400;  // 窗口宽度
                            var iHeight = 300; // 窗口高度
                            var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
                            var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;

                            win = window.open(\"https://rtc.laychat.net/call/\" + cha, \"_blank\", \"toolbar=no,directories=no, status=no, menubar=yes, scrollbars=yes, resizable=no, copyhistory=yes, width=\" + iWidth + \", height=\" + iHeight + \",top=\" + iTop + \",left=\" + iLeft + \",alwaysRaised=yes\");

                        
                    },
                    btn2: function () {

                        laychat.jquery.ajax({
                            url:'{$url}/publics/index/refuse',
                            type:'post',
                            data:{appkey:laychat.app_key,id:userid}
                        });

                         layer.close(layer.index);
                    }
                });
            });

        });

        channel.on('video-refuse',function (data) {

            layui.use('layer',function () {
               layer =layui.layer;
               layer.alert(data.message);
            });
        });


        var groups = res.group;


        channel.on('addgroup', function (data) {

            var channels = woker.subscribe('group' + data.message.id);
            channels.on('getmessage', function (data) {
                if (data.message.fromid != res.mine.id) {
                    layui.layim.getMessage(data.message);
                }
            });
        });


        channel.on('livegroup', function (data) {

            woker.unsubscribe('group' + res.message.id);
            layui.each(groups, function (k, v) {
                if (v.id == data.message) {
                    groups[k] = '';
                }
            });
        });


        // 接受群消息

        if (groups.length >= 1) {
            layui.each(groups, function (k, v) {
                var channels = woker.subscribe('group' + v.id);
                channels.on('getmessage', function (data) {

                    if (data.message.fromid != res.mine.id) {
                        layui.layim.getMessage(data.message);
                    }
                });

            });
        }

        // 重连接
        woker.connection.on('state_change', function (states) {
            // states = {previous: 'oldState', current: 'newState'}
          if (states.current == 'unavailable' || states.current == 'disconnected' || states.current == 'failed') {

                woker.unsubscribe('user' +res.mine.id);
                if (groups.length >= 1) {

                    layui.each(groups, function (k, v) {
                        woker.unsubscribe('group' + v.id);
                    });

                }
                if (typeof woker.isdisconnect == 'undefined') {
                    woker.isdisconnect = true;
                    woker.disconnect();
                    delete woker;
                    window.setTimeout(function(){
                        laychat.connect(res);
                    },1000);
                }

            }

        });
    },
    addfriend: function (data) {

        laychat.jquery.ajax({
            url: laychat.addFriendUrl,
            type: 'post',
            data: data,
            success: function (res) {
                if (res) {
                    var res1 = laychat.jquery.parseJSON(res);
                    if (res1.code != 0) {
                        layui.layer.msg(res1.msg);
                    }
                }
            }
        });

        layui.layim.addList(data.addfriend);

    },
    removefriend: function (data) {

        laychat.jquery.ajax({
            url: laychat.removeFriendUrl,
            type: 'post',
            data: data,
            success: function (res) {
                if (res) {
                    var res1 = laychat.jquery.parseJSON(res);
                    if (res1.code != 0) {
                        layui.layer.msg(res1.msg);
                    }
                }
            }
        });
        layui.layim.removeList(data.removefriend);
    },
    addgroup: function (data) {

        laychat.jquery.ajax({
            url: laychat.addGroupUrl,
            type: 'post',
            data: data,
            success: function (res) {
                if (res) {
                    var res1 = laychat.jquery.parseJSON(res);
                    if (res1.code != '-1') {
                        layui.layer.msg(res1.msg);
                    }
                }
            }
        });

        layui.layim.addList(data.addgroup);

    },
    removegroup: function (data) {

        laychat.jquery.ajax({
            url: laychat.leaveGroupUrl,
            type: 'post',
            data: data,
            success: function (res) {
                if (res) {
                    var res1 = laychat.jquery.parseJSON(res);
                    if (res1.code != 0) {
                        layui.layer.msg(res1.msg);
                    }
                }
            }
        });
        layui.layim.removeList(data.removegroup);

    },
    
    focusInsert : function(str){
        
        if(laychat.jquery('.layim-chat-list .layim-this').index() < 0){
             console.log('没有聊天框的存在');
        }else{
            var obj =laychat.jquery('.layim-chat-textarea textarea')[laychat.jquery('.layim-chat-list .layim-this').index()];
       
            var result, val = obj.value;
            obj.focus();
            if(document.selection){ 
              result = document.selection.createRange(); 
              document.selection.empty(); 
              result.text = str; 
            } else {
              result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
              obj.focus();
              obj.value = result.join('');
            }

        }

    },
 
    imgReader:function(item){

            var blob = item.getAsFile(),
                        reader = new FileReader();
                    reader.onload = function( e ){
                        imgSrc = e.target.result;

                    
                        layui.layer.msg('正在上传截图',{end:function(){

                              laychat.jquery.post('{$url}/publics/set/uploadimg',{imageContent:imgSrc},function(result){
                                if(result.code == 0){

                                  laychat.focusInsert('img['+result.data.src+']');
                                    
                                }else{
                                    layui.layer.msg(result.msg);
                                }
                            },'json');

                        }});
                    };

                    reader.readAsDataURL( blob );

    },

   Listenpaste:function(){

        layui.use(['jquery'], function () {
         laychat.jquery = layui.jquery;

         laychat.jquery('body').unbind('paste','.layim-chat-textarea textarea').bind('paste','.layim-chat-textarea textarea',function(e){
                       var clipboardData = event.clipboardData || window.clipboardData || event.originalEvent.clipboardData;
                        var   i = 0, items, item, types;
                        if( clipboardData ){
                            items = clipboardData.items;
                            if( !items ){
                                return;
                            }
                            item = items[0];
                            types = clipboardData.types || [];
                            for(var i = 0; i < types.length; i++ ){
                                if( types[i] === 'Files' ){
                                    item = items[i];
                                    break;
                                }
                            }
                            if( item && item.kind === 'file' && item.type.match(/^image\//i) ){
                               laychat.imgReader( item );
                            }
                        }
                    });

     });


   },

    initIM: function (res) {

        var module = laychat.isMobile ? 'mobile' : 'layim';


        layui.use([module, 'recorder'], function (layim) {


            if (laychat.isMobile) {
                var mobile = layui.mobile,
                    layim = mobile.layim,
                    layer = mobile.layer;
                layui.layim = layim;
                layui.layer = layer;
            }

            // 初始化消息
            layim.on('ready', function (options) {
               
            });


            // 监听查看群员
            layim.on('members', function (data) {
                console.log(data);
            });
            // 监听发送消息
            layim.on('sendMessage', function (res) {

                if (res.to.type == 'friend') {

                
                        var newdata = {
                            username: res.mine.username //消息来源用户名
                            , avatar: res.mine.avatar //消息来源用户头像
                            , fromid: res.mine.id
                            , type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                            , content: res.mine.content //消息内容
                            , toid: res.to.id
                            , appkey: laychat.app_key
                        };
                  
                } else {

                  
                        var newdata = {
                            username: res.mine.username //消息来源用户名
                            , avatar: res.mine.avatar //消息来源用户头像
                            , fromid: res.mine.id
                            , type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                            , content: res.mine.content //消息内容
                            , toid: res.to.id
                            , appkey: laychat.app_key

                        };
                   
                }


                laychat.jquery.ajax({
                    url: laychat.sendMessageUrl,
                    type: 'post',
                    data: newdata,
                    success: function (res) {

                        if (res) {
                            var res1 = laychat.jquery.parseJSON(res);
                            if (res1.code != 0) {
                               layui.layer.msg(res1.msg);
                            }
                        }
                    }
                });
            });

            var data = res;

          

            layim.on('tool(video)', function (insert, send, obj) {

                //console.log(data.mine.id);

                if (obj.data.type == 'friend') {

                    var times = (new Date()).valueOf();

                    laychat.jquery.ajax({
                        url: '{$url}/publics/index/apply',
                        type: 'post',
                        data: {
                            id: obj.data.id,
                            appkey: laychat.app_key,
                            channel: times,
                            name: laychat.mine.username,
                            avatar: laychat.mine.avatar,
                            mine: laychat.mine.id
                        },
                        success: function (res) {
                            if (res) {
                                layui.use('layer', function () {
                                    layer = layui.layer;
                                    layer.msg(res, {icon: 2});
                                });
                            }
                        }
                    });

                    var falg = window.location.protocol;
                
                        var iWidth = 400;  // 窗口宽度
                        var iHeight = 300; // 窗口高度
                        var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
                        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;

                        win = window.open(\"https://rtc.laychat.net/call/\" + times, \"_blank\", \"toolbar=no,directories=no, status=no, menubar=yes, scrollbars=yes, resizable=no, copyhistory=yes, width=\" + iWidth + \", height=\" + iHeight + \",top=\" + iTop + \",left=\" + iLeft + \",alwaysRaised=yes\");

                } else {
                    layui.use('layer', function () {
                        layer = layui.layer;
                        layer.msg('群聊暂时不支持视频通话！');
                    });

                }

            });


            //监听自定义工具栏点击，以添加代码为例
            layim.on('tool(voice)', function (insert, send, obj) {

                //音频先加载
                var audio_context;
                var recorder;
                var wavBlob;
                //创建音频
                try {
                    // webkit shim
                    window.AudioContext = window.AudioContext || window.webkitAudioContext;
                    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.mediaDevices.getUserMedia;
                    window.URL = window.URL || window.webkitURL;

                    audio_context = new AudioContext;

                    if (!navigator.getUserMedia) {
                        console.log('语音创建失败');
                    }
                    ;
                } catch (e) {
                    console.log(e);
                    return;
                }
                navigator.getUserMedia({audio: true}, function (stream) {
                    var input = audio_context.createMediaStreamSource(stream);
                    recorder = new Recorder(input);

                    var falg = window.location.protocol;
                    if (falg == 'https:') {
                        recorder && recorder.record();

                        //示范一个公告层
                        layui.use(['jquery', 'layer'], function () {
                            var layer = layui.layer;

                            layer.msg('录音中...', {
                                icon: 16
                                , shade: 0.01
                                , skin: 'layui-layer-lan demo-class'
                                , time: 0 //20s后自动关闭
                                , btn: ['发送', '取消']
                                , yes: function (index, layero) {
                                    //按钮【按钮一】的回调
                                    recorder && recorder.stop();
                                    recorder && recorder.exportWAV(function (blob) {
                                        wavBlob = blob;
                                        var fd = new FormData();
                                        var wavName = encodeURIComponent('audio_recording_' + new Date().getTime() + '.wav');
                                        fd.append('wavName', wavName);
                                        fd.append('file', wavBlob);

                                        var xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                jsonObject = JSON.parse(xhr.responseText);
                                                voicemessage = \"audio[\" + jsonObject.data.src + \"]\";
                                                insert(voicemessage);
                                                send();
                                            }
                                        };
                                        xhr.open('POST', laychat.uploadVoiceUrl);
                                        xhr.send(fd);
                                    });
                                    recorder.clear();
                                    layer.close(index);
                                }
                                , btn2: function (index, layero) {
                                    //按钮【按钮二】的回调
                                    recorder && recorder.stop();
                                    recorder.clear();
                                    audio_context.close();
                                    layer.close(index);
                                }
                            });

                        });
                    } else {
                        layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.msg('音频输入只支持https协议！');
                        });
                    }


                }, function (e) {
                    layui.use('layer', function () {
                            var layer = layui.layer;
                            layer.msg('音频输入只支持https协议！');
                        });
                });


            });
            
            if (laychat.friendList === false ) {
                laychat.enableFriend = false;
            }

            if (laychat.groupList === false ) {
                laychat.enableGroup = false;
            }

            if (laychat.enableAudio == false && laychat.enableVideo == false) {
                var tooldata = '';
            }

            if (laychat.enableAudio == true && laychat.enableVideo == false) {
                var tooldata = [{alias: 'voice', title: '音频', icon: '&#xe688;'}];
            }

            if (laychat.enableAudio == false && laychat.enableVideo == true) {
                var tooldata = [{alias: 'video', title: '视频', icon: '&#xe6ed;'}];
            }

            if (laychat.enableAudio == true && laychat.enableVideo == true) {
                var tooldata = [{alias: 'video', title: '视频', icon: '&#xe6ed;'}, {
                    alias: 'voice',
                    title: '音频',
                    icon: '&#xe688;'
                }];
            }
            
            if(laychat.uploadImageUrl == false){
               var imgdata = false;
            }else{
                var imgdata ={
                    url:laychat.uploadImageUrl
                }
            }

            if(laychat.uploadFileUrl == false){
                var filedata =false;
            }else{
                var filedata={
                    url:laychat.uploadFileUrl
                }
            }

            layui.layim.config({

                //初始化接口
                init: data
                , tool: tooldata
                // 上传图片
                , uploadImage: imgdata
                // 上传文件
                , uploadFile: filedata
                , isgroup: laychat.enableGroup
                , isfriend: laychat.enableFriend
                , isNewFriend:  laychat.enableNewFriend
                //聊天记录地址
                , chatLog: laychat.chatlog + data.mine.id + '/'+ laychat.app_key
                , copyright: false //是否授权
                , title: laychat.appName
                , min: laychat.setMin
                , brief: laychat.brief
                , right: laychat.right
                , isAudio:laychat.isAudio
                , isVideo:laychat.isVideo
                , minRight: laychat.minRight
                , initSkin: laychat.initSkin
                , voice:laychat.voice
                , notice: laychat.notice
                , maxLength: laychat.maxLength
                , skin: laychat.skin
                , members: {
                    url: laychat.membersUrl
                }
            });

        });
    }

}

    ");

            file_put_contents('index.php', "<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
session_start();
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

define('ROOT', __DIR__ );

// 定义配置文件目录
define('CONF_PATH', __DIR__ . '/../config/'); 

define('Api_Host', '{$api}');
define('Api_port', '{$aport}');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
");


            ?>

        <?php endif; ?>

    <?php endif; ?>

    <?php else: ?>

        <div class="alert alert-success"><strong>已经安装成功</strong></div>


    <?php endif; ?>
</div>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">&copy; 蜗壳 2017</p>
    </div>
</footer>
<!-- javascript -->
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
