<?php
// Functions
function viewitem_handleInput($Field, $Input, $FieldType, $Config, $Default){

    if(!empty($Default[$Field])){
        return $Default[$Field];
    }
    return $Input;
}

function viewitem_setup($Field, $Table, $Config = false){


	$Pre = '';


	if(!empty($Config['Content']['_overRide'][$Field])){
		$Pre = $Config['Content']['_overRide'][$Field];
	}

	$Return = 'GET Var Overide: <input type="text" name="Data[Content][_overRide]['.$Field.']" value="'.$Pre.'" class="textfield" size="5" />&nbsp;<br />';
	$Return .= '<span class="description">NOTE: In order to capture the Selected ID in a new entry, the field needs to be present in the form!</span>';

return $Return;

}

?>