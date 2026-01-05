
function renderImage() {
    let fileField = document.getElementById("picField");
    let file = fileField.files[0];
    let absolutePath = URL.createObjectURL(file);
    let imageRenderBox;
    if (!(imageRenderBox = document.getElementById("imageRenderBox"))) {
        imageRenderBox = document.createElement("img");
        imageRenderBox.setAttribute("id", "imageRenderBox");
    }
    imageRenderBox.setAttribute("src", absolutePath);
    document.getElementById("imageRenderOuterBox").appendChild(imageRenderBox);
}

function generateURL(path, pathPrevHowMuch) {
    const pathPrev = "../".repeat(pathPrevHowMuch);
    return fetch(`${pathPrev}js_env.php?var=PUBLIC_SITE_URL`)
        .then(response => response.json())
        .then(env => {
            urlMainPath = env.PUBLIC_SITE_URL;
            let origin = window.location.origin;
            let fullLink = `${origin}/${urlMainPath}/`;
            return fullLink + path;
        });
}

function showBlurOnTop() {
    const messagesBox = document.getElementById("messages");
    messagesBox.classList.toggle("at-top", messagesBox.scrollTop === 0);
}