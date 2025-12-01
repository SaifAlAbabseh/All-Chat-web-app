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

function setSavedCreds() {
    if (localStorage.getItem("all_chat-username") != null && localStorage.getItem("all_chat-password") != null) {
        document.getElementById("username_inputfield").value = localStorage.getItem("all_chat-username");
        document.getElementById("userpassword_field").value = localStorage.getItem("all_chat-password");
    }
}

function hideMainBoxBeforeLoading(box_id) {
    $("#" + box_id + "").css({
        'pointer-events': 'none',
        'user-select': 'none'
    });
    $("#" + box_id + "").animate({
        opacity: 0
    }, 500);
}

function showLoadingBox() {
    let temp = document.createElement('div');
    temp.setAttribute('id', 'loading_box_outer_id');
    temp.setAttribute('class', 'loading_box_outer');
    temp.innerHTML = '<div class=loading_bar></div><h3 class=loading_label>Loading</h3>';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    $('#loading_box_outer_id').animate({
        opacity: 1
    }, 1000);
}

function returnAfterLoading(box_id) {
    setTimeout(function () {
        $("#" + box_id + "").css({
            'pointer-events': 'all'
        });
        $("#" + box_id + "").animate({
            opacity: 1
        }, 1000);
        $('#loading_box_outer_id').animate({
            opacity: 0
        }, 1000);
        $('#loading_box_outer_id').css({
            'z-index': '-100'
        });
    }, 5000);
}

function startLoading(box_id) {
    hideMainBoxBeforeLoading(box_id);
    showLoadingBox();
    returnAfterLoading(box_id);
}

function rememberCreds(username, password) {
    localStorage.setItem("all_chat-username", username);
    localStorage.setItem("all_chat-password", password);
}

function switchToSignupForm(doIt) {
    const signupFormElement = document.getElementById("signup_box");
    signupFormElement.style.opacity=doIt ? "1" : "0";
    signupFormElement.style.width=doIt ? "100%" : "0px";
    signupFormElement.style.pointerEvents=doIt ? "all" : "none";

    const loginFormElement = document.getElementById("login_box"); 
    loginFormElement.style.pointerEvents=doIt ? "none" : "all";
}