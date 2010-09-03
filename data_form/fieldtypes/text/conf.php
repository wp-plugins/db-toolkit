<?php
// Field Types Config| 
$FieldTypeTitle = 'Text Input';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['singletext'] 	= array('name' => 'Single Text Field'	, 'func' => 'text_presuff'	, 'visible' => true);
$FieldTypes['presettext'] 	= array('name' => 'Preset Value'	, 'func' => 'text_preset'	, 'visible' => false);
$FieldTypes['textarea'] 	= array('name' => 'Text Area'		, 'func' => 'null'		, 'visible' => true);
$FieldTypes['textarealarge'] 	= array('name' => 'Text Area Large'	, 'func' => 'null'		, 'visible' => true);
$FieldTypes['phpcodeblock'] 	= array('name' => 'PHP Code Block'	, 'func' => 'null'		, 'visible' => true);
$FieldTypes['wysiwyg'] 		= array('name' => 'Wysiwyg Editor'	, 'func' => 'null'		, 'visible' => true);
$FieldTypes['url'] 		= array('name' => 'URL'			, 'func' => 'null'		, 'visible' => true);
$FieldTypes['colourpicker'] 	= array('name' => 'Colour Picker'	, 'func' => 'null'		, 'visible' => true);


?>