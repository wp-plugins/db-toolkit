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
if(file_exists('styles/themes/'.$themeDir.'/toolbar.css')){
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