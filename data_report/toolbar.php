<?php
// Control Buttons
    //_useToolbarTemplate _layoutTemplate
    if(!empty($_SESSION['DF_Notification'])){
        
        ob_start();
        foreach($_SESSION['DF_Notification'] as $Key=>$Notice){
        $uid = uniqid();
        ?>
            <div class="alert alert-<?php echo $_SESSION['DF_NotificationTypes'][$Key]; ?>" id="<?php echo $uid; ?>">
            <a class="close" onClick="jQuery('#<?php echo $uid; ?>').fadeOut('slow');">×</a>
            <?php echo $Notice; ?>
            </div>
        <?
        }
        unset($_SESSION['DF_Notification']);
        echo ob_get_clean();
    }

if (!empty($Config['_Hide_Toolbar'])) {

    if(!empty($Config['_useToolbarTemplate'])){
    //    echo
        
        $Template = $Config['_layoutTemplate']['_Toolbar'];
        // Replace Codes with Buttons

        
        //For Add Item - Only if showed
        if (empty($Config['_New_Item_Hide'])) {
            $ajaxSubmit = 'true';
            if (is_admin ()) {
                if (empty($Config['_ajaxForms'])) {
                    $ajaxSubmit = 'false';
                }
            } else {
                if (empty($Config['_ajaxForms'])) {
                    $ajaxSubmit = 'false';
                }
            }
            if(!empty($_GET)){
                $hasQuery = build_query($_GET);
            }else{
                $hasQuery = false;
            }
            $Template = str_replace('{{_button_addItem}}', dr_toolbarButton($Config['_New_Item_Title'], 'df_buildQuickCaptureForm(\'' . $Media['ID'] . '\', ' . $ajaxSubmit . ',\''.$hasQuery.'\');return false;','add'), $Template);
        }else{
            $Template = str_replace('{{_button_addItem}}', '', $Template);
        }

        // replace import button
        if (!empty($Config['_Show_Import'])) {
            //{{_button_import}}
            $Template = str_replace('{{_button_import}}', dr_toolbarButton('Import', 'df_buildImportForm(\'' . $Media['ID'] . '\');return false;', 'import'), $Template);
        }else{
            $Template = str_replace('{{_button_import}}', '', $Template);
        }

        //replace {{_button_toggleFilters}}
        if (!empty($Config['_Show_Filters'])) {
            $Template = str_replace('{{_button_toggleFilters}}', dr_toolbarButton('Filters', 'jQuery(\'#filterPanel_' . $Media['ID'] . '\').toggle();', 'filterbutton'), $Template);
        }else{
            $Template = str_replace('{{_button_toggleFilters}}', '', $Template);
        }

        // replace {{_button_reload}}
        if (!empty($Config['_showReload'])) {
                $Template = str_replace('{{_button_reload}}', dr_toolbarButton('Reload', 'dr_goToPage(\'' . $Media['ID'] . '\', false, false);', 'reload'), $Template);
        }else{

        }

        // replace {{_button_selectAll}}
        if (!empty($Config['_Show_Delete'])) {
            if (!empty($Config['_Show_Select'])) {
                $Template = str_replace('{{_button_selectAll}}', dr_toolbarButton('Select All', 'dr_selectAll(\'' . $Media['ID'] . '\');', 'selectall'), $Template);
                $Template = str_replace('{{_button_unselect}}', dr_toolbarButton('Unselect All', 'dr_deSelectAll(\'' . $Media['ID'] . '\');', 'unselectall'), $Template);
            }else{
                $Template = str_replace('{{_button_selectAll}}', '', $Template);
                $Template = str_replace('{{_button_unselect}}', '', $Template);
            }

            $Template = str_replace('{{_button_deleteSelected}}', dr_toolbarButton('Delete Selected', 'dr_deleteEntries(\'' . $Media['ID'] . '\');"', 'delete'), $Template);
        }else{
            $Template = str_replace('{{_button_selectAll}}', '', $Template);
            $Template = str_replace('{{_button_unselect}}', '', $Template);
            $Template = str_replace('{{_button_deleteSelected}}', '', $Template);
        }


        //replace {{_button_export_pdf}} and {{_button_export_csv}}
        if (!empty($Config['_Show_Export'])) {
            if(empty($Global))
                $Global = false;

            $Template = str_replace('{{_button_export_pdf}}', dr_toolbarButton('Export PDF', 'dr_exportReport(\'?format_' . $Media['ID'] . '=pdf\', \'' . $Media['ID'] . '\',\'' . $Global . '\');', 'export'), $Template);
            $Template = str_replace('{{_button_export_csv}}', dr_toolbarButton('Export CSV', false, 'export', '?format_' . $Media['ID'] . '=csv'), $Template);
        }else{
            $Template = str_replace('{{_button_export_pdf}}', '', $Template);
            $Template = str_replace('{{_button_export_csv}}', '', $Template);
        }

        


        echo $Template;
    }else{


        $customClass= '';
        if(!empty($Config['_toolbarClass'])){
            $customClass= $Config['_toolbarClass'];
        }

        echo '<div id="report_tools_' . $Media['ID'] . '" class="report_tools list_row3 '.$customClass.'">';

        if (empty($Config['_New_Item_Hide'])) {
            $ajaxSubmit = 'true';
            if (is_admin ()) {
                if (empty($Config['_ajaxForms'])) {
                    $ajaxSubmit = 'false';
                }
            } else {
                if (empty($Config['_ajaxForms'])) {
                    $ajaxSubmit = 'false';
                }
            }

            //vardump($Config['_ReturnFields']);
           echo dr_toolbarButton($Config['_New_Item_Title'], 'df_buildQuickCaptureForm(\'' . $Media['ID'] . '\', ' . $ajaxSubmit . ', \''.build_query($_GET).'\');return false;','add');
           echo dr_toolbarSeperator();
            //echo '<div class="fbutton"><div class="button add-new-h2" onclick=""><span class="add">' . $Config['_New_Item_Title'] . '</span></div></div>';
        }

        if (!empty($Config['_Show_Import'])) {
            echo dr_toolbarButton('Import', 'df_buildImportForm(\'' . $Media['ID'] . '\');return false;', 'import');
            echo dr_toolbarSeperator();
        }

        //if(empty($_SESSION['lockedFilters'][$Media['ID']]) || !empty($_SESSION['UserLogged'])){
        if (!empty($Config['_Show_Filters'])) {
            if (!empty($Config['_toggle_Filters'])) {
                echo dr_toolbarButton('Filters', 'jQuery(\'#filterPanel_' . $Media['ID'] . '\').toggle();', 'filterbutton');
                echo dr_toolbarSeperator();
            }
        }
        //}
        if (!empty($Config['_showReload'])) {
                echo dr_toolbarButton('Reload', 'dr_goToPage(\'' . $Media['ID'] . '\', false, false);', 'reload');
                echo dr_toolbarSeperator();
        }

        //dr_selectAll
        if (!empty($Config['_Show_Delete'])) {
            if (!empty($Config['_Show_Select'])) {
                echo dr_toolbarButton('Select All', 'dr_selectAll(\'' . $Media['ID'] . '\');', 'selectall');
                echo dr_toolbarSeperator();

                echo dr_toolbarButton('Unselect All', 'dr_deSelectAll(\'' . $Media['ID'] . '\');', 'unselectall');
                echo dr_toolbarSeperator();
            }

            echo dr_toolbarButton('Delete Selected', 'dr_deleteEntries(\'' . $Media['ID'] . '\');"', 'delete');
            echo dr_toolbarSeperator();
        }


        if (!empty($Config['_Show_Export'])) {
            if(empty($Global))
                $Global = false;

            echo dr_toolbarButton('Export PDF', 'dr_exportReport(\'?format_' . $Media['ID'] . '=pdf\', \'' . $Media['ID'] . '\',\'' . $Global . '\');', 'export');
            echo dr_toolbarSeperator();

            echo dr_toolbarButton('Export CSV', 'dr_exportReport(\'?format_' . $Media['ID'] . '=csv\', \'' . $Media['ID'] . '\',\'' . $Global . '\');', 'export');
            echo dr_toolbarSeperator();
        }


        //echo '<div class="btnseparator ui-dialog-tile" style="display:none;"></div>';
        //echo '<div class="fbutton ui-dialog-tile" style="display:none;"><div class="button add-new-h2"><span class="selectall"  onclick="dialog_tile();">Tile Dialogs</span></div></div>';

        if (!empty($Config['_Show_Plugins'])) {
            $ListButtons = loadFolderContents(WP_PLUGIN_DIR . '/db-toolkit/data_report/plugins');
            foreach ($ListButtons as $PlugButton) {
                foreach ($PlugButton as $Button) {
                    include(WP_PLUGIN_DIR . '/db-toolkit/data_report/plugins/' . $Button[1] . '/button.php');
                }
            }
        }
        echo '<div style="clear:both;"></div></div>';
    }
}
//echo $Buttons;

if (!empty($Config['_Show_Filters'])) {
    $Filters = false;
    $FilterVisiable = 'none';
    if (empty($Config['_toggle_Filters'])) {
        $FilterVisiable = 'block';
    }
    if (!empty($_SESSION['reportFilters'][$Media['ID']])) {
        if (count($_SESSION['reportFilters'][$Media['ID']]) > 1) {
            $Filters = df_cleanArray($_SESSION['reportFilters'][$Media['ID']]);
            $FilterVisiable = 'block';
        }
    }


    $customClass= '';
    if(!empty($Config['_filterbarClass'])){
        $customClass= $Config['_filterbarClass'];
    }
    $customClassButtonBar= '';
    if(!empty($Config['_filterbuttonbarClass'])){
        $customClassButtonBar= $Config['_filterbuttonbarClass'];
    }

    //if(empty($_SESSION['lockedFilters'][$Media['ID']]) || !empty($_SESSION['UserLogged'])){
?>
    <div class="filterpanels" id="filterPanel_<?php echo $Media['ID']; ?>" style="visibility:visible; display:<?php echo $FilterVisiable; ?>;">

        <form id="setFilters_<?php echo $Media['ID']; ?>" name="setFilters" method="post" action="" style="margin:0;">
            <input type="hidden" id="reportFilters_<?php echo $Media['ID']; ?>" value="<?php echo $Media['ID']; ?>" name="reportFilter[<?php echo $Media['ID']; ?>][EID]" />
            <div class="report_filters_panel <?php echo $customClass; ?>">
<?php
    echo dr_BuildReportFilters($Config, $Media['ID'], $Filters);
?>
                <div style="clear:both"></div>
            </div>
            <div class="list_row3 <?php echo $customClassButtonBar; ?>" style="clear:both;">
            <?php
            if(empty($Config['_ajax_Filters'])){
                echo dr_toolbarButton('Apply Filters', 'jQuery(\'#setFilters_'.$Media['ID'].'\').submit();', 'applyfilter');
            }else{
                echo dr_toolbarButton('Apply Filters', 'dr_applyFilters(\''.$Media['ID'].'\');', 'applyfilter');
            }
                echo dr_toolbarSeperator();
            ?>

<?php
////if(!empty($_SESSION['reportFilters'][$Media['ID']])){
?>
            <div class="btnseparator"></div><input type="hidden" name="reportFilter[ClearFilters]" id="clearFilters_<?php echo $Media['ID']; ?>" value="" />
            <?php
            if(empty($Config['_ajax_Filters'])){
                echo dr_toolbarButton('Clear Filters', 'jQuery(\'#clearFilters_'.$Media['ID'].'\').val(1); jQuery(\'#setFilters_'.$Media['ID'].'\').submit();', 'clearfilter');
            }else{
                echo dr_toolbarButton('Clear Filters', 'dr_applyFilters(\''.$Media['ID'].'\', true);', 'clearfilter');
            }
                echo dr_toolbarSeperator();

//}
            if (is_admin ()) {
                if (empty($Config['_Hide_FilterLock'])) {
                    if (empty($_SESSION['lockedFilters'][$Media['ID']])) {
?>
                        <input type="hidden" name="reportFilter[reportFilterLock]" id="lockFilters_<?php echo $Media['ID']; ?>" value="" />
                        <?php
                            echo dr_toolbarButton('Lock Filters', 'jQuery(\'#lockFilters_'.$Media['ID'].'\').val(\''.$Media['ID'].'\'); jQuery(\'#setFilters_'.$Media['ID'].'\').submit();', 'lockfilterfilter');
                            echo dr_toolbarSeperator();
                    }
                    if (!empty($_SESSION['lockedFilters'][$Media['ID']])) {
                        ?>
                        <input type="hidden" name="reportFilter[reportFilterUnlock]" id="unlockFilters_<?php echo $Media['ID']; ?>" value="" />
                        <?php
                            echo dr_toolbarButton('Unlock Filters', 'jQuery(\'#unlockFilters_'.$Media['ID'].'\').val(\''.$Media['ID'].'\'); jQuery(\'#setFilters_'.$Media['ID'].'\').submit();', 'unlockfilterfilter');
                            echo dr_toolbarSeperator();
                    }
                }
?>
<?php
            }
?>
<?php
    if (!empty($Config['_toggle_Filters'])) {

    echo dr_toolbarButton('Close Filters', 'jQuery(\'#filterPanel_'.$Media['ID'].'\').toggle(); return false; ', 'closefilter');
    echo dr_toolbarSeperator();
    
    }
?>            <?php
            /* <!-- <input type="submit" name="reportFilter[Submit]" id="button add-new-h2" value="Apply Filters" class="buttons" />&nbsp;<input type="button add-new-h2" name="button add-new-h2" id="button add-new-h2" value="Close Panel" class="buttons" onclick="toggle('filterPanel_<?php echo $Media['ID']; ?>'); return false; " />&nbsp;<input type="submit" name="reportFilter[ClearFilters]" id="button add-new-h2" value="Clear Filters" class="buttons" onclick="return confirm('Are you sure you want to clear the filters?');" /></div> --> */
            ?>
            <div style="clear:both;"></div>
        </div>
    </form>

</div>
            <?php
            //}
        }
            ?>