function toggle(){
    if ($("#m").hasClass("menuC1")) {
            $("#m").toggleClass("menuC2");
        } else if ($("#m").hasClass("menuC2")) {
            $("#m").toggleClass("menuC1");
        } else {
            $("#m").toggleClass("menuC1");
        }
        $("#mainHeader").slideToggle();
}

function hide() {
    $(".outer_reqs_box").hide();
    $("#username_reqs").hide();
    $("#image_reqs").hide();
}


$(document).ready(function() {
    $("#userreq").click(function () {
        $(".outer_reqs_box").show();
        $("#username_reqs").show();
        $("#image_reqs").hide();
    });
    $("#imagereq").click(function () {
        $(".outer_reqs_box").show();
        $("#username_reqs").hide();
        $("#image_reqs").show();
    });
    $("#exit").click(exitDialog);
    $("#exit_menu_button").click(function(){
        toggle();
        $("#friendBox").css({"pointer-events": "initial","opacity": "1","user-select": "auto"});
        $("#groupsBox").css({"pointer-events": "initial","opacity": "1","user-select": "auto"});
    });
    $("#m").click(function(){
        toggle();
        $("#friendBox").css({"pointer-events": "none","opacity": "0.2","user-select": "none"});
        $("#groupsBox").css({"pointer-events": "none","opacity": "0.2","user-select": "none"});
    });
    var friends_url = generateURL("friendsync.php");
    var groups_url = generateURL("groupsync.php");
    setInterval(function() {
        $("#innerData").load(friends_url);
        $("#groupsInnerData").load(groups_url);
    }, 2000);
});

function generateURL(path){
    let origin = window.location.origin;
    let fullLink = origin + "/All_Chat/";
    return fullLink + path;
}

function exitDialog() {
    $("#whole_box_id").css({
        "pointer-events": "initial",
        "opacity": "1",
        "user-select": "auto"
    });
    $("#createGroupBox").hide();
}