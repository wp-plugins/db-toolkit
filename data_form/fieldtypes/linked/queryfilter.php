<?php

// linked adds :
// queryJoins - for the tables joined
// querySelect - for the additional fields
//
//


    // Add the table to the joins
    
    if(empty($queryJoin[$Config['_Linkedfields'][$Field]['Table']][$prime][$Config['_Linkedfields'][$Field]['ID']])){
        $queryJoin[$Config['_Linkedfields'][$Field]['Table']][$prime][$Config['_Linkedfields'][$Field]['ID']][$joinIndex] = $Config['_Linkedfields'][$Field]['JoinType'].' `'.$Config['_Linkedfields'][$Field]['Table'].'` AS `'.$joinIndex.'` ON ('.$prime.' = `'.$joinIndex.'`.`'.$Config['_Linkedfields'][$Field]['ID'].'`)';
    }else{
        foreach($queryJoin[$Config['_Linkedfields'][$Field]['Table']][$prime][$Config['_Linkedfields'][$Field]['ID']] as $joinIndex=>$joinStr){
            // yes i know messy, but at least it resets the join index ok!
        }
    }


    // Add the selects
    if(!empty($querySelects[$Field])){
        if(count($Config['_Linkedfields'][$Field]['Value']) > 1){
            // concat fields
            $querySelects[$Field] = 'CONCAT(`'.$joinIndex.'`.`'.implode('`,\' \',`'.$joinIndex.'`.`', $Config['_Linkedfields'][$Field]['Value']).'`)';
        }else{
            $querySelects[$Field] = '`'.$joinIndex.'`.`'.$Config['_Linkedfields'][$Field]['Value'][0].'`';
        }
    }

    // Add WHERE
    //vardump($Config['_Linkedfields'][$Field]);
    if(!empty($Config['_Linkedfields'][$Field]['_Filter']) && !empty($Config['_Linkedfields'][$Field]['_FilterBy'])){
        $queryWhere['AND'][] = '`'.$joinIndex.'`.`'.$Config['_Linkedfields'][$Field]['_Filter'].'` = \''.$Config['_Linkedfields'][$Field]['_FilterBy'].'\'';
    }
    if(!empty($_SESSION['reportFilters'][$EID][$Field])){
        unset($_SESSION['reportFilters'][$EID][$Field][array_search('Select All', $_SESSION['reportFilters'][$EID][$Field])]);
        
        $queryWhere['AND'][] = '`'.$joinIndex.'`.`'.$Config['_Linkedfields'][$Field]['ID'].'` IN ('.implode(',', $_SESSION['reportFilters'][$EID][$Field]).') ';
        //die;
    }
    if(!empty($_SESSION['reportFilters'][$EID]['_keywords'])){
        foreach(explode(',', $_SESSION['reportFilters'][$EID]['_keywords']) as $keyWord){
            $queryWhere['OR'][] = $querySelects[$Field]." LIKE '%".trim($keyWord)."%'";
        }
    }

    // Put down the filters

?>