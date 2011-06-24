<?php

if (!empty($_GET['APIKey'])) {
    // validate API Key
    $apikey = explode('_', $_GET['APIKey']);
    $Intrface = get_option('dt_intfc' . $apikey[0]);
    $Config = unserialize(base64_decode($Intrface['Content']));    
    $VerifyKey = md5($apikey[0].$Config['_APISeed']);
    if ($VerifyKey !== $apikey[1]) {
        api_Deny();
        die;
    }
    $Format = 'xml';
    if (empty($_GET['format'])) {
        $_GET['format'] = 'xml';
    }
    if(empty($_GET['action'])){
        $_GET['action'] = 'list';
    }
    $Page = false;
    if(!empty($_GET['page'])){
        $Page = $_GET['page'];
    }
    $Limit = false;
    if(!empty($_GET['limit'])){
        $Limit = $_GET['limit'];
    }
    if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
            default:
            case 'list':
                if (!empty($_GET['format'])) {
                    if (strtolower($_GET['format']) != 'xml' && strtolower($_GET['format']) != 'json') {
                        api_Deny();
                    }
                    //header("content-type: text/" . strtolower($_GET['format']));
                        //($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false)
                    $ReturnID = false;
                    if(!empty($_GET['_returnValue'])){                        
                        $Return = array($Config['_ReturnFields'][0]=>$_GET['_returnValue']);
                    }
                    echo dr_BuildReportGrid('dt_intfc' . $apikey[0], $Page, false, false, strtolower($_GET['format']), $Limit, $Return);
                    die;
                }
                break;
            case 'insert':
                if(!empty($_POST)){
                    $result = df_processInsert('dt_intfc' . $apikey[0], $_POST);
                    echo json_encode($result);
                }else{
                    $Return['Message'] = 'No Data submitted';
                    echo json_encode($Return);
                }
                die;
                break;
            case 'update':
                if(!empty($_POST)){
                $result = df_processupdate($_POST, 'dt_intfc' . $apikey[0]);
                echo json_encode($result);
                }else{
                    $Return['Message'] = 'No Data submitted';
                    echo json_encode($Return);
                }
                die;
                break;
        }
    } else {
        api_Deny();
    }
    api_Deny();
    //header ("content-type: text/xml");
    //echo dr_BuildReportGrid($_GET['subvars'][3], false, false, false, 'xml');
}

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
    header("HTTP/1.0 404 Not Found");
    die;
}

?>
