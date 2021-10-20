<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/8
 * Time: 8:24
 */

namespace app\admin\controller;
use app\admin\library\Auth;
use think\Controller;
//use think\facade\Validate;
use think\facade\Session;
class Common extends Controller
{
    protected $checkLoginExclude=[];
    protected $auth;
    protected function initialize()
    {
       $this->auth=Auth::getInstance();
       $controller=$this->request->controller();
       $action=$this->request->action();
       if(in_array($action,$this->checkLoginExclude)){
           return ;
       }
       if(!$this->auth->isLogin()){
           $this->error('您还没登陆.','Index/login');
        }
        if(!$this->request->isAjax()){
            $layout_login_user['username']=Session::get('username');
//        $this->assign('layout_menu',$layout_menu);
            $this->assign('layout_login_user',$layout_login_user);
            $this->view->engine->layout('common/layout');
//            $this->assign('layout_menu',$this->auth->menu($controller));
        }
    }
}