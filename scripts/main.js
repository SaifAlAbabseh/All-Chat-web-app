function toggle() {
    if ($("#m").hasClass("menuC1")) {
        $("#m").toggleClass("menuC2");
    } else if ($("#m").hasClass("menuC2")) {
        $("#m").toggleClass("menuC1");
    } else {
        $("#m").toggleClass("menuC1");
    }
    const header = $("#mainHeader");
    const overlay = $("#drawer-overlay");
    
    if (header.hasClass("drawer-open")) {
        header.removeClass("drawer-open");
        overlay.removeClass("show");
    } else {
        // Ensure overlay exists
        if (overlay.length === 0) {
            $("body").append('<div id="drawer-overlay" class="drawer-overlay"></div>');
            $("#drawer-overlay").click(function() {
                toggle();
                $("#friendBox").css({ "pointer-events": "initial", "opacity": "1", "user-select": "auto" });
                $("#groupsBox").css({ "pointer-events": "initial", "opacity": "1", "user-select": "auto" });
            });
        }
        header.addClass("drawer-open");
        $("#drawer-overlay").addClass("show");
    }
}

function hide() {
    $(".outer_reqs_box").hide();
    $("#username_reqs").hide();
    $("#image_reqs").hide();
}


$(document).ready(function () {
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
    $("#exit_menu_button").click(function () {
        toggle();
    });
    $("#m").click(function () {
        toggle();
    });
    generateURL("friendsync.php", 1).then(url => {
        const friends_url = url;
        generateURL("groupsync.php", 1).then(url => {
            const groups_url = url;
            setInterval(function () {
                $("#innerData").load(friends_url);
                $("#groupsInnerData").load(groups_url);
            }, 2000);
        });
    });
});

function exitDialog() {
    $("#whole_box_id").css({
        "pointer-events": "initial",
        "opacity": "1",
        "user-select": "auto"
    });
    $("#createGroupBox").hide();
}