$(document).ready(function () {
    $("#exit").click(exitDialog);
    $("#userreq").click(function () {
        $(".outer_reqs_box").show();
        $("#username_reqs").show();
        $("#password_reqs").hide();
    });
    $("#passreq").click(function () {
        $(".outer_reqs_box").show();
        $("#password_reqs").show();
        $("#username_reqs").hide();
    });
});

function hide() {
    $(".outer_reqs_box").hide();
    $("#password_reqs").hide();
    $("#username_reqs").hide();
}

function exitDialog() {
    $("#main_box-id").css({
        "pointer-events": "initial",
        "opacity": "1",
        "user-select": "auto"
    });
    $("#mainDialogBox").hide();
}