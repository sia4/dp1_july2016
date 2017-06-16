<?php

require_once ('functions/config.php');
require_once ('functions/functions.php');
require_once ('functions/db_functions.php');


if(logged()) {
    $user_reservation = get_user_seats(get_username()); //Seats the logged user has already booked
    $reserved = get_reserved_seats(get_username()); //Seats reserved by other users
    $release = get_seats_to_release(); //Seats the logged user want to release
} else{
    $user_reservation = array();
    $reserved = get_reserved_seats(); //Seats reserved by all users
}
$selected = get_selected_seats(); //Seats selected by the user in order to book them

if(isset($_GET['action']) && $_GET['action'] == 'reserve') {

    if(empty($selected)) {
        $_SESSION['message'] = "<p class='message error'>Please select the seats you want to reserved.</p>";
    } else {
        setcookie("continue_operation", "true");
        if (!logged()) {
            header("Location: login.php");
            exit;
        } else {
            if (insert_seats(get_username(), $selected)) {
                $_SESSION['message'] = "<p class='message success'>Your seats are now booked.</p>";
                $user_reservation = array_merge($user_reservation, $selected);
            } else {
                $_SESSION['message'] = "<p class='message error'>An error occurred while performing the operation.</p>";
            }
            $selected = array();
            delete_cookies("selected");
        }
    }
    header("Location: index.php");
    exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'release' && !empty($release)){

    if(!logged()){
        $_SESSION['message'] = "<p class='message error'>Session expired. Log in again to perform the operation.</p>";
    }else{
        if(delete_seats(get_username(), $release)){
            $_SESSION['message'] = "<p class='message success'>Your reservation has been cancelled.</p>";
            $user_reservation = get_user_seats(get_username());
        } else{
            $_SESSION['message'] = "<p class='message error'>An error occurred while performing the operation.</p>";
        }
        $release = array();
        delete_cookies("release");
    }
    header("Location: index.php");
    exit;
}


require_once('header.php');
?>

<div id="container">
    <div id="wrapper">
    <table id="seats">
        <tbody>
            <?php
            for($i = 0; $i < $rows; $i++){
                echo '<tr>';
                for($j = 0; $j < $columns; $j++){
                    $id = $i*$columns + $j + 1;
                    if(in_array($id, $reserved)){
                        echo '<td><img id="'.$id.'" class="seats reserved" src="images/armchair.png"/></td>';
                    }elseif (logged() && in_array($id, $user_reservation)){
                        echo '<td><img id="'.$id.'" class="seats user_reservation" src="images/armchair.png"/></td>';
                    }elseif (in_array($id, $selected)){
                        echo '<td><img id="'.$id.'" class="seats selected" src="images/armchair.png"/></td>';
                    }else{
                        echo '<td><img id="'.$id.'" class="seats available" src="images/armchair.png"/></td>';
                    }
                }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <div id="message-box">
        <a href="index.php?action=reserve"><input type="button" id="reserve" name="reserve" value="Reserve" /></a>
        <?php
        if(isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            $_SESSION['message'] = '';
        }
        ?>
    
        <p class="confirm">Are you sure you want to cancel your reservation?</p>
        <a href="index.php?action=release" class="confirm"><input type="button" id="release" name="release" class="confirm" value="Confirm"/></a>
        <input type="button" id="cancel" name="cancel" class="confirm" value="Cancel"/>

        <h2>STATS</h2>
        <table id="stats">
            <tbody>
                <tr>
                    <td>
                        <label for="tot_num">Total number of seats:</label>
                    </td>
                    <td>
                        <span id="tot_num"><?php echo $columns*$rows?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="num_reserved" style="color: #cc0000">Reserved seats:</label>
                    </td>
                    <td>
                        <span id="num_reserved"><?php echo count($reserved)?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="num_available" style="color: #008000">Available seats:</label>
                    </td>
                    <td>
                        <span id="num_available"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="num_selected" style="color: #ffcc00">Selected seats:</label>
                    </td>
                    <td>
                        <span id="num_selected"><?php echo count($selected)?></span>
                    </td>
                </tr>
                <?php
                if(logged()) {
                    ?>
                    <tr>
                        <td>
                            <label for="num_user_reservation" style="color: #ff8000">Your seats:</label>
                        </td>
                        <td>
                            <span id="num_user_reservation"><?php echo count($user_reservation) ?></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    </div>
</div>
<?php
include('footer.php');
?>