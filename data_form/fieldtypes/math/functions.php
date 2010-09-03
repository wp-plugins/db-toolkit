<?php

function math_processValue($Value, $Type, $Field, $Config, $EID, $Data){
	//echo $Value;
	if($Type == 'multiply'){
	return $Value;
	}
	if($Type == 'datediff'){
		return math_timeDuration($Data[$Config['_dateDiff'][$Field]['A']], $Data[$Config['_dateDiff'][$Field]['B']]);
	}
}

function math_postProcess($Field, $Input, $FieldType, $Config, $Data, $ID){
}
function math_handleInput($Field, $Input, $FieldType, $Config, $Data){
	if($FieldType == 'multiply'){
		$Value = number_format($Data[$Config['Content']['_multiply'][$Field]['A']]*$Data[$Config['Content']['_multiply'][$Field]['B']], 2);
		return $Value;
	}
	if($FieldType == 'datediff'){
		return math_timeDuration($Data[$Config['Content']['_dateDiff'][$Field]['A']], $Data[$Config['Content']['_dateDiff'][$Field]['B']]);
	}

}

// Args = $Field, $table, ElementConfig 
function math_multiplysetup($Field, $Table, $ElementConfig = false){
		$Return .= math_loadfields($Table, $Field);
	return $Return;
}
function math_datesetup($Field, $Table, $ElementConfig = false){
		$Return .= math_loaddates($Table, $Field);
	return $Return;
}

function math_loadfields($Table, $Field, $Defaults = false){
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
	$IReturn .= '</select> X ';
	$VReturn .= '<select name="Data[Content][_multiply]['.$Field.'][B]" id="Ref_'.$Table.'">';
		$VReturn .= $ValueReturn;
	$VReturn .= '</select></div>';
	

return $IReturn.$VReturn;
}
function math_loaddates($Table, $Field, $Defaults = false){
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
    if(empty($date1)) {
        return "No Start Date";
    }
    if(empty($date2)) {
        return "No End Date";
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

?>