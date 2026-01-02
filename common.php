<?php

$urlMainPath = "";

function formatMessage($message)
{

    // Format if there is links in it
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $pattern = '/(https?:\/\/[^\s]+)/';
    $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer" class="chatMessageLink">$1</a>';
    return preg_replace($pattern, $replacement, $message);
    //


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
    $query = "SELECT picture FROM users WHERE BINARY username='" . $username . "'";
    $result = mysqli_query($conn, $query);
    $imageName = "user";
    if ($result) {
        if (mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            $imageName = $row[0];
        }
        else 
            return null;
    }
    return $imageName;
}

function asset($filePath) {
    $version = filemtime($filePath);
    return $filePath . '?v=' . $version;
}