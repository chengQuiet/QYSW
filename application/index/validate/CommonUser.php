<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/21
 * Time: 9:27
 */

namespace app\index\validate;


use think\Validate;

class CommonUser extends Validate
{
    protected $rule=[
        'username'=>'require|max:9',
        'password'=>'require|min:3',
    ];
    protected $message=[
        'username.require'=>'用户名不能为空',
        'username.max'=>'用户名最多为9位',
        'password.require'=>'密码不能为空',
        'password.min'=>'密码最少为10位',
        'captcha.require'=>'验证码不能为空',
        'captcha.captcha'=>'验证码有误',
    ];
    public function sceneLogin()
    {
        return $this->only(['username','password']);
    }
    public function sceneLogon()
    {
        return $this->only(['username','password']);
    }
}