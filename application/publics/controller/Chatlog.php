<?php

namespace app\publics\controller;

use think\Controller;
use app\publics\model\User;
use think\Paginator;


class Chatlog extends Controller
{
    public function mine()
    {
       $flag= preg_match("/.*?\/.*?\/mine\/([^?]*)/", $_SERVER['REQUEST_URI'], $match);
     
       if($flag){
        $get = $this->request->get();
        $modes =explode("/", htmlspecialchars($match[1]));
        $mine =$modes[0];
        $app_key=$modes[1];
        $id = htmlspecialchars($get['id']);
        // get 能够获取 id 和 type
        // 如何判断是谁与id的聊天记录

        if (!isset($get['hid'])) {

            $type = $get['type'];
            if($type == "friend"){
               // $chatdata = User::query("select * from message where (`from` ='{$id}' or `to` ='{$id}') and (`from` ='{$mine}' or `to` ='{$mine}') and type='{$type}' and `app_key`='{$app_key}' order by `mid` desc limit 11");

                $chatdata =User::table("message")->where('from',['=',$id],['=',$mine],"or")->where('to',['=',$id],['=',$mine],"or")->where(['type'=>$type,'app_key'=>$app_key])->order("mid desc")->limit(11)->select();
            }else{
               // $chatdata = User::query("select * from message where (`to` ='{$id}') and type='{$type}' and `app_key`='{$app_key}' order by `mid` desc limit 11");
               
                $chatdata =User::table("message")->where('to',$id)->where(['type'=>$type,'app_key'=>$app_key])->order("mid desc")->limit(11)->select();

            }
            
        } elseif ($get["view_type"] == 'prev') {
            $type = $get['type'];
            $msgid = $get['hid'];
            if($type == "friend"){

                 $chatdata =User::table("message")->where('from',['=',$id],['=',$mine],"or")->where('to',['=',$id],['=',$mine],"or")->where(['type'=>$type,'app_key'=>$app_key])->where('mid','<',$msgid)->order("mid desc")->limit(11)->select();

            }else{
             

                $chatdata =User::table("message")->where('to',$id)->where(['type'=>$type,'app_key'=>$app_key])->where('mid','<',$msgid)->order("mid desc")->limit(11)->select();

            }
            
         

        } elseif ($get["view_type"] == 'next') {

            $type = $get['type'];
            $msgid = $get['hid'];
         
            if($type == "friend"){
              
                $chatdata =User::table("message")->where('from',['=',$id],['=',$mine],"or")->where('to',['=',$id],['=',$mine],"or")->where(['type'=>$type,'app_key'=>$app_key])->where('mid','>',$msgid)->order("mid")->limit(11)->select();


            }else{
              
              $chatdata =User::table("message")->where('to',$id)->where(['type'=>$type,'app_key'=>$app_key])->where('mid','>',$msgid)->order("mid")->limit(11)->select();

            
            }
            
        }

        if ($chatdata) {
            
            if(isset($get["view_type"]) &&  $get["view_type"]== 'next'){
                 $result =$chatdata;
            }else{
                 $result = array_reverse($chatdata);
            }
           
            $data = [];
            foreach ($result as $value) {
                $data[] = $value['data'];
            }
            $id = $get["id"];
            $type = $get["type"];
            reset($result);         
            $mode = current($result);
            $arr = array_pop($result);

            $page = '';
            if (!isset($get['hid'])) {
                if(count($data) > 10){
                    array_shift($data);
                    $hid = $mode['mid']+1;
                    $page .= '<div class="history-page">';
                    $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $hid . "&id=" . $id . "&type=" . $type . "&view_type=prev'>上一页</a>";
                    $page .= '下一页</div>';
                }else{
                    $page.='<div class="history-page"> 上一页  下一页 </div>';
                }
            } else {

                if(count($data) < 11 && $get['view_type'] == 'prev'){
                      $bid = $arr["mid"];
                      $page .= '<div class="history-page">';
                      $page .= '上一页';
                      $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $bid . "&id=" . $id . "&type=". $type . "&view_type=next'>下一页</a></div>";

                }elseif(count($data) < 11 && $get['view_type'] == 'next'){
                    $hid = $mode['mid'];
                    $page .= '<div class="history-page">';
                    $page .="<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $hid . "&id=" . $id . "&type=" . $type . "&view_type=prev'>上一页</a>";
                    $page .= '下一页</div>';
                }elseif(count($data) > 10 && $get['view_type'] == 'prev'){
                  
                    $hid = $mode['mid'];
                    $bid = $arr["mid"];
                    $page .= '<div class="history-page">';
                    $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $hid . "&id=" . $id . "&type=" . $type . "&view_type=prev'>上一页</a>";
                    $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $bid . "&id=" . $id . "&type=". $type . "&view_type=next'>下一页</a></div>";

                }elseif(count($data)>10 && $get['view_type'] == 'next'){
                    
                    $hid = $mode['mid'];
                    $bid = $arr["mid"];
                    $page .= '<div class="history-page">';
                    $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" . $hid . "&id=" . $id . "&type=" . $type . "&view_type=prev'>上一页</a>";
                    $page .= "<a href='/publics/Chatlog/mine/{$mine}/{$app_key}?hid=" .$bid. "&id=" . $id . "&type=". $type . "&view_type=next'>下一页</a></div>";
                }
            }

            $json = json_encode( $data);

        } else {
            $page = '<div class="history-page">没有历史数据</div>';
            $json = json_encode($chatdata);
        }

        $this->assign('chatdata', $json);
        $this->assign('page', $page);
        return $this->fetch();
       }else{
           header("Status: 401 Not authenticated");
       }
    }
}