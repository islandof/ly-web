<?php
/**
 * Handler File Class
 *
 * @author liliang <liliang@wolive.cc>
 * @email liliang@wolive.cc
 * @date 2017/06/01
 */

namespace app\admin\validate;

use think\Validate;

/**
 *  注册验证器.
 */
class Admin extends Validate
{

    /**
     * 验证规则.
     * [$rule description]
     * @var [type]
     */
    protected $rule = [
        "username" => "require|length:1,16|alphaNum|unique:admin",
        "password" => "require|length:6,16|alphaNum",
        "password2" => "require|confirm:password",
        'code' => 'require|captcha:regist_login',
    ];

    /**
     * 验证失败信息.
     * [$message description]
     * @var array
     */
    protected $message = [
        "username.require" => "请填写用户名称",
        "username.unique" => "该用户名存在",
        "username.alphaNum" => "用户名只能是字母和数字",
        "password.alphaNum" => "用户名只能是字母和数字",
        "username.length" => "用户名长度为1~16个字符",
        "password.requireIf" => "请填写登录密码",
        "password.length" => "登录密码长度为6~16个字符",
        "password2.confirm" => "密码不一致",
        "password2.require" => "请再次输入密码",
        'code.require' => '请填写验证码',
        'code.captcha' => '验证码不正确',
    ];


    /**
     * 验证场景.
     * @access protected
     * @var array
     */
    protected $scene = [

    ];

}
