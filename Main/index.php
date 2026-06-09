<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../");
}

require_once("../DB.php");
require_once(dirname(__DIR__, 1) . '/common.php');

if (isset($_POST) && isset($_POST["acceptFriendRequestButton"])) {
    extract($_POST);
    $query = "DELETE FROM friend_requests WHERE request_id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $acceptFriendRequestField);
    $result = mysqli_stmt_execute($stmt);
    if ($result && mysqli_affected_rows($conn) > 0) {
        // Actually to be a freind
        $query = "INSERT INTO friends VALUES (?,?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION["who"], $acceptFriendRequestUsernameField);
        if (mysqli_stmt_execute($stmt)) {
            $tablename = "" . $_SESSION["who"] . "" . $acceptFriendRequestUsernameField;
            $query = "CREATE TABLE " . $tablename . " (fromwho varchar(1000) , message varchar(1000),pos varchar(1000), whenSent DATETIME DEFAULT CURRENT_TIMESTAMP, reactions TEXT DEFAULT NULL, is_edited TINYINT(1) DEFAULT 0)";
            $stmt = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_execute($stmt);
            }
        } else {
            echo "<script>window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Connection Error.');</script>";
        }
    }
}

if (isset($_POST) && isset($_POST["rejectFriendRequestButton"])) {
    extract($_POST);
    $query = "DELETE FROM friend_requests WHERE request_id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $rejectFriendRequestField);
    $result = mysqli_stmt_execute($stmt);
}

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="../Extra/images/favicon.ico">
    <title>All Chat</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="<?= asset('../Extra/styles/cssFiles/themes.css') ?>" />
    <?php if (isset($_SESSION['pendingAlert'])): ?>
        <script>
            window.pendingAlerts = window.pendingAlerts || [];
            window.pendingAlerts.push(<?= json_encode($_SESSION['pendingAlert']) ?>);
        </script>
        <?php unset($_SESSION['pendingAlert']); ?>
    <?php endif; ?>
    <style>
        @keyframes bellWiggle {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(5deg); }
            40% { transform: rotate(-5deg); }
            50% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        .notificationsButton:not(.notificationsButtonDisabled) {
            animation: bellWiggle 1.5s infinite ease-in-out;
            transform-origin: top center;
        }
        .notificationsButtonDisabled .hasNotifications {
            display: none;
        }
        body,
        html {
            height: 100%;
        }

        @media only screen and (max-width:1430px) {
            body {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mainHeaderImage {
                display: none;
            }
        }

        /* Modern Delete Friend Modal */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .custom-modal.show {
            display: flex;
            opacity: 1;
        }
        .custom-modal-content {
            background: #2B303A;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transform: scale(0.8);
            transition: transform 0.3s ease;
            min-width: 300px;
            border: 1px solid #3d4554;
        }
        .custom-modal.show .custom-modal-content {
            transform: scale(1);
        }
        .custom-modal-actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .custom-modal-actions button {
            flex: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        .btn-cancel {
            background: #394240;
            color: #fff;
        }
        .btn-cancel:hover {
            background: #506C7F;
        }
        .btn-confirm {
            background: #F76D57;
            color: #fff;
        }
        .btn-confirm:hover {
            background: #ff523a;
            box-shadow: 0 0 15px rgba(247, 109, 87, 0.4);
        }

        /* Modern User Card */
        .user-card {
            background: rgba(43, 48, 58, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 15px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
        }
        .user-card-name {
            color: #F9EBB2;
            margin: 0;
            font-size: 1.6rem;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .user-card-logout {
            background: linear-gradient(135deg, #F76D57 0%, #ff523a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 24px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(247, 109, 87, 0.3);
            text-decoration: none;
        }
        .user-card-logout:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(247, 109, 87, 0.5);
        }
        .profile-img-styled {
            border-radius: 50%;
            border: 3px solid #F76D57;
            box-shadow: 0 4px 15px rgba(247, 109, 87, 0.4);
            transition: transform 0.3s ease;
        }
        .profile-img-styled:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .modern-box-header {
            font-size: 1.4rem;
            color: #F76D57;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(247, 109, 87, 0.3);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?= asset('../scripts/main.js') ?>"></script>
    <script src="<?= asset('../scripts/commonMethods.js') ?>"></script>
    <script>
        function hideMainBoxBeforeLoading(box_id) {
            $("#" + box_id + "").css({
                'pointer-events': 'none',
                'user-select': 'none',
                'opacity': '0'
            });
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

        function showLoadingBoxForLogout() {
            document.body.removeChild(document.getElementById("loading_box_outer_id"));
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
            setTimeout(function() {
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

        function returnAfterLoadingForLogout() {
            setTimeout(function() {
                window.location.replace('Logout/');
            }, 5000);
        }

        function startLoadingToLogout(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBoxForLogout();
            returnAfterLoadingForLogout();
        }

        function startLoading(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBox();
            returnAfterLoading(box_id);
        }

        $(document).ready(function() {
            // Theme toggle logic
            const currentTheme = localStorage.getItem('theme') || 'dark';
            if (currentTheme === 'light') {
                $('body').addClass('light-mode');
            }

            // Update button active states
            function updateThemeButtons() {
                if ($('body').hasClass('light-mode')) {
                    $('#themeLightBtn').addClass('active');
                    $('#themeDarkBtn').removeClass('active');
                } else {
                    $('#themeDarkBtn').addClass('active');
                    $('#themeLightBtn').removeClass('active');
                }
            }

            setTimeout(updateThemeButtons, 100);

            $(document).on('click', '#themeLightBtn', function(e) {
                e.preventDefault();
                $('body').addClass('light-mode');
                localStorage.setItem('theme', 'light');
                updateThemeButtons();
            });

            $(document).on('click', '#themeDarkBtn', function(e) {
                e.preventDefault();
                $('body').removeClass('light-mode');
                localStorage.setItem('theme', 'dark');
                updateThemeButtons();
            });

            startLoading('whole_box_id');
            $("#create_group_button").click(function() {
                $("#whole_box_id").css({
                    "pointer-events": "none",
                    "opacity": "0.2",
                    "user-select": "none"
                });
                $("#createGroupBox").show();
            });

            $("#notificationsButton").click(function() {
                if (typeof toggle === 'function' && $("#mainHeader").hasClass("drawer-open")) {
                    toggle();
                }
                if (typeof toggle === 'function' && $("#mainHeader").hasClass("drawer-open")) {
                    toggle();
                }
                $("#whole_box_id").css({
                    "pointer-events": "none",
                    "opacity": "0.2",
                    "user-select": "none"
                });
                $("#notificationsBox").show();
            });

            $(document).on("click", "#closeNotificationsButton", function() {
                $("#whole_box_id").css({
                    "pointer-events": "all",
                    "opacity": "1",
                    "user-select": "none"
                });
                $("#notificationsBox").hide();
            });
        });

        var wsToken = "<?php echo isset($_SESSION['ws_token']) ? $_SESSION['ws_token'] : ''; ?>";
        var protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
        var wsHost = "<?php echo getVarFromEnv('WS_URL') ?: (isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] . ':8080' : 'localhost:8080'); ?>";
        var wsUrl = protocol + wsHost + '?token=' + encodeURIComponent(wsToken);
        var wsMain = new WebSocket(wsUrl);

        wsMain.onmessage = function(event) {
            try {
                var data = JSON.parse(event.data);
                if (data.type === 'notification' && data.content === 'friend_request') {
                    $('#notificationsBox').load(window.location.href + ' #notificationsBox > *', function() {
                        $('#notificationsButton').removeClass('notificationsButtonDisabled');
                    });
                }
            } catch(e) {}
        };

        function openDeleteModal(friendName) {
            $("#deleteFriendName").text(friendName);
            $("#confirmDeleteBtn").off("click").on("click", function() {
                if (wsMain && wsMain.readyState === WebSocket.OPEN) {
                    wsMain.send(JSON.stringify({
                        type: 'delete_friend',
                        target: friendName
                    }));
                }
                setTimeout(() => {
                    window.location.href = "Delete_Friend/?name=" + encodeURIComponent(friendName);
                }, 100);
            });
            $("#deleteFriendModal").css("display", "flex");
            setTimeout(() => $("#deleteFriendModal").addClass("show"), 10);
        }

        function closeDeleteModal() {
            $("#deleteFriendModal").removeClass("show");
            setTimeout(() => {
                $("#deleteFriendModal").css("display", "none");
            }, 300);
        }

        function openLogoutModal() {
            $("#logoutModal").css("display", "flex");
            setTimeout(() => $("#logoutModal").addClass("show"), 10);
        }

        function closeLogoutModal() {
            $("#logoutModal").removeClass("show");
            setTimeout(() => {
                $("#logoutModal").css("display", "none");
            }, 300);
        }
    </script>
</head>

<body>
    <div id="deleteFriendModal" class="custom-modal">
        <div class="custom-modal-content">
            <h3 style="color:#fff; margin-bottom: 20px;">Confirm Deletion</h3>
            <p style="color:#ccc; margin-bottom: 30px;">Are you sure you want to delete your friend <span id="deleteFriendName" style="color:#F76D57; font-weight:bold;"></span>?</p>
            <div class="custom-modal-actions">
                <button onclick="closeDeleteModal()" class="btn-cancel">Cancel</button>
                <button id="confirmDeleteBtn" class="btn-confirm">Delete</button>
            </div>
        </div>
    </div>
    <div id="logoutModal" class="custom-modal">
        <div class="custom-modal-content">
            <h3 style="color:#fff; margin-bottom: 20px;">Confirm Logout</h3>
            <p style="color:#ccc; margin-bottom: 30px;">Are you sure you want to log out of All Chat?</p>
            <div class="custom-modal-actions">
                <button onclick="closeLogoutModal()" class="btn-cancel">Cancel</button>
                <button onclick="closeLogoutModal(); startLoadingToLogout('whole_box_id');" class="btn-confirm">Logout</button>
            </div>
        </div>
    </div>
    <div id="createGroupBox">
        <div class="outer_reqs_box" onclick="hide()">
            <div class="reqs" id="username_reqs">
                <p>
                    Group name shoud not contain symbols and it should be at least 6 characters in length and at most 12.
                </p>
            </div>
            <div class="reqs" id="image_reqs">
                <p>
                    Group image shoud not be bigger than 1 MB and it should be of type PNG.
                </p>

            </div>
        </div>
        <button id="exit">&times;</button>
        <div id="createGroupBox-inner">
            <form action="" method="post" enctype="multipart/form-data" class="createGroupForm" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                <div class="modern-box-header" style="margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 1.5rem;">Create New Group</h2>
                </div>
                
                <div class="modern-input-group" style="display: flex; flex-direction: column; gap: 8px; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.95rem; opacity: 0.9;">Group Name:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="group_name" class="modern-input" style="flex: 1; min-width: 0;" placeholder="Enter group name...">
                        <div class="modern-info-btn" id="userreq" style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 12px; background: rgba(255,255,255,0.1); cursor: pointer; border: 1px solid rgba(255,255,255,0.2);">i</div>
                    </div>
                </div>

                <div class="modern-input-group" style="display: flex; flex-direction: column; gap: 8px; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.95rem; opacity: 0.9;">Group Profile Image:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="file" onchange='renderImage()' name="image" id="picField" class="modern-input-file" style="flex: 1; padding: 10px; min-width: 0;">
                        <div class="modern-info-btn" id="imagereq" style="display: flex; align-items: center; justify-content: center; width: 40px; border-radius: 12px; background: rgba(255,255,255,0.1); cursor: pointer; border: 1px solid rgba(255,255,255,0.2);">i</div>
                    </div>
                </div>
                
                <div id="imageRenderOuterBox" style="text-align: center;"></div>
                
                <button type="submit" name="create_group_button" class="modern-submit-btn">Create Group</button>
            </form>
        </div>
    </div>
    <div id="notificationsBox">
        <button class="closeButtonForNotifications" id="closeNotificationsButton">X</button>
        <table class="notificationsInnerBox">
            <tr>
                <th>
                    Requester:
                </th>
                <th>
                    Sent Date:
                </th>
                <th>
                    Action:
                </th>
            </tr>
            <?php
            $query = "SELECT * FROM friend_requests WHERE requested_user=?";
            $stmt = mysqli_prepare($conn, $query);
            $who = $_SESSION["who"];
            mysqli_stmt_bind_param($stmt, "s", $who);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $hasNotifications = false;
            if ($result && mysqli_num_rows($result) > 0) {
                $hasNotifications = true;
                while ($row = mysqli_fetch_row($result)) {
            ?>
                    <tr>
                        <td>
                            <img src="View_Image/?u=<?php echo $row[1]; ?>" alt="requester profile image" class="notificationUserImage">
                            <h4 class="notificationUsername"> <?php echo $row[1]; ?> </h4>
                        </td>
                        <td>
                            <?php echo $row[3]; ?>
                        </td>
                        <td>
                            <form action="" method="POST" style="margin-bottom:5px;">
                                <input type="hidden" name="acceptFriendRequestField" value="<?php echo $row[0]; ?>">
                                <input type="hidden" name="acceptFriendRequestUsernameField" value="<?php echo $row[1]; ?>">
                                <input type="submit" name="acceptFriendRequestButton" value="Accept" class="accept-btn">
                            </form>
                            <form action="" method="POST">
                                <input type="hidden" name="rejectFriendRequestField" value="<?php echo $row[0]; ?>">
                                <input type="submit" name="rejectFriendRequestButton" value="Reject" class="reject-btn">
                            </form>
                        </td>
                    </tr>
            <?php
                }
            } else {
            ?>
                <tr><td colspan="3"><h4 style="text-align:center; color:#ccc; margin-top:20px;">No new notifications.</h4></td></tr>
            <?php
            }
            ?>
        </table>
    </div>
    <div class="whole-box" id="whole_box_id">
        <div class="menuBar">
            <svg id="m" class="menu-icon-svg" style="cursor:pointer; width: 35px; height: 35px; color: inherit; transition: transform 0.5s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </div>
        <div class="mainHeaderImage" id="mainHeader">
            <div>
                <style>
                    .brand-logo-img {
                        width: 85%;
                        height: 120px;
                        object-fit: contain;
                        margin: 20px auto 15px auto;
                        display: block;
                        border-radius: 20px;
                        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                        animation: floatLogo 4s ease-in-out infinite;
                        transition: all 0.3s ease;
                    }
                    .brand-logo-img:hover {
                        transform: scale(1.05) translateY(-5px);
                        box-shadow: 0 12px 35px rgba(0,0,0,0.5);
                    }
                    @keyframes floatLogo {
                        0% { transform: translateY(0px); }
                        50% { transform: translateY(-8px); }
                        100% { transform: translateY(0px); }
                    }
                </style>
                <img src="../Extra/styles/images/main.png" alt="All Chat Brand Logo" class="brand-logo-img" />
                <hr class="hrLine">
                <div class="menuContainer">
                    <a href="Add/" class="link" id="addLink">Add New Friend</a>
                    <a href="Profile/" class="link" id="editLink">Edit Profile</a>
                </div>
            </div>
            <?php
            $query = "SELECT picture FROM users WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            $who = $_SESSION["who"];
            mysqli_stmt_bind_param($stmt, "s", $who);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                $row = mysqli_fetch_row($result);
            ?>

                <div class='profileBox'>
                    <button <?php if ($hasNotifications) { ?> <?php } ?> title="Notifications" id="notificationsButton" class="notificationsButton <?php if (!$hasNotifications) echo "notificationsButtonDisabled"; ?>">
                        <svg id="bellImage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" id="Layer_1" width="800px" height="800px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                            <g>
                                <path fill="#394240" d="M56,48c-3.313,0-6-2.687-6-6V22c0-8.577-6.004-15.74-14.035-17.548C35.984,4.304,36,4.154,36,4   c0-2.209-1.791-4-4-4s-4,1.791-4,4c0,0.154,0.016,0.304,0.035,0.452C20.004,6.26,14,13.423,14,22v20c0,3.313-2.687,6-6,6   c-2.209,0-4,1.791-4,4s1.791,4,4,4h16c0,4.418,3.582,8,8,8s8-3.582,8-8h16c2.209,0,4-1.791,4-4S58.209,48,56,48z M32,2   c1.104,0,2,0.896,2,2c0,0.04-0.014,0.075-0.017,0.115C33.332,4.043,32.671,4,32,4s-1.332,0.043-1.983,0.115   C30.014,4.075,30,4.04,30,4C30,2.896,30.896,2,32,2z M16,22c0-8.837,7.163-16,16-16s16,7.163,16,16v12H16V22z M16,36h32v4H16V36z    M32,62c-3.313,0-6-2.687-6-6h12C38,59.313,35.313,62,32,62z M56,54H8c-1.104,0-2-0.896-2-2s0.896-2,2-2c4.418,0,8-3.582,8-8h32   c0,4.418,3.582,8,8,8c1.104,0,2,0.896,2,2S57.104,54,56,54z" />
                                <path fill="#506C7F" d="M32,2c1.104,0,2,0.896,2,2c0,0.04-0.014,0.075-0.017,0.115C33.332,4.043,32.671,4,32,4   s-1.332,0.043-1.983,0.115C30.014,4.075,30,4.04,30,4C30,2.896,30.896,2,32,2z" />
                                <path fill="#F76D57" d="M16,22c0-8.837,7.163-16,16-16s16,7.163,16,16v12H16V22z" />
                                <rect x="16" y="36" fill="#F9EBB2" width="32" height="4" />
                                <path fill="#B4CCB9" d="M32,62c-3.313,0-6-2.687-6-6h12C38,59.313,35.313,62,32,62z" />
                                <path fill="#F76D57" d="M56,54H8c-1.104,0-2-0.896-2-2s0.896-2,2-2c4.418,0,8-3.582,8-8h32c0,4.418,3.582,8,8,8   c1.104,0,2,0.896,2,2S57.104,54,56,54z" />
                            </g>
                        </svg>
                        <span class="hasNotifications"></span>
                    </button>
                    <img src='View_Image/?u=<?php echo $_SESSION["who"]; ?>' width='100px' height='100px' class='profile-img-styled' />
                    
                    <div class="user-card">
                        <h2 class="user-card-name" style="margin-bottom:5px; text-align:center;"><?php echo $_SESSION["who"]; ?></h2>
                        <div style="display:flex; justify-content:center; gap:10px; margin-bottom:15px; width:100%;">
                            <button id="themeLightBtn" class="theme-btn" title="Light Mode">☀️</button>
                            <button id="themeDarkBtn" class="theme-btn" title="Dark Mode">🌙</button>
                        </div>
                        <button onclick="openLogoutModal()" class="user-card-logout">
                            <span style="margin-right: 8px; font-size:1.2rem;">🚪</span> Logout
                        </button>
                    </div>
                </div>

            <?php

            } else {
                destroy();
            }
            function destroy()
            {
                session_destroy();
                header("Location:../");
            }

            ?>
            <div id="exit_menu_button_box">
                <div id="exit_menu_button">&times;</div>
            </div>
        </div>


        <div class="groupsListBox" id="groupsBox">
            <div class="modern-box-header" style="margin-bottom: 15px;">
                <span>👥 Groups</span>
                <button class="plus_button" id="create_group_button" title="Create Group">+</button>
            </div>
            <div id="groupsInnerData">
            </div>
        </div>

        <div class="friendsListBox" id="friendBox">
            <div class="modern-box-header">
                <span>💬 Friends</span>
            </div>
            <div id="innerData">
            </div>
        </div>
    </div>
</body>

</html>
<?php

function checkGroupName($group_name)
{
    if (trim($group_name) == "") {
        return false;
    }
    $everythingIsGood = true;
    if (!(strlen($group_name) >= 6 && strlen($group_name) <= 12)) {
        $everythingIsGood = false;
    }
    if ($everythingIsGood) {
        for ($i = 0; $i < strlen($group_name); $i++) {
            if (!((ord(substr($group_name, $i, 1)) == 32) || (ord(substr($group_name, $i, 1)) >= 48 && ord(substr($group_name, $i, 1)) <= 57) || (ord(substr($group_name, $i, 1)) >= 65 && ord(substr($group_name, $i, 1)) <= 90) || (ord(substr($group_name, $i, 1)) >= 97 && ord(substr($group_name, $i, 1)) <= 122))) {
                $everythingIsGood = false;
                break;
            }
        }
    }
    return $everythingIsGood;
}

function generateGroupID($conn)
{
    $id_query = "SELECT group_id FROM all_chat_groups ORDER BY group_id DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $id_query);
    mysqli_stmt_execute($stmt);
    $id_result = mysqli_stmt_get_result($stmt);
    if ($id_result) {
        $new_id = 1;
        if (mysqli_num_rows($id_result)) {
            $new_id = mysqli_fetch_row($id_result)[0] + 1;
        }
        return $new_id;
    }
    return "error";
}

function createGroup($conn, $group_name)
{
    $group_id = generateGroupID($conn);
    if ($group_id == "error") {
        die("Unknown Error");
    }
    $tmp_name = $_FILES["image"]["tmp_name"];
    $image_name = "i" . $group_id . ".png";

    if (!is_dir("../Extra/styles/images/groups_images")) {
        mkdir("../Extra/styles/images/groups_images", 0777);
    }

    $path = "../Extra/styles/images/groups_images/$image_name";
    move_uploaded_file($tmp_name, $path);

    $insert_query = "INSERT INTO all_chat_groups VALUES (?,?,?,?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    $img_name = $image_name;
    $gname = $group_name;
    $who = $_SESSION["who"];
    $gid = $group_id;
    mysqli_stmt_bind_param($stmt, "isss", $gid, $gname, $who, $img_name);
    $insert_result = mysqli_stmt_execute($stmt);

    if ($insert_result) {
        createTables($conn, $group_id, $group_name);
    } else {
        die("Error");
    }
}

function createTables($conn, $group_id, $group_name)
{
    $users_table_name = "g" . $group_id . "_users";
    $users_table_query = "CREATE TABLE " . $users_table_name . " (
        username VARCHAR(255),
        user_type VARCHAR(255)
    )
    ";
    $stmt = mysqli_prepare($conn, $users_table_query);
    $leader_insertion_result = ($stmt && mysqli_stmt_execute($stmt)) ? true : false;
    if ($leader_insertion_result) {
        $users2_query = "INSERT INTO $users_table_name VALUES (?,?)";
        $stmt = mysqli_prepare($conn, $users2_query);
        $utype = 'leader';
        $who = $_SESSION["who"];
        mysqli_stmt_bind_param($stmt, "ss", $who, $utype);
        $users2_result = mysqli_stmt_execute($stmt);
        if ($users2_result) {
            $normal_group_table_name = "g" . $group_id;
            $normal_group_table_query = "CREATE TABLE " . $normal_group_table_name . " (
                fromwho varchar(1000), 
                message varchar(1000),
                pos varchar(1000), 
                whenSent DATETIME DEFAULT CURRENT_TIMESTAMP, 
                reactions TEXT DEFAULT NULL,
                is_edited TINYINT(1) DEFAULT 0
            )";
            $stmt = mysqli_prepare($conn, $normal_group_table_query);
            $normal_group_query_result = ($stmt && mysqli_stmt_execute($stmt)) ? true : false;
            if ($normal_group_query_result) {
                echo
                "
                <script>
                window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Successfully created group: " . $group_name . "');
                </script>
                ";
            } else {
                die("Error");
            }
        } else {
            die("Error");
        }
    } else {
        die("Unknown Error");
    }
}

if (isset($_POST) && isset($_POST["create_group_button"])) {
    extract($_POST);
    $isGroupNameGood = checkGroupName($group_name);
    if ($_FILES['image']['name'] != "" && $isGroupNameGood) {
        $name = $_FILES["image"]["name"];
        $type = substr($name, strlen($name) - 3, strlen($name) - 1);
        if ($type == "png") {
            define('MB', 1024000);
            $size = round((($_FILES["image"]["size"]) / MB), 0);
            if ($size <= 1) {
                createGroup($conn, $group_name);
            } else {
                echo
                "
                            <script>
                            window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Image size is bigger than 1 MB');
                            </script>
                            ";
            }
        } else {
            echo
            "
                            <script>
                            window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Image type is not PNG');
                            </script>
                            ";
        }
    } else {
        echo "<script>
            window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Check group name requirements or image file is not present.');
        </script>";
    }
}
mysqli_close($conn);
?>