<?php

function math_processValue($Value, $Type, $Field, $Config, $EID, $Data){
	//echo $Value;

        if($Type == 'percentage'){

            //return $Field;
            //return $Value;
            //echo $Field;
            //dump($Data);

            $ARes = mysql_query($_SESSION['queries'][$EID]);            
            while($postData = mysql_fetch_assoc($ARes)){
                $Pre[] = $postData[$Field];
                //dump($postData);
            }
            $Return = round(($Value/array_sum($Pre))*100, 2);
            unset($Pre);
            return $Return;
            //return $_SESSION['queries'][$EID];


        }

	if($Type == 'multiply'){
            //dump($Data);
            //dump($Config['_multiply']);
            return $Data[$Config['_multiply'][$Field]['A']]*$Data[$Config['_multiply'][$Field]['B']];
            //return $Value;
	}
	if($Type == 'datediff'){
		return math_timeDuration($Data[$Config['_dateDiff'][$Field]['A']], $Data[$Config['_dateDiff'][$Field]['B']]);
	}
        if($Config['_mathMysqlFunc'][$Field] == 'sumtotal'){
            $_SERVER['fieldMath'][$Field] = $_SERVER['fieldMath'][$Field]+$Value;
            return $_SERVER['fieldMath'][$Field];
        }
        return $Value;
}

function math_postProcess($Field, $Input, $FieldType, $Config, $Data, $ID){
}
function math_handleInput($Field, $Input, $FieldType, $Config, $Data){
	if($FieldType == 'multiply'){
		$Value = number_format($Data[$Config['Content']['_multiply'][$Field]['A']]*$Data[$Config['Content']['_multiply'][$Field]['B']], 2);
		//return 'pi';//$Value;
	}
	if($FieldType == 'datediff'){
		return math_timeDuration($Data[$Config['Content']['_dateDiff'][$Field]['A']], $Data[$Config['Content']['_dateDiff'][$Field]['B']]);
	}

}

// Args = $Field, $table, ElementConfig 
function math_multiplysetup($Field, $Table, $ElementConfig = false){
		$Return .= math_loadfields($Table, $Field, false, $ElementConfig);
	return $Return;
}
function math_datesetup($Field, $Table, $ElementConfig = false, $Default = false){    
		$Return .= math_loaddates($Table, $Field, $ElementConfig['Content']['_dateDiff']);
	return $Return;
}

function math_loadfields($Table, $Field, $Defaults = false, $Media = false){
    
        $Config = $Media['Content'];
        //dump($Config);
	$result = mysql_query("SHOW COLUMNS FROM ".$Table);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Defaults[$Field]['Value'])){
				if($Defaults[$Field]['Value'][0] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$ValueReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
			$Sel = '';
			if(!empty($Defaults[$Field]['ID'])){
				if($Defaults[$Field]['ID'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$IDReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	$IReturn = '<div class="list_row1" style="padding:3px;"><select name="Data[Content][_multiply]['.$Field.'][A]" id="Ref_'.$Table.'">';
		$IReturn .= $IDReturn;

                if(!empty($Config)) {
                    if(!empty ($Config['_CloneField'])) {
                        $IReturn .= '<optgroup label="Cloned Fields">';
                        foreach ($Config['_CloneField'] as $FieldKey=>$Array) {
                            $Sel = '';
                            if($Defaults[$Field]['ID'] == $FieldKey) {
                                $Sel = 'selected="selected"';
                            }
                            if($FieldKey != $Field){
                                $IReturn .= '<option value="'.$FieldKey.'" '.$Sel.'>'.$Config['_FieldTitle'][$FieldKey].'</option>';
                            }
                        }

                    }
                }
                
	$IReturn .= '</select> X ';
	$VReturn .= '<select name="Data[Content][_multiply]['.$Field.'][B]" id="Ref_'.$Table.'">';
		$VReturn .= $ValueReturn;

            if(!empty($Config)) {
                if(!empty ($Config['_CloneField'])) {
                    $VReturn .= '<optgroup label="Cloned Fields">';
                    foreach ($Config['_CloneField'] as $FieldKey=>$Array) {
                        $Sel = '';
                        if($Defaults[$Field]['ID'] == $FieldKey) {
                            $Sel = 'selected="selected"';
                        }
                        $VReturn .= '<option value="'.$FieldKey.'" '.$Sel.'>'.$Config['_FieldTitle'][$FieldKey].'</option>';
                    }

                }
            }
            
	$VReturn .= '</select></div>';
	

return $IReturn.$VReturn;
}
function math_loaddates($Table, $Field, $Defaults = false){
    
	$result = mysql_query("SHOW COLUMNS FROM ".$Table);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Defaults[$Field]['A'])){
				if($Defaults[$Field]['A'] == $row['Field']){                                    
					$Sel = 'selected="selected"';
				}
			}
			$ValueReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
			$Sel = '';
			if(!empty($Defaults[$Field]['B'])){
				if($Defaults[$Field]['B'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$IDReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	$IReturn = '<div class="list_row1" style="padding:3px;">Start Date<select name="Data[Content][_dateDiff]['.$Field.'][A]" id="Ref_'.$Table.'">';
		$IReturn .= $IDReturn;
	$IReturn .= '</select> End Date ';
	$VReturn .= '<select name="Data[Content][_dateDiff]['.$Field.'][B]" id="Ref_'.$Table.'">';
		$VReturn .= $ValueReturn;
	$VReturn .= '</select></div>';
	

return $IReturn.$VReturn;
}

function math_timeDuration($date1, $date2)
{


    if(empty($date1) || $date1 == '0000-00-00 00:00:00') {
        return ".--";
    }
    if(empty($date2) || $date2 == '0000-00-00 00:00:00') {
        return "--.";
    }
   
    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");
   
    $now             = strtotime($date2);
    $unix_date         = strtotime($date1);
   
       // check validity of date
    if(empty($unix_date)) {   
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {   
        $difference     = $now - $unix_date;
        $tense         = "ago";
       
    } else {
        $difference     = $unix_date - $now;
        $tense         = "from now";
    }
   
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
   
    $difference = round($difference);
   
    if($difference != 1) {
        $periods[$j].= "s";
    }
    return $difference.' '.$periods[$j];
}


function math_mysqlfunc($Field, $Table, $ElementConfig = false){



        $Return = 'Function: <select name="Data[Content][_mathMysqlFunc]['.$Field.']" id="" >';
	$sel = '';
        if($ElementConfig['Content']['_mathMysqlFunc'][$Field] == 'sum'){
		$sel = 'selected="selected"';
	}
        $Return .= '<option value="sum" '.$sel.'>sum()</option>';
        $sel = '';
        if($ElementConfig['Content']['_mathMysqlFunc'][$Field] == 'sumtotal'){
		$sel = 'selected="selected"';
	}
        $Return .= '<option value="sumtotal" '.$sel.'>count() incremental</option>';
        $sel = '';
        if($ElementConfig['Content']['_mathMysqlFunc'][$Field] == 'count'){
		$sel = 'selected="selected"';
	}
        $Return .= '<option value="count" '.$sel.'>count()</option>';
        $sel = '';
        if($ElementConfig['Content']['_mathMysqlFunc'][$Field] == 'avg'){
		$sel = 'selected="selected"';
	}
        $Return .= '<option value="avg" '.$sel.'>avg()</option>';
        $Return .= '</select>';

        
return $Return;

}
?>