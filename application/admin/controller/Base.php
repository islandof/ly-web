<?php
/**
 * Created by PhpStorm.
 * User: chenrui
 * Date: 17-11-8
 * Time: 下午12:05
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\App;

class Base extends Controller
{
  public function _initialize(){
      if (!isset($_SESSION["user"])) {
          $this->redirect('admin/login/index');
      }

      $user = $_SESSION['user'];
      $result = App::table("admin")->where('id', $user['id'])->find(); 
      $data = json_encode($result);
      $this->assign('user', $user);
      $this->assign('data',$data);
  }
}