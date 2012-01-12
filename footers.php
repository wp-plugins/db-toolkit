<?php
/* 
 * Footers output for db toolkit
 */

$_SESSION['dataform']['OutScripts'] .= "
    jQuery(\".formular select, .formular input:checkbox, .formular input:radio, .formular input:file\").uniform();
";

if(!empty($_SESSION['dataform']['OutScripts'])){
    echo "<script language=\"javascript\">\n";
    echo "jQuery(document).ready(function($) {\n";
    echo $_SESSION['dataform']['OutScripts'];
    unset($_SESSION['dataform']['OutScripts']);
    echo "});\n";
    echo "</script>\n";
}
?>