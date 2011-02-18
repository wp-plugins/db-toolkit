<?php


// Control Buttons
    if(empty($Config['_Hide_Toolbar'])) {
        echo '<div id="report_tools_'.$Media['ID'].'" class="report_tools list_row3">';
        if(empty($Config['_New_Item_Hide'])) {
            $ajaxSubmit = 'true';
            if(is_admin()) {
                if(!empty($Config['_ItemViewInterface'])) {
                    $ajaxSubmit = 'false';
                }
            }else {
                if(!empty($Config['_ItemViewInterface'])) {
                    $ajaxSubmit = 'false';
                }

            }
            echo '<div class="fbutton"><div class="button"><span class="add" style="padding-left: 20px;" onclick="df_buildQuickCaptureForm(\''.$Media['ID'].'\', '.$ajaxSubmit.');return false;">'.$Config['_New_Item_Title'].'</span></div></div>';
        }

        if(!empty($Config['_Show_Import'])) {
                    echo '<div class="btnseparator"></div>';
                    echo '<div class="fbutton"><div class="button"><span class="import" style="padding-left: 20px;" onclick="df_buildImportForm(\''.$Media['ID'].'\');return false;">Import</span></div></div>';
                    echo '<div class="btnseparator"></div>';
        }

        //if(empty($_SESSION['lockedFilters'][$Media['ID']]) || !empty($_SESSION['UserLogged'])){
        if(!empty($Config['_Show_Filters'])) {
            if(!empty($Config['_toggle_Filters'])) {
                echo '<div class="fbutton"><div class="button"><span class="filterbutton" style="padding-left: 20px;" onclick="toggle(\'filterPanel_'.$Media['ID'].'\');">Show Filters</span></div></div>';
            }
        }
        //}
        if(!empty($Config['_showReload'])) {
            echo '<div class="btnseparator"></div>';
            echo '<div class="fbutton" id="liveView_'.$Media['ID'].'"><div class="button"><span class="reload" style="padding-left: 20px;" onclick="dr_goToPage(\''.$Media['ID'].'\', false);">Reload</span></div></div>';
            //echo '<div class="fbutton"><div class="reloadLiveOff"><span class="reload" style="padding-left: 20px;" onclick="dr_goToPage(\''.$Media['ID'].'\', false);">Reload</span></div></div>';
        }

        //dr_selectAll
        if(!empty($Config['_Show_Delete'])) {
            echo '<div class="btnseparator"></div>';
            if(!empty($Config['_Show_Select'])) {
                echo '<div class="fbutton"><div class="button"><span class="selectall" style="padding-left: 20px;" onclick="dr_selectAll(\''.$Media['ID'].'\');">Select All</span></div></div>';
                echo '<div class="fbutton"><div class="button"><span class="unselectall" style="padding-left: 20px;" onclick="dr_deSelectAll(\''.$Media['ID'].'\');">Unselect All</span></div></div>';
                echo '<div class="btnseparator"></div>';
            }
            echo '<div class="fbutton"><div class="button"><span class="delete" style="padding-left: 20px;" onclick="dr_deleteEntries(\''.$Media['ID'].'\');">Delete Selected</span></div></div>';
        }


        if(!empty($Config['_Show_Export'])) {
            echo '<div class="btnseparator"></div>';
            echo '<div class="fbutton"><div class="button"><a href="?format_'.$Media['ID'].'=pdf" target="_blank"><span class="export" style="padding-left: 20px;">Export PDF</span></a></div></div>';



            echo '<div class="btnseparator"></div>';
            echo '<div class="fbutton"><div class="button"><a href="?format_'.$Media['ID'].'=csv" target="_blank"><span class="export" style="padding-left: 20px;">Export CSV</span></a></div></div>';

        }


        //echo '<div class="btnseparator ui-dialog-tile" style="display:none;"></div>';
        //echo '<div class="fbutton ui-dialog-tile" style="display:none;"><div class="button"><span class="selectall" style="padding-left: 20px;" onclick="dialog_tile();">Tile Dialogs</span></div></div>';

        if(!empty($Config['_Show_Plugins'])) {
            $ListButtons = loadFolderContents(WP_PLUGIN_DIR.'/db-toolkit/data_report/plugins');
            foreach($ListButtons as $PlugButton) {
                foreach($PlugButton as $Button) {
                    include(WP_PLUGIN_DIR.'/db-toolkit/data_report/plugins/'.$Button[1].'/button.php');
                }
            }
        }
        echo '<div style="clear:both;"></div></div>';

    }
    //echo $Buttons;

    if(!empty($Config['_Show_Filters'])) {
        $Filters = false;
        $FilterVisiable = 'none';
        if(empty($Config['_toggle_Filters'])) {
            $FilterVisiable = 'block';
        }
        if(!empty($_SESSION['reportFilters'][$Media['ID']])) {
            if(count($_SESSION['reportFilters'][$Media['ID']]) > 1) {
                $Filters = df_cleanArray($_SESSION['reportFilters'][$Media['ID']]);
                $FilterVisiable = 'block';
            }
        }

        //if(empty($_SESSION['lockedFilters'][$Media['ID']]) || !empty($_SESSION['UserLogged'])){
        ?>
<div class="filterpanels" id="filterPanel_<?php echo $Media['ID']; ?>" style="visibility:visible; display:<?php echo $FilterVisiable; ?>;">
            <?php
            InfoBox('Filters');
        ?>
    <form id="setFilters_<?php echo $Media['ID']; ?>" name="setFilters" method="post" action="" style="margin:0;">
        <input type="hidden" id="reportFilters_<?php echo $Media['ID']; ?>" value="<?php echo $Media['ID']; ?>" name="reportFilter[<?php echo $Media['ID']; ?>][EID]" />
        <div class="report_filters_panel">
                    <?php
                    echo dr_BuildReportFilters($Config, $Media['ID'], $Filters);
        ?>
            <div style="clear:both"></div>
        </div>
        <div class="list_row3" style="clear:both;">
            <div class="fbutton">
                <div class="button">
                    <span class="applyfilter" style="padding-left: 20px;" onclick="jQuery('#setFilters_<?php echo $Media['ID']; ?>').submit();">Apply Filters</span>
                </div>
            </div>
                    <?php
                    if(!empty($Config['_toggle_Filters'])) {
            ?>
            <div class="btnseparator"></div>
            <div class="fbutton">
                <div class="button">
                    <span class="closefilter" style="padding-left: 20px;" onclick="toggle('filterPanel_<?php echo $Media['ID']; ?>'); return false; ">Close Panel</span>
                </div>
            </div>
                        <?php
                    }
        ?>
            <div class="btnseparator"></div>
            <div class="fbutton">
                <div class="button">
                    <span class="clearfilter" style="padding-left: 20px;" onclick="jQuery('#clearFilters_<?php echo $Media['ID']; ?>').val(1); jQuery('#setFilters_<?php echo $Media['ID']; ?>').submit();"><input type="hidden" name="reportFilter[ClearFilters]" id="clearFilters_<?php echo $Media['ID']; ?>" value="" />Clear Filters</span>
                </div>
            </div>
                    <?php
                    if(is_admin()) {
                        if(empty($Config['_Hide_FilterLock'])) {
                            if(empty($_SESSION['lockedFilters'][$Media['ID']])) {
                    ?>
            <div class="btnseparator"></div>
            <div class="fbutton">
                <div class="button">
                    <span class="lockfilterfilter" style="padding-left: 20px;" onclick="jQuery('#lockFilters_<?php echo $Media['ID']; ?>').val('<?php echo $Media['ID']; ?>'); jQuery('#setFilters_<?php echo $Media['ID']; ?>').submit();"><input type="hidden" name="reportFilter[reportFilterLock]" id="lockFilters_<?php echo $Media['ID']; ?>" value="" />Lock Filters</span>
                </div>
            </div>
                                <?php
                            }
                            if(!empty($_SESSION['lockedFilters'][$Media['ID']])) {

                    ?>
            <div class="btnseparator"></div>
            <div class="fbutton">
                <div class="button">
                    <span class="unlockfilterfilter" style="padding-left: 20px;" onclick="jQuery('#unlockFilters_<?php echo $Media['ID']; ?>').val('<?php echo $Media['ID']; ?>'); jQuery('#setFilters_<?php echo $Media['ID']; ?>').submit();"><input type="hidden" name="reportFilter[reportFilterUnlock]" id="unlockFilters_<?php echo $Media['ID']; ?>" value="" />Unlock Filters</span>
                </div>
            </div>
                                <?php
                            }
                        }
                        ?>
                        <?php
                    }
        ?>
       <!-- <input type="submit" name="reportFilter[Submit]" id="button" value="Apply Filters" class="buttons" />&nbsp;<input type="button" name="button" id="button" value="Close Panel" class="buttons" onclick="toggle('filterPanel_<?php echo $Media['ID']; ?>'); return false; " />&nbsp;<input type="submit" name="reportFilter[ClearFilters]" id="button" value="Clear Filters" class="buttons" onclick="return confirm('Are you sure you want to clear the filters?');" /></div> -->
            <div style="clear:both;"></div>
        </div>
    </form>
            <?php
            endinfobox();
        ?>
</div>
        <?php
        //}
    }
    ?>