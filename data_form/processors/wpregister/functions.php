<?php
/* 
 * wp register functions
 * function naming:
 * 
 *      post_process_{{folder}}($Data)
 *      pre_process_{{folder}}($Data)
 *      config_{{folder}}($Config = false)
 *
 */


 

function pre_process_wpregister($Data, $Setup, $Config){


    //notification mail email and name change
    $_SESSION['wpregister_SetupVars'] = $Setup;

    add_filter('wp_mail_from','wpregister_email_from');
    add_filter('wp_mail_from_name','wpregister_email_from_name');

        
    $pass = wp_generate_password( 12, false);
    if(!empty($Data[$Setup['_wpregister']['pass']])){
        $pass = $Data[$Setup['_wpregister']['pass']];
    }

    $Data[$Setup['_wpregister']['userid']] = wp_create_user( $Data[$Setup['_wpregister']['user']], $pass, $Data[$Setup['_wpregister']['email']] );

    if ( is_wp_error($Data[$Setup['_wpregister']['userid']]) ){
        
        $Message = $Data[$Setup['_wpregister']['userid']]->get_error_message();       
        $Data['__fail__'] = true;
        $Data['__error__'] = $Message;
        return $Data;
    }

    // Send Out Notifications
    /*
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";
        
	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
        */
    
    //Set up the Password change nag.
    update_user_option($Data[$Setup['_wpregister']['userid']] , 'default_password_nag', true, true );
    //Set up the Admin Bar off.
    update_user_meta($Data[$Setup['_wpregister']['userid']] , 'show_admin_bar_front', 'false');
    
    // Login User
    wp_set_auth_cookie($Data[$Setup['_wpregister']['userid']]);

    //clear out the password
    $Data[$Setup['_wpregister']['pass']] = '';

    //clear out temp
    unset($_SESSION['wpregister_SetupVars']);

return $Data;
}

function config_wpregister($ProcessID, $Table, $Config = false){
    
    global $wpdb;

    $user = wp_get_current_user();

    $Fields = $wpdb->get_results( "SHOW COLUMNS FROM `".$Table."`", ARRAY_N);
    
    $Return .= '<h2>Login Fields</h2>';

    $userfield = '<option value=""></option>';
    $pass = '<option value=""></option>';
    $userid = '<option value=""></option>';
    $email = '<option value=""></option>';

    foreach($Fields as $FieldName){
        
        $check = '';
        if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['user'])){
            if($Config['_FormProcessors'][$ProcessID]['_wpregister']['user'] == $FieldName[0]){
                $check = 'selected="selected"';
            }
        }
        $userfield .= "<option value=\"".$FieldName[0]."\" ".$check.">".$FieldName[0]."</option>";

        $check = '';
        if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['userid'])){
            if($Config['_FormProcessors'][$ProcessID]['_wpregister']['userid'] == $FieldName[0]){
                $check = 'selected="selected"';
            }
        }
        $userid .= "<option value=\"".$FieldName[0]."\" ".$check.">".$FieldName[0]."</option>";

        $check = '';
        if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['pass'])){
            if($Config['_FormProcessors'][$ProcessID]['_wpregister']['pass'] == $FieldName[0]){
                $check = 'selected="selected"';
            }
        }
        $pass .= "<option value=\"".$FieldName[0]."\" ".$check.">".$FieldName[0]."</option>";

        $check = '';
        if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['email'])){
            if($Config['_FormProcessors'][$ProcessID]['_wpregister']['email'] == $FieldName[0]){
                $check = 'selected="selected"';
            }
        }
        $email .= "<option value=\"".$FieldName[0]."\" ".$check.">".$FieldName[0]."</option>";

    }    

    $senderEmail = $user->data->user_email;
    if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['emailSender'])){
        $senderEmail = $Config['_FormProcessors'][$ProcessID]['_wpregister']['emailSender'];
    }
    $senderName = $user->data->display_name;
    if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['emailName'])){
        $senderName = $Config['_FormProcessors'][$ProcessID]['_wpregister']['emailName'];
    }
    $Return .= '<p>Notification Email Address: <input type="text" name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][emailSender]" value="'.$senderEmail.'" /><br /><span class="description">The email address the registration notification comes from. This is the email containint the new users login and password.</span></p>';
    $Return .= '<p>Notification Email Name: <input type="text" name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][emailName]" value="'.$senderName.'" /><br /><span class="description">The Senders name of the notification.</span></p>';
    
    

    $Return .= '<p>UserID Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][userid]">';
        $Return .= $userid;
    $Return .= '</select><br /> <span class="description">This is the field that captures the new user ID value. It links this table to the new user.</span></p>';

    $Return .= '<p>Username Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][user]">';
        $Return .= $userfield;
    $Return .= '</select> <br /><span class="description">This is the field that captures the username.</span></p>';

    $Return .= '<p>Email Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][email]">';
        $Return .= $email;
    $Return .= '</select> <br /><span class="description">This is the field that captures the email.</span></p>';

    $Return .= '<p>Password Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][pass]">';
        $Return .= $pass;
    $Return .= '</select> <br /><span class="description">This is the field that captures the password. It is only used for the new user and is truncated before going into this table, but is saved for the WordPress User table. Leave it blank for auto generating password.</span></p>';

    return $Return;
}


        function wpregister_email_from($mail) {           
            return $_SESSION['wpregister_SetupVars']['_wpregister']['emailSender'];
        }

        function wpregister_email_from_name($name) {
            return $_SESSION['wpregister_SetupVars']['_wpregister']['emailName'];
        }

?>
