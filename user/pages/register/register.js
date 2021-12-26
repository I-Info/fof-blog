function check() {
    for (var i = 0; i < $("p").length; i++) {
        if ($("p")[i].getAttribute("class") == "err") {
            alert("请注意格式！");
            return false;
        }
    }

    for (var i = 0; i < $("input").length; i++) {
        if ($("input")[i].value == "") {
            alert("注册内容不能为空哦！");
            return false;
        }
    }

    $.post("/api/auth/register.php", JSON.stringify(
        {
            username: $('#username').val(),
            passwd: $('#passwd').val(),
            email: $('#email').val(),
            tel: $('#tel').val()
        }),
        function (data) {
            if (data.msg == "ok") {
                alert("注册成功！");
                location.href="../login/login.html";
            }else{
                alert("注册失败！");
            };
        });
}

function clearInput(){
    $('#username').val("");
    $('#username_p').html("");
    $('#passwd').val("");
    $('#passwd_p').html("");
    $('#passwd_confirm').val("");
    $('#passwd_confirm_p').html("");
    $('#email').val("");
    $('#email_p').html("");
    $('#tel').val("");
    $('#tel_p').html("");
}

function usnTip() {
    $("#username").attr("class", "inp tip");
    $("#username_p").html("请输入1-15位字符");
    $("#username_p").removeAttr("class");
}

function testUsn() {
    $("#username").attr("class", "inp");
    var username = $("#username").val();

    if (username == "") {
        $("#username_p").html("请输入用户名！");
        $("#username_p").attr("class", "err");
    }
    if (username.length > 15) {
        $("#username_p").html("请输入1-15位字符！");
        $("#username_p").attr("class", "err");
    }
    if (username.length >= 1 && username.length <= 15) {
        $.post("/api/user/find.php", JSON.stringify({username: $('#username').val()}),
        function (data) {
            if (data.msg == "ok") {
                $("#username_p").html("该用户名已存在！");
                $("#username_p").attr("class", "err");
            }else{
                $("#username_p").html("");
            };
        });
    }
}

function psdTip() {
    $("#passwd").attr("class", "inp tip");
    $("#passwd_p").html("请输入5-25位字符");
    $("#passwd_p").removeAttr("class");
}

function testPsd() {
    $("#passwd").attr("class", "inp");
    var passwd = $("#passwd").val();
    var passwd_confirm = $("#passwd_confirm").val();
    if (passwd == "") {
        $("#passwd_p").html("请输入密码！");
        $("#passwd_p").attr("class", "err");
    }
    if (passwd.length < 5 && passwd.length > 0 || passwd.length > 25) {
        $("#passwd_p").html("请输入6-25位字符！");
        $("#passwd_p").attr("class", "err");
    }
    if (passwd.length >= 5 && passwd.length <= 25) {
        if (passwd == passwd_confirm) {
            $("#passwd_p").html("");
            $("#passwd_confirm_p").html("");
            $("#passwd_p").removeAttr("class");
            $("#passwd_confirm_p").removeAttr("class");
        } else {
            $("#passwd_p").html("您的密码输入不一致！");
            $("#passwd_confirm_p").html("您的密码输入不一致！");
            $("#passwd_p").attr("class", "err");
            $("#passwd_confirm_p").attr("class", "err");
        }
    }
}

function confirmTip() {
    $("#passwd_confirm").attr("class", "inp tip");
    $("#passwd_confirm_p").html("请确认您的密码");
    $("#passwd_confirm_p").removeAttr("class");
}

function testConfirm() {
    $("#passwd_confirm").attr("class", "inp");
    var passwd = $("#passwd").val();
    var passwd_confirm = $("#passwd_confirm").val();
    if (passwd_confirm == "") {
        $("#passwd_confirm_p").html("请确认密码！");
        $("#passwd_confirm_p").attr("class", "err");
    }
    if (passwd_confirm.length < 5 && passwd_confirm.length > 0 || passwd_confirm.length > 25) {
        $("#passwd_confirm_p").html("请输入6-25位字符！");
        $("#passwd_confirm_p").attr("class", "err");
    }
    if (passwd_confirm.length >= 5 && passwd_confirm.length <= 25) {
        if (passwd == passwd_confirm) {
            $("#passwd_p").html("");
            $("#passwd_confirm_p").html("");
            $("#passwd_p").removeAttr("class");
            $("#passwd_confirm_p").removeAttr("class");
        } else {
            $("#passwd_p").html("您的密码输入不一致！");
            $("#passwd_confirm_p").html("您的密码输入不一致！");
            $("#passwd_p").attr("class", "err");
            $("#passwd_confirm_p").attr("class", "err");
        }
    }
}

function emailTip() {
    $("#email").attr("class", "inp tip");
    $("#email_p").html("格式为username@domain.com");
    $("#email_p").removeAttr("class");
}

function testEmail() {
    $("#email").attr("class", "inp");
    var emailReg = /^[a-z0-9]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/;
    var email = $("#email").val();
    if (email == "") {
        $("#email_p").html("邮箱不能为空！");
        $("#email_p").attr("class", "err");
    } else {
        if (emailReg.test(email)) {
            $("#email_p").html("");
        } else {
            $("#email_p").html("邮箱格式错误！");
            $("#email_p").attr("class", "err");
        }
    }
}

function telTip() {
    $("#tel").attr("class", "inp tip");
    $("#tel_p").html("请输入您的电话号码");
    $("#tel_p").removeAttr("class");
}

function testTel() {
    $("#tel").attr("class", "inp");
    var telReg = /^1[3-9][0-9]\d{8}$/;
    var tel = $("#tel").val();
    if (tel == "") {
        $("#tel_p").html("电话号码不能为空！");
        $("#tel_p").attr("class", "err");
    } else {
        if (telReg.test(tel)) {
            $("#tel_p").html("");
        } else {
            $("#tel_p").html("电话号码格式错误！");
            $("#tel_p").attr("class", "err");
        }
    }
}