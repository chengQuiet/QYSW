<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/23
 * Time: 16:00
 */

namespace app\admin\library;
use app\admin\model\AdminUser as UserModel;
use app\admin\model\BooksName as BooksModel;
use app\admin\model\CommonUser as CommonModel;
use app\admin\model\CommonUser;
use think\facade\Session;
class Auth
{
    protected $error;
    public function setError($error)
    {
        $this->error=$error;
        return $this;
    }
    public function getError()
    {
        return $this->error;
    }
    public function login($username,$password)
    {
        $user=UserModel::get(['username'=>$username]);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        if($user->password!=$password){
            $this->setError('用户名或密码错误');
            return false;
        }
        Session::set('username',$username);
        return true;
    }
    public function logon($username,$password)
    {
        $userobj=new UserModel();
        $users['username'] = $username;
        $users['password'] = $password;
        $user=UserModel::get(['username'=>$username]);
        if($user)
        {
            $this->setError('用户已存在');
            return false;
        }
        if($state=$userobj->data($users)->save()){
            return true;
        }
        else{
            $this->setError('添加用户失败');
            return false;
        }
    }
    protected static $instance;
    public static function getInstance($options=[])
    {
        if(is_null(self::$instance)){
            self::$instance=new static($options);
        }
        return self::$instance;
    }
    public function isLogin(){
        return Session::has('username');
    }
    public function password($data){
        $username=Session::get('username');
        $user=UserModel::get(['username'=>$username]);
        $data['password']=$this->passwordMD5($data['password'],$user->salt);
        if($flag=UserModel::where('username',$username)->update($data)){
            return true;
        }
        else{
            return false;
        }
    }
    public function logout(){
        Session::delete(Session::get('username'));
        return true;
    }
    public function getBooks(){
        $books=BooksModel::select()->toArray();
        if($books){
            return $books;
        }
        else{
            return false;
        }
    }
    public function getAdmin(){
        $admins=UserModel::select()->toArray();
        if($admins){
            return $admins;
        }
        else{
            return false;
        }
    }
    public function getCommon(){
        $commons=CommonModel::select()->toArray();
        if($commons){
            return $commons;
        }
        else{
            return false;
        }
    }
    public function deleteBooks($id){
            $book=BooksModel::get($id);
            $res=$book->delete();
            if($res)
            {
                var_dump($res);
                return true;
            }
            else{
                return false;
            }
        }
    public function deleteAdmins($id){
        $book=UserModel::get($id);
        $res=$book->delete();
        if($res)
        {
            var_dump($res);
            return true;
        }
        else{
            return false;
        }
    }
    public function deleteUser($id){
        $book=CommonModel::get($id);
        $res=$book->delete();
        if($res)
        {
            var_dump($res);
            return true;
        }
        else{
            return false;
        }
    }
}