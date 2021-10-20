<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/23
 * Time: 16:00
 */

namespace app\index\library;
use app\index\model\CommonUser as UserModel;
use app\index\model\BooksName as BooksModel;
use think\facade\Session;
class Auth
{
    protected static $instance;
    protected $error;
    public function setError($error){
        $this->error=$error;
        return $this;
    }
    public function getError(){
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
        $obj=new UserModel();
        $users['username'] = $username;
        $users['password'] = $password;
        $user=UserModel::get(['username'=>$username]);
        if($user)
        {
            $this->setError('用户已存在');
            return false;
        }
        if($state=$obj->data($users)->save()){
            return true;
        }
        else{
            $this->setError('添加用户失败');
            return false;
        }
    }
    public function password($password){
        $username=Session::get('username');
//        var_dump($username);
        $res=UserModel::where('username',$username)->update(['password'=>$password]);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
    public static function getInstance($options=[]){
        if(is_null(self::$instance)){
            self::$instance=new static($options);
        }
        return self::$instance;
    }
    public function getUser($username){
        $users=UserModel::where('username',$username)->find()->toArray();
//        echo '<pre>';
//        var_dump($booksId);
        if($users){
            return $users;
        }
        else{
            return false;
        }
    }
    public function getSelect($key,$option){
        $obj=new BooksModel();
        $books=$obj->select()->where("$key",'like',$option)->toArray();
        if($books)
        {
            return $books;
        }
        else{
            return false;
        }
    }
    public function getBooks($key,$option){
        $obj=new BooksModel();
        $books=$obj->where("$key",$option)->find();
        if(!empty($books))
        {
            $books->toArray();
            return $books;
        }
        else{
            return false;
        }
    }
    public function addBooks($username,$booksId){
        if($res=UserModel::where('username',$username)->update(['booksId'=>$booksId])){
            return true;
        }
        else{
            return false;
        }
    }
    public function hateBooks($username,$booksId){
        if($res=UserModel::where('username',$username)->update(['booksId'=>$booksId])){
            return true;
        }
        else{
            return false;
        }
    }
    public function getSelects($key=''){
        $obj=new BooksModel();
        if(!empty($key))
        {
            $books=$obj->select()->order("$key",'asc')->toArray();
        }
        else
        {
            $books=$obj->select()->toArray();
        }
        if($books)
        {
            return $books;
        }
        else{
            return false;
        }
    }
    public function get_detail($key='',$option){
        $obj=new BooksModel();
        if(!empty($key))
        {
            $books=$obj->where("$key",'like',$option)->find()->toArray();
//            echo '<pre>';
//            var_dump($books);
            $pic=$books['detail_pic'];
//              echo '<pre>';
//              var_dump($books);die();
            $chapters=file($pic);
//            echo '<pre>';
//            var_dump($contents);die();
        }
        else
        {
            $books=$obj->select()->toArray();
            $chapters=file($books['detail_pic']);
        }
        if($chapters)
        {
            return $chapters;
        }
        else{
            return false;
        }
    }
}