<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $query = "SELECT * FROM friend_requests WHERE requested_user=?";
    $stmt = mysqli_prepare($conn, $query);
    $who = $_SESSION["who"];
    mysqli_stmt_bind_param($stmt, "s", $who);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_row($result)) {
            echo '
            <tr>
                <td>
                    <img src="View_Image/?u=' . htmlspecialchars($row[1]) . '" alt="requester profile image" class="notificationUserImage">
                    <h4 class="notificationUsername"> ' . htmlspecialchars($row[1]) . ' </h4>
                </td>
                <td>
                    ' . htmlspecialchars($row[3]) . '
                </td>
                <td>
                    <form action="" method="POST" style="margin-bottom:5px;">
                        <input type="hidden" name="acceptFriendRequestField" value="' . htmlspecialchars($row[0]) . '">
                        <input type="hidden" name="acceptFriendRequestUsernameField" value="' . htmlspecialchars($row[1]) . '">
                        <input type="submit" name="acceptFriendRequestButton" value="Accept" class="accept-btn" onclick="return customConfirm(\'Are you sure you want to accept this request?\', this.form, this.name, this.value);">
                    </form>
                    <form action="" method="POST">
                        <input type="hidden" name="rejectFriendRequestField" value="' . htmlspecialchars($row[0]) . '">
                        <input type="submit" name="rejectFriendRequestButton" value="Reject" class="reject-btn" onclick="return customConfirm(\'Are you sure you want to reject this request?\', this.form, this.name, this.value);">
                    </form>
                </td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="3"><h4 style="text-align:center; color:#ccc; margin-top:20px;">No new notifications.</h4></td></tr>';
    }
} else {
    echo '<tr><td colspan="3"><h4 style="text-align:center; color:#ccc; margin-top:20px;">Error loading notifications.</h4></td></tr>';
}
?>
