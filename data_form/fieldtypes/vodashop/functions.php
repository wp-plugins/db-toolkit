<?php
// Functions
function vodashop_handleInput($Field, $Input, $FieldType, $Config){
	return $Input;
}
function vodashop_processValue($Value, $Type, $Field, $Config, $EID, $Data){
	if($Type == 'scanner'){
//		dump($Data);
		if(empty($_SESSION['num_get_vscanner'])){
			$_SESSION['num_get_vscanner'] = 1;
		}
		$Pre = '';
		$val = '';
		if(!empty($Data['IMEISerial'])){
			$Pre = 'disabled="disabled"';
			$val = $Data['IMEISerial'];
		}else{
			$_SESSION['num_get_vscanner']++;
		}
		return '<input type="text" name="vodashopScanner[]" id="scan_'.$_SESSION['num_get_vscanner'].'" class="vodashopScanner" rel="'.$Data['_return_ID'].'" value="'.$val.'" ref="'.$_SESSION['num_get_vscanner'].'" '.$Pre.' />';
	}
        if($Type == 'allocatebutton'){
            $Title = 'Allocate';
            if($Value == '1'){
                $Title = 'Un-allocate';
            }
            //dump($Data);
            //dump();

            return '<input type="button" value="'.$Title.'" class="ui-button ui-state-default ui-corner-all ui-state-hover" onclick="dr_BuildUpDateForm(\''.$EID.'\',\''.$Data['_return_'.$Config['_ReturnFields'][0]].'\');" />';
        }

	return $Type;//$Value;
}

function vodashop_syncMSISDN($Number, $Process){
	//return 'syncing....'.$Number;
	if($Process == 'insert'){
		$preQuery = mysql_query("SELECT ID FROM `Clients` WHERE `Msisdn` = '".$Number."';");
		if(mysql_num_rows($preQuery) >= 1){
			$Output['status'] = 0;
			$Output['result'] = $Number.' Already Exists in Database';
			return $Output;
		}
	}
	if(strlen($Number) == 10 && substr($Number,0,1) == '0'){
		$Number = substr($Number,1).'000';	
	}
	include(WP_PLUGIN_DIR."/dbtoolkit/data_form/fieldtypes/vodashop/nusoap/nusoap.php");
	$client = new nusoap_client('http://retailweb.vodacomsp.co.za/finlayson.cfc?wsdl');
	$err = $client->getError();
	$args = array(array($Number), 'V/tOQ5hnyI32/+IcsHGpMA==');
	$result = $client->call('upgrdate', $args);
	//print_r($client);
	$Ouptut = array();
	if ($client->fault) {
		$Output['status'] = 0;
		$Output['result'] = $result['faultstring'];
	} else {
		// Check for errors
		$err = $client->getError();
		if ($err) {
			$Output['status'] = 0;
			$Output['result'] = $err;
		} else {
			// Display the result
			//$Part = explode(',',$result[0]);
			$Part = str_getcsv($result[0]);
			//ob_start();
			//dump($result);
			//return ob_get_clean();
			$Output['status'] = 1;
			$Output['MSISDN'] = $Part[0];
			$Output['upgradedate'] = $Part[1];
			if($Part[4] == 'BUSINESS'){
			// contract name Name
				$Output['name'] = ucwords(strtolower($Part[2]));
			}else{
				$Name = explode(' ', $Part[2]);
				$Output['title'] = '';
				if(count($Name) >= 2){
					//$Output['title'] = ucwords(strtolower($Name[0]));
					$Output['name'] = ucwords(strtolower($Name[0]));
					$Output['surname'] = ucwords(strtolower($Name[count($Name)-1]));
				}else{
					$Output['name'] = ucwords(strtolower($Name[0]));
					$Output['surname'] = ucwords(strtolower($Name[1]));
				}
			}
			$Output['phoneuser'] = ucwords(strtolower($Part[3]));
			$Output['type'] = $Part[4];
			$Output['referalnumber'] = $Part[5];
			$Output['idnumber'] = $Part[7];
			
			$byear = substr($Part[7],0, 2);
			$bmonth = substr($Part[7],2, 2);
			$bday = substr($Part[7],4, 2);
			$Output['birthday'] = '19'.$byear.'-'.$bmonth.'-'.$bday;
			$Output['dealerid'] = $Part[8];
			$Output['result'] = $result[0];
			$Output['package'] = $Part[9];
			$Output['spend'] = $Part[10];
			$Output['lastsyncdate'] = date('Y-m-d');
			$Output['lastsynctime'] = date('H:i:s');
			//$Output['debug'] = $result;
		}
	}
	//ob_start();
	//dump($result);
	//return ob_get_clean();
return $Output;
}

function vodacom_smsupgradedate($D){
	return date('Y-m-d', strtotime($D.' + 31 days'));
}

function vodacom_checkNumber($num){
	
	return num;

}

function vodashop_scanned($Code, $ID){

	$Res = mysql_query("SELECT ID FROM `OrderItems` WHERE `IMEISerial` = '".$Code."'");
	if(mysql_num_rows($Res) >= 1){
		$Out['message'] = 'Item already been allocated';
		$Out['status'] = 1;
		return $Out;
	}
	

	$Res = mysql_query("UPDATE `OrderItems` SET `IMEISerial` = '".$Code."', `Status` = 14, `DateAllocated` = NOW() WHERE ID = '".$ID."' LIMIT 1;");
	$out['message'] = $Code;
	$out['status'] = 0;
	return $out;
}

?>