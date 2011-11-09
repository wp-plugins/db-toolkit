<?php
//Filters query variables for the field type
		if($Type[1] == 'linked'){


                        $joinIndexSet = $joinIndex;
                        if(!empty($queryJoins[$joinIndex])){
                            $joinIndexSet = $queryJoins[$joinIndex];
                        }

			// replace primary table field with linked table field as primary table field name
			$outList = array();
			if($Config['_Linkedfields'][$Field]['Type'] != 'checkbox'){

                                if(count($Config['_Linkedfields'][$Field]['Value']) > 1){
				foreach($Config['_Linkedfields'][$Field]['Value'] as $Key=>$outValue){

                                    if(!empty($Config['_Linkedfields'][$Field]['Prefix'][$Key])){
                                        $outList[] = "'".$Config['_Linkedfields'][$Field]['Prefix'][$Key]."'";
                                    }else{
                                        $outList[] = "' '";
                                    }
					$outList[] = $joinIndexSet.'.'.$outValue;
                                    if(!empty($Config['_Linkedfields'][$Field]['Suffix'][$Key])){
                                        $outList[] = "'".$Config['_Linkedfields'][$Field]['Suffix'][$Key]."'";
                                    }else{
                                        $outList[] = "' '";
                                    }
                                }

                                    $outString = 'CONCAT('.implode(',',$outList).')';
                                }else{
                                    $outString = $joinIndexSet.'.`'.$Config['_Linkedfields'][$Field]['Value'][0].'`';
                                }
                                $querySelects[$Field] = $outString;


			}
			if(!empty($Config['_Linkedfields'][$Field]['LocalURL'])){
				if($Config['_Linkedfields'][$Field]['LocalURL'] != 'none'){
					$querySelects['_LURL_Link_'.$Field] = 'prim.'.$Config['_Linkedfields'][$Field]['LocalURL'];
				}
			}
			if(!empty($Config['_Linkedfields'][$Field]['URL'])){
				if($Config['_Linkedfields'][$Field]['URL'] != 'none'){
					$querySelects['_URL_Link_'.$Field] = $joinIndexSet.'.'.$Config['_Linkedfields'][$Field]['URL'];
				}
			}

			$querySelects['_sourceid_'.$Field] = $joinIndexSet.'.`'.$Config['_Linkedfields'][$Field]['ID'].'`';

			// left Join linked table
			if($Config['_Linkedfields'][$Field]['Type'] == 'oldcheckbox'){

				//$LinkingTable = '_linking_'.$Config['_Linkedfields'][$Field]['Table'].'_'.$Config['_Linkedfields'][$Field]['ID'];
				//$queryJoin .= " LEFT JOIN `".$LinkingTable."` AS ".$joinIndex."_linking on (prim.".$Config['_ReturnFields'][0]." = ".$joinIndex."_linking.from) \n";
				//$queryJoin .= " LEFT JOIN `".$Config['_Linkedfields'][$Field]['Table']."` AS ".$joinIndex." on (".$joinIndex."_linking.to = ".$joinIndex.".".$Config['_Linkedfields'][$Field]['ID'].") \n";
				//echo $LinkingTable;
				//die;
				//select u.name,p.product from user u
				// join user_product_link pl on pl.userid=u.id
				// join product p on p.id=pl.productid;

			}else{

                                $Primary = 'prim.`'.$Field.'`';
                                if(!empty($Config['_CloneField'][$Field]['Master'])){
                                    $Primary = 'prim.`'.$Config['_CloneField'][$Field]['Master'].'`';

                                }
                                if(empty($queryJoins[$joinIndex])){                                    
                                    $queryJoin .= " ".$Config['_Linkedfields'][$Field]['JoinType']." `".$Config['_Linkedfields'][$Field]['Table']."` AS ".$joinIndexSet." on (".$Primary." = ".$joinIndexSet.".`".$Config['_Linkedfields'][$Field]['ID']."`) \n";
                                    //$queryJoins[$Config['_Linkedfields'][$Field]['Table']] = $joinIndex;
                                    $queryJoins[$joinIndex] = $joinIndex;
                                }
                        //New Join Methods
                            //$queryJoins[$Config['_Linkedfields'][$Field]['Table']] = $joinIndex;

			}
                        
                        if(!empty($Config['_Linkedfields'][$Field]['_Filter']) && !empty($Config['_Linkedfields'][$Field]['_FilterBy'])){
                            if($WhereTag == ''){
                                    $WhereTag = " WHERE ";
                            }
                            $queryWhere[] = $joinIndexSet.".`".$Config['_Linkedfields'][$Field]['_Filter']."` = '".  mysql_real_escape_string($Config['_Linkedfields'][$Field]['_FilterBy'])."'";
                        }

		}
		if($Type[1] == 'linkedfiltered'){

                        $joinIndexSet = $joinIndex;
                        if(!empty($queryJoins[$joinIndex])){
                            $joinIndexSet = $queryJoins[$joinIndex];
                        }

			$outList = array();
			if($Config['_Linkedfilterfields'][$Field]['Type'] != 'checkbox'){

				foreach($Config['_Linkedfilterfields'][$Field]['Value'] as $Key=>$outValue){

                                    if(!empty($Config['_Linkedfilterfields'][$Field]['Prefix'][$Key])){
                                        $outList[] = "'".$Config['_Linkedfilterfields'][$Field]['Prefix'][$Key]."'";
                                    }else{
                                        $outList[] = "' '";
                                    }
					$outList[] = $joinIndex.'.'.$outValue;
                                    if(!empty($Config['_Linkedfilterfields'][$Field]['Suffix'][$Key])){
                                        $outList[] = "'".$Config['_Linkedfilterfields'][$Field]['Suffix'][$Key]."'";
                                    }else{
                                        $outList[] = "' '";
                                    }
                                }

				$outString = 'CONCAT('.implode(',',$outList).')';



                            $querySelects[$Field] = $outString;
			}
			//dump($Config['_Linkedfilterfields'][$Field]) ;
			// replace primary table field with linked table field as primary table field name
			//$querySelects[$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['Value'].' AS '.$Field;

			// Linked URL
			if(!empty($Config['_Linkedfilterfields'][$Field]['LocalURL'])){
				if($Config['_Linkedfilterfields'][$Field]['LocalURL'] != 'none'){

					$querySelects['_LURL_Link_'.$Field] = 'prim.'.$Config['_Linkedfilterfields'][$Field]['LocalURL'];
				}
			}
			if(!empty($Config['_Linkedfilterfields'][$Field]['URL'])){
				if($Config['_Linkedfilterfields'][$Field]['URL'] != 'none'){

					$querySelects['_URL_Link_'.$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['URL'];
				}
			}
			$querySelects['_sourceid_'.$Field] = $joinIndex.'.'.$Config['_Linkedfilterfields'][$Field]['ID'];

			// left Join linked table
				 //JoinType
                        if(empty($queryJoins[$joinIndex])){
                        $Primary = 'prim.'.$Field;
			$queryJoin .= " ".$Config['_Linkedfilterfields'][$Field]['JoinType']." `".$Config['_Linkedfilterfields'][$Field]['Table']."` AS ".$joinIndex." on (".$Primary." = ".$joinIndex.".".$Config['_Linkedfilterfields'][$Field]['Ref'].") \n";

                        $queryJoins[$joinIndex] = $joinIndex;

                        }


		}



		// Setup Where Clause in Query
                //clear out empty arrays
                for($o=0; $o<= count($_SESSION['reportFilters'][$EID][$Field])-1; $o++){
                    if(empty($_SESSION['reportFilters'][$EID][$Field][$o])){
                        unset($_SESSION['reportFilters'][$EID][$Field][$o]);
                    }
                }
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



                            if(empty($Config['_Linkedfields'][$Field]['TextSearch'])){
                                if(is_array($_SESSION['reportFilters'][$EID][$Field])){
                                    $queryWhere[] = 'prim.`'.$Field."` in ('".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."')";
                                }else{
                                    $queryWhere[] = "prim.`".$Field."` = '".$_SESSION['reportFilters'][$EID][$Field]."'";
                                }
                            }else{
                                //echo $_SESSION['reportFilters'][$EID][$Field];
                                $queryWhere[] = $outString." LIKE '%".$_SESSION['reportFilters'][$EID][$Field]."%' ";
                            }
			}
		}
		// apply Where Filter


?>