<?php
/*
 * Core Actions Library - DB Toolkit
 * (C) David Cramer 2010 - 2011
 *
 */


// admin menus ! :D
add_action( 'admin_bar_menu', 'dt_adminMenus', 1000);

// Assign Actions
add_action('init', 'dt_start');
add_action('admin_init', 'dt_admin_init');
add_action('admin_menu', 'dt_menus');
add_action('wp_ajax_dt_ajaxCall', 'dt_ajaxCall');

// Add actions to front end
if(basename($_SERVER['PHP_SELF']) == 'index.php'){
    add_action('wp_head', 'dt_headers');
    add_action('wp_print_styles', 'dt_styles');
    add_action('wp_print_scripts', 'dt_scripts');
    add_action('wp_footer', 'dt_footers');
    add_action('wp_dashboard_setup', 'dt_dashboard_widgets' );
    add_action('wp_dashboard_setup', 'dt_remove_dashboard_widgets' );
}

?>
