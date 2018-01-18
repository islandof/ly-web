<?php
/**
 * Created by PhpStorm.
 * User: chenrui
 * Date: 17-11-8
 * Time: 下午12:02
 */

namespace app\admin\controller;

use think\Controller;
use think\config;
use app\admin\model\App;
use think\captcha\Captcha;


class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function captcha()
    {
        $captcha = new Captcha(Config::get('captcha'));
        return $captcha->entry('admin_login');
    }

    public function recaptcha()
    {
        $captcha = new Captcha(Config::get('captcha'));
        return $captcha->entry('regist_login');
    }

    public function check()
    {
        $post = $this->request->post();

        $result = $this->validate($post, 'login');

        if ($result !== true) {
             $arr=[
              'code'=>0,
              'data'=>$result
            ];
            return json_encode($arr);
        }

        //判断密码 和 用户名
        $pass = md5($post['username'] . "laychat" . $post['password']);

        $admin = App::table('admin')->where(['username' => $post['username'], "password" => $pass])->find();

        if (!$admin) {
             $arr=[
              'code'=>0,
              'data'=>'账号或密码错误！'
            ];
            return json_encode($arr);
        }
        $login = $admin->getData();
        unset($login['password']);
        $_SESSION['user'] = $login;

        $arr=[
          'code'=>1,
          'url'=>'/admin/index/index'
        ];

        return json_encode($arr);
    }

    public function regist()
    {
        return $this->fetch();
    }

    /**
     * 注册验证方法
     * @return array
     */
    public function sign()
    {

        $post = $this->request->post();
        $result = $this->validate($post, "Admin");
        if ($result !== true) {
            $data = [
                'code' => 0,
                'msg' => $result
            ];
            return json_encode($data);
        }

        unset($post['password2']);
        $pass = md5($post['username'] . "laychat" . $post['password']);
        $post['password'] = $pass;
        unset($post['code']);
        $admin = App::table('admin')->insert($post);
        $data = [
            'code' => 1,
            'msg' => '注册成功！',
        ];
        return json_encode($data);
    }

    public function logout()
    {
        $_SESSION['user'] = null;
        $this->redirect(url('admin/login/index'));
    }
}