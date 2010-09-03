<?php
// Field Types Config| 
$FieldTypeTitle = 'Join';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['linked'] = array('name' => 'Join Table','func' => 'linked_tableSetup', 'visible' => true);
$FieldTypes['linkedfiltered'] = array('name' => 'Filtered Join Table','func' => 'linked_tablefilteredSetup', 'visible' => true);


?>