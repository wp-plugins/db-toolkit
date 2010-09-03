<?php
// Field Types Config 
$FieldTypeTitle = 'Linking Table';
// is set visible
$isVisible = true;

//ready the fieldtype group array
$FieldTypes = array();

// FieldType ID				  Type name					Setup function for insert/setup	    is field a visible or hidden field				
$FieldTypes['linkingtable'] = array('name' => 'Linking Table','func' => 'linkingtable_linkingTable', 'visible' => true, 'cloneview' => true);


?>