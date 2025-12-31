<?php

    $urlMainPath = "";

    function formatMessage($message) {

        // Format if there is links in it
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $pattern = '/(https?:\/\/[^\s]+)/';
        $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer" class="chatMessageLink">$1</a>';
        return preg_replace($pattern, $replacement, $message);
        //


    }

?>