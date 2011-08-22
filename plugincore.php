<?php
/*
Plugin Name: DB-Toolkit
Plugin URI: http://dbtoolkit.digilab.co.za
Description: Plugin for building database table managers and data viewer interfaces.
Author: David Cramer
Version: 0.3.0.9 BETA
Author URI: http://dbtoolkit.digilab.co.za
*/

//initilize plugin
if(empty($_SESSION['dataform']['OutScripts'])) {
    $_SESSION['dataform']['OutScripts'] = '';
}
define('DB_TOOLKIT', plugin_dir_path(__FILE__));

//hide notices while I work through the cleanup.
error_reporting(E_ALL ^ E_NOTICE);

require_once DB_TOOLKIT.'libs/apps.php';
require_once DB_TOOLKIT.'libs/functions.php';
require_once DB_TOOLKIT.'libs/actions.php';
require_once DB_TOOLKIT.'libs/shortcodes.php';
require_once DB_TOOLKIT.'libs/apps.php';
require_once DB_TOOLKIT.'libs/process.php';
require_once DB_TOOLKIT.'libs/widgets.php';


register_activation_hook( __FILE__, 'interface_VersionCheck');

?>