<?php
/* 
 * emailer functions
 * function naming:
 * 
 *      post_process_{{folder}}($Data)
 *      pre_process_{{folder}}($Data)
 *      config_{{folder}}($Config = false)
 *
 */

function post_process_emailer($Data, $Setup, $Config){


    if(empty($Setup['_recipient'])){
        return $Data;
    }

    $default_headers = array(
        'Version' => 'Version'
    );
    $version = get_file_data(WP_PLUGIN_DIR.'/db-toolkit/plugincore.php', $default_headers, 'db-toolkit-fieldtype');
    $Headers = 'From: '.$Setup['_recipient'] . "\r\n" .
               'Reply-To: '.$Setup['_recipient'] . "\r\n" .
               'X-Mailer: DB-Toolkit/'.$version['Version'];

    $Body = "Submitted Data from ".date("r")."\r\n";
    $Body .= "=============================\r\n";
    foreach($Data as $FieldKey=>$FieldValue){
        if(strpos($FieldKey, '_control_') === false){
            $Body .= $FieldKey.": ".$FieldValue."\r\n";
        }
    }
    $Body .= "=============================\r\n";
    $Body .= "Powered By DB-Toolkit\r\n";
    mail($Setup['_recipient'], $Setup['_subject'], $Body, $Headers);


return $Data;
}

//function pre_process_emailer($Data, $Setup, $Config){

//    return $Data;
//}

function config_emailer($ProcessID, $Table, $Config = false){

    $rval = '';
    if(!empty($Config['_FormProcessors'][$ProcessID]['_recipient'])){
        $rval = $Config['_FormProcessors'][$ProcessID]['_recipient'];
    }
    $sval = '';
    if(!empty($Config['_FormProcessors'][$ProcessID]['_subject'])){
        $sval = $Config['_FormProcessors'][$ProcessID]['_subject'];
    }

    $Return = 'Email Address: <input type="text" value="'.$rval.'" name="Data[Content][_FormProcessors]['.$ProcessID.'][_recipient]" />';
    $Return .= ' Email Subject: <input type="text" value="'.$sval.'" name="Data[Content][_FormProcessors]['.$ProcessID.'][_subject]" />';


    return $Return;
}

?>
