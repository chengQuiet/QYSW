<?php
/**
 * Created by PhpStorm.
 * User: hpc
 * Date: 2021/4/25
 * Time: 16:49
 */

namespace app\index\controller;

use app\index\library\Auth;

use think\Controller;

class Common extends Controller
{
    protected $auth;
    protected function initialize()
    {

        $this->auth=Auth::getInstance();
    }
}