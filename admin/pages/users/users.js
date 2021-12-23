$(document).ready(function () {
    getUsers();
    liSkip();
    deleteUser();
    $('.container2').css("border-bottom-left-radius", "0px");
    $('.container2').css("border-bottom-right-radius", "0px");
})

function logout() {
    $.post("/fof-blog/api/auth/logout.php",
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

function getUsers() {
    var list = [];
    var str = "";
    var uid = -1;
    $.post({
        url: "/fof-blog/api/user/get.php",
        async: false,
        success: function (data) {
            if (data.msg == "ok") {
                list = data.data;
                for (var i = 0; i < list.length; i++) {
                    str = str + '<div class="user">' + list[i].name;
                    str = str + '<button class="deleteButt">删除！</button>';
                    str = str + '</div>';
                    str = str + '<div class="userId" hidden="hidden">' + list[i].id + '</div>';
                }
            }
        }
    });
    $("#blog").html(str);
}

function deleteUser() {
    $(".deleteButt").click(function () {
        alert("确认删除这个用户？");
        var userId = $(this).parents(".user").next().text();
        $.post({
            url: "/fof-blog/api/user/delete.php",
            async: false,
            data: JSON.stringify({uid: userId}),
            success: function (data) {
                if (data.msg == "ok") {
                    alert("删除成功！");
                }
            },
        });
        getUsers();
        deleteUser();
    })
}




