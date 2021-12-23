$(document).ready(function () {
    getCurrentId();
    getContent();
    liSkip();
    deleteComm()
    $('.container2').css("border-bottom-left-radius", "0px");
    $('.container2').css("border-bottom-right-radius", "0px");
})

var currentId = -1;

function getCurrentId() {
    currentId = -1;
    $.post({
        url: "/fof-blog/api/auth/login.php",
        async: false,
        data: JSON.stringify({username: "", passwd: ""}),
        success: function (data) {
            if (data.code == 302) {
                currentId = data.msg;
                $.post({
                    url: "/fof-blog/api/user/get.php",
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
    $.post("/fof-blog/api/auth/logout.php",
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

var str = "";
var id = -1;
var uid = -1;
var commId = -1;

function getContent() {
    var list = [];
    str = "";
    id = -1;
    uid = -1;
    commId = -1;
    $.post({
        url: "/fof-blog/api/blog/get.php",
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

                    var tipList = [];
                    $.post({
                        url: "/fof-blog/api/comment/get.php",
                        async: false,
                        data: JSON.stringify({blog_id: id}),
                        success: function (data) {
                            ``
                            if (data.msg == "ok") {
                                tipList = data.data;
                                for (var i = 0; i < tipList.length; i++) {
                                    if (tipList[i].uid == currentId) {
                                        $.post({
                                            url: "/fof-blog/api/user/get.php",
                                            async: false,
                                            data: JSON.stringify({uid: uid}),
                                            success: function (res) {
                                                if (res.msg == "ok") {
                                                    str = str + '<div class="container3">';
                                                    str = str + '<div class="user">' + res.data.name + '</div>';
                                                    str = str + '<div class="time">' + createTime + '</div>' + '<br>';
                                                    str = str + '<div class="detail">' + content + '</div>' + '<br>' + '<br>' + '<br>';
                                                    str = str + '<div class="blogId" hidden="hidden">' + id + '</div>';
                                                    str = str + '<div class="userId" hidden="hidden">' + uid + '</div>';
                                                    str = str + '<div class="publish">\n' +
                                                        '                    <textarea class="commArea" placeholder="发布你的评论"></textarea>\n' +
                                                        '                    <br>\n' +
                                                        '                    <button class="commentSend">评论</button>\n' + '<br>' +
                                                        '                </div>';
                                                    str = str + '<br>';
                                                    var commList = [];
                                                    $.post({
                                                        url: "/fof-blog/api/comment/get.php",
                                                        async: false,
                                                        data: JSON.stringify({blog_id: id}),
                                                        success: function (data) {
                                                            ``
                                                            if (data.msg == "ok") {
                                                                commList = data.data;
                                                                for (var j = 0; j < commList.length; j++) {
                                                                    commId = commList[j].id;
                                                                    var comment = commList[j];
                                                                    var uid = comment.uid;
                                                                    var content = commList[j].content;
                                                                    var createTime = commList[j].create_time;
                                                                    $.post({
                                                                        url: "/fof-blog/api/user/get.php",
                                                                        async: false,
                                                                        data: JSON.stringify({uid: uid}),
                                                                        success: function (res) {
                                                                            if (res.msg == "ok") {
                                                                                str = str + '<div class="commId" hidden="hidden">' + commId + '</div>';
                                                                                if (currentId == uid) {
                                                                                    str = str + '<br>';
                                                                                    str = str + '<div class="commUser">' + '<span class="currentUserComm">' + res.data.name + '（我）' + '</span>' + ':' + '&nbsp;' + '<span class="commDetail">' + content + '</span>';
                                                                                    str = str + '<button class="deleteButt">删除！</button>';
                                                                                    str = str + '</div>';
                                                                                    str = str + '<div class="time">' + createTime + '</div>';
                                                                                } else {
                                                                                    str = str + '<br>';
                                                                                    str = str + '<div class="commUser">' + '<span class="notCurrentUserComm">' + res.data.name + '</span>' + ':' + '&nbsp;' + '<span class="commDetail">' + content + '</span>' + '</div>';
                                                                                    str = str + '<div class="time">' + createTime + '</div>';
                                                                                }
                                                                            }
                                                                            ;
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        },
                                                    });
                                                    str = str + '</div>' + '<br>';
                                                }
                                                ;
                                            }
                                        });
                                        break;
                                    }
                                }
                                ;
                            }
                        },
                    });
                }
                $("#blog").html(str);
                commBorder();
                commBack();
                commentSend();
            } else {
                alert("微博加载失败！");
            }
        },
    });
}

function commentSend() {
    $(".commentSend").click(function () {
        var comment = $(this).siblings(".commArea").val();
        var blogId = $(this).parents(".publish").siblings(".blogId").text();
        $.post({
            url: "/fof-blog/api/comment/add.php",
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
        getContent();
        deleteComm();
    })
}

function deleteComm() {
    $(".deleteButt").click(function () {
        // alert("确认删除这条评论？");
        var commId = $(this).parents(".commUser").prev().prev().text();
        $.post({
            url: "/fof-blog/api/comment/delete.php",
            async: false,
            data: JSON.stringify({id: commId}),
            success: function (data) {
                if (data.msg == "ok") {
                    alert("删除成功！");
                }
            },
        });
        getContent();
        deleteComm();
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




