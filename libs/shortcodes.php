<?php
/*
 * Core Shortcode Library - DB Toolkit
 * (C) David Cramer 2010 - 2011
 *
 */

// Enable shortcode
add_shortcode("interface", "dt_renderInterface");
add_shortcode("visibility", "dt_publicReg");


// enable shortcode in widgets
add_filter('widget_text', 'do_shortcode');



?>
