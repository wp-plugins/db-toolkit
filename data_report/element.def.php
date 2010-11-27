<?php








$test = array();
$Render = true;
$TitleNotice = '';
foreach($Config['_Field'] as $Key=>$Value) {
    if($Value == 'viewitem_filter') {
        if(!empty($Config['_overRide'][$Key])) {
            if(!isset($_GET[$Config['_overRide'][$Key]])) {
                $Render = false;
                if(is_admin()) {
                    $Render = true;
                    $TitleNotice = ' [Selected Item filter on '.$Key.' looking for '.$Config['_overRide'][$Key].']';
                }
            }
        }else {
            if(!isset($_GET[$Key])) {
                $Render = false;
                if(is_admin()) {
                    $Render = true;
                    $TitleNotice = ' [Selected Item filter on '.$Key.' looking for '.$Key.']';
                }
            }
        }
    }
}
if($Render != true) {
    return;
}

$FilterLocks = get_option('filter_Lock_'.$Media['ID']);
if(!empty($FilterLocks)) {
    $_SESSION['lockedFilters'][$Media['ID']] = $_SESSION['reportFilters'][$Media['ID']];
    if(empty($_SESSION['reportFilters'][$Media['ID']])) {
        $_SESSION['reportFilters'][$Media['ID']] = $FilterLocks;
    }else {
        array_merge($_SESSION['reportFilters'][$Media['ID']], $FilterLocks);
    }
    $_SESSION['lockedFilters'][$Media['ID']] = $FilterLocks;
    //vardump($_SESSION['reportFilters'][$Media['ID']]);
}
return;
?>