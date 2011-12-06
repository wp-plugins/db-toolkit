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
    
    $Data[$Setup['_wpregister']['userid']] = wp_create_user( $Data[$Setup['_wpregister']['user']], $Data[$Setup['_wpregister']['pass']], $Data[$Setup['_wpregister']['email']] );

    //clear out the password
    $Data[$Setup['_wpregister']['pass']] = '';



    if ( is_wp_error($Data[$Setup['_wpregister']['userid']]) ){
        $Data['__fail__'] = true;
        $Data['__error__'] = $Data[$Setup['_wpregister']['userid']]->get_error_message();
        return $Data;
    }

return $Data;
}

function config_wpregister($ProcessID, $Table, $Config = false){
    
    global $wpdb;

    $Fields = $wpdb->get_results( "SHOW COLUMNS FROM ".$Table, ARRAY_N);
    
    $Return .= '<h2>Login Fields</h2>';

    $user = '';
    $pass = '';
    
    foreach($Fields as $FieldName){
        
        $check = '';
        if(!empty($Config['_FormProcessors'][$ProcessID]['_wpregister']['user'])){
            if($Config['_FormProcessors'][$ProcessID]['_wpregister']['user'] == $FieldName[0]){
                $check = 'selected="selected"';
            }
        }
        $user .= "<option value=\"".$FieldName[0]."\" ".$check.">".$FieldName[0]."</option>";

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


    

    $Return .= '<p>UserID Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][userid]">';
        $Return .= $userid;
    $Return .= '</select><br /> <span class="description">This is the field that captured the new user ID value. It links this table to the new user.</span></p>';

    $Return .= '<p>Username Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][user]">';
        $Return .= $user;
    $Return .= '</select> <br /><span class="description">This is the field that captures the username.</span></p>';

    $Return .= '<p>Email Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][email]">';
        $Return .= $email;
    $Return .= '</select> <br /><span class="description">This is the field that captures the email..</span></p>';

    $Return .= '<p>Password Field:<select name="Data[Content][_FormProcessors]['.$ProcessID.'][_wpregister][pass]">';
        $Return .= $pass;
    $Return .= '</select> <br /><span class="description">This is the field that captures the password. It is only used for the new user and is truncated before going into this table, but is saved for the WordPress User table.</span></p>';

    return $Return;
}

?>
