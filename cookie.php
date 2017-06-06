<?php
require_once('header.php');

if(isset($_COOKIE['test']) && $_COOKIE['test']=='test') {

    $_SESSION['cookie_enabled'] = 'true';
    header("location: https://".$_SESSION['page']);
    
} else { ?>
    <div id="container">
        <p class='message'>Cookies are not enabled. Please enable cookies to start booking your seats!</p>
    </div>
<?php } ?>