<?php
// Field Types Config| 
$FieldTypeTitle = 'File';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['image'] 		= array('name' => 'Image Upload'	, 'func' => 'file_imageConfig'	, 'visible' => true);
$FieldTypes['file'] 		= array('name' => 'File Upload'		, 'func' => 'null'				, 'visible' => true);
$FieldTypes['multi'] 		= array('name' => 'Multi File Upload'	, 'func' => 'null'				, 'visible' => true);
$FieldTypes['mp3'] 		= array('name' => 'MP3 File Upload'	, 'func' => 'file_playerSetup'				, 'visible' => true);


?>