<?php

function check_https() {
    /*if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }*/
}

function check_cookie() {
    /*
    $page = explode('/', $_SERVER['PHP_SELF']);

    if (!isset($_SESSION['cookie_enabled']) && $page[count($page) - 1] != 'cookie.php') {
        $_SESSION['page'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        setcookie('test', 'test', time() + 3600);
        header("location: cookie.php");
        exit;
    }*/
}

?>