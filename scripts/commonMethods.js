function renderImage(){
    let fileField = document.getElementById("picField");
    let file = fileField.files[0];
    let absolutePath = URL.createObjectURL(file);
    let imageRenderBox;
    if(!(imageRenderBox = document.getElementById("imageRenderBox"))){
        imageRenderBox = document.createElement("img");
        imageRenderBox.setAttribute("id", "imageRenderBox");
    }
    imageRenderBox.setAttribute("src", absolutePath);
    document.getElementById("imageRenderOuterBox").appendChild(imageRenderBox);
}

function generateURL(path){
    let origin = window.location.origin;
    let fullLink = origin + "/All-Chat-web-app/";
    return fullLink + path;
}