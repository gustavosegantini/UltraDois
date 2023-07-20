<?php
function verify_session($session_key, $redirect_page) {
    if (!isset($_SESSION[$session_key])) {
        header("Location: $redirect_page");
        exit();
    }
}
?>
