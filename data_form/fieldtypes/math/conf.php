<?php
// Field Types Config| 
$FieldTypeTitle = 'Math';
$isVisible = true;
$FieldTypes = array();

$FieldTypes['multiply'] = array('name' => 'Field Calculator','func' => 'math_multiplysetup', 'visible' => true);
$FieldTypes['datediff'] = array('name' => 'Date Difference','func' => 'math_datesetup', 'visible' => false);
$FieldTypes['percentage'] = array('name' => 'Percentage','func' => 'null', 'visible' => false);
$FieldTypes['mysqlfunc'] = array('name' => 'Mysql Function','func' => 'math_mysqlfunc', 'visible' => false);



?>