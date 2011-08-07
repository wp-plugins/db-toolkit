<?php
// Field Types Config| 
$FieldTypeTitle = 'Auto Values';
$isVisible = true;
$FieldTypes = array();

//$FieldTypes['UUID'] 	= array('name' => 'UUID' , 'func' => 'null', 'visible' => false);
$FieldTypes['session'] 	= array('name' => 'Session Value' , 'func' => 'session_value', 'visible' => false);
$FieldTypes['userbase'] 	= array('name' => 'User ID' , 'func' => 'session_config', 'visible' => false);
$FieldTypes['ipaddress'] 	= array('name' => 'IP Address' , 'func' => 'null', 'visible' => false);
$FieldTypes['autovalue'] 	= array('name' => 'Preset Autovalue' , 'func' => 'auto_preset', 'visible' => false);
$FieldTypes['GETValue'] 	= array('name' => 'GET Auto Value' , 'func' => 'auto_get', 'visible' => false);


?>