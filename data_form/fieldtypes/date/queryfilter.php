<?php
//Filters query variables for the field type

		if(!empty($filterSet[$Field][0]) && !empty($filterSet[$Field][1])){
			if($WhereTag == ''){
				$WhereTag = " WHERE ";	
			}
			if($filterSet[$Field][0] == $filterSet[$Field][1]){
				$filterSet[$Field][1]++;
			}
			$queryWhere[] = "( prim.".$Field." BETWEEN '".$filterSet[$Field][0]."' AND '".$filterSet[$Field][1]."' )";
		}
	
?>