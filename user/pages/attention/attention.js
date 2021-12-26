$(document).ready(function () {
    getCurrentId();
    getContent();
    liSkip();
    send();
    comment();
    follow();
    $('.container2').css("border-bottom-left-radius", "0px");
    $('.container2').css("border-bottom-right-radius", "0px");
})

var currentId = -1;

function getCurrentId() {
    currentId = -1;
    $.post({
        url: "/api/auth/login.php",
        async: false,
        data: JSON.stringify({username: "", passwd: ""}),
        success: function (data) {
            if (data.code == 302) {
                currentId = data.msg;
                $.post({
                    url: "/api/user/get.php",
                    async: false,
                    data: JSON.stringify({uid: currentId}),
                    success: function (res) {
                        var currentName=res.data.name;
                        $("#login").hide();
                        $("#register").hide();
                        $("#currentUser").text(currentName);
                    }
                })
            }else {
                alert("请先登录！");
                location.href = "../login/login.html";
            }
        }
    });
}

function login() {
    location.href = "../login/login.html";
}

function register() {
    location.href = "../register/register.html";
}

function logout() {
    $.post("/api/auth/logout.php",
        function (data) {
            if (data.msg == "ok") {
                alert("您已成功退出！");
                location.href = "../login/login.html";
            }
        });
}

function liSkip(){
    $(".navigate ul li").click(function (){
        var clickLi=$(this).children("a");
        location.href=$(clickLi[0]).attr("href");
    })
}

function border() {
    $("#area").attr("class", "tip");
}

function back() {
    $("#area").removeAttr("class");
}

var str = "";
var id = -1;
var uid = -1;

function getContent() {
    var list = [];
    str = "";
    id = -1;
    uid = -1;
    $.post({
        url: "/api/blog/get.php",
        async: false,
        success: function (data) {
            if (data.msg == "ok") {
                list = data.data;
                for (var i = 0; i < list.length; i++) {
                    var blog = list[i];
                    id = blog.id;
                    uid = blog.uid;
                    var content = list[i].content;
                    var createTime = list[i].create_time;
                    $.post({
                        url: "/api/user/get.php",
                        async: false,
                        data: JSON.stringify({uid: uid}),
                        success: function (res) {
                            if (res.msg == "ok") {
                                if (currentId != uid) {
                                    $.post({
                                        url: "/api/user/follows.php",
                                        async: false,
                                        data: JSON.stringify({uid: currentId}),
                                        success: function (data) {
                                            if (data.msg == "ok") {
                                                var followData = data.data;
                                                var flag = false;
                                                for (var j = 0; j < followData.length; j++) {
                                                    if (uid == followData[j]) {
                                                        str = str + '<div class="container3">';
                                                        str = str + '<div class="user">' + res.data.name + '&nbsp;' + '<span class="followTip">' + '（我的关注）' + '</span>' + '</div>';
                                                        str = str + '<div class="time">' + createTime + '</div>' + '<br>';
                                                        str = str + '<div class="detail">' + content + '</div>' + '<br>' + '<br>' + '<br>';
                                                        str = str + '<button class="commButt">我要评论</button>';
                                                        str = str + '<div class="commCon">' + '</div>';
                                                        str = str + '<div class="blogId" hidden="hidden">' + id + '</div>';
                                                        str = str + '<div class="userId" hidden="hidden">' + uid + '</div>';
                                                        str = str + '<div class="commDetail">' + '</div>';
                                                        str = str + '</div>' + '<br>';
                                                        flag = true;
                                                        break;
                                                    } else {
                                                        flag = false;
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }


                            }
                            ;
                        }
                    });
                }
                $("#blog").html(str);
            } else {
                alert("微博加载失败！");
            }
        },
    });
}

function comment() {
    $(".commButt").click(function () {
        if ($(this).siblings(".commCon").html() == "") {
            var commentStr = "";
            commentStr = commentStr + '<div class="publish">\n' +
                '                    <textarea class="commArea" placeholder="发布你的评论"></textarea>\n' +
                '                    <br>\n' +
                '                    <button class="commentSend">评论</button>\n' + '<br>' +
                '                </div>';
            $(this).attr("class", "clickCommButt");
            $(this).siblings(".commCon").html(commentStr);
            commBorder();
            commBack();
            commentSend();
            var blogId = $(this).siblings(".blogId").text();

            var list = [];
            var commDetail = "";
            $.post({
                url: "/api/comment/get.php",
                async: false,
                data: JSON.stringify({blog_id: blogId}),
                success: function (data) {
                    ``
                    if (data.msg == "ok") {
                        list = data.data;
                        for (var i = 0; i < list.length; i++) {
                            var comment = list[i];
                            var uid = comment.uid;
                            var content = list[i].content;
                            var createTime = list[i].create_time;
                            $.post({
                                url: "/api/user/get.php",
                                async: false,
                                data: JSON.stringify({uid: uid}),
                                success: function (res) {
                                    if (res.msg == "ok") {
                                        commDetail = commDetail + '<br>';
                                        commDetail = commDetail + '<div class="commUser">' + res.data.name + ':' + '&nbsp;' + '<span class="commDetail">' + content + '</span>' + '</div>';
                                        commDetail = commDetail + '<div class="time">' + createTime + '</div>';
                                    }
                                    ;
                                }
                            });
                        }
                    }
                },
            });
            $(this).siblings(".commDetail").html(commDetail);
        } else {
            $(this).attr("class", "commButt");
            $(this).siblings(".commCon").html("");
            $(this).siblings(".commDetail").html("");
        }
        ;
    });
}

function send() {
    $(".send").click(function () {
        $.post("/api/blog/add.php", JSON.stringify({content: $('#area').val()}),
            function (data) {
                if (data.msg == "ok") {
                    alert("您已成功发表！");
                    $('#area').val("");
                    getContent();
                    comment();
                    $(".container2").css("border-bottom-left-radius", "0px");
                    $(".container2").css("border-bottom-right-radius", "0px");
                } else {
                    alert("发送微博失败！");
                }
                ;
            });
    })

}

function follow() {
    $(".followButt").click(function () {
        var userId = $(this).siblings(".userId").text();
        $.post({
            url: "/api/user/follow.php",
            async: false,
            data: JSON.stringify({uid: userId}),
            success: function (data) {
                if (data.msg == "ok") {
                    alert("关注成功！");
                }
            },
        });
        $(this).siblings(".user").children().text("（我的关注）");
        $(this).hide();
        getContent();
        follow();
    })
}

function commentSend() {
    $(".commentSend").click(function () {
        var comment = $(this).siblings(".commArea").val();
        var blogId = $(this).parents(".commCon").siblings(".blogId").text();
        $.post({
            url: "/api/comment/add.php",
            async: false,
            data: JSON.stringify({
                content: comment,
                blog_id: blogId
            }),
            success: function (data) {
                if (data.msg == "ok") {
                    alert("评论成功！");
                    $('.commArea').val("");
                } else {
                    alert("评论失败！");
                }
                ;
            },
        });
        var list = [];
        var commDetail = "";
        $.post({
            url: "/api/comment/get.php",
            async: false,
            data: JSON.stringify({blog_id: blogId}),
            success: function (data) {
                ``
                if (data.msg == "ok") {
                    list = data.data;
                    for (var i = 0; i < list.length; i++) {
                        var comment = list[i];
                        var uid = comment.uid;
                        var content = list[i].content;
                        var createTime = list[i].create_time;
                        $.post({
                            url: "/api/user/get.php",
                            async: false,
                            data: JSON.stringify({uid: uid}),
                            success: function (res) {
                                if (res.msg == "ok") {
                                    commDetail = commDetail + '<br>';
                                    commDetail = commDetail + '<div class="commUser">' + res.data.name + ':' + '&nbsp;' + '<span class="commDetail">' + content + '</span>' + '</div>';
                                    commDetail = commDetail + '<div class="time">' + createTime + '</div>';
                                }
                                ;
                            }
                        });
                    }
                } else {
                    alert("评论加载失败！");
                }
            },
        });
        $(this).parents(".commCon").siblings(".commDetail").html(commDetail);
    })
}

function commBorder() {
    $(".commArea").focus(function () {
        $(this).css("border-color", "#cb78de");
    })
}

function commBack() {
    $(".commArea").blur(function () {
        $(this).css("border-color", "gray");
    })
}




