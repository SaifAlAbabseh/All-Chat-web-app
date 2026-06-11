
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

function checkChatIfEmpty(messagesContainer) {
    const messsages = document.getElementsByClassName("message-container-outer");
    if (messsages.length === 0) {
        messagesContainer.innerHTML = "";
    }
}

// Global Custom Audio Player Logic
let currentAudio = null;
let currentPlayBtn = null;

document.addEventListener('click', function(e) {
    if (e.target.closest('.audio-play-btn')) {
        const btn = e.target.closest('.audio-play-btn');
        const playerDiv = btn.closest('.custom-audio-player');
        const src = playerDiv.getAttribute('data-src');
        const progressDiv = playerDiv.querySelector('.audio-progress-bar');
        const timeSpan = playerDiv.querySelector('.audio-time');
        
        if (currentAudio && currentAudio.src.includes(src)) {
            if (currentAudio.paused) {
                currentAudio.play();
                btn.innerHTML = '⏸';
            } else {
                currentAudio.pause();
                btn.innerHTML = '▶';
            }
        } else {
            if (currentAudio) {
                currentAudio.pause();
                if (currentPlayBtn) currentPlayBtn.innerHTML = '▶';
            }
            currentAudio = new Audio(src);
            currentPlayBtn = btn;
            
            currentAudio.addEventListener('timeupdate', () => {
                if (!currentAudio.duration) return;
                const percent = (currentAudio.currentTime / currentAudio.duration) * 100;
                progressDiv.style.width = percent + '%';
                
                let mins = Math.floor(currentAudio.currentTime / 60);
                let secs = Math.floor(currentAudio.currentTime % 60);
                if (secs < 10) secs = '0' + secs;
                timeSpan.innerText = mins + ':' + secs;
            });
            
            currentAudio.addEventListener('ended', () => {
                btn.innerHTML = '▶';
                progressDiv.style.width = '0%';
                timeSpan.innerText = '0:00';
            });
            
            currentAudio.play();
            btn.innerHTML = '⏸';
        }
    }
    
    // Scrubbing/Seeking logic
    if (e.target.closest('.audio-progress-container')) {
        if (!currentAudio) return;
        const container = e.target.closest('.audio-progress-container');
        const playerDiv = container.closest('.custom-audio-player');
        const src = playerDiv.getAttribute('data-src');
        
        // Only seek if this is the currently loaded audio
        if (currentAudio.src.includes(src) && currentAudio.duration) {
            const rect = container.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percent = clickX / rect.width;
            currentAudio.currentTime = percent * currentAudio.duration;
        }
    }
});

// --- Custom Alert Implementation ---
document.addEventListener('DOMContentLoaded', () => {
    if (window.pendingAlerts && window.pendingAlerts.length > 0) {
        window.pendingAlerts.forEach(msg => window.customAlert(msg));
        window.pendingAlerts = [];
    }
});

window.customAlert = function(message) {
    function createAlert() {
        let modal = document.getElementById('globalCustomAlertModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'globalCustomAlertModal';
            modal.className = 'custom-modal';
            modal.style.zIndex = '99999';
            modal.innerHTML = `
                <div class="custom-modal-content">
                    <h3 style="color:#F76D57; margin-bottom: 20px; font-size: 1.4rem;">Notice</h3>
                    <p id="globalCustomAlertText" style="color:#ccc; margin-bottom: 30px; font-size: 1.1rem; line-height: 1.4;"></p>
                    <div class="custom-modal-actions" style="justify-content: center;">
                        <button onclick="closeCustomAlert()" class="btn-confirm" style="min-width: 120px;">OK</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        document.getElementById('globalCustomAlertText').innerText = message;
        modal.style.display = "flex";
        setTimeout(() => modal.classList.add("show"), 10);
    }

        if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createAlert);
    } else {
        createAlert();
    }
};

window.customConfirm = function(message, formElement, btnName, btnValue) {
    function createConfirm() {
        let modal = document.getElementById('globalCustomConfirmModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'globalCustomConfirmModal';
            modal.className = 'custom-modal';
            modal.style.zIndex = '99999';
            modal.innerHTML = `
                <div class="custom-modal-content">
                    <h3 style="color:#F76D57; margin-bottom: 20px; font-size: 1.4rem;">Confirmation</h3>
                    <p id="globalCustomConfirmText" style="color:#ccc; margin-bottom: 30px; font-size: 1.1rem; line-height: 1.4;"></p>
                    <div class="custom-modal-actions">
                        <button id="customConfirmCancelBtn" class="btn-cancel">Cancel</button>
                        <button id="customConfirmConfirmBtn" class="btn-confirm">Confirm</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        document.getElementById('globalCustomConfirmText').innerText = message;
        
        let cancelBtn = document.getElementById('customConfirmCancelBtn');
        let confirmBtn = document.getElementById('customConfirmConfirmBtn');
        
        // Remove old event listeners
        let newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        let newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newCancelBtn.addEventListener('click', () => {
            modal.classList.remove("show");
            setTimeout(() => { modal.style.display = "none"; }, 300);
        });
        
        newConfirmBtn.addEventListener('click', () => {
            modal.classList.remove("show");
            setTimeout(() => { modal.style.display = "none"; }, 300);
            
            // Submit form with button data
            let hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = btnName;
            hidden.value = btnValue;
            formElement.appendChild(hidden);
            formElement.submit();
        });
        
        modal.style.display = "flex";
        setTimeout(() => modal.classList.add("show"), 10);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createConfirm);
    } else {
        createConfirm();
    }
    return false; // Prevents default form submission
};

window.closeCustomAlert = function() {
    let modal = document.getElementById('globalCustomAlertModal');
    if (modal) {
        modal.classList.remove("show");
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
};