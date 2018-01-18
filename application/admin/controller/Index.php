<?php
/**
 * Created by PhpStorm.
 * User: chenrui
 * Date: 17-11-8
 * Time: 下午12:09
 */

namespace app\admin\controller;

use app\admin\model\App;


class Index extends Base
{
  
  public function index(){
      $user =$_SESSION['user'];
      $data = App::table("app")->where("orderid",$user['id'] )->select();
      $result = json_encode($data);
      $this->assign("arr", $result);
      return $this->fetch();
  }

  public function demo(){

      $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

      $domain =$http_type.$_SERVER['HTTP_HOST'];
      $get = $this->request->get();
      $app = App::table('app')->where("id", $get['app_id'])->find();
      $this->assign("app", $app);
      $this->assign("appid", $get['app_id']);
      $this->assign("domain",$domain);
      return $this->fetch();
  }

  public function set(){
      $get = $this->request->get();
      $app = App::table('app')->where("id", $get['app_id'])->find();
      $ldata = App::table('gaglist')->where("app_id", $get['app_id'])->select();
      $wdata = App::table('gagwords')->where("app_id", $get['app_id'])->select();
      $bdata =App::table('white_list')->where("app_id",$get['app_id'])->select();
      if (!$ldata) {
          $ldata = '';
      }

      if (!$wdata) {
          $wdata = "";
      }

      if(!$bdata){
         $bdata ="";
      }
      $this->assign('app', $app);
      $this->assign("appid", $get['app_id']);
      $this->assign('ldata', $ldata);
      $this->assign('wdata', $wdata);
      $this->assign('bdata', $bdata);      
      return $this->fetch();
  }

  public function show(){
      $get = $this->request->get();
      $app = App::table('app')->where("id", $get['app_id'])->find();
      $this->assign('app', $app);
      $this->assign("appid", $get['app_id']);
      return $this->fetch();

  }

  public function test()
    {   
      $get =$_POST;
      if($get){
        $data =$get['data'];
        $_SESSION['data']=$data;       
      }else{
            $data =$_SESSION['data'];
      }
          $this->assign("data",$data);   
      return $this->fetch();
      
    }
}