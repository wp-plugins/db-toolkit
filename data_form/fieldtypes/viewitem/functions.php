<?php
// Functions
function viewitem_handleInput($Field, $Input, $FieldType, $Config, $Default){

    
    if(!empty($Config['Content']['_overRide'][$Field])){
        if(!empty($_GET[$Config['Content']['_overRide'][$Field]])){
            $_SESSION['viewitemFilter'][$Config['ID']][$Field] = $_GET[$Config['Content']['_overRide'][$Field]];
        }
    }else{
        if(!empty($_GET[$Field])){
            $_SESSION['viewitemFilter'][$Config['ID']][$Field] = $_GET[$Field];
        }
    }


    if(!empty($_SESSION['viewitemFilter'][$Config['ID']][$Field])){
        return $_SESSION['viewitemFilter'][$Config['ID']][$Field];
    }
    return $Default[$Field];
}

function viewitem_setup($Field, $Table, $Config = false){


	$Pre = '';


	if(!empty($Config['Content']['_overRide'][$Field])){
		$Pre = $Config['Content']['_overRide'][$Field];
	}

	$Return = 'GET Var Overide: <input type="text" name="Data[Content][_overRide]['.$Field.']" value="'.$Pre.'" class="textfield" size="5" />&nbsp;';
return $Return;

}

?>