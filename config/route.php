<?php
/**
 * 全局URL路由配置
 * Created by PhpStorm.
 * @author  cbwfree
 */

return [

    //public 模块路由
    'publics/sendmessage'  => 'publics/index/sendmessage',
    'publics/upload_file'  => 'publics/index/upload_file',
    'publics/upload_image' => 'publics/index/upload_image',
    'publics/add_friend'   => 'publics/index/add_friend',
    'publics/remove_friend'=> 'publics/index/remove_friend',
    'publics/join_group'   => 'publics/index/join_group',
    'publics/leave_group'   => 'publics/index/leave_group',
    'publics/members'      => 'publics/index/members',
    'publics/saveVoice'      => 'publics/index/saveVoice',
     //privates 模块路由
    'privates/send_message' => 'privates/index/send_message',
    'privates/add_friend'   => 'privates/index/add_friend',
    'privates/remove_friend'=> 'privates/index/remove_friend',
    'privates/join_group'   => 'privates/index/join_group',
    'privates/leve_group'   => 'privates/index/leve_group',
    'privates/noreadMsg'    => 'privates/index/noreadMsg',
    'privates/chatlog'      => 'privates/index/chatlog'
    
];
