<?php
// Field Types Config| 
$FieldTypeTitle = 'Text Input';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['singletext'] 	= array('name' => 'Single Text Field'	, 'func' => 'text_presuff'	, 'visible' => true);
$FieldTypes['presettext'] 	= array('name' => 'Preset Value'	, 'func' => 'text_preset'	, 'visible' => false);
$FieldTypes['password'] 	= array('name' => 'Password (md5)'	, 'func' => 'null'      	, 'visible' => true);
$FieldTypes['textarea'] 	= array('name' => 'Text Area'		, 'func' => 'null'		, 'visible' => true);
$FieldTypes['textarealarge'] 	= array('name' => 'Text Area Large'	, 'func' => 'null'		, 'visible' => true);
$FieldTypes['telephonenumber'] 	= array('name' => 'Telephone Number'	, 'func' => 'null'      	, 'visible' => true);
$FieldTypes['emailaddress'] 	= array('name' => 'Email Address'	, 'func' => 'text_emailSetup'	, 'visible' => true);
$FieldTypes['phpcodeblock'] 	= array('name' => 'PHP Code Block'	, 'func' => 'null'		, 'visible' => true);
$FieldTypes['wysiwyg'] 		= array('name' => 'Wysiwyg Editor'	, 'func' => 'text_wysiwygsetup'		, 'visible' => true);
$FieldTypes['url'] 		= array('name' => 'URL'			, 'func' => 'null'		, 'visible' => true);
//$FieldTypes['colourpicker'] 	= array('name' => 'Colour Picker'	, 'func' => 'null'		, 'visible' => true);


?>