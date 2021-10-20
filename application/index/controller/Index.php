<?php

namespace app\index\controller;
use app\index\validate\CommonUser as UserValidate;
use think\facade\Session;

class Index extends Common
{
    public function login()
    {
        if($this->request->isPost())
        {
            $data=[
                'username'=>$this->request->post('username/s','','trim'),
                'password'=>$this->request->post('password/s',''),
                'captcha'=>$this->request->post('captcha/s','','trim'),
            ];
            $validate=new UserValidate();
            if(!$validate->scene('login')->check($data))
            {
                $this->error('登录失败:'.$validate->getError().'。');
            }
            if(!$this->auth->login($data['username'],$data['password'])){
                $this->error('登陆失败:'.$this->auth->getError().'。');
            }
            $this->success('登陆成功！','index/index');
        }
        else
        {
            return $this->fetch('login');
        }
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
            $this->success('注册成功！','login');
        }
        else
        {
          return $this->fetch('logon');
        }

    }
    public function password(){
        if($this->request->isPost()){
            $password=$this->request->post('password');
//            var_dump($password);
            if($this->auth->password($password)){
                return $this->success('修改成功');
            }
            else{
                return $this->error('修改失败');
            }
        }
        return $this->fetch('password');
    }
    public function index(){
        $books=$this->auth->getSelects();
        $this->assign('books',$books);
        return $this->fetch('index');
    }
    public function isLogin(){
        if(Session::has('username')){
            return true;
        }
        else{
            return false;
        }
    }
    public function like(){
        $name=$this->request->post('name');
//        var_dump($name);
        if($this->isLogin()){
            if($username=Session::get('username')){
                $users=$this->auth->getUser($username);
                $booksId=$users['booksId'];
//                echo $booksId;
                if($books=$this->auth->getBooks('name',$name)){
                    if(strpos($booksId,strval($books['id']))!==false){
                        echo '已收藏';
                    }
                    else{
                        echo '加入书架';
                    }
                }
            }
        }
        else{
            echo '加入书架';
        }
//        return 'ok';
    }
    public function logLike(){
        $name=$this->request->post('name');
//        var_dump($name);
        if($this->isLogin()){
            if($username=Session::get('username')){
                $users=$this->auth->getUser($username);
                $booksId=$users['booksId'];
//                var_dump($booksId);
                if($books=$this->auth->getBooks('name',$name)){
//                    var_dump($books['id']);
                    if(strpos($booksId,strval($books['id']))!==false){
                        echo '已收藏';
                    }
                    else{
                        if(!empty($booksId)){
                            $booksId=$booksId.','.$books['id'];
                            $res=$this->auth->addBooks($username,$booksId);
                            if($res){
                                echo '已收藏';
                            }
                            else{
                                echo '加入书架';
                            }
                        }
                        else{
                            $booksId=$booksId.$books['id'];
                            $res=$this->auth->addBooks($username,$booksId);
                            if($res){
                                echo '已收藏';
                            }
                            else{
                                echo '加入书架';
                            }
                        }

                    }
                }
            }
        }
        else{
            return false;
        }
//        return 'ok';
    }
    public function hate(){
        $name=$this->request->post('name');
        var_dump($name);
        if($username=Session::get('username')){
            $users=$this->auth->getUser($username);
            $booksId=$users['booksId'];
//                var_dump($booksId);
            if($books=$this->auth->getBooks('name',$name)){
                    var_dump($books['id']);
                if(strpos($booksId,strval($books['id']))!==false){
                    $booksId=explode(',',$booksId);
                    $key=array_search($books['id'],$booksId);
                    unset($booksId["$key"]);
                    $booksId=implode(',',$booksId);
                    $res=$this->auth->hateBooks($username,$booksId);
                    if($res){
                        echo true;
                    }
                    else{
                        echo false;
                    }
                }
            }
        }
    }
    public function show(){
        return $this->fetch('show');
    }
    public function mine(){
//        Session::clear();
        $data=Session::get('username');
        return $data;
    }
    public function logout(){
        Session::clear();
        return $this->index();
    }
    public function rank()
    {
        if ($this->request->isGet()) {
            $data = $this->request->get();
//            var_dump($data);
            $i = 0;
            if (!empty($data)) {
                foreach ($data as $key => $v) {
                    if ($i) {
                        if (!$books = $this->auth->getSelects($key)) {
                            $this->error('操作失败');
                        } else {
//                    echo '<pre>';
//                    var_dump($books);
                            $this->assign('books', $books);
                            return $this->fetch('ranking');
                        }
                    }
                    $i++;
                }
            }
        }
        $this->error('操作失败','index/index');
    }
    public function myBooks(){
        if($this->isLogin()){
            $user=Session::get('username');
            if($users=$this->auth->getUser($user)){
//                var_dump($users);
                $booksId=$users['booksId'];
                if(!empty($booksId)){
                    $booksId=explode(',',$booksId);
//                var_dump($booksId);
                    foreach ($booksId as $key=>$value){
                        $books[]=$this->auth->getBooks('id',$value);
                    }
//                echo '<pre>';
//                var_dump($books);
                    $this->assign('books',$books);
                    return $this->fetch('myBooks');
                }
                else{
                    return$this->fetch('allEmpty');
                }
            }
            else{
                $this->error('查找失败','index/index');
            }
        }
        else{
            $this->error('请登录','login');
        }
    }
    public function select(){
        if($this->request->isPost()) {
            $data = $this->request->post();
//            var_dump($data);
            if (!empty($data)) {
                foreach ($data as $key=>$v){
                    $books = $this->auth->getSelect($key, $v);
                    if (!$books) {
                        $this->error('操作失败');
                    } else {
//                  echo '<pre>';
//                  var_dump($books);
                        $this->assign('books', $books);
                        return $this->fetch('show');
//                  $this->success('操作成功','show');
                    }
                }

            } else {
                $this->error('操作失败', 'index');
            }
        }
        if($this->request->isGet()){
            $data=$this->request->get();
//            var_dump($data);
            $i=0;
            if(!empty($data)){
                foreach ($data as $key=>$v)
                {
                    if($i)
                    {
                        if(!$books=$this->auth->getSelect($key,$v)){
                            $this->error('操作失败');
                        }
                        else{
//                    echo '<pre>';
//                    var_dump($books);
                            $this->assign('books',$books);
                            return $this->fetch('show');
//                    $this->success('操作成功','show');
                        }
                    }
                    $i++;
                }
            }
            else{
                $this->error('操作失败');
            }
        }
        else{
            $this->error('操作失败','index');
        }
        $this->error('操作失败','index');
    }
    public function selects(){
        if($this->request->isGet()){
            $data=$this->request->get();
            $i=0;
            if(isset($data)){
                foreach ($data as $key=>$v)
                {
                    if($i)
                    {
                        if(!$books=$this->auth->getSelects($key)){
                            $this->error('操作失败');
                        }
                        else{
//                    echo '<pre>';
//                    var_dump($books);
                            $this->assign('books',$books);
                            return $this->fetch('show');
//                    $this->success('操作成功','show');
                        }
                    }
                    $i++;
                }
            }
        }
        else{
            $this->error('操作失败','index');
        }
    }
    public function get_detail(){
        if($this->request->isGet()){
            $id=$this->request->get('id');
            $page=$this->request->get('page');
            $name=$this->request->get('name');
            if(isset($name)){
                if(!$chapters=$this->auth->get_detail('name',$name)){
                    $this->error('操作失败');
                }
                if(!isset($page)){
//                   echo '<pre>';
//                   var_dump($books);die();
                     return $this->chapter_list($chapters,$name);
//                   $this->success('操作成功','show');
                }
                else{
                    return $this->read($chapters,$page,$name,$id);
                }
            }
        }
        else{
            $this->error('操作失败','index');
        }
    }
    public function chapter_list($chapters,$name){
        $list=array();
        foreach ($chapters as $key=>$v){
            if($key!=0){
                $list["$key"]=$v;
            }
        }
        $this->assign('list',$list);
        $this->assign('name',$name);
        return $this->fetch('chapter_list');
    }
    public function read($chapters,$page,$name,$id){
        $pic=$chapters['0'];
        $pic= substr($pic,0,strlen($pic)-2);
        $title=explode('/',$pic);
        if($id==1){
            if($page<1){
                $page=1;
            }
        }
        if($id==2){
            if($page>count($chapters)-1){
                $page=count($chapters)-1;
            }
        }
        $pic=$pic."$page".'.txt';
        $this->assign('page',$page);
        $contents=file($pic);
        $this->assign('title',$title['2']);
        $this->assign('name',$name);
        $this->assign('contents',$contents);
        return $this->fetch('read_detail');
    }
}