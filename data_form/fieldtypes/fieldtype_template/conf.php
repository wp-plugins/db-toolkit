<?php

// Field Type Template
// Field Types Config


$FieldTypeTitle = 'OptGroup Title';
// is set visible
$isVisible = true;

//ready the fieldtype group array
$FieldTypes = array();

$FieldTypes['linked'] = array(
    
    'name'      => 'Linking Table',         // Name of the Field Type
    'func'      => 'linked_tableSetup',     // Setup field type function call
    'visible'   => true,                    // true: visible field type | false: hidden field
    'cloneview' => false                    // is visible in a clone field

    );


?>