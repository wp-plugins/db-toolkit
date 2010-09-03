<?php
$themeDir = get_bloginfo('template_directory');
$themeURL = get_bloginfo('template_url');

	if(file_exists('styles/themes/'.$themeDir.'/form.css')){
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $themeURL; ?>/form.css" />
<?php
}else{
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_PLUGIN_URL; ?>/dbtoolkit/data_form/css/form.css" />
<?php
}
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_PLUGIN_URL; ?>/dbtoolkit/data_form/css/ui.timepickr.css" />
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/dbtoolkit/data_form/js/ui.timepickr.js"></script>
<?php
	$Types = loadFolderContents(WP_PLUGIN_DIR.'/dbtoolkit/data_form/fieldtypes');
	foreach($Types[0] as $Type){
		if(file_exists(WP_PLUGIN_DIR.'/dbtoolkit/data_form/fieldtypes/'.$Type[1].'/header.php')){
			include(WP_PLUGIN_DIR.'/dbtoolkit/data_form/fieldtypes/'.$Type[1].'/header.php');
		}
	}

?>