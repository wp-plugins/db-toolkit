<?php

// linked adds :
// queryJoins - for the tables joined
// querySelect - for the additional fields
//
//


    // Add the table to the joins

    $queryJoin[] = $Config['_Linkedfields'][$Field]['JoinType'].' `'.$Config['_Linkedfields'][$Field]['Table'].'` AS `'.$joinIndex.'` ON ('.$prime.' = `'.$joinIndex.'`.`'.$Config['_Linkedfields'][$Field]['ID'].'`)';


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

    // Put down the filters

?>