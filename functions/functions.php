<?php
include_once 'sanitize.php';

session_start();

/**
 * Session Utilities
 */
function logged(){
    return isset($_SESSION['username']);
}

function set_session($username){

    $_SESSION['username'] = $username;

}

function get_username(){
    if(!logged()){
        return null;
    }
    return $_SESSION['username'];
}

function log_out(){

    cancel_session();	
	foreach ( $_COOKIE as $key => $value ) {
		delete_cookies($key);
	}
    
    
}

function cancel_session() {
	$_SESSION = array();

    if (ini_get("session.use_cookies")) {
        
        $params = session_get_cookie_params();
        
        setcookie(session_name(), '', time() - 3600*24, 
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        
    }
	session_destroy();
}

function check_inactivity_time(){

    $t = time();
    $diff = 0;
    
    if (isset($_SESSION['time'])){
        $t0 = $_SESSION['time'];
        $diff = ($t - $t0);  // inactivity period
    } 
    
    if ($diff > 120) { // new or with inactivity period too long
        cancel_session();
    } else {
        $_SESSION['time'] = time();
    }
}

/**
 * Cookies Utilities
 */

function get_selected_seats() {
    
    $selected = array();
    
    if(isset($_COOKIE['selected']) && !empty($_COOKIE['selected'])){
        $selected = explode(", ", $_COOKIE['selected']);
    }
    
    return $selected;
}

function delete_cookies($name) {

    setcookie($name, "", time() - 3600);

}

function get_seats_to_release() {

    $release = array();

    if(isset($_COOKIE['release']) && !empty($_COOKIE['release'])){
        $release = explode(", ", $_COOKIE['release']);
    }

    return $release;

}