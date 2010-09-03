<?php


if(!empty($_GET['APIKey'])){
	// validate API Key
	$apikey = explode('_', $_GET['APIKey']);
        $Intrface = unserialize(get_option('dt_intfc'.$apikey[0]));
        $Config = unserialize(base64_decode($Intrface['Content']));
        $VerifyKey = md5(serialize($Config));
        if($VerifyKey !== $apikey[1]){
            api_Deny();
            die;
        }
        $Format = 'xml';
        if(!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'list':
                    if(!empty($_GET['format'])) {
                        if(strtolower($_GET['format']) != 'xml' && strtolower($_GET['format']) != 'json') {
                            api_Deny();
                        }
                        header("content-type: text/".strtolower($_GET['format']));
                        echo dr_BuildReportGrid('dt_intfc'.$apikey[0], false, false, false, strtolower($_GET['format']));
                        die;
                    }
                    break;
            }
        }else {
            api_Deny();
        }
	api_Deny();
	//header ("content-type: text/xml");
	//echo dr_BuildReportGrid($_GET['subvars'][3], false, false, false, 'xml');
}



function api_encode_string($str){
	//$str = gzdeflate($str);
	$str = base64_encode($str);
	return urlencode(str_replace('=', '', $str));
}
function api_dencode_string($str){
	$str = urldecode($str);
	$str = base64_decode($str);
	//$str = gzinflate($str);
	return str_replace('=', '', $str);
}

function api_getPageID($pagename){
	$pagename = str_replace('_', ' ', $pagename);
	$Res = mysql_query("SELECT ID FROM `dais_documents` WHERE `Title` = '".mysql_real_escape_string(urldecode($pagename))."' LIMIT 1");
	if(mysql_num_rows($Res) == 0){
		return false;
	}
	$Page = mysql_fetch_assoc($Res);
	return $Page['ID'];
}

function api_Deny(){
	mysql_close();
	header("HTTP/1.0 404 Not Found");
	die;
}


?>
