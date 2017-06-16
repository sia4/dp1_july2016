<?php

require_once ('functions/config.php');
require_once ('functions/functions.php');
require_once ('functions/db_functions.php');

/* If the user was doing a booking the operation will continue
after registration and/or login */

if(isset($_POST["login"])) {

	if(isset($_COOKIE['continue_operation']) && $_COOKIE['continue_operation'] = "true") {
		$url = 'index.php?action=reserve';
		delete_cookies('continue_operation');
	}else{
		$url = 'index.php';
	}

    $login = check_username_password($_POST['username_l'], $_POST['password_l']);
    if($login){
        set_session($_POST['username_l']);
        header("Location: " . $url);
        exit;
    }
}
if(isset($_POST['register'])){
    $return = create_user($_POST['username_r'], $_POST['password_r']);
}

if(!logged()){
    include_once 'header.php';
    ?>
    <div id="container">
        <div id="login_box">
            <h2>Log In</h2>
            <p class="message">Log in to easily book your seats with just on click!</p>
            <form id="login_form" method="post" action="">
            <input type="email" placeholder="Email" id="username_l" name="username_l"/><br>
            <input type="password" placeholder="Password" id="password_l" name="password_l"/><br>
            <input type="submit" id="login" name="login" value="Log in"/>
            </form>
            <?php
            if(isset($login) && !$login){?>
               <p class="error">Wrong password or mail, please try again!</p>
        <?php
            }
            ?>
        </div>
        <div id="register_box">
            <h2>Aren't you registred yet? Sign up!</h2>
            <form id="register_form" method="post" action="">
                <input type="email" placeholder="Email" id="username_r" name="username_r"/><br>
                <input type="password" placeholder="Password" id="password_r" name="password_r"/></br>
                <input type="password" placeholder="Check password" id="check_password_r"/></br>
                <input type="submit" name="register" id="register" value="Register"/>
            </form>
            <span id="form_error1_r"></span>
			<span id="form_error2_r"></span>
            <?php
            if(isset($return) && $return){?>
                <p class="success">Successfully registered! Log in to book your seats.</p>
                <?php
            }elseif(isset($return) && !$return){?>
                <p class="error">Something went wrong during the operation, please try again!</p>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
include('footer.php');
?>