//表单验证  李本卿  2011-6-13
$(document).ready
    (
        function()
        {
            if (navigator.geolocation) {
                //$("#loginBtn").val('定位中').attr("disabled","disabled");
                navigator.geolocation.getCurrentPosition(
                    function(position) { 
                        var lat = position.coords.latitude; //纬度
                        var lon = position.coords.longitude;  //经度 
                        //alert (lon);
                        $("#longitude").val(lon);
                        $("#latitude").val(lat);
                        $("#longitude_register").val(lon);
                        $("#latitude_register").val(lat);
                        $('#locationTip').text('定位成功');
                        $('#locationLoading').hide();
                        //这里需要定时，如果超过一定时间则提示用户定位失败，进入主页面再进行重新定位，不过好像定位挺快的
                        $("#loginBtn").text('登录').attr("disabled",false);
                    }
                    );
            } else {
                $('#geoResults').html('<p>Your browser does not support geolocation.</p>');
            }
            //卿 2011-6-10
            //POST()函数调用的语法格式如下所示:$.post(url,[data],[callback],[type])
            //url表示等待加载的数据地址，可选项[data]表示发送到服务器的数据其格式为key/value
            //可选项目[callback]参数表示加载成功时执行的回调函数
            //可选项[type]表示返回数据的格式，如html,xml,js,json,text等
            //注册验证  POST
            $('#rigisterLoading').hide();
            $('#email_input').blur(function(){
                $('#rigisterLoading').show();
                $.post("register_check.php", {
                    useremail: $('#email_input').val()
                }, function(response){
                    $('#rigisterResult').fadeOut();
                    setTimeout("finishAjax('rigisterResult', '"+escape(response)+"')", 400);
                });
                return false;
            });
                
            $('#loginLoading').hide();
            //将覆盖原有的submit方法   李本卿  2011-6-13
            $("#login").submit
            (
                function()
                {
                    login();
                    return false;
                }
                );
        });
function login()
{
    var email = $("#email").val();
    var password = $("#password").val();
    var longitude=$('#longitude').val();
    var latitude=$('#latitude').val();
     
    $.ajax({
        type: "POST",
        url: "login.php",
        data: "email=" + email + "&password=" + password+"&longitude="+longitude+"&latitude="+latitude,
        beforeSend: function(){
            $('#loginLoading').show();
        },
        success: function(msg){
            if(escape(msg) == 'success'){
                $('#loginLoading').hide();
                // $("#confirm").html("\u767b录中"); 
                location.href = "index.html";
            }else {
                $('#loginLoading').hide();
                $("#confirm").html("\u7528户名或者密\u7801错误，请重新输入").fadeOut(4000);
            }
        }
    });
}

function finishAjax(id, response) {
    $('#rigisterLoading').hide();
    $('#'+id).html(unescape(response));
    $('#'+id).fadeIn();
    $('#'+id).fadeOut(5000);
}
   