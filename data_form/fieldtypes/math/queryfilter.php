<?php

//dump($querySelects[$Field]);
//echo $Field;
// Clear out cloned field
$FieldSelect = $querySelects[$Field];
if(!empty($Config['_CloneField'][$Field])){
    $FieldSelect = str_replace(' AS '.$Field, '', $FieldSelect);
}
$type = $Config['_mathMysqlFunc'][$Field];
//dump($_SERVER['fieldMath'][$Field]);
if($Config['_mathMysqlFunc'][$Field] == 'sumtotal'){
    $type = 'count';
    $_SERVER['fieldMath'][$Field] = 0;
}

$querySelects[$Field] = $type.$FieldSelect;
?>