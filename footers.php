<?php
/* 
 * Footers output for db toolkit
 */
if(!empty($_SESSION['adminscripts'])){
    echo "<script language=\"javascript\">\n";
    echo "jQuery(function() {\n";
    echo $_SESSION['adminscripts'];
    unset($_SESSION['adminscripts']);
    echo "});\n";
    echo "</script>\n";
}
?>