<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'chat.php' == basename($_SERVER['SCRIPT_FILENAME']))
    if (!isset($_GET['userid']))
        die('You can not access this page directly!');
?>
<!DOCTYPE html>
<html>
    <head>
        <!--head中信息仅测试中使用-->
        <title>聊天</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta content="yes" name="apple-mobile-web-app-capable"/>

    </head>
    <body>
        <div id="main" data-role="page" data-theme="b" data-add-back-btn="true">
            <div data-role="header" data-theme="b">

                <h2>Chatting</h2>
                <a data-role="button" data-icon="gear" class="ui-btn-right">设置</a>

            </div>
            <div data-role="content">
                <ol id="chat">
                </ol>
                <hr/>
                <div class="l-warning-box">
                    <label class="l-warning-chat">聊天内容不能为空</label>
                </div>
                <textarea id="messagetext" required></textarea>

                <div class="ui-grid-a">
                    <div class="ui-block-a"><input type="reset" value="消除"/></div>
                    <div class="ui-block-b"><input id="chatBtn" type="button" onclick="" value="发送"/>
                        <script>
                            $('.l-warning-chat').hide();
                            
                            $(function(){
                                function getMessages()
                                {
                                    $.post('chat/get_messages.php',{'chatuserid':<?php echo $_GET['userid'] ?>},function(data){
                                        $("#chat").html(data);
                                        
                                        window.setTimeout( getMessages, 3000 );
                                    });
                                }
                                
                                getMessages();
                                
                                $('#chatBtn').click(function(){
                                    var message = $('#messagetext').val();
                                    if(message){
                                        var divHeightO=$('#main').height();
                                        //传递信息与对方id过去
                                        $.post('chat/add.php',{'message':message,'userid':<?php echo $_GET['userid'] ?>},function(data){
                                            $("#chat").html(data);
                                        });
                                        $('#messagetext').val("");
                                        var divHeightS=$('#main').height();
                                        $('#main').scrollHeight(divHeightO-divHeightS);
                                    } else {
                                        $('.l-warning-chat').show();
                                    }
                                    
                                });
                                
                                $('#messagetext').focus(function (){
                                    $('.l-warning-chat').hide('slow');
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div data-role="footer" data-theme="b">
                <h2 >Team Luff</h2>
            </div>
        </div>
    </body>
</html>
