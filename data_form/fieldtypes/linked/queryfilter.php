<?php
//Filters query variables for the field type
		if($Type[1] == 'linked'){
			// replace primary table field with linked table field as primary table field name
			$outList = array();
			if($Config['_Linkedfields'][$Field]['Type'] != 'checkbox'){
				foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
					$outList[] = $joinIndex.'.'.$outValue;
				}
			//if(count($outList) >= 2){
				$outString = 'CONCAT('.implode(',\' \',',$outList).')';
			//}else{
			//	$outString = $outList[0];
			//}
				$querySelects[$Field] = $outString.' AS '.$Field;
			}
			if(!empty($Config['_Linkedfields'][$Field]['LocalURL'])){
				if($Config['_Linkedfields'][$Field]['LocalURL'] != 'none'){
					$querySelects['_LURL_Link_'.$Field] = 'prim.'.$Config['_Linkedfields'][$Field]['LocalURL'].' AS LURL_LINK_'.$Field;
				}
			}
			if(!empty($Config['_Linkedfields'][$Field]['URL'])){
				if($Config['_Linkedfields'][$Field]['URL'] != 'none'){
					$querySelects['_URL_Link_'.$Field] = $joinIndex.'.'.$Config['_Linkedfields'][$Field]['URL'].' AS LURL_LINK_'.$Field;
				}
			}
			
			$querySelects['_sourceid_'.$Field] = $joinIndex.'.'.$Config['_Linkedfields'][$Field]['ID'].' AS _sourceid_'.$Field;
			
			// left Join linked table
			if($Config['_Linkedfields'][$Field]['Type'] == 'checkbox'){

				//$LinkingTable = '_linking_'.$Config['_Linkedfields'][$Field]['Table'].'_'.$Config['_Linkedfields'][$Field]['ID'];
				//$queryJoin .= " LEFT JOIN `".$LinkingTable."` AS ".$joinIndex."_linking on (prim.".$Config['_ReturnFields'][0]." = ".$joinIndex."_linking.from) \n";
				//$queryJoin .= " LEFT JOIN `".$Config['_Linkedfields'][$Field]['Table']."` AS ".$joinIndex." on (".$joinIndex."_linking.to = ".$joinIndex.".".$Config['_Linkedfields'][$Field]['ID'].") \n";
				//echo $LinkingTable;
				//die;
				//select u.name,p.product from user u
				// join user_product_link pl on pl.userid=u.id
				// join product p on p.id=pl.productid;
				
			}else{
				$queryJoin .= " ".$Config['_Linkedfields'][$Field]['JoinType']." `".$Config['_Linkedfields'][$Field]['Table']."` AS ".$joinIndex." on (prim.".$Field." = ".$joinIndex.".".$Config['_Linkedfields'][$Field]['ID'].") \n";
			}
		}
		if($Type[1] == 'linkedfiltered'){
			$outList = array();
			if($Config['_Linkedfilterfields'][$Field]['Type'] != 'checkbox'){
				foreach($Config['_Linkedfilterfields'][$Field]['Value'] as $outValue){
					$outList[] = $joinIndex.'.'.$outValue;
				}
			//if(count($outList) >= 2){
				$outString = 'CONCAT('.implode(',\' \',',$outList).')';
			//}else{
			//	$outString = $outList[0];
			//}
				$querySelects[$Field] = $outString.' AS '.$Field;
			}
			//dump($Config['_Linkedfilterfields'][$Field]) ;
			// replace primary table field with linked table field as primary table field name
			//$querySelects[$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['Value'].' AS '.$Field;

			// Linked URL
			if(!empty($Config['_Linkedfilterfields'][$Field]['LocalURL'])){
				if($Config['_Linkedfilterfields'][$Field]['LocalURL'] != 'none'){

					$querySelects['_LURL_Link_'.$Field] = 'prim.'.$Config['_Linkedfilterfields'][$Field]['LocalURL'].' AS URL_LINK_'.$Field;
				}
			}
			if(!empty($Config['_Linkedfilterfields'][$Field]['URL'])){
				if($Config['_Linkedfilterfields'][$Field]['URL'] != 'none'){

					$querySelects['_URL_Link_'.$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['URL'].' AS URL_LINK_'.$Field;
				}
			}
			$querySelects['_sourceid_'.$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['ID'].' AS _sourceid_'.$Field;

			// left Join linked table
				 //JoinType
			$queryJoin .= " ".$Config['_Linkedfilterfields'][$Field]['JoinType']." `".$Config['_Linkedfilterfields'][$Field]['Table']."` AS ".$joinIndex." on (prim.".$Field." = ".$joinIndex.".".$Config['_Linkedfilterfields'][$Field]['Ref'].") \n";

		}
		// Setup Where Clause in Query
		if(!empty($_SESSION['reportFilters'][$EID][$Field])){
			if($WhereTag == ''){
				$WhereTag = " WHERE ";	
			}
			if($Config['_Linkedfields'][$Field]['Type'] == 'checkbox'){
			$LinkingTable = '_linking_'.$Config['_main_table'].'_'.$Config['_Linkedfields'][$Field]['Table'];
			//$queryJoin .= " LEFT JOIN `".$LinkingTable."` AS ".$joinIndex." on (prim.".$Field." = ".$joinIndex.".".$Config['_Linkedfilterfields'][$Field]['Ref'].") \n";
				$prewhere = array();
				foreach($_SESSION['reportFilters'][$EID][$Field] as $like){
					$prewhere[] = 'prim.'.$Field." LIKE '%|".$like."|%' ";
				}
				$queryWhere[] = '('.implode(' OR ', $prewhere).')';
			}else{
				$queryWhere[] = 'prim.'.$Field." in ('".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."')";
			}
		}
		// apply Where Filter


?>