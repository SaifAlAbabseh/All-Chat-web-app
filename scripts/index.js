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
    const wrapper = document.getElementById("forms_wrapper");
    if (doIt) {
        wrapper.classList.add("show-signup");
    } else {
        wrapper.classList.remove("show-signup");
    }
}