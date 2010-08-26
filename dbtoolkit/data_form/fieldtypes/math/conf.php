<?php
// Field Types Config| 
$FieldTypeTitle = 'Math';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['multiply'] = array('name' => 'Multiply Colums','func' => 'math_multiplysetup', 'visible' => true);
$FieldTypes['datediff'] = array('name' => 'Date Difference','func' => 'math_datesetup', 'visible' => false);

?>