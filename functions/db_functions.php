<?php
include_once('config.php');
include_once 'sanitize.php';

$table_users = "dp1_july2016_users";
$table_bookings = "dp1_july2016_bookings";

/**
 * Open connection
 *
 * @return connection opened
 */
function create_connection(){

    global $host, $username, $password, $dbname;
    $conn = new mysqli($host,$username, $password, $dbname);
    if ($conn->connect_errno) {
        echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
        die;
    }

    $conn->set_charset("utf8");
    
    return $conn;

}

/**
 * Create tables
 *
 * @param $string
 * @return string
 */
function init(){

    global $table_users, $table_bookings;

    $conn = create_connection();

    $query = "CREATE TABLE IF NOT EXISTS $table_users( email varchar(255),
                              password varchar(255),
                              PRIMARY KEY(email))
                              CHARACTER SET=utf8;";

    if($conn->query($query) === false){
        printf("Error creating table users");
        close_connection($conn);
        return false;
    }

    $query = "CREATE TABLE IF NOT EXISTS $table_bookings( id INT(4),
              email varchar(255),
              PRIMARY KEY(id),
              FOREIGN KEY(email) REFERENCES users(email))
              CHARACTER SET=utf8;";

    if($conn->query($query) === false){
        printf("Error creating table bookings");
        close_connection($conn);
        return false;
    }

    close_connection($conn);
    return true;
}

/**
 * Close connection
 *
 * @param connection
 */
function close_connection($conn){

    $conn->close();

}

/**
 * Insert a new user
 *
 * @param $username, $password
 * @return bool
 */
function create_user($username, $pw) {

    global $table_users;

    if($username === "" || $pw === ""){
        return false;
    }

    $conn = create_connection();

    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $pw = security_encrypt($pw);
    

    $query = "INSERT INTO $table_users(email,password) VALUES ('$user','$pw')";

    if($conn->query($query) == false){
        close_connection($conn);
        return false;
    }

    close_connection($conn);
    return true;

}

/**
 * Check if a user exist
 *
 * @param $username
 * @return bool
 */
function exist_user($username) {

    global $table_users;

    if($username === ""){
        return false;
    }

    $conn = create_connection();
    
    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $query = "SELECT COUNT(*) FROM $table_users WHERE email = '$user'";
    $result = $conn->query($query);
    if($result === false){
        close_connection($conn);
        return false;
    }

    $count = $result->fetch_array(MYSQLI_NUM);
    $result->free();
    if($count[0] == 0){
        close_connection($conn);
        return false;
    }else {
        close_connection($conn);
        return true;
    }
}

/**
 * During log in check username and pw
 *
 * @param $username, $password
 * @return bool
 */
function check_username_password($username, $pw){

    global $table_users;

    if($username === "" || $pw === ""){
        return false;
    }
    
    $conn = create_connection();

    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $pw = security_encrypt($pw);

    $query = "SELECT COUNT(*) FROM $table_users WHERE email = '$user' AND password = '$pw'";
    $result = $conn->query($query);
    if($result == false){
        close_connection($conn);
        return false;
    }
    $count = $result->fetch_array(MYSQLI_NUM);
    $result->free();
    if($count[0] == 0){
        close_connection($conn);
        return false;
    }else {
        close_connection($conn);
        return true;
    }


}

/**
 * Return all seats booked by user $username
 *
 * @param $username
 * @return %ids (array)
 */
function get_user_seats($username) {

    global $table_bookings;

    if($username === ""){
        return false;
    }

    $conn = create_connection();
    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $query = "SELECT id FROM $table_bookings WHERE email = '$user'";
    $result = $conn->query($query);
    if($result === false){
        close_connection($conn);
        return false;
    }

    $ids = array();
    while($id = $result->fetch_array(MYSQLI_NUM)){
        $ids[] = $id[0];
    }

    $result->free();
    close_connection($conn);
    return $ids;

}

/**
 * Return all seats booked by all users that are not $username
 * (if it is set)
 *
 * @param $username
 * @return %ids (array)
 */
function get_reserved_seats($username = null) {

    global $table_bookings;
    
    if($username != null) {
        if($username === ""){
            return false;
        }

        $conn = create_connection();
        $user = sanitize_string($username, $conn);

        if($user === false){
            close_connection($conn);
            return false;
        }

        $query = "SELECT id FROM $table_bookings WHERE email <> '$user'";
        $result = $conn->query($query);
        if($result === false){
            close_connection($conn);
            return false;
        }

        $ids = array();
        while($id = $result->fetch_array(MYSQLI_NUM)){
            $ids[] = $id[0];
        }

        $result->free();

        close_connection($conn);
        return $ids;
    }else{
        $conn = create_connection();

        $query = "SELECT id FROM $table_bookings";
        $result = $conn->query($query);
        if($result === false){
            close_connection($conn);
            return false;
        }
        
        $ids = array();
        while($id = $result->fetch_array(MYSQLI_NUM)){
            $ids[] = $id[0];
        }
        $result->free();
        close_connection($conn);
        return $ids;
        
    }
    
}

/**
 * Insert seats in booking table
 *
 * @param $username, %ids
 * @return bool
 */
function insert_seats($username, $ids) {

    global $table_bookings;


    if($username === ""){
        return false;
    }

    $conn = create_connection();
    $user = sanitize_string($username, $conn);

    if($user === false){
        close_connection($conn);
        return false;
    }

    $conn->autocommit(false);

    foreach ($ids as $id){

        if(!check_integer($id)){
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return false;
        }
        
        $query = "INSERT into $table_bookings(id, email) VALUES($id, '$username')";
        $result = $conn->query($query);
        if($result === false){
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return false;
        }
    }
    $conn->commit();
    $conn->autocommit(true);
    close_connection($conn);
    return true;
}

/**
 * Delete seats $ids booked by user $username
 *
 * @param $username, $ids
 * @return bool
 */
function delete_seats($username, $ids) {

    global $table_bookings;


    if($username === ""){
        return false;
    }

    $conn = create_connection();
    $user = sanitize_string($username, $conn);

    if($user === false){
        return false;
    }
    
    $conn->autocommit(false);

    foreach ($ids as $id){
        if(!check_integer($id)){
            $conn->rollback();
            $conn->autocommit(true);
            close_connection($conn);
            return false;
        }
        $query = "DELETE FROM $table_bookings WHERE id = $id AND email = '$username'";
        $result = $conn->query($query);
        if($result === false){
            $conn->rollback();
            close_connection($conn);
            return false;
        }
    }
    $conn->commit();
    close_connection($conn);
    return true;
}