<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../");
}

require_once("../DB.php");
require_once(dirname(__DIR__, 1) . '/common.php');
require_once(dirname(__DIR__, 1) . '/ws_auth.php');

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
            
            echo "<script>
            window.addEventListener('load', function() {
                var attemptSocket = function() {
                    if (window.wsMain && window.wsMain.readyState === WebSocket.OPEN) {
                        window.wsMain.send(JSON.stringify({ type: 'accept_friend_request', target: '" . htmlspecialchars($acceptFriendRequestUsernameField, ENT_QUOTES) . "' }));
                    } else {
                        setTimeout(attemptSocket, 500);
                    }
                };
                attemptSocket();
            });
            </script>";
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
    <?php if (isset($_SESSION['pending_ws_events']) && is_array($_SESSION['pending_ws_events']) && count($_SESSION['pending_ws_events']) > 0): ?>
        <script>
            window.addEventListener('load', function() {
                var pendingEvents = <?php echo json_encode($_SESSION['pending_ws_events']); ?>;
                var attemptSocket = function() {
                    if (window.wsMain && window.wsMain.readyState === WebSocket.OPEN) {
                        pendingEvents.forEach(function(evt) {
                            window.wsMain.send(JSON.stringify(evt));
                        });
                    } else {
                        setTimeout(attemptSocket, 500);
                    }
                };
                attemptSocket();
            });
        </script>
        <?php unset($_SESSION['pending_ws_events']); ?>
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
        .modern-badge {
            background: linear-gradient(135deg, rgba(247, 109, 87, 0.15) 0%, rgba(255, 82, 58, 0.25) 100%);
            border: 1px solid rgba(247, 109, 87, 0.4);
            padding: 6px 16px;
            border-radius: 0 0 20px 0;
            font-size: 1.1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(247, 109, 87, 0.1);
            backdrop-filter: blur(5px);
            text-shadow: none;
        }
        body.light-mode .modern-badge {
            background: linear-gradient(135deg, rgba(247, 109, 87, 0.1) 0%, rgba(255, 82, 58, 0.15) 100%);
            box-shadow: 0 2px 8px rgba(247, 109, 87, 0.1);
            border: 1px solid rgba(247, 109, 87, 0.3);
        }
        .unread-bell {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.6);
            animation: bellWiggle 1s ease-in-out infinite;
            z-index: 10;
            display: none; /* hidden by default */
            border: 2px solid #2B303A;
        }
        body.light-mode .unread-bell {
            border: 2px solid #f8f9fa;
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
        var wsHost = "<?php echo getVarFromEnv('WS_URL') ?: (isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] . ':'.getVarFromEnv('WS_PORT') : 'localhost:'.getVarFromEnv('WS_PORT')); ?>";
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
                
                if (data.type === 'friend_deleted' || data.type === 'friend_added') {
                    if (window.reloadFriendsList) window.reloadFriendsList();
                }
                
                if (data.type === 'group_added' || data.type === 'group_destroyed' || data.type === 'user_kicked' || data.type === 'user_promoted' || data.type === 'user_demoted') {
                    if (window.reloadGroupsList) window.reloadGroupsList();
                }
                if (data.type === 'personal') {
                    if (data.lastwho !== '<?php echo $_SESSION["who"]; ?>') {
                        let unreads = JSON.parse(localStorage.getItem('unread_friends') || '[]');
                        if (!unreads.includes(data.lastwho)) unreads.push(data.lastwho);
                        localStorage.setItem('unread_friends', JSON.stringify(unreads));
                        if (window.restoreUnreadState) window.restoreUnreadState();
                    }
                }

                if (data.type === 'group') {
                    if (data.lastwho !== '<?php echo $_SESSION["who"]; ?>') {
                        let unreads = JSON.parse(localStorage.getItem('unread_groups') || '[]');
                        if (!unreads.includes(data.groupId.toString())) unreads.push(data.groupId.toString());
                        localStorage.setItem('unread_groups', JSON.stringify(unreads));
                        if (window.restoreUnreadState) window.restoreUnreadState();
                    }
                }

                if (data.type === 'status_change') {
                    var userRow = $('.friendRow[data-username="'+data.username+'"]');
                    if (userRow.length > 0) {
                        var color = data.ava === "1" ? "#10b981" : "#ef4444";
                        userRow.find('img').css('border-color', color);
                    }
                }
            } catch(e) {}
        };

        window.restoreUnreadState = function() {
            let unreadFriends = JSON.parse(localStorage.getItem('unread_friends') || '[]');
            let unreadGroups = JSON.parse(localStorage.getItem('unread_groups') || '[]');

            $('.friendRow').each(function() {
                let username = $(this).data('username');
                if (unreadFriends.includes(username)) {
                    $(this).find('.unread-bell').show();
                } else {
                    $(this).find('.unread-bell').hide();
                }
            });

            $('.groupRow').each(function() {
                let groupid = $(this).data('groupid').toString();
                if (unreadGroups.includes(groupid)) {
                    $(this).find('.unread-bell').show();
                } else {
                    $(this).find('.unread-bell').hide();
                }
            });
        };

        window.clearUnreadFriend = function(username) {
            let unreadFriends = JSON.parse(localStorage.getItem('unread_friends') || '[]');
            unreadFriends = unreadFriends.filter(u => u !== username);
            localStorage.setItem('unread_friends', JSON.stringify(unreadFriends));
        };

        window.clearUnreadGroup = function(groupId) {
            let unreadGroups = JSON.parse(localStorage.getItem('unread_groups') || '[]');
            unreadGroups = unreadGroups.filter(g => g !== groupId.toString());
            localStorage.setItem('unread_groups', JSON.stringify(unreadGroups));
        };

        function openDeleteModal(friendName) {
            $("#deleteFriendName").text(friendName);
            $("#confirmDeleteBtn").off("click").on("click", function() {
                window.location.href = "Delete_Friend/?name=" + encodeURIComponent(friendName);
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
                        <input type="file" onchange='renderImage()' name="image" id="picField" class="modern-input-file" style="flex: 1; padding: 10px; min-width: 0;" accept=".png">
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
            ?>
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
                                <input type="submit" name="acceptFriendRequestButton" value="Accept" class="accept-btn" onclick="return customConfirm('Are you sure you want to accept this request?', this.form, this.name, this.value);">
                            </form>
                            <form action="" method="POST">
                                <input type="hidden" name="rejectFriendRequestField" value="<?php echo $row[0]; ?>">
                                <input type="submit" name="rejectFriendRequestButton" value="Reject" class="reject-btn" onclick="return customConfirm('Are you sure you want to reject this request?', this.form, this.name, this.value);">
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
                        animation: floatLogo 4s ease-in-out infinite;
                        transition: all 0.3s ease;
                        border-radius: 50%;

                        
                        /* Dark Mode integration: Remove white bg and make blue pop */
                        filter: invert(1) hue-rotate(180deg) brightness(1.5);
                        mix-blend-mode: screen;
                        opacity: 0.95;
                    }
                    .brand-logo-img:hover {
                        transform: scale(1.05) translateY(-5px);
                        opacity: 1;
                        filter: invert(1) hue-rotate(180deg) brightness(1.8);
                    }
                    
                    body.light-mode .brand-logo-img {
                        /* Light Mode integration: Multiply removes the white background */
                        filter: none;
                        mix-blend-mode: multiply;
                        opacity: 0.9;
                    }
                    body.light-mode .brand-logo-img:hover {
                        filter: brightness(0.8);
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
                        <svg id="bellImage" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
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
                <span class="modern-badge">👥 Groups</span>
                <button class="plus_button" id="create_group_button" title="Create Group">+</button>
            </div>
            <div id="groupsInnerData">
            </div>
        </div>

        <div class="friendsListBox" id="friendBox">
            <div class="modern-box-header">
                <span class="modern-badge">💬 Friends</span>
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