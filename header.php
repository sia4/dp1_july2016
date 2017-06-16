<?php
require_once ('functions/config.php');
require_once ('functions/functions.php');
require_once ('functions/db_functions.php');
require_once ('functions/initial_checks.php');

/* INITIAL CHECKS */
check_https();
check_cookie();
check_inactivity_time();

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Polito's Theatre</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="js/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="js/jsfunctions.js"></script>
    <link href="stylesheet/style.css" rel="stylesheet" type="text/css">
    <noscript>JavaScript is disabled. In this way the website could not work well.</noscript>
</head>

<body>

    <div id="header">
        <div id="title_header"><p>Polito's Theatre</p></div>
    </div>
    <div id="menu">
        <div><h2>MENU</h2></div>

        <?php
            /* Highlight the current menu element */
            $page = explode('/', $_SERVER['PHP_SELF']);

                switch ($page[count($page) - 1]){
                    case 'index.php':
                        $active_el = 1;
                        break;
                    case 'login.php':
                        $active_el = 2;
                        
                        break;
                    case 'logout.php':
                        $active_el = 3;
                        break;
                    default:
                        $active_el = 1;
                        break;
                }

        ?>

        <div><a class="menu_el <?php if($active_el == 1) echo 'active'; ?>" href="index.php">Home</a></div>
            <?php if (!logged()) { ?>
                
                <div><a class="menu_el <?php if($active_el == 2) echo 'active'; ?>" href="login.php">Login / Sign Up</a></div>
            <?php }else { ?>
                  <div><a class="menu_el <?php if($active_el == 3) echo 'active'; ?>" href="logout.php">Log Out</a></div>
            <?php } ?>
    </div>