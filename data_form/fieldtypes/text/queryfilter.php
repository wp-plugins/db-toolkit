<?php
//Filters query variables for the field type
if(!empty($_SESSION['reportFilters'][$EID][$Field])) {
    if($WhereTag == '') {
        $WhereTag = " WHERE ";
    }

if($Type[1] == 'integer'){
        $queryWhere[] = "( prim.".$Field." >= '".floatval($_SESSION['reportFilters'][$EID][$Field][0])."' AND prim.".$Field." <= '".floatval($_SESSION['reportFilters'][$EID][$Field][1])."')";
    }else{
        $filterParts = explode(';', $_SESSION['reportFilters'][$EID][$Field]);
        if(is_array($filterParts)){
            foreach($filterParts as $querySearch){
                switch ($Config['_filterMode'][$Field]){
                    case 'mid':
                        $qline = "%".$querySearch."%";
                        break;
                    case 'before':
                        $qline = "%".$querySearch;
                        break;
                    case 'after':
                        $qline = $querySearch."%";
                        break;
                    default:
                        $qline = "%".$querySearch."%";
                        break;
                }
                $queryWhere[] = "( prim.".$Field." LIKE '".$qline."' )";
            }
        }
    }
}
?>