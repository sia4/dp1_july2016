<?php


if(isset($_COOKIE['test']) && $_COOKIE['test']=='test') {

    $_SESSION['cookie_enabled'] = 'true';
    header("location: https://".$_SESSION['page']);
    
} else {
    require_once('header.php'); ?>
    <div id="container">
        <p class='message'>Cookies are not enabled. Please enable cookies to start booking your seats!</p>
    </div>
<?php } ?>