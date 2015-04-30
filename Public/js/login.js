/**
 * Created by zhang on 2015/4/11.
 */
function checkUserLogin(){
    var url = $("#loginform").attr("action");
    $.post(url, { username: $("#username").val(), password: $("#password").val()},
    function(data){
        var jsonData;
        eval("jsonData="+data);
        alert(jsonData.info);
    });
    return false;
}