$(document).ready(function(){
//alert($('#cuserid').val()+$('#bcuserid').val());

function showComment(){
    $(this).parent().next('.l-tweet-comment').removeClass('l-displaynone');
}

function hideComment(){
//               $(this).parent().addClass('l-displaynone');
}

function addComment(){
    $(this).next('.l-tweet-comment').removeClass('l-displaynone');
    //获取当前提交按钮所有的评论的输入框的内容与tweetid                  
    
    //Todo 评论存入数据库 并在回调函数中 执行hideComment
    $.post("comment_update.php", {
        tweet_id:$(this).parent().find('.tweetid').val(),
        c_userid:$('#cuserid').val(),
        b_c_userid:$('#bcuserid').val(),
        content:$(this).parent().parent().find('textarea').val()
    },function(){
        //alert(data);
        //回调函数，应该刷新页面
        //window.location.reload();
//                        $("#content").load(location.href+"status.php?user_id="+$('#bcuserid').val());
//                        var tid = $("#TopicID").val();
//                        $("#LoadArticleReply").load("/ArticleReply.aspx", { "ID": tid }, function() {
//                        $("#LoadArticleReply").fadeIn("slow");
//}
//      );
//                        alert('提交评论成功');
          $.get(location.href);
        
    });
}

function clearComment(){
    $(this).text("");
    $(this).parent().parent().removeClass('l-invalid');
}



$('.l-button-show').live('vclick', showComment);
$('.l-button-show').click(function(){
});
//Todo 用addComment中的回调函数替代这个
$('.l-button-add').live('vclick', addComment).bind('vclick', hideComment);
$('.l-tweet-comment textarea').focus(clearComment);
})