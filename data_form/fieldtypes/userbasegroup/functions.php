<?php

/*
functions to be used within the field type
these are the functions that can de defined in ajax and admin ajax.

function can be added as needed but there are some functions that are called when the field is used.
function call hooks:
 * [folder]_handleInput($FieldName, $SubmitedValue, $FieldType, $ElementConfig, $AllDataSubmitted);
    before Inserting all data into the database (for each field)
    return value is saved in database for that field

 * [folder]_postProcess($FieldName, $InputValue, $FieldType, $ElementConfig, $AllData, $ReturnFieldValue);
    after all data is inserted into the database (for each field)
    return void
	
function calls for viewing
	returned data is what is displayed in the report/list view
	[folder]_processValue($Value, $FieldType, $FieldName, $ElementConfig, $ElementID, $AllFieldsData)




fieldtype function naming convention
[folder]_functionname


*/

function userbasegroup_processValue($Value, $Type, $Field, $Config, $EID, $Data){
	$Det = unserialize(get_option($Value));
        return $Det['name'];
}
function userbasegroup_postProcess($Field, $Input, $FieldType, $Config, $Data, $ID){

    

}
function userbasegroup_handleInput($Field, $Input, $FieldType, $Config, $Data){
	return $Input;
}


function userbasegroup_setup($Field, $Table, $Config = false){

	$filter = '';
	if(!empty($Config['Content']['_UserGroupFilter'][$Field])){
		$filter = 'checked="checked"';
	}

	$Return = '<br />&nbsp;Apply Hard Filter: <input type="checkbox" name="Data[Content][_UserGroupFilter]['.$Field.']" value="1" '.$filter.' />';

return $Return;
}


// setup name varies according to conf.php


// Show Filters
/* adds a filter panel to the filters box. returned value is whats displayed.
function _folder_showFilter($FieldName, $FieldType, $AllData, $ElementConfig, $ElementID){
	return false;
}
*/



?>