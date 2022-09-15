function getSuggesstions(value, if_exact){
    if(value.trim() != ""){
        var url = generateURL("getSuggestions.php");
        if(if_exact){
            $("#sug_box").load(url + "?value=" + value + "&exact=1");
        }
        else{
            $("#sug_box").load(url + "?value=" + value + "&exact=0");
        }
    }
    else{
        $("#sug_box").html("<h2 class='no_sug_label' id='no_sug_label_box'>No Suggestions.</h2>");
    }
}
function generateURL(path){
    let origin = window.location.origin;
    let fullLink = origin + "/All_Chat/";
    return fullLink + path;
}
function fill_result_username(row_num){
    var username = document.getElementById(row_num).innerHTML;
    var input_field = document.getElementById("friendUsername_field");
    input_field.value="" + username;
    getSuggesstions(username, true);
}