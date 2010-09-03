<?php
//Filters query variables for the field type

		if(!empty($_SESSION['reportFilters'][$EID][$Field][0]) && !empty($_SESSION['reportFilters'][$EID][$Field][1])){
			if($WhereTag == ''){
				$WhereTag = " WHERE ";	
			}
			if($_SESSION['reportFilters'][$EID][$Field][0] == $_SESSION['reportFilters'][$EID][$Field][1]){
				$_SESSION['reportFilters'][$EID][$Field][1]++;
			}
			$queryWhere[] = "( prim.".$Field." BETWEEN '".$_SESSION['reportFilters'][$EID][$Field][0]."' AND '".$_SESSION['reportFilters'][$EID][$Field][1]."' )";
		}
	
?>