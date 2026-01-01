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

function generateTokenCode()
{
    $token = "";
    $how_many_chars = rand(4, 8);
    for ($i = 1; $i <= $how_many_chars; $i++) {
        $token .= generateRandomChar();
    }
    return $token;
}

function updateUserImageToken($conn, $username)
{
    $token = generateTokenCode();
    $query = "UPDATE users SET view_image_token='" . $token . "' WHERE username='" . $username . "'";
    return [mysqli_query($conn, $query), $token];
}
