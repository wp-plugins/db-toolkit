<?php

/*
functions to be used within the field type
these are the functions that can de defined in ajax and admin ajax.

function can be added as needed but there are some functions that are called when the field is used.
function call hooks:
	before Inserting all data into the database (for each field)
	[folder]_handleInput($FieldName, $SubmitedValue, $FieldType, $ElementConfig, $AllDataSubmitted);
	return value is saved in database for that field
	
	after all data is inserted into the database (for each field)
	[folder]_postProcess($FieldName, $InputValue, $FieldType, $ElementConfig, $AllData, $ReturnFieldValue);
	return void
	
function calls for viewing
	returned data is what is displayed in the report/list view
	[folder]_processValue($Value, $FieldType, $FieldName, $ElementConfig, $ElementID, $AllFieldsData)




fieldtype function naming convention
[folder]_functionname


*/

function linkingtable_processValue($Value, $Type, $Field, $Config, $EID, $Data){



    //$Res = mysql_query("SELECT ".$Config['_Linkingtablefields'][$Field]['DestValue']." FROM `".$Config['_Linkingtablefields'][$Field]['DestinationTable']."` ORDER BY `".$Config['_Linkingtablefields'][$Field]['DestValue']."` ASC;");
    $Query = "SELECT dest.".$Config['_Linkingtablefields'][$Field]['DestValue']." FROM ".$Config['_Linkingtablefields'][$Field]['LinkingTable']." AS prim
           LEFT JOIN ".$Config['_Linkingtablefields'][$Field]['DestinationTable']." AS dest ON (prim.".$Config['_Linkingtablefields'][$Field]['LinkDestID']." = dest.".$Config['_Linkingtablefields'][$Field]['DestID'].")
           WHERE prim.".$Config['_Linkingtablefields'][$Field]['LinkID']." = '".$Value."';
            ";
    
    $Res = mysql_query($Query);

    while($row = mysql_fetch_assoc($Res)){
        $List[] = $row[$Config['_Linkingtablefields'][$Field]['DestValue']];
    }
    if(!empty($List)){
        return implode(', ', $List);//'pingo';//$Value;
    }

}

function linkingtable_postProcess($Field, $Input, $FieldType, $Config, $Data, $ID){

    //vardump($Input);
    //vardump($Data);
    //vardump($Config['Content']['_CloneField'][$Field]);

    
    if(!is_array($Input)){
        $List = unserialize($Input);
    }else{
        $List = $Input;
    }
    //vardump($Config['Content']['_Linkingtablefields'][$Field]);
    mysql_query("DELETE FROM `".$Config['Content']['_Linkingtablefields'][$Field]['LinkingTable']."` WHERE `".$Config['Content']['_Linkingtablefields'][$Field]['LinkID']."` = '".$Data[$Config['Content']['_CloneField'][$Field]['Master']]."'; ");


    $Query = "INSERT INTO `".$Config['Content']['_Linkingtablefields'][$Field]['LinkingTable']."` (`".$Config['Content']['_Linkingtablefields'][$Field]['LinkID']."`, `".$Config['Content']['_Linkingtablefields'][$Field]['LinkDestID']."`) VALUES ";

    //vardump($Data);

    if(!empty($List)){
        foreach($List as $Link){
            $Links[] = "('".$Data[$Config['Content']['_CloneField'][$Field]['Master']]."', '".$Link."')";
        }

        $Values = implode(',', $Links);
        
        mysql_query($Query.$Values);
    }
return $Input;

}

function linkingtable_handleInput($Field, $Input, $FieldType, $Config, $Data){
    return $Input;
}


function linkingtable_linkingTable($Field, $Table, $ElementConfig = false){
    global $wpdb;

    $Return = '';
    if(substr($Field,0,2) != '__'){
        $Return .= '<div class="highlight">Thie Field Type is best used on a Cloned Field</div>';
    }


	$Data = $wpdb->get_results( "SHOW TABLES", ARRAY_N);


	$Default = '';
	if(is_array($ElementConfig)){
		if(!empty($ElementConfig['Content']['_Linkingtablefields'][$Field]['LinkingTable'])){
			$DefaultLinking = $ElementConfig['Content']['_Linkingtablefields'][$Field]['LinkingTable'];
		}
		if(!empty($ElementConfig['Content']['_Linkingtablefields'][$Field]['DestinationTable'])){
			$DefaultDestination = $ElementConfig['Content']['_Linkingtablefields'][$Field]['DestinationTable'];
		}
	}
        $Linking = '';
        $Destination = '';

	foreach($Data as $Tables){
		//vardump($Tables);
		$Value = $Tables[0];
		$Sel = '';
		if($DefaultLinking == $Value){
			$Sel = 'selected="selected"';
		}
		//if(substr($Value, 0, 5) != 'dais_'){
			$List[] = $Value;
			$Linking .= '<option value="'.$Value.'" '.$Sel.'>'.$Value.'</option>';

                $Sel = '';
		if($DefaultDestination == $Value){
			$Sel = 'selected="selected"';
		}
		//if(substr($Value, 0, 5) != 'dais_'){
			$List[] = $Value;
			$Destination .= '<option value="'.$Value.'" '.$Sel.'>'.$Value.'</option>';
		//}

	}
        $LinkCFG = uniqid('lt_');
        $Return = '<div>Linking Table: <select name="Data[Content][_Linkingtablefields]['.$Field.'][LinkingTable]" id="linkedField_'.$Field.'" onchange="linkingtable_loadfields(this.value, \''.$Field.'\', \''.$Table.'\', \''.$LinkCFG.'\');">';
	$Return .= '<option value=""></option>';
        $Return .= $Linking;
        $Return .= '</select></div>';
        $Return .= '<div id="'.$LinkCFG.'">';
            if(!empty($ElementConfig['Content']['_Linkingtablefields'])){
                $Return .= linkingtable_loadfields($ElementConfig['Content']['_Linkingtablefields'][$Field]['LinkingTable'], $Field, $Table, $ElementConfig['Content']['_Linkingtablefields']);
            }
        $Return .= '</div>';

        $DestCFG = uniqid('dt_');
        $Return .= '<div>Destination Table: <select name="Data[Content][_Linkingtablefields]['.$Field.'][DestinationTable]" id="linkedField_'.$Field.'" onchange="linkingtable_loaddestfields(this.value, \''.$Field.'\', \''.$Table.'\', \''.$DestCFG.'\');">';
	$Return .= '<option value=""></option>';
        $Return .= $Destination;
        $Return .= '</select></div>';
        $Return .= '<div id="'.$DestCFG.'">';
            if(!empty($ElementConfig['Content']['_Linkingtablefields'])){
                $Return .= linkingtable_loaddestfields($ElementConfig['Content']['_Linkingtablefields'][$Field]['DestinationTable'], $Field, $Table, $ElementConfig['Content']['_Linkingtablefields']);
            }
        $Return .= '</div>';

        $Return .= '<span id="linkingConfig_'.$Field.'"></span>';

    return $Return;

}




function linkingtable_loadfields($Table, $Field, $MainTable, $Defaults = false){
        global $wpdb;
        // vardump($Defaults[$Field]);
	$result = mysql_query("SHOW COLUMNS FROM ".$Table);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Defaults[$Field]['LinkDestID'])){
				if($Defaults[$Field]['LinkDestID'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$ValueReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
			$Sel = '';
			if(!empty($Defaults[$Field]['LinkID'])){
				if($Defaults[$Field]['LinkID'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$IDReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';

		}
	}
	$IReturn = '<div class="list_row1" style="padding:3px;">Linking ID Field: <select name="Data[Content][_Linkingtablefields]['.$Field.'][LinkID]" id="Ref_'.$Table.'">';
		$IReturn .= $IDReturn;
	$IReturn .= '</select></div>';

	$VReturn .= '<div class="list_row1" style="padding:3px;">Linking Destination ID Field:<select name="Data[Content][_Linkingtablefields]['.$Field.'][LinkDestID]" id="Ref_'.$Table.'">';
		$VReturn .= $ValueReturn;
	$VReturn .= '</select></div>';
	//$URLField = '<div class="list_row1" style="padding:3px;">Linked URL:<select name="Data[Content][_Linkedfields]['.$Field.'][URL]" id="url_'.$Table.'" onchange="jQuery(\'#localurl_'.$Table.'\').val(\'\');">';
	//	$URLField .= '<option>none</option>';
	//	$URLField .= $URL;
	//$URLField .= '</select></div>';
	// Local URL Field
	$result = mysql_query("SHOW COLUMNS FROM ".$MainTable);
	if (@mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Defaults[$Field]['LocalURL'])){
				if($Defaults[$Field]['LocalURL'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$LURL .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	//$LocalURLField = '<div class="list_row1" style="padding:3px;">Local URL:<select name="Data[Content][_Linkedfields]['.$Field.'][LocalURL]" id="localurl_'.$Table.'" onchange="jQuery(\'#url_'.$Table.'\').val(\'\');">';
	//	$LocalURLField .= '<option>none</option>';
	//	$LocalURLField .= $LURL;
	//$LocalURLField .= '</select></div>';


return $IReturn.$VReturn;
}

function linkingtable_loaddestfields($Table, $Field, $MainTable, $Defaults = false){
        global $wpdb;
        $IReturn = '';
        $valReturn = '';
	$result = mysql_query("SHOW COLUMNS FROM ".$Table);
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){

			$Sel = '';
			if(!empty($Defaults[$Field]['DestID'])){
				if($Defaults[$Field]['DestID'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$IDReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
			$Sel = '';
			if(!empty($Defaults[$Field]['DestValue'])){
				if($Defaults[$Field]['DestValue'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$valReturn .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	$IReturn = '<div class="list_row1" style="padding:3px;">Destination ID Field: <select name="Data[Content][_Linkingtablefields]['.$Field.'][DestID]" id="Ref_'.$Table.'">';
		$IReturn .= $IDReturn;
	$IReturn .= '</select></div>';
	$IReturn .= '<div class="list_row1" style="padding:3px;">Destination Value Field: <select name="Data[Content][_Linkingtablefields]['.$Field.'][DestValue]" id="Ref_'.$Table.'">';
		$IReturn .= $valReturn;
	$IReturn .= '</select></div>';

	//$URLField = '<div class="list_row1" style="padding:3px;">Linked URL:<select name="Data[Content][_Linkedfields]['.$Field.'][URL]" id="url_'.$Table.'" onchange="jQuery(\'#localurl_'.$Table.'\').val(\'\');">';
	//	$URLField .= '<option>none</option>';
	//	$URLField .= $URL;
	//$URLField .= '</select></div>';
	// Local URL Field
	$result = mysql_query("SHOW COLUMNS FROM ".$MainTable);
	if (@mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Defaults[$Field]['LocalURL'])){
				if($Defaults[$Field]['LocalURL'] == $row['Field']){
					$Sel = 'selected="selected"';
				}
			}
			$LURL .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	//$LocalURLField = '<div class="list_row1" style="padding:3px;">Local URL:<select name="Data[Content][_Linkedfields]['.$Field.'][LocalURL]" id="localurl_'.$Table.'" onchange="jQuery(\'#url_'.$Table.'\').val(\'\');">';
	//	$LocalURLField .= '<option>none</option>';
	//	$LocalURLField .= $LURL;
	//$LocalURLField .= '</select></div>';


return $IReturn;
}




// Show Filters
/* adds a filter panel to the filters box. returned value is whats displayed.
function _folder_showFilter($FieldName, $FieldType, $AllData, $ElementConfig, $ElementID){
	return false;
}
*/



function linkingtable_showFilter($Field, $Type, $Default, $Config, $EID) {
    //vardump($Default);
    $FieldTitle = '';
    if(!empty($Config['_FieldTitle'][$Field])) {
        $FieldTitle = $Config['_FieldTitle'][$Field];
    }

    //dump($Config['_Linkedfields']);
    //if(empty($Config['_Linkedfields'][$Field]['SingleSelect'])) {
        $Multiple = 'multiple="multiple" size="1" class="filterBoxes"';
    //}
    
    $SelectID = $EID.'-'.$Field;//urlencode('reportFilter['.$EID.']['.$Field.']');
    $Res = mysql_query("SELECT ".$Config['_Linkingtablefields'][$Field]['DestID'].", ".$Config['_Linkingtablefields'][$Field]['DestValue']." FROM `".$Config['_Linkingtablefields'][$Field]['DestinationTable']."` ORDER BY `".$Config['_Linkingtablefields'][$Field]['DestValue']."` ASC;");
    $Return .= '<div style="float:left;padding:2px;" '.$Class.'><strong><strong>'.$FieldTitle.'</strong></strong><br /><select id="'.$SelectID.'" name="reportFilter['.$EID.']['.$Field.'][]" '.$Multiple.'>';
    $Return .= '<option></option>';
    while($row = mysql_fetch_assoc($Res)) {
        
        
        $Sel = '';
        if(!empty($Default[$Field])) {
            if(in_array($row[$Config['_Linkingtablefields'][$Field]['DestID']], $Default[$Field])) {
                $Sel = 'selected="selected"';
            }
        }
        $Return .= '<option value="'.$row[$Config['_Linkingtablefields'][$Field]['DestID']].'" '.$Sel.'>'.$row[$Config['_Linkingtablefields'][$Field]['DestValue']].'</option>';
    }
    $Return .= '</select>&nbsp;&nbsp;&nbsp;</div>';

     $_SESSION['dataform']['OutScripts'] .= "
        $(\"#".$SelectID."\").dropdownchecklist({ firstItemChecksAll: true});
    ";

    return $Return;
}


?>