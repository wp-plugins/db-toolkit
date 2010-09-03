<?php
//Filters query variables for the field type
if(!empty($_SESSION['reportFilters'][$EID][$Field])){
	if($WhereTag == ''){
		$WhereTag = " WHERE ";	
	}

	$queryWhere[] = 'prim.'.$Field." = '".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."'";
}
?>