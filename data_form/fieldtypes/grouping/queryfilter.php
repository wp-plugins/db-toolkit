<?php
//add to the query that is generated for the FINAL QUERY IN VIEW/REPORT/LIST
/*
variables avaiable

 $Type - array(0=>folder,1=>fieldtype);
 $Field - field name
 $Config - config for the element
 $EID - element ID
 $querySelects - fields to be returned - array(fieldnames)
 $queryWhere[] - array with string of where clause e.g 'prim.'.$Field." in ('".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."')";
 $joinIndex - uniqu index value created for joins
 $queryJoin - string .= with join string

 other important things -
 $joinIndex is used only for joined tbles - the primary table that is being returned starts with "prim.{{fieldname}}"

*/
$groupBy[$Field] = $prime;

                //if(!empty ($groupBy[$Field]) && !empty($Config['_dateFormat'][$Field])){
                //    $groupBy[$Field] = 'SUBSTRING('.$Field.',1,'.strlen(date($Config['_dateFormat'][$Field])).')';
                //}


if($Config['_GroupingFields'][$Field]['Action'] == 'concat'){
    //$querySelects[$Field] = $Config['_GroupingFields'][$Field]['Action'].'(`'.$Config['_GroupingFields'][$Field]['Field'].'`)';
    $querySelects[$Field] = 'GROUP_CONCAT(\' \', `prim`.`'.$Config['_GroupingFields'][$Field]['Field'].'`)';
}else{
    $querySelects[$Field] = strtoupper($Config['_GroupingFields'][$Field]['Action']).'('.$querySelects[$Config['_GroupingFields'][$Field]['Field']].')';
}
//dump($querySelects);
?>