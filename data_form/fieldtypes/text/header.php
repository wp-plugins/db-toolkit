<link rel="stylesheet" media="screen" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_form/fieldtypes/text/css/colorpicker.css" />
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_form/fieldtypes/text/js/colorpicker.js"></script>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_form/fieldtypes/text/ckeditor/ckeditor.js"></script>
<?php
    if(!empty($_GET['interface'])){
        if(is_admin ()){
            echo '<link rel="stylesheet" media="screen" type="text/css" href="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/text/ckeditor/skins/kama/editor.css?t=B0VI4XQ" />';
        }
    }
?>