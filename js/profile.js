function chat(){
$('#chat').click(function(){
    $.post('chat.php', {
        userid:$('#userid').val(),
        usernickname:$('#nickname').val()
    });
});
}