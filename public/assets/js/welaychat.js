var laychat={
    appName : 'LayChat',
    host:'http://www.wechat.rr',
    sendMessageUrl :this.host+'/publics/sendmessage',
    uploadImageUrl :this.host+'/publics/upload_image',
    uploadFileUrl  :this.host+'/publics/upload_file',
    addFriendUrl   :this.host+'/publics/add_friend',
    removeFriendUrl:this.host+'/publics/remove_friend',
    addGroupUrl    :this.host+'/publics/join_group',
    liveGroupUrl   :this.host+'/publics/leve_group',
    brief             : false,
    min               : false,
    right             : '0px',
    minRight          : null,
    initSkin          : '',
    isAudio           : false,
    isVideo           : false,
    notice            : false,
    maxLength         : 3000,
    skin              : null,
    copyright         : false,
    isMobile          : false,
    setMin            : false,
    enableAudio       : true,
    enableVideo       : true,
    enableGroup       : true,
    enableFriend      : true,
    voice             : false,
    inited            : 0,
    socket            : null,
    open              : function(){
        if (this.inited) return;
        if(this.isIE6or7()) return;
        this.connect();
        this.initIM();
    },
     isIE6or7 : function(){
        var b = document.createElement('b');
        b.innerHTML = '<!--[if IE 5]><i></i><![endif]--><!--[if IE 6]><i></i><![endif]--><!--[if IE 7]><i></i><![endif]-->';
        return b.getElementsByTagName('i').length === 1;
    },
    connect: function() {
            var woker = new  Woker(laychat.app_key, {
                encrypted: false                
                , enabledTransports: ['ws']
                , wsHost: laychat.websocket
                , wsPort: 80                
                , authEndpoint: '/auth.php'
                ,disableStats: true
              });

           
             
             var channel =woker.subscribe("user" + laychat.mine.id);
             // 接受消息
             channel.on("getmessage",function(data){

                 // var msg =$.parseJSON(data.message);
                 layui.layim.getMessage(data.message
                  );
             });

          
            var groups =laychat.groupList;



             channel.on("addgroup",function(data){
                 groups.push(data.message);
             });
            
             channel.on("livegroup",function(data){
               // alert(data.message.id);
       
             woker.unsubscribe("group"+data.message.id);
                 $.each(groups,function(k,v){
                       if(v.id ==data.message){
                          groups[k] ="";
                       }
                 });
             });

            // console.log(groups);
            // 接受群消息
        
            if(groups.length >= 1){
              $.each(groups,function(k,v){
               var channels =woker.subscribe("group"+v.id); 
                channels.on("getmessage",function(data){
                    
                    if(data.message.fromid != laychat.mine.id){
                       layui.layim.getMessage(data.message);         
                    }  
               });
              });
            }

              // 重连接
              woker.connection.on('state_change', function(states) {
                // states = {previous: 'oldState', current: 'newState'}
                if(states.current == 'unavailable' || states.current == "disconnected" || states.current == "failed" ){

                    woker.unsubscribe("user" + laychat.mine.id);
                     if(groups.length >= 1){

                        $.each(groups,function(k,v){
                     
                       woker.unsubscribe("group"+v.id);
                      });
              
                     }   
                    laychat.connect();              
                }

            });
    },
    addfriend:function(data){
     // var data={
     //     "mine":laychat.mine,
     //     "addfriend":{
     //        'type': 'friend' //列表类型，只支持friend和group两种
     //        ,'avatar': "http://www.wechat.rr/assets/upload/8.jpg" //好友头像
     //        ,'username': '冲田杏梨' //好友昵称
     //        ,'groupid': 1 //所在的分组id
     //        ,'id': "123" //好友id
     //        ,'sign': "本人冲田杏梨将结束AV女优的工作" //好友签名
     //        },
     //  };

      layui.layim.addList(data.addfriend);

    },
    removefriend:function(data){
      // code={ type: 'friend' ,id: 1238668}
     // var code={
     //     "mine":laychat.mine,
     //     "removefriend":{
     //       "type": 'friend' //或者group
     //      ,"id": 100001 //好友或者群组ID
     //  }
     // };
      $.ajax({
        url:laychat.removeFriendUrl,
        type:"post",
        data:data
      });
      layui.layim.removeList(data.removefriend);

    },
    addgroup:function(data){
    // var data={
    //      "mine":laychat.mine.id,
    //      "addgroup":{
    //            type: 'group' //列表类型，只支持friend和group两种
     //          ,avatar: "a.jpg" //群组头像
    //           ,groupname: 'Angular开发' //群组名称
    //           ,id: "12333333" //群组id
    //         },
    //   }; 

     
     $.ajax({
         url:laychat.addGroupUrl,
         type:'post',
         data:data.mine
     });

     layui.layim.addList(data);

    },
    removegroup:function(data){

    // var data={
    //      "mine":laychat.mine.id,
    //      "removegroup":{
    //             'type': 'group' //或者group
    //             ,'id': '101' //好友或者群组ID
    //         },
    //   }; 


      $.ajax({
        url:laychat.liveGroupUrl,
        type:"post",
        data:data
      });


    layui.layim.removeList(data.removegroup);

    },

    initIM  : function(){
           
          var module = laychat.isMobile ? 'mobile' : 'layim';

           layui.use(module, function(layim){
             if (laychat.isMobile) {
                var mobile = layui.mobile, 
                    layim = mobile.layim, 
                    layer = mobile.layer;
                    layui.layim = layim;
                    layui.layer = layer;
            }

            layim.on('ready', function(options){
              console.log(options.mine);
              //do something
              $.ajax({
                 url:"http://www.wechat.rr/publics/index/index",
                 type:'post',
                 data:laychat.mine
              });
            });
      
            // 监听发送消息
            layim.on('sendMessage', function(res){
               // console.log(res);
              if(res.to.type == 'friend'){

                  if(res.to.fromid){
                     var newdata={
                      username: res.mine.username //消息来源用户名
                      ,avatar: res.mine.avatar //消息来源用户头像
                      ,fromid: res.mine.id 
                      ,type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                      ,content: res.mine.content //消息内容
                      ,mine: true//是否我发送的消息，如果为true，则会显示在右方
                      ,id:res.mine.id   // 窗口号
                      ,toid:res.to.fromid
                      ,appkey:laychat.app_key
                    };

                 }else{
                 var newdata={
                  username: res.mine.username //消息来源用户名
                  ,avatar: res.mine.avatar //消息来源用户头像
                  ,fromid: res.mine.id 
                  ,type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                  ,content: res.mine.content //消息内容
                  ,mine: true//是否我发送的消息，如果为true，则会显示在右方
                  ,id:res.mine.id
                  ,toid:res.to.id
                  ,appkey:laychat.app_key
                 };

               }

              }else{

                 if(res.to.fromid){
                       var newdata={
                        username: res.mine.username //消息来源用户名
                        ,avatar: res.mine.avatar //消息来源用户头像
                        ,fromid: res.mine.id 
                        ,type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                        ,content: res.mine.content //消息内容
                        ,mine: true//是否我发送的消息，如果为true，则会显示在右方
                        ,id:res.to.id
                        ,toid:res.to.id
                        ,appkey:laychat.app_key
               
                      };
                  }else{

                     var newdata={
                      username: res.mine.username //消息来源用户名
                      ,avatar: res.mine.avatar //消息来源用户头像
                      ,fromid: res.mine.id 
                      ,type: res.to.type //聊天窗口来源类型，从发送消息传递的to里面获取
                      ,content: res.mine.content //消息内容
                      ,mine: true//是否我发送的消息，如果为true，则会显示在右方
                      ,id:res.to.id
                      ,toid:res.to.id
                      ,appkey:laychat.app_key
                    };

                   }

              }
              
                $.ajax({
                  url:laychat.sendMessageUrl,
                  type:'post',
                  data:newdata
                });
           
            });


         // 合成数据
          var data={
                  "mine":laychat.mine,
                  "friend":laychat.friendList,
                  "group":laychat.groupList
          };



         layui.layim.config({
                //初始化接口
                init: data
                // 上传图片
                ,uploadImage: {
                    url: laychat.uploadImageUrl
                }
                // 上传文件
                ,uploadFile: {
                    url: laychat.uploadFileUrl
                }
                ,isgroup: laychat.enableGroup
                ,isfriend: laychat.enableFriend
                //聊天记录地址
                ,chatLog: laychat.host+"/publics/chatlog"
                //,find: laychat.findUrl
                ,copyright: false //是否授权
                ,title: laychat.appName
                ,min: laychat.setMin
                ,isAudio: laychat.enableAudio
                ,isVideo: laychat.enableVideo
                ,voice: laychat.voice
                ,brief: laychat.brief
                ,min: laychat.min
                ,right:laychat.right
                ,minRight:laychat.minRight
                ,initSkin:laychat.initSkin
                ,notice:laychat.notice
                ,maxLength:laychat.maxLength
                ,skin:laychat.skin
                ,members:null    // 看群组成员
                //,msgbox: layui.cache.dir + 'css/modules/layim/html/msgbox.html' //消息盒子页面地址，若不开启，剔除该项即可
            });

          });
    }

};


// 注册用户得到appkey
laychat.app_key ='b054014693241bcd9c26';


laychat.websocket='ws.wolive.cc';

// 自己的信息
laychat.mine = {
      "username": "陈锐" //我的昵称
      ,"id": "100" //我的ID
      ,"status": "online" //在线状态 online：在线、hide：隐身
      ,"sign": "在深邃的编码世界" //我的签名
      ,"avatar": "http://www.wechat.rr/assets/upload/0.jpg" //我的头像
};

// 好友列表信息
laychat.friendList =[{
                     "groupname": "前端" //好友分组名
                    ,"id": 1 //分组ID
                    ,"list": [{ //分组下的好友列表
                     "username": "贤心" //好友昵称
                    ,"id": "100001" //好友ID
                    ,"avatar": "http://www.wechat.rr/assets/upload/1.jpg" //好友头像
                    ,"sign": "这些都是测试数据，实际使用请严格按照该格式返回" //好友签名
                    ,"status": "online" //若值为offline代表离线，online或者不填为在线
                      }]
                  }];
// 群组信息
laychat.groupList =[{
                      "groupname": "前端群" //群组名
                      ,"id": "101" //群组ID
                      ,"avatar": "http://www.wechat.rr/assets/upload/2.jpg" //群组头像
                  }];

// 打开聊天面板
laychat.open();


