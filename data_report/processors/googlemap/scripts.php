<?php
// included in the scripts location
// use wp_enqueue_script in here


// fetch the API key
//vardump($Config['_ViewProcessors']['_process']);

wp_register_script('googleMap', 'http://maps.googleapis.com/maps/api/js?sensor=true');
wp_enqueue_script('googleMap');

?>