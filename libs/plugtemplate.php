<?php
/*
Plugin Name: {{appName}}
Plugin URI: {{appURI}}
Description: {{appDescription}}
Author: {{appAuthor}}
Version: {{appVersion}}
Author URI: {{authorURI}}
*/



/* Please do not touch anything bellow this line. */
if(!in_array( 'db-toolkit/plugincore.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))){
    add_action('admin_init', 'adminInit{{appID}}');
    function adminInit{{appID}}(){
        add_action('admin_notices', 'dependancyNotice{{appID}}');
    }
    function dependancyNotice{{appID}}() {

        if ( !current_user_can( 'manage_options' ) || !empty($_GET['tab']))
                return;
        ?>
        <div id="message" class="error">
            <h3>Contact Book requires DB-Toolkit to be installed and Activated. <a href="plugin-install.php?tab=search&type=term&s=DB-Toolkit" class="button" id="wpcom-connect">Learn More</a></h3>
        </div>
        <?php
    }

}else{
    register_activation_hook(__FILE__, 'install{{appID}}');
    register_deactivation_hook(__FILE__, 'uninstall{{appID}}');
    function install{{appID}}(){
        $data = "{{exportData}}";

        $installData = unserialize(base64_decode(urldecode($data)));
        $apps = get_option('dt_int_Apps');
        //vardump($apps);
        $appTitle = sanitize_title($installData['application']);
        update_option('_'.$appTitle.'_app', $installData['appInfo']);
        //add to apps list;
        $apps[$appTitle]['state'] = $installData['appInfo']['state'];
        $apps[$appTitle]['name'] = $installData['appInfo']['name'];
        update_option('dt_int_Apps', $apps);
        //die;

        foreach($installData['interfaces'] as $interface=>$cfg){
            update_option($interface, $cfg);
        }
        //vardump($installData['application']);
        //die;

    }
    function uninstall{{appID}}(){
        echo 'removed';
    }
}

?>