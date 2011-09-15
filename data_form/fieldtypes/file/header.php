<script type="text/javascript" src="<?php echo WP_PLUGIN_URL.'/db-toolkit/'; ?>data_form/fieldtypes/file/js/audio-player.js"></script>
<script type="text/javascript">  
    AudioPlayer.setup("<?php echo WP_PLUGIN_URL.'/db-toolkit/'; ?>data_form/fieldtypes/file/player.swf", {  
        width: '100%',  
        initialvolume: 100,  
        transparentpagebg: "yes",  
        left: "000000",  
        lefticon: "FFFFFF"  
    });  
</script>
<?php

        wp_register_script('uploadifyJS', WP_PLUGIN_URL. '/db-toolkit/data_form/fieldtypes/file/js/jquery.uploadify.min.js');
        wp_enqueue_script("uploadifyJS");
        wp_register_style('uploadifyCSS', WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/css/uploadify.css');
        wp_enqueue_style('uploadifyCSS');

?>