<?php
$themeDir = get_theme_root().'/'.get_template();
$themeURL = get_bloginfo('template_url');

if(is_admin()){
/*
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/css/table.css" />
<?php
*/

}else{
    if(file_exists($themeDir.'/table.css')) {
        ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $themeURL; ?>/table.css" />
        <?php
    }
}
?>
<?php
if(file_exists('styles/themes/'.themeDir.'/toolbar.css')){
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $themeURL; ?>/toolbar.css" />
<?php
}else{
?>
<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/css/style.css" type="text/css" />
<?php
}
?>
<?php
        if($_GET['page'] == 'Database_Toolkit'){
            $EID = $_GET['renderinterface'];
        }else{
            $EID = $_GET['page'];
        }
        if(isset($_SESSION['reportFilters'][$EID]['__lastSet'])){
            unset($_SESSION['reportFilters'][$EID]);
            unset($_SESSION['lockedFilters'][$EID]);            
        }        
	if(!empty($_POST['reportFilter'])){
            
		foreach($_POST['reportFilter'] as $EID=>$FilterSet){                        
                        if(is_array($FilterSet)){                            
                            $FilterSet = core_cleanArray($FilterSet);
                            //vardump($FilterSet);
                            if(!empty($_POST['reportFilter']['ClearFilters'])){
                                    unset($_SESSION['reportFilters'][$EID]);
                            }else{
                                    unset($_SESSION['reportFilters'][$EID]);
                                    $_SESSION['reportFilters'][$EID] = $FilterSet;
                            }
                            if(!empty($_POST['reportFilter']['reportFilterLock'])){
                                dr_lockFilters($_POST['reportFilter']['reportFilterLock'], $FilterSet);
                            }
                            if(!empty($_POST['reportFilter']['reportFilterUnlock'])){
                                dr_unlockFilters($_POST['reportFilter']['reportFilterUnlock']);
                            }

                        }
		}                		
	}
        

        //echo $EID;
        //vardump($_SESSION['reportFilters'][$EID]);
        if(!empty($_GET['ftab'])){
            if($_GET['page'] == 'Database_Toolkit'){
                $EID = $_GET['renderinterface'];
            }else{
                $EID = $_GET['page'];
            }
            //if(!empty($_SESSION['lockedFilters'][$EID])){
            //    unset($_SESSION['lockedFilters'][$EID]);
            //}
            
            $fset = get_option('dt_set_'.$EID);
            foreach($fset as $setKey=>$filterSet){
                $fkey = array_search($_GET['ftab'], $filterSet);
                if(!empty($fkey)){
                    $filterSet['Filters']['__lastSet'] = $setKey;
                    if(!empty($_SESSION['reportFilters'][$EID])){
                        $_SESSION['reportFilters'][$EID] = array_merge($_SESSION['reportFilters'][$EID], $filterSet['Filters']);
                    }else{
                        $_SESSION['reportFilters'][$EID] = $filterSet['Filters'];
                    }
                    $_SESSION['lockedFilters'][$EID] = $filterSet['Filters'];
                    break;
                }

            }

        }

?>