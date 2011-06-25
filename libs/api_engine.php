<?php

/* TODO: need to add a search/filters method
 * TODO: need to add Edit, Update, Insert, Delte Methods
 *
 */
//  Key / Method / Format / ? GET Variables


    $interfaceID = trim($matches[0], '/');    
    $vars = explode($interfaceID, $_SERVER['REQUEST_URI']);
    $vars = explode('/', ltrim($vars[1], '/'));
    

    
    if(!empty($pattern['interfaces'][$interfaceID])){
        $interfaceID = $pattern['interfaces'][$interfaceID];
    }

    // validate API Key    
    $Intrface = get_option($interfaceID);
    $APIkey = $vars[0];
    $Method = $vars[1];
    $Format = $vars[2];

    $Page = 0;
    if(!empty($_GET['offset'])){
        $Page = $_GET['offset'];
    }
    $Limit = false;
    if(!empty($_GET['limit'])){
        $Limit = $_GET['limit'];
    }
    $Config = unserialize(base64_decode($Intrface['Content']));

    if($Config['_APIAuthentication'] == 'key'){
        //echo API_getCurrentUsersKey();
        if($userData = API_decodeUsersAPIKey($APIkey)){
            if($user = get_user_by('id', $userData['id'])){
                if($user->user_pass != $userData['pass_word']){
                    api_Deny();
                    exit;
                }
            }else{
                api_Deny();
                exit;
            }
        }else{
            api_Deny();
            exit;
        }

    }else{
        $VerifyKey = md5($interfaceID.$Config['_APISeed']);
        if ($VerifyKey !== $APIkey) {
            api_Deny();
            exit;
        }
    }

    if (!empty($Method)) {
        switch ($Method) {
            default:
            case 'list':
                if (!empty($Format)) {
                    if (strtolower($Format) != 'xml' && strtolower($Format) != 'json') {
                        api_Deny();
                    }
                    header("content-type: text/" . strtolower($Format));
                        //($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false)
                    $Return = false;
                    echo dr_BuildReportGrid($interfaceID, $Page, false, false, strtolower($Format), $Limit, $Return);
                    exit;
                }
                break;
            case 'fetch':
                if (!empty($Format)) {
                    if (strtolower($Format) != 'xml' && strtolower($Format) != 'json') {
                        api_Deny();
                    }
                    header("content-type: text/" . strtolower($Format));
                        //($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false)
                    $Return = false;
                    if(!empty($_GET['itemID'])){
                        $Return = array($Config['_ReturnFields'][0]=>$_GET['itemID']);
                    }
                    echo dr_BuildReportGrid($interfaceID, $Page, false, false, strtolower($Format), $Limit, $Return);
                    exit;
                }
                break;
            case 'insert':
                if(!empty($_POST)){
                    $result = df_processInsert($interfaceID, $_POST);
                    echo json_encode($result);
                }else{
                    $Return['Message'] = 'No Data submitted';
                    echo json_encode($Return);
                }
                exit;
                break;
            case 'update':
                if(!empty($_POST)){
                $result = df_processupdate($_POST, $interfaceID);
                echo json_encode($result);
                }else{
                    $Return['Message'] = 'No Data submitted';
                    echo json_encode($Return);
                }
                exit;
                break;
        }
    } else {
        api_Deny();
    }
    api_Deny();
    //header ("content-type: text/xml");
    //echo dr_BuildReportGrid($_GET['subvars'][3], false, false, false, 'xml');


function api_encode_string($str) {
    //$str = gzdeflate($str);
    $str = base64_encode($str);
    return urlencode(str_replace('=', '', $str));
}

function api_dencode_string($str) {
    $str = urldecode($str);
    $str = base64_decode($str);
    //$str = gzinflate($str);
    return str_replace('=', '', $str);
}

function api_Deny() {
    mysql_close();
    header("content-type: text/html");
    echo 'Access Denied';
    exit;
}

?>
