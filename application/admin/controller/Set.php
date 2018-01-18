<?php 
namespace app\admin\controller;

use app\admin\model\App;

class Set extends Base
{
	
  /**
   * .添加敏感词
   * [addwords description]
   * @return [type] [description]
   */
   public function addwords(){
     $post =$_POST;
     $data =App::table('gagwords')->insert($post);

     if($data){
        return '添加成功！';
     }else{
        return '添加失败！';
     }

  }
  
  /**
   * .删除敏感词
   * [deletewords description]
   * @return [type] [description]
   */
  public function deletewords(){
    $post=$_POST;
    $data =App::table('gagwords')->where('id',$post["id"])->delete();
    if($data){
      return '删除成功！';
    }else{
      return '删除失败！';
    }
  }



  public function deletewhite(){
   $post=$_POST;
    $data =App::table('white_list')->where('id',$post["id"])->delete();
    if($data){
      return '删除成功！';
    }else{
      return '删除失败！';
    }
  }
   
   /**
    * .添加被禁人
    * [addgag description]
    * @return [type] [description]
    */
   public function addgag(){
     $post =$_POST;
     $wlist =App::table('white_list')->where('userid',$post['userid'])->find();
   
      if($wlist){
        return '该成员已被设为白名单，不能执行该操作！';
      }


     $data =App::table('gaglist')->insert($post);

     if($data){
        return '添加成功';
     }else{
        return '添加失败';
     }

   }
  
  /**
   * .开放被禁人
   * [deletelist description]
   * @return [type] [description]
   */
   public function deletelist(){
    $post=$_POST;
    $data =App::table('gaglist')->where('id',$post["id"])->delete();
     if($data){
        return '删除成功！';
     }else{
        return '删除失败！';
     }
   }
   /**
    * .改变app模式
    * [change description]
    * @return [type] [description]
    */
   public function change(){
       $post =$_POST;

       if($post['debug'] == 'open_private'){
          $res =App::table('app')->where('id',$post['app_id'])->find();
          $type =$res['state'];
          if($type == 'forbidden_private_chat'){
             $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'using']);
          }else{
             $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_group_chat']);
          }

          return '修改成功！';
       }elseif($post['debug'] == 'close_private'){
          $res =App::table('app')->where('id',$post['app_id'])->find();
          $type =$res['state'];
          if($type == 'using'){
            $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_private_chat']);
          }else{
            $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_all_chat']);
          }
           return '修改成功！';
       }elseif($post['debug'] == 'close_group'){

          $res =App::table('app')->where('id',$post['app_id'])->find();
          $type =$res['state'];
          if($type == 'using'){
            $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_group_chat']);
          }else{
            $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_all_chat']);
          }
           return '修改成功！';
       }elseif($post['debug'] == 'open_group'){
          $res =App::table('app')->where('id',$post['app_id'])->find();
          $type =$res['state'];
          if($type == 'forbidden_group_chat'){
             $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'using']);
          }else{
             $result =App::table('app')->where('id',$post['app_id'])->update(['state'=>'forbidden_all_chat']);
          }
           return '修改成功！';
       }

     }

    public function limit(){
          $post=$_POST;
          $app =App::table("app")->where("id",$post['appid'])->update(["min_limit"=>$post['mlimit'],"day_limit"=>$post["dlimit"]]);
          if($app){
               return "修改成功";
          }
    }

    /**
     * 修改密码
     * [modify description]
     * @return [type] [description]
     */
    public function modify(){
         $post =$this->request->post();
         $result = $this->validate($post, 'Check');  
         if($result !== true){
            return $result;
         }

         $user =App::table('admin')->where("id",$post['id'])->find();
         $pass = md5($user['username']."laychat" . $post['oldpass']);
         if($user['password'] == $pass){
            $newpass =md5($user['username']."laychat" . $post['newpass']);
            $res =App::table("admin")->where("id",$post['id'])->update(["password"=>$newpass]);
            if($res){
                return '修改成功';
            }
         }else{
            return '旧密码不正确！';
         }
    }
    
    /**
     * 添加app
     *
     */
    public function addapp(){
      $post =$this->request->post();
     
      $arr =[
         'app_key' =>$post['appkey'],
         'app_secret'=>$post['appsecret'],
         'orderid' =>$post['id'],
         'buy_time' =>time(),
         'create_time'=>time()
      ];
      $res =App::table('app')->insert($arr);

      if($res){
        return '添加成功';
      }
    }

    public function addwhite(){
      
      $post =$this->request->post();

      $gaglist =App::table('gaglist')->where('userid',$post['userid'])->find();

      if($gaglist){
        return '该成员已被禁，不能执行该操作！';
      }

      $res =App::table("white_list")->insert($post);

      if($res){
         return '添加成功！';
      }

    }
}
