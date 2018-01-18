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
 *
 * 登陆验证器.
 */
class Login extends Validate
{

    /**
     * 验证规则.
     * [$rule description]
     * @var array
     */
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'code' => 'require|captcha:admin_login'
    ];

    /**
     * 验证消息.
     * [$messege description]
     * @var [type]
     */
    protected $message = [
        'username.require' => '请填写登录帐号',
        'password.require' => '请填写登录密码',
        'code.require' => '请填写验证码',
        'code.captcha' => '验证码不正确',
    ];
}
