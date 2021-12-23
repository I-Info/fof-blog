function check() {
    if ($('#username').val()=="admin"&&$('#passwd').val()=="password"){
        $.post("/fof-blog/api/auth/login.php", JSON.stringify({username: $('#username').val(), passwd: $('#passwd').val()}),
            function (data) {
                if (data.msg == "ok") {
                    location.href="../../../admin/pages/users/users.html";
                } else {
                    alert("用户名或密码错误！");
                }
                ;
            });
    }else {
        $.post("/fof-blog/api/auth/login.php", JSON.stringify({username: $('#username').val(), passwd: $('#passwd').val()}),
            function (data) {
                if (data.msg == "ok") {
                    location.href = "../index/index.html";
                } else {
                    alert("用户名或密码错误！");
                }
                ;
            });
    }
    return false;

}

