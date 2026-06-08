<?php

require_once 'env.php';

$urlMainPath = getVarFromEnv("PUBLIC_SITE_URL");

function formatMessage($message)
{

    $urlMainPath = getVarFromEnv("PUBLIC_SITE_URL");

    // Format if there is links in it
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Format files: [FILE:path:name]
    $filePattern = '/\[FILE:\s*(.+?):\s*(.+?)\]/';
    $message = preg_replace_callback($filePattern, function($matches) use ($urlMainPath) {
        $path = $matches[1];
        $name = $matches[2];
        $filename = basename($path);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $basePath = !empty($urlMainPath) ? "/" . $urlMainPath : "";
        $fullPath = $basePath . "/serveFile.php?f=" . urlencode($filename) . "&n=" . urlencode($name);
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return "<br/><a href='" . $fullPath . "' target='_blank'><img src='" . $fullPath . "' onload='var b=document.getElementById(\"messages\"); if(b && (b.scrollHeight - b.scrollTop - b.clientHeight <= 300)) b.scrollTop=b.scrollHeight;' style='max-width:100%; height:auto; max-height:200px; border-radius:10px; margin-top:5px; display:block; box-sizing:border-box;' alt='" . $name . "'/></a>";
        } else if (in_array($ext, ['mp4', 'webm'])) {
            return "<br/><video src='" . $fullPath . "' onloadeddata='var b=document.getElementById(\"messages\"); if(b && (b.scrollHeight - b.scrollTop - b.clientHeight <= 300)) b.scrollTop=b.scrollHeight;' controls style='max-width:100%; height:auto; max-height:200px; border-radius:10px; margin-top:5px; display:block; box-sizing:border-box;'></video>";
        } else if (in_array($ext, ['mp3', 'wav'])) {
            return "<br/><audio src='" . $fullPath . "' controls style='max-width:100%; margin-top:5px; display:block; box-sizing:border-box;'></audio>";
        } else {
            return "<br/><a href='" . $fullPath . "' target='_blank' class='chatFileLink' style='display:inline-block; margin-top:5px; padding:5px 10px; border-radius:10px; text-decoration:none; color:inherit;'>📎 " . $name . "</a>";
        }
    }, $message);
    
    $pattern = '/(https?:\/\/[^\s]+)/';
    $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer" class="chatMessageLink">$1</a>';
    $message = preg_replace($pattern, $replacement, $message);

    // Format mentions
    $mentionPattern = '/(?<!\w)@(\w+)/';
    $mentionReplacement = '<span class="mention-text">@$1</span>';
    $message = preg_replace($mentionPattern, $mentionReplacement, $message);

    return $message;


}

function generateRandomChar()
{
    $capital_alphabets = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $small_alphabets = "abcdefghijklmnopqrstuvwxyz";
    $numbers = "0123456789";

    $char_rand_alpha_or_number = rand(1, 2);
    if ($char_rand_alpha_or_number == 1) {
        $small_or_capital = rand(1, 2);
        if ($small_or_capital == 1) {
            return "" . $small_alphabets[rand(0, strlen($small_alphabets) - 1)];
        } else if ($small_or_capital == 2) {
            return "" . $capital_alphabets[rand(0, strlen($capital_alphabets) - 1)];
        }
    } else {
        return "" . $numbers[rand(0, strlen($numbers) - 1)];
    }
}

function getUserImageName($conn, $username) {
    $query = "SELECT picture FROM users WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $imageName = "user";
    if (mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            $imageName = $row[0];
        } else 
            return null;
    return $imageName;
}

function asset($filePath) {
    $version = filemtime($filePath);
    return $filePath . '?v=' . $version;
}