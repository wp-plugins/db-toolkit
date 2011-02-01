<?php
// Functions
function text_handleInput($Field, $Input, $FieldType, $Config, $Default){
	if($FieldType == 'presettext'){
		return $Config['Content']['_Preset'][$Field];
	}
        if($FieldType == 'password'){
            return md5($Input);
        }
	return $Input;
}
function text_processValue($Value, $Type, $Field, $Config, $EID, $Data){

switch ($Type){
		case 'textarealarge':
			return nl2br($Value);
			break;
		case 'phpcodeblock':
				return '<input type="button" id="codeRun_'.$Data['_return_'.$Config['_ReturnFields'][0]].'" value="Run Code" onclick="text_runCode(\''.$Field.'\', \''.$EID.'\', \''.$Data['_return_'.$Config['_ReturnFields'][0]].'\');" />';
			break;			
		default:
			$Pre = '';
			$Suf = '';
		
			if(!empty($Config['_Prefix'][$Field])){
				$Pre = $Config['_Prefix'][$Field];
			}
			if(!empty($Config['_Suffix'][$Field])){
				$Suf = $Config['_Suffix'][$Field];
			}
		
			if(strlen($Value) == 100 && empty($_GET['format_'.$EID])){
				$outText = '<span title="'.htmlentities($Value).'" name="'.htmlentities($Value).'">'.substr($Value, 0 ,100).'&hellip;</span>';	
			}
			
			return $Pre.$Value.$Suf;
			break;
			
		}
}

function text_postProcess($Field, $Input, $Type, $Element, $Data){
    if($Type == 'emailaddress'){

        if(!empty($Element['Content']['_forwardResult'][$Field])){


            $default_headers = array(
                'Version' => 'Version'
            );
            $version = get_file_data(WP_PLUGIN_DIR.'/db-toolkit/plugincore.php', $default_headers, 'db-toolkit-fieldtype');
            $Headers = 'From: '.$Element['Content']['_emailSender'][$Field] . "\r\n" .
                       'Reply-To: '.$Element['Content']['_emailSender'][$Field] . "\r\n" .
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
           mail($Data[$Field], $Element['Content']['_emailForwardSubject'][$Field], $Body, $Headers);
        }

    }
    //vardump($Data);
}



function text_presuff($Field, $Table, $Config = false){
	
	$PreLen = '98%';
	$Pre = '';
	$Suf = '';
	
	if(!empty($Config['Content']['_FieldLength'][$Field])){
		$PreLen = $Config['Content']['_FieldLength'][$Field];
	}
	if(!empty($Config['Content']['_Prefix'][$Field])){
		$Pre = $Config['Content']['_Prefix'][$Field];
	}
	if(!empty($Config['Content']['_Suffix'][$Field])){
		$Suf = $Config['Content']['_Suffix'][$Field];
	}

	$Return = 'Length: <input type="text" name="Data[Content][_FieldLength]['.$Field.']" value="'.$PreLen.'" class="textfield" size="5" />&nbsp;';
	$Return .= 'Prefix: <input type="text" name="Data[Content][_Prefix]['.$Field.']" value="'.$Pre.'" class="textfield" size="5" />&nbsp;';
	$Return .= ' Suffix: <input type="text" name="Data[Content][_Suffix]['.$Field.']" value="'.$Suf.'" class="textfield" size="5" />';
return $Return;
}

function text_preset($Field, $Table, $Config = false){
	
	$Preset = '';
	$PreLen = '';
	if(!empty($Config['Content']['_FieldLength'][$Field])){
		$PreLen = $Config['Content']['_Prefix'][$Field];
	}
	if(!empty($Config['Content']['_Preset'][$Field])){
		$Preset = $Config['Content']['_Preset'][$Field];
	}
	$Return = 'Length: <input type="text" name="Data[Content][_FieldLength]['.$Field.']" value="'.$PreLen.'" class="textfield" size="5" />&nbsp;';
	$Return .= '&nbsp;Preset Value: <input type="text" name="Data[Content][_Preset]['.$Field.']" value="'.$Preset.'" class="textfield" /> ';
return $Return;
}

function text_emailSetup($Field, $Table, $Config = false){


        $sel = '';
	if(!empty($Config['Content']['_forwardResult'][$Field])){
		$sel = 'checked="checekd"';
	}

        $Return .= '&nbsp;Forward result to this address: <input type="checkbox" name="Data[Content][_forwardResult]['.$Field.']" value="1" '.$sel.' /><br />';

        $Pre = 'Confirmation of Submitted data';
	if(!empty($Config['Content']['_emailForwardSubject'][$Field])){
		$Pre = $Config['Content']['_emailForwardSubject'][$Field];
	}
        $Return .= 'Email Subject: <input type="text" name="Data[Content][_emailForwardSubject]['.$Field.']" value="'.$Pre.'" class="textfield" />&nbsp;';
                

        $Pre = 'db-toolkit';       
	if(!empty($Config['Content']['_emailSender'][$Field])){
		$Pre = $Config['Content']['_emailSender'][$Field];
	}
        $Return .= 'Email Sender: <input type="text" name="Data[Content][_emailSender]['.$Field.']" value="'.$Pre.'" class="textfield" />&nbsp;';

return $Return;
}


function text_runCode($Field, $EID, $ID){
	$Element = getelement($EID);
	$Config = $Element['Content'];
	//dump($Config);
	$Res = mysql_query("SELECT ".$Field." FROM ".$Config['_main_table']." where `".$Config['_ReturnFields'][0]."` = '".$ID."' LIMIT 1;");
	$Data = mysql_fetch_assoc($Res);
	ob_start();
	eval($Data[$Field]);
	return ob_get_clean();
}
?>