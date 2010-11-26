<?php
//Filters query variables for the field type
if(!empty($filterSet[$Field])){
	if($WhereTag == ''){
		$WhereTag = " WHERE ";	
	}

	$queryWhere[] = 'prim.'.$Field." = '".implode('\',\'', $filterSet[$Field])."'";
}
?>