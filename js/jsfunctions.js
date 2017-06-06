/**
 * function reserve
 *
 * change color of img on user click
 *
 * @param el img
 */
function reserve(el){
  
    $nAvailable = $('#num_available').text();
    $nSelected = $('#num_selected').text();
    $('.message').text('');

    switch($(el).attr('class')) {
  
        case 'seats available':

            $(el).attr('class' ,'seats selected');
            $('#num_available').text(--$nAvailable);
            $('#num_selected').text(++$nSelected);
            setCookie(el.id, "selected");
            break;

        case 'seats user_reservation':

            $(el).attr('class' ,'seats release');
            setCookie(el.id, "release");
            $('.confirm').show();
            break;

        case 'seats release':
            
            if(updateCookie(el.id, "release") == "") {
                $('.confirm').hide();
                $('.release').attr('class' ,'seats user_reservation');
            }
            $(el).attr('class' ,'seats user_reservation');
            break;
        
        case 'seats selected':

            updateCookie(el.id,"selected");
            $(el).attr('class' ,'seats available');
            $('#num_available').text(++$nAvailable);
            $('#num_selected').text(--$nSelected);
            break;

        default:
            break;
    }

}

/**
 * Check a mail
 * 
 * @param email_address
 * @return bool
 */
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    return pattern.test(emailAddress);
};

/**
 * Set a cookie
 * 
 * @param val
 * @param name
 */
function setCookie(val, name) {

    var value = getCookie(name);
    if (value != null) {
        document.cookie = name + "=" + value + ", " + val;
    } else {
        document.cookie = name + "=" + val;
    }

}

/**
 * Get cookie content
 *
 * @param name
 */
function getCookie (name) {

    var value = null;
    document.cookie.split(";").forEach(function(e) {
        var cookie = e.split("=");
        if(name == cookie[0].trim()) {
            value = cookie[1].trim();
        }
    });
    return value;

}

/**
 * Update cookie value
 *
 * @param val
 * @param name
 */
function updateCookie(val, name) {

    var value = getCookie(name);
    if(value == null) {
        return;
    }

    var newValue = "";

    var ids = value.split(", ").forEach(function(e){
        if(e != val){
            if(newValue == ""){
                newValue = e;
            }else{
                newValue += ", " + e;
            }
        }
    });

    if(newValue == ""){
        document.cookie = name+"=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    }else {
        document.cookie = name+"=" + newValue;
    }

    return newValue;
}

/**
 * Delete a cookie
 *
 * @param name
 */
function cancelCookie(name){
    document.cookie = name+"=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}

$(document).ready(function(){

    /* Get number of free seats */
    $numAvailable = $('.available').length;
    $('#num_available').text($numAvailable);

    /* Click listeners */
    $(".seats").click(function(){
        reserve(this);
    });

    $("#cancel").click(function () {
        cancelCookie("release");
        $('.confirm').hide();
        $('.release').attr('class' ,'seats user_reservation');
    });

    /* Align elements in index page */
    $margin = $('#seats').css('margin-right');
    $('#message-box').css({'margin-right':$margin});
    $('#message-box').css({'margin-left':$margin});
    
    /* Check forms */
   $('#login_form').submit(function () {
       $check = true;
       if(!$('#username_l').val()) {
           $('#username_l').css('border-color', '#cc0000');
           $check = false;
       } else {
           $('#username_l').css('border-color', '#ddd');
       }
	   
       if(!$('#password_l').val()) {
           $('#password_l').css('border-color', '#cc0000');
           $check = false;
       } else {
           $('#password_l').css('border-color', '#ddd');
       }
       return $check;
    });

    $('#register_form').submit(function () {
        $check = true;
        if(!$('#username_r').val()) {
            $('#username_r').css('border-color', '#cc0000');
            $check = false;
        }else {
            $('#username_r').css('border-color', '#ddd');
        }
		
		if(!isValidEmailAddress($('#username_r').val())) {
		   $('#username_r').css('border-color', '#cc0000');
		   $('#form_error1_r').text("Please insert a valid email address.");
           $check = false;
	   }  else {
           $('#username_r').css('border-color', '#ddd');
       }

        if(!$('#password_r').val()) {
            $('#password_r').css('border-color', '#cc0000');
            $check = false;
        }else {
            $('#password_r').css('border-color', '#ddd');
        }

        if(!$('#check_password_r').val()) {
            $('#check_password_r').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#check_password_r').css('border-color', '#ddd');
        }

        if($('#password_r').val() != $('#check_password_r').val()) {
            $('#password_r').css('border-color', '#cc0000');
            $('#check_password_r').css('border-color', '#cc0000');
            $('#form_error2_r').text("Passwords do not coincide.");
            $check = false;
        }
        return $check;
    });
});