<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/21
 * Time: 8:53
 */

namespace app\admin\controller;
use app\admin\validate\AdminUser as UserValidate;
use think\App;
use think\Db;

class Index extends Common
{
    protected $checkLoginExclude=['login'];
    public function login()
    {
        if($this->request->isPost())
        {
            $data=[
                'username'=>$this->request->post('username/s','','trim'),
                'password'=>$this->request->post('password/s',''),
                'captcha'=>$this->request->post('captcha/s','','trim'),
            ];
//            var_dump($data);
            $validate=new UserValidate();
            if(!$validate->scene('login')->check($data))
            {
                $this->error('登录失败:'.$validate->getError().'。');
            }
             if(!$this->auth->login($data['username'],$data['password'])){
                 $this->error('登录失败：'.$this->auth->getError().'。');
             }
            $this->success('登陆成功！','index/index');
        }
        else
        {
            return $this->fetch('login');
        }
        return $this->fetch('login');

    }
    public function logon(){
        if($this->request->isPost())
        {
            $data=[
                'username'=>$this->request->post('username/s','','trim'),
                'password'=>$this->request->post('password/s',''),
                'captcha'=>$this->request->post('captcha/s','','trim'),
            ];
//            var_dump($data);
            $validate=new UserValidate();
            if(!$validate->scene('logon')->check($data))
            {
                $this->error('注册失败:'.$validate->getError().'。');
            }
            if(!$this->auth->logon($data['username'],$data['password'])){
                $this->error('注册失败：'.$this->auth->getError().'。');
            }
            $this->success('注册成功！');

        }
        else
        {
          return $this->fetch('logon');
        }
        return $this->fetch('logon');
    }
    public function index(App $app)
    {
        $this->assign('server_info', [
            'server_version' => $this->request->server('SERVER_SOFTWARE'),
            'thinkphp_version' => $app->version(),
            'mysql_version' => $this->getMySQLVer(),
            'server_time' => date('Y-m-d H:i:s', time()),
            'upload_max_filesize' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : '已禁用',
            'max_execution_time' => ini_get('max_execution_time') . '秒'
        ]);
        return $this->fetch();
    }
    private function getMySQLVer(){
        $res=Db::query('SELECT VERSION() AS ver');
        return isset($res[0])?$res[0]['ver']:'未知';
    }
    public function password(){
        if($this->request->isPost()){
            $data=$this->request->post();
            if($this->auth->password($data)){
                $this->success('修改成功');
            }
            else{
                 $this->error('修改失败');
            }
        }
        else{
            return $this->fetch('password');
        }
    }
    public function logout(){
        $res=$this->auth->logout();
        if($res){
            $this->redirect('Index/login');
        }

    }
    public function books(){
        $data=$this->auth->getBooks();
        $this->assign('data',$data);
        return $this->fetch('books');
    }
    public function delete_book(){
        $id=$this->request->param('id');
        var_dump($id);
        if($this->auth->deleteBooks($id)){
            $this->error('删除成功');
        }
        else{
            $this->success('删除失败');
        }
    }
    public function delete_admins(){
        $id=$this->request->param('id');
        var_dump($id);
        if($this->auth->deleteAdmins($id)){
            $this->error('删除成功');
        }
        else{
            $this->success('删除失败');
        }
    }
    public function delete_user(){
        $id=$this->request->param('id');
        var_dump($id);
        if($this->auth->deleteUser($id)){
            $this->error('删除成功');
        }
        else{
            $this->success('删除失败');
        }
    }
    public function commons(){
        $data=$this->auth->getCommon();
        $this->assign('data',$data);
        return $this->fetch('commons');
    }
    public function admins(){
        $data=$this->auth->getAdmin();
        $this->assign('data',$data);
        return $this->fetch('admins');
    }
}