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

$(document).ready(function() {
    $("#exit_menu_button").click(function(){
        toggle();
        $("#friendBox").css({"pointer-events": "initial","opacity": "1","user-select": "auto"});
    });
    $("#m").click(function(){
        toggle();
        $("#friendBox").css({"pointer-events": "none","opacity": "0.2","user-select": "none"});
    });
    var url = generateURL("friendsync.php");
    setInterval(function() {
        $("#innerData").load(url);
    }, 2000);
});

function generateURL(path){
    let origin = window.location.origin;
    let fullLink = origin + "/All_Chat/";
    return fullLink + path;
}