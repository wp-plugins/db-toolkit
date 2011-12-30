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

function onoff_processValue($Value, $Type, $Field, $Config, $EID, $Data){
	
	$Sel = '';
	if($Value == 1){
		$Sel = 'checked="checked"';
	}
	$Return = '<input type="checkbox" id="onOff_'.$EID.'_'.$Data['_return_'.$Config['_ReturnFields'][0]].'" name="onOff['.$EID.']['.$Field.']" value="1" '.$Sel.' onchange="onoff_toggleInline(\''.$EID.'\', \''.$Field.'\', \''.$Data['_return_'.$Config['_ReturnFields'][0]].'\');" />';
        
	
	//$Return .= $EID;
	
	return $Return;
}

function onoff_handleInput($Field, $Input, $FieldType, $Config, $Data){
	return $Input;
}


function onoff_setValue($id, $field, $eid){


	$Value = 0;
	$element = getelement($eid);
	$Config = $element['Content'];    
	$datestamp = '';
	if(!empty($Config['_onoff'][$field]['datestamp'])){
		$datestamp = ", `".$Config['_onoff'][$field]['datestampField']."` = NOW()";
	}

        $pre = mysql_query("SELECT `".$field."` FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$id."' LIMIT 1;");
        $data = mysql_fetch_assoc($pre);

        $Value = 1;
        if($data[$field] === '1'){
            $Value = 0;
        }

	$query = "UPDATE `".$Config['_main_table']."` SET `".$field."` = '".mysql_real_escape_string($Value)."' ".$datestamp." WHERE `".$Config['_ReturnFields'][0]."` = '".$id."' LIMIT 1;";
	//echo $query;
	//die;
	mysql_query($query);
	//dump($Config);
	return;
}


// setup the fieldtype
function onoff_timestamp($Field, $Table, $Config = false){
    //vardump($Config['Content']['_onoff'][$Field]);
        if(empty($Table)){
            $Table = $Config['Content']['_main_table'];
        }
	$result = mysql_query("SHOW COLUMNS FROM `".$Table."`");
	$Sel = '';
	if(!empty($Config['Content']['_onoff'][$Field]['datestamp'])){
		$Sel = 'checked="checked"';	
	}
	$Return = '<div style="padding:3px;" class="list_row3">Enable Date Stamping: <input type="checkbox" name="Data[Content][_onoff]['.$Field.'][datestamp]" id="_autodatestamp_enable" '.$Sel.' /></div>';
	$Return .= '<div style="padding:3px;" class="list_row3">Date Stamp Field: <select name="Data[Content][_onoff]['.$Field.'][datestampField]" id="_autodatestamp_Field">';
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)){
			$Sel = '';
			if(!empty($Config['Content']['_onoff'][$Field]['datestampField'])){
				if($Config['Content']['_onoff'][$Field]['datestampField'] == $row['Field']){
					$Sel = 'selected="selected"';	
				}
			}
			$Return .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
		}
	}
	$Return .= '</select>';
	$Return .= '</div>';
	return $Return;
	
}


// Show Filters
/* adds a filter panel to the filters box. returned value is whats displayed.*/


function onoff_showFilter($Field, $Type, $Default, $Config, $EID){
	$FieldTitle = df_parseCamelCase($Field);
	if(!empty($Config['_FieldTitle'][$Field])){
		$FieldTitle = $Config['_FieldTitle'][$Field];
	}

	$Return .= '<div style="float:left;padding:2px;" '.$Class.'><h2>'.$FieldTitle.'</h2>';
	$Out .= '<select class="filterBoxes" id="filter_'.$Field.'" name="reportFilter['.$EID.']['.$Field.'][]" multiple="multiple" size="1">';
	$Out .= '<option value="">Select Both</option>';
		$Sel = '';
		if(!empty($Default[$Field])){
			if(in_array('unchecked', $Default[$Field])){
				$Sel = 'selected="selected"';
			}
		}
	$Out .= '<option value="unchecked" '.$Sel.' >Unchecked</option>';
		$Sel = '';
		if(!empty($Default[$Field])){
			if(in_array('checked', $Default[$Field])){
				$Sel = 'selected="selected"';
			}
		}
	$Out .= '<option value="checked" '.$Sel.' >Checked</option>';

	$Return .= $Out.'</select>&nbsp;&nbsp;&nbsp;</div>';
     $_SESSION['dataform']['OutScripts'] .= "
        jQuery(\"#filter_".$Field."\").multiselect();
    ";

	
return $Return;
}




?>