<?php


if(is_admin()){
/*
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/css/table.css" />
<?php
*/

}else{

}
	if(!empty($_POST['reportFilter'])){
		foreach($_POST['reportFilter'] as $EID=>$FilterSet){
			if(!empty($_POST['reportFilter']['ClearFilters'])){
				unset($_SESSION['reportFilters'][$EID]);
			}else{
				unset($_SESSION['reportFilters'][$EID]);
				$_SESSION['reportFilters'][$EID] = $FilterSet;
			}
		}
		if(!empty($_POST['reportFilter']['reportFilterLock'])){
			dr_lockFilters($_POST['reportFilter']['reportFilterLock']);
			//dump($_POST['reportFilter']);
			//dump($EID);
		}
		if(!empty($_POST['reportFilter']['reportFilterUnlock'])){
			dr_unlockFilters($_POST['reportFilter']['reportFilterUnlock']);
			//dr_lockFilters($_POST['reportFilter']['reportFilterLock']);
			//dump($_POST['reportFilter']);
			//dump($EID);
		}
	}
?>