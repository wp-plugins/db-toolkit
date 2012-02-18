<?php

// use filters
$Defaults = false;
$FilterVisiable = 'none';

/*if (empty($_GET['PageData']['ID'])) {
    $_GET['PageData']['ID'] = $_SESSION['DocumentLoaded'];
}
if (!empty($_SESSION['dr_filters'][$_GET['PageData']['ID']])) {
    $Defaults = df_cleanArray($_SESSION['dr_filters'][$_GET['PageData']['ID']]);
    $FilterVisiable = 'block';
}
*/

if (empty($Config['_useListTemplate'])) {
    echo '<div id="reportPanel_' . $Media['ID'] . '">';
}
//($EID, $Page = 1, $SortField = false, $SortDir = false)

if (empty($_SESSION['report_' . $Media['ID']]['SortField'])) {
    $_SESSION['report_' . $Media['ID']]['SortField'] = $Config['_SortField'];
}
if (empty($_SESSION['report_' . $Media['ID']]['SortDir'])) {
    $_SESSION['report_' . $Media['ID']]['SortDir'] = $Config['_SortDirection'];
}
// Check sorts are valid
if (!empty($Config['_IndexType'][$_SESSION['report_' . $Media['ID']]['SortField']])) {
    $SortPart = explode('_', $Config['_IndexType'][$_SESSION['report_' . $Media['ID']]['SortField']]);
    if (!empty($SortPart[1])) {
        if ($SortPart[1] == 'hide') {
            $_SESSION['report_' . $Media['ID']]['SortField'] = $Config['_SortField'];
            $_SESSION['report_' . $Media['ID']]['SortDir'] = $Config['_SortDirection'];
        }
    } else {
        $_SESSION['report_' . $Media['ID']]['SortField'] = $Config['_SortField'];
        $_SESSION['report_' . $Media['ID']]['SortDir'] = $Config['_SortDirection'];
    }
} else {
    $_SESSION['report_' . $Media['ID']]['SortField'] = $Config['_SortField'];
    $_SESSION['report_' . $Media['ID']]['SortDir'] = $Config['_SortDirection'];
}

if (!empty($Config['_Field'][$_SESSION['report_' . $Media['ID']]['SortDir']])) {
    //echo 'not';
}

if (!empty($_SESSION['reportFilters'][$Media['ID']]) || empty($Config['_SearchMode'])) {
    $gotTo = false;
    if (!empty($_GET['_pg'])) {
        $gotTo = floatval($_GET['_pg']);
    }

    $Query = dr_BuildReportGrid($Media['ID'], $gotTo, $_SESSION['report_' . $Media['ID']]['SortField'], $_SESSION['report_' . $Media['ID']]['SortDir']);
    $Data = $wpdb->get_results($Query, ARRAY_A);
    if(!empty($Config['_useListTemplate'])){
        // User the template the user has done
        if(!empty($Config['_TemplateWrapper'])){
            $WrapperEl = $Config['_TemplateWrapper'];
        }
        $Wrapperclasses = '';
        if(!empty($Config['_TemplateClass'])){
            $Wrapperclasses = $Config['_TemplateClass'];
        }
        if(!empty($Config['_TemplateWrapper'])){
            echo '<'.$WrapperEl.' id="reportPanel_'.$Media['ID'].'" class="interfaceWrapper '.$Wrapperclasses.'">';
        }
        include('templatemode.php');
        if(!empty($Config['_TemplateWrapper'])){
        echo '</'.$WrapperEl.'>';
        }

    }else{
        // Use the default list grid'
        // set table classes
        $EID = $Media['ID'];
        $tableClass = 'data_report_Table';
        if(is_admin ()){
            $tableClass = 'wp-list-table widefat fixed posts data_report_Table';
        }
        
        // Run View Processes
        if(!empty($Config['_ViewProcessors'])){

            foreach($Config['_ViewProcessors'] as $viewProcess){
                if(empty($_GET['format_'.$EID])){
                    //ignore on export
                    if(file_exists(DB_TOOLKIT.'data_report/processors/'.$viewProcess['_process'].'/functions.php')){
                        include_once(DB_TOOLKIT.'data_report/processors/'.$viewProcess['_process'].'/functions.php');
                        $func = 'pre_process_'.$viewProcess['_process'];
                        $Data = $func($Data, $viewProcess, $Config, $EID);
                        if(empty($Data)){
                            return;
                        }
                    }
                }
            }

        }
        

        echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" style=\"cursor:default;\" id=\"data_report_".$EID."\" class=\"".$tableClass."  ".$Config['_ListTableClass']."\">\n";
            
            //headers
            echo "<thead>\n";
                echo "<tr id=\"the-list\">\n";
                // in admin check box selector
                // May make this standard as its more wordpressy
                if(is_admin ()){
                    echo "<th style=\"\" class=\"manage-column column-cb check-column\" id=\"dbt_cb\" scope=\"col\"><input type=\"checkbox\"></th>\n";
                }

                // Field Headings                
                foreach($Config['_FieldTitle'] as $Field=>$Title){
                    if(strpos($Config['_IndexType'][$Field],'_show') !== false){

                        $Direction = 'ASC';
                        if ($_SESSION['report_' . $EID]['SortDir'] == 'ASC') {
                            $Direction = 'DESC';
                        }
                        $sortClass = 'report_header';
                        if ($_SESSION['report_' . $EID]['SortField'] == $Field) {
                            $sortClass = 'sorting_' . $_SESSION['report_' . $EID]['SortDir'];
                        }
                        $sortAction = '';
                        if (!empty($Config['_Sortable'][$Field])) {
                            $sortAction = 'onclick="dr_sortReport(\'' . $EID . '\', \'' . $Field . '\', \'' . $Direction . '\');" class="' . $sortClass . '"';
                        }

                        echo "<th nowrap=\"nowrap\" scope=\"col\" width=\"" . ($Config['_WidthOverride'][$Field] == '' ? '{{width_' . $Field . '}}px' : $Config['_WidthOverride'][$Field] . 'px') . "\" ".$sortAction."><span>".$Title."</span></th>\n";
                    }
                }
                // Action Headings
                
                echo "</tr>\n";
            echo "</thead>\n";
            //vardump($Config);
            //die;

            /// Rows
            echo "<tbody>\n";
            foreach($Data as $Row){
                $actionCheck = false;
                echo "<tr id=\"row_".$EID."_1\" ref=\"10 highlight\" class=\" itemRow_".$EID." report_entry\">\n";
                    foreach($Row as $Field=>$Value){
                        $RowID = uniqid();
                        // ignore the return fields
                        if(strpos($Config['_IndexType'][$Field],'_show') !== false){
                            if(is_admin ()){
                                if(empty($actionCheck)){
                                    echo "<th class=\"check-column\" scope=\"row\"><input type=\"checkbox\" value=\"1\" name=\"post[]\"></th>";
                                }
                            }
                            $sortClass = '';
                            if ($_SESSION['report_' . $EID]['SortField'] == $Field) {
                                $sortClass = 'column_sorting_' . $_SESSION['report_' . $EID]['SortDir'];
                            }
                            echo "<td \" ref=\"itemRow_".$EID."\" id=\"".$RowID."\" scope=\"col\" class=\"".$sortClass." \" style=\"text-align:" . $Config['_Justify'][$Field] . "; \">\n";
                                // Run FieldType Processor
                                if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Config['_Field'][$Field][0] . '/functions.php')) {
                                    include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Config['_Field'][$Field][0] . '/functions.php';
                                }
                                $func = $Config['_Field'][$Field][0].'_processValue';
                                if(function_exists($func)){
                                    echo $func($Value, $Config['_Field'][$Field][1], $Field, $Config, $EID, $Data);
                                }else{
                                    echo $Value;
                                }
                                if(is_admin ()){
                                    if(empty($actionCheck)){
                                        echo "<div class=\"row-actions\"><span class=\"view\"><a rel=\"permalink\" title=\"View this item\" href=\"    \">View</a> | </span><span class=\"edit\"><a title=\"Edit this item\" href=\"        \">Edit</a> | </span><span class=\"trash\"><a href=\"     \" title=\"Delete this item\" class=\"submitdelete\">Delete</a></span></div>\n";                                        
                                    }
                                }
                            echo "</td>\n";
                            $actionCheck = true;
                        }                    
                    }
                echo "</tr>\n";

            }
            echo "</tbody>\n";
        echo "</table>\n";
    }

    if (!empty($Config['_autoPolling'])) {
        $Rate = $Config['_autoPolling'] * 1000;

        $_SESSION['dataform']['OutScripts'] .= "
                setInterval('dr_reloadData(\'" . $Media['ID'] . "\')', " . $Rate . ");
        ";
    }

}
if (empty($Config['_useListTemplate'])) {
    echo '</div>';
}

if (is_admin ()) {
    //$SharedSecret = md5($Media['ID']).md5(serialize($Config));
    //echo '<div class="list_row1">API Key: <input type="text" value="'.$SharedSecret.'" style="width:98%;" onclick="jQuery(this).select();" onchange="jQuery(this).val(\''.$SharedSecret.'\');" /></div>';
    //echo '<div class="list_row2">Channel: '.$Media['ID'].'</div>';
}
?>