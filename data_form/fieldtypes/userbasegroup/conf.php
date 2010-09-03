<?php

// Field Type Template
// Field Types Config


$FieldTypeTitle = 'Access Control';
// is set visible
$isVisible = true;

//ready the fieldtype group array
$FieldTypes = array();

$FieldTypes['usergroupfilter'] = array(
    
    'name'      => 'User Group Filter',         // Name of the Field Type
    'func'      => 'userbasegroup_setup',     // Setup field type function call
    'visible'   => true,                    // true: visible field type | false: hidden field
    'cloneview' => false                    // is visible in a clone field

    );


?>