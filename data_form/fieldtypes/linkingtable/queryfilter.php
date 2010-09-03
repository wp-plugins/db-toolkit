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

if(!empty($_SESSION['reportFilters'][$EID][$Field])){
    if($WhereTag == ''){
            $WhereTag = " WHERE ";
    }
    

    //vardump($Config['_ReturnFields'][0]);
    //vardump($Config['_CloneField'][$Field]['Master']);

    
    $queryJoin .= " LEFT JOIN `".$Config['_Linkingtablefields'][$Field]['LinkingTable']."` AS ".$joinIndex."_linking on (prim.".$Config['_CloneField'][$Field]['Master']." = ".$joinIndex."_linking.".$Config['_Linkingtablefields'][$Field]['LinkID'].") \n";
    $queryJoin .= " LEFT JOIN `".$Config['_Linkingtablefields'][$Field]['DestinationTable']."` AS ".$joinIndex."_destination on (".$joinIndex."_linking.".$Config['_Linkingtablefields'][$Field]['LinkDestID']." = ".$joinIndex."_destination.".$Config['_Linkingtablefields'][$Field]['DestID'].") \n";

    //$queryWhere[] = 'prim.'.$Config['_CloneField'][$Field]['Master']." in ('".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."')";
    $queryWhere[] = $joinIndex."_destination.".$Config['_Linkingtablefields'][$Field]['DestID']." in ('".implode('\',\'', $_SESSION['reportFilters'][$EID][$Field])."')";

    
    $groupBy[$Config['_ReturnFields'][0]] = 'prim.'.$Config['_ReturnFields'][0];
}
				//$LinkingTable = '_linking_'.$Config['_Linkedfields'][$Field]['Table'].'_'.$Config['_Linkedfields'][$Field]['ID'];
				//$queryJoin .= " LEFT JOIN `".$LinkingTable."` AS ".$joinIndex."_linking on (prim.".$Config['_ReturnFields'][0]." = ".$joinIndex."_linking.from) \n";
				//$queryJoin .= " LEFT JOIN `".$Config['_Linkedfields'][$Field]['Table']."` AS ".$joinIndex." on (".$joinIndex."_linking.to = ".$joinIndex.".".$Config['_Linkedfields'][$Field]['ID'].") \n";
				//echo $LinkingTable;
				//die;
				//select u.name,p.product from user u
				// join user_product_link pl on pl.userid=u.id
				// join product p on p.id=pl.productid;

?>