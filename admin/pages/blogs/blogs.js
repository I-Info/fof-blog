$(document).ready(function () {
    getBlogs();
    liSkip();
    deleteBlog();
    $('.container2').css("border-bottom-left-radius", "0px");
    $('.container2').css("border-bottom-right-radius", "0px");
})

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
                location.href = "../../../user/pages/login/login.html";
            }
        });
}

function liSkip() {
    $(".navigate ul li").click(function () {
        var clickLi = $(this).children("a");
        location.href = $(clickLi[0]).attr("href");
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

function getBlogs() {
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
                                str = str + '<div class="container3">';
                                str = str + '<div class="user">' + res.data.name;
                                str = str + '<button class="deleteButt">删除！</button>';
                                str = str + '</div>';
                                str = str + '<div class="time">' + createTime + '</div>' + '<br>';
                                str = str + '<div class="detail">' + content + '</div>' + '<br>' + '<br>' + '<br>';
                                str = str + '<div class="commCon">' + '</div>';
                                str = str + '<div class="blogId" hidden="hidden">' + id + '</div>';
                                str = str + '<div class="userId" hidden="hidden">' + uid + '</div>';
                                str = str + '<div class="commDetail">' + '</div>';
                                str = str + '</div>' + '<br>';
                            }
                            ;
                        }
                    });
                }
                console.log(str);
                $("#blog").html(str);
            } else {
                alert("微博加载失败！");
            }
        },
    });
}

function deleteBlog() {
    $(".deleteButt").click(function () {
        alert("确认删除这条微博？");
        var blogId = $(this).parents(".user").siblings(".blogId").text();
        console.log(blogId);
        $.post({
            url: "/api/blog/delete.php",
            async: false,
            data: JSON.stringify({blog_id: blogId}),
            success: function (data) {
                if (data.msg == "ok") {
                    alert("删除成功！");
                }
            },
        });
        getBlogs();
        deleteBlog()
    })
}



