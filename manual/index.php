<?php
/* 
Book: Database Interface Toolkit Manual
Chapter: Getting Started
Section: General Settings
Author: David Cramer
Version: 0.1
Publication Date: 20 October 2010
Author URL: http://www.digilab.co.za
*/
?>
<div class="wrap">
    <div id="icon-edit-pages" class="icon32"></div><h2><?php _e('Database Toolkit Documentation'); ?></h2>
    Not all topics covered just yet, but I'm adding to it as i can.
    <br class="clear" /><br />



    <?php
        $default_headers = array(
		'Book' => 'Book',
		'Chapter' => 'Chapter',
		'Section' => 'Section',
		'Author' => 'Author',
                'Version' => 'Version',
                'Publication Date' => 'Publication Date',
                'AuthorURL' => 'Author URL',
            
	);

        $plugdata = get_file_data(WP_PLUGIN_DIR.'/db-toolkit/manual/index.php', $default_headers, 'plugin');
        vardump($plugdata);


    ?>



</div>