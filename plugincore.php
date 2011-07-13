<?php
/*
Plugin Name: DB-Toolkit
Plugin URI: http://dbtoolkit.digilab.co.za
Description: Plugin for building database table managers and data viewer interfaces.
Author: David Cramer
Version: 0.2.7.0
Author URI: http://dbtoolkit.digilab.co.za
*/

//initilize plugin

define('DB_TOOLKIT', plugin_dir_path(__FILE__));

require_once DB_TOOLKIT.'libs/functions.php';
require_once DB_TOOLKIT.'libs/actions.php';
require_once DB_TOOLKIT.'libs/shortcodes.php';
require_once DB_TOOLKIT.'libs/process.php';
require_once DB_TOOLKIT.'libs/apps.php';

register_activation_hook( __FILE__, 'interface_VersionCheck');

?>