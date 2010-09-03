<?php









$test = array();
$Render = true;
$TitleNotice = '';
foreach($Config['_Field'] as $Key=>$Value) {
    if($Value == 'viewitem_filter') {
        if(!empty($Config['_overRide'][$Key])) {
            if(!isset($_GET[$Config['_overRide'][$Key]])) {
                $Render = false;
                if(is_admin()) {
                    $Render = true;
                    $TitleNotice = ' [Selected Item filter on '.$Key.' looking for '.$Config['_overRide'][$Key].']';
                }
            }
        }else {
            if(!isset($_GET[$Key])) {
                $Render = false;
                if(is_admin()) {
                    $Render = true;
                    $TitleNotice = ' [Selected Item filter on '.$Key.' looking for '.$Key.']';
                }
            }
        }
    }
}
if($Render != true) {
    return;
}
$FilterLocks = unserialize(get_option('filter_Lock_'.$Media['ID']));
if(!empty($FilterLocks)) {
    $_SESSION['lockedFilters'][$Media['ID']] = $_SESSION['reportFilters'][$Media['ID']];if(empty($_SESSION['reportFilters'][$Media['ID']])){
        $_SESSION['reportFilters'][$Media['ID']] = $FilterLocks;
    }else{
       array_merge($_SESSION['reportFilters'][$Media['ID']], $FilterLocks);
    }
    $_SESSION['lockedFilters'][$Media['ID']] = $FilterLocks;
    //vardump($_SESSION['reportFilters'][$Media['ID']]);
}
//vardump($_SESSION['reportFilters'][$Media['ID']]);
// Form Mode
if(!empty($Config['_FormMode'])) {
    foreach($Config['_Field'] as $Field=>$Value){
        $typeSet = explode('_', $Value);
        if(function_exists($typeSet[0].'_preForm')) {
                $Func = $typeSet[0].'_preForm';
                $Func($Field, $typeSet[1], $Media, $Config);
        }
    }
    if(!empty($_GET[$Config['_ReturnFields'][0]])) {
        $Form = dr_BuildUpDateForm($Media['ID'], $_GET[$Config['_ReturnFields'][0]]);
    }else {
        $Form = df_buildQuickCaptureForm($Media['ID']);
    }
    foreach($Config['_Field'] as $Field=>$Value){
        $typeSet = explode('_', $Value);
        if(function_exists($typeSet[0].'_postForm')) {
                $Func = $typeSet[0].'_postForm';
                $Func($Field, $typeSet[1], $Media, $Config);
        }
    }
    if(empty($Config['_HideFrame'])) {
        InfoBox($Form['title']);
    }
    echo $Form['html'];
    if(empty($Config['_HideFrame'])) {
        EndInfoBox();
    }
    return;
}
//dump($Config);
if(!empty($Config['_ViewMode'])) {
    if(!empty($_SESSION['DocumentLoaded'])) {
        echo '<div class="warning">Database Interface View Mode. Requires: ';
        echo implode(', ', $Config['_ReturnFields']);
        echo '</div>';
    }
    if(!empty($_GET[$Config['_ReturnFields'][0]])) {
        $Item = di_showItem($Media['ID'], $_GET[$Config['_ReturnFields'][0]]);
        //dump($Item);
        if(empty($Config['_HideFrame'])) {
            InfoBox($Config['_ReportTitle']);
        }
        echo $Item['html'];
        if(empty($Config['_HideFrame'])) {
            EndInfoBox();
        }
    }
    return;
}

if(empty($Config['_HideFrame'])) {
    InfoBox($Config['_ReportTitle'].$TitleNotice);
}
if(empty($Config['_SearchMode'])) {
    // Control Buttons
    if(empty($Config['_Hide_Toolbar'])) {
        echo '<div id="report_tools_'.$Media['ID'].'" class="report_tools list_row3">';
        if(empty($Config['_New_Item_Hide'])) {
            $ajaxSubmit = 'true';
            if(is_admin()){
                if(!empty($Config['_ItemViewInterface'])){
                    $ajaxSubmit = 'false';
                }
            }else{
                if(!empty($Config['_ItemViewInterface'])){
                    $ajaxSubmit = 'false';
                }

            }
            echo '<div class="fbutton"><div class="button"><span class="add" style="padding-left: 20px;" onclick="df_buildQuickCaptureForm(\''.$Media['ID'].'\', '.$ajaxSubmit.');return false;">'.$Config['_New_Item_Title'].'</span></div></div>';
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
            echo '<div class="fbutton"><div class="button"><a href="?format_'.$Media['ID'].'=pdf" target="_blank"><span class="export" style="padding-left: 20px;">Export</span></a></div></div>';
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
}else {
    InfoBox($Config['_ReportTitle']);
    ?>
<div class="report_filters_panel">
    <form id="setFilters_<?php echo $Media['ID']; ?>" name="setFilters" method="post" action="" style="margin:0;">
        <input type="hidden" id="reportFilters_<?php echo $Media['ID']; ?>" value="<?php echo $Media['ID']; ?>" name="reportFilter[<?php echo $Media['ID']; ?>][EID]" />
            <?php
            if(!empty($_SESSION['reportFilters'][$Media['ID']])) {
                if(count($_SESSION['reportFilters'][$Media['ID']]) > 1) {
                    $Filters = df_cleanArray($_SESSION['reportFilters'][$Media['ID']]);
                    $_SESSION['reportFilters'][$Media['ID']] = $Filters;
                    $FilterVisiable = 'block';
                }
            }
            echo dr_BuildReportFilters($Config, $Media['ID'], $Filters);
            ?>
        <div style="clear:both;"></div>
            <?php
            $ButtonAlign = 'center';
            if(!empty($Config['_SubmitAlignment'])) {
                $ButtonAlign = $Config['_SubmitAlignment'];
            }

            ?>
        <div style="padding: 2px; text-align:<?php echo $ButtonAlign; ?>">
            <input type="submit" value="Search" class="filterSearchbutton" />&nbsp;
                <?php
                if(!empty($_SESSION['reportFilters'][$Media['ID']])) {
                    //dump($_SESSION['reportFilters']);
                    ?>
            <input type="submit" value="Clear Results" class="filterSearchbutton" onclick="jQuery('#clearFilters_<?php echo $Media['ID']; ?>').val(1); jQuery('#setFilters_<?php echo $Media['ID']; ?>').submit();" /><input type="hidden" name="reportFilter[ClearFilters]" id="clearFilters_<?php echo $Media['ID']; ?>" value="" />
                    <?php
                }
                ?>
        </div>
    </form>
    <div style="clear:both"></div>
</div>
    <?php
    endInfoBox();
}

// list data

// use filters
$Defaults = false;
$FilterVisiable = 'none';
if(empty($_GET['PageData']['ID'])) {
    $_GET['PageData']['ID'] = $_SESSION['DocumentLoaded'];
}
if(!empty($_SESSION['dr_filters'][$_GET['PageData']['ID']])) {
    $Defaults = df_cleanArray($_SESSION['dr_filters'][$_GET['PageData']['ID']]);
    $FilterVisiable = 'block';
}
echo '<div id="reportPanel_'.$Media['ID'].'">';
//($EID, $Page = 1, $SortField = false, $SortDir = false)

if(empty($_SESSION['report_'.$Media['ID']]['SortField'])) {
    $_SESSION['report_'.$Media['ID']]['SortField'] = $Config['_SortField'];
}
if(empty($_SESSION['report_'.$Media['ID']]['SortDir'])) {
    $_SESSION['report_'.$Media['ID']]['SortDir'] = $Config['_SortDirection'];
}	
// Check sorts are valid
if(!empty($Config['_IndexType'][$_SESSION['report_'.$Media['ID']]['SortField']])) {
    $SortPart = explode('_', $Config['_IndexType'][$_SESSION['report_'.$Media['ID']]['SortField']]);
    if(!empty($SortPart[1])) {
        if($SortPart[1] == 'hide') {
            $_SESSION['report_'.$Media['ID']]['SortField'] = $Config['_SortField'];
            $_SESSION['report_'.$Media['ID']]['SortDir'] = $Config['_SortDirection'];
        }
    }else {
        $_SESSION['report_'.$Media['ID']]['SortField'] = $Config['_SortField'];
        $_SESSION['report_'.$Media['ID']]['SortDir'] = $Config['_SortDirection'];
    }
}else {
    $_SESSION['report_'.$Media['ID']]['SortField'] = $Config['_SortField'];
    $_SESSION['report_'.$Media['ID']]['SortDir'] = $Config['_SortDirection'];
}

if(!empty($Config['_Field'][$_SESSION['report_'.$Media['ID']]['SortDir']])) {
    echo 'not';
}

if(!empty($_SESSION['reportFilters'][$Media['ID']]) || empty($Config['_SearchMode'])) {
    $gotTo = false;
    if(!empty($_GET['_pg'])) {
        $gotTo = $_GET['_pg'];
    }
    echo dr_BuildReportGrid($Media['ID'], $gotTo, $_SESSION['report_'.$Media['ID']]['SortField'], $_SESSION['report_'.$Media['ID']]['SortDir']);
    if(!empty($Config['_autoPolling'])){
        $Rate = $Config['_autoPolling']*1000;
    
        $_SESSION['dataform']['OutScripts'] .= "
            
                setInterval('dr_reloadData(\'".$Media['ID']."\')', ".$Rate.");
        ";

    }
    
    /*
     * Experimental Graphing
     

    global $wpdb;
    $Query = dr_BuildReportGrid($Media['ID'], $gotTo, $_SESSION['report_'.$Media['ID']]['SortField'], $_SESSION['report_'.$Media['ID']]['SortDir'],'sql');
    //$Query = explode('LIMIT', $Query);
    //$Query = $Query[0];
    $rowData = $wpdb->get_results($Query, ARRAY_A);
    //vardump($rowData);
    $graphData =  "var data = [ ";
    $inputData = array();
    foreach($rowData as $Entry){
        
        if($Entry['DateOrdered'] != '0000-00-00 00:00:00'){
            //echo $Entry['DateOrdered'].'<br />';
            $inputData[] = '['.(strtotime($Entry['DateOrdered'])*1000).','.$Entry['__4c0a9aaf39956'].']';
        }//vardump($Entry);
        //break;
    }
    $graphData .= implode(',', $inputData);
    $graphData .= "];";
    
    echo '<div id="placeholder" style="width:680px; height: 450px;">graph</div>';
$_SESSION['dataform']['OutScripts'] .= $graphData;
$_SESSION['dataform']['OutScripts'] .= "

    

    $.plot($(\"#placeholder\"), [{
            label: \"Phone sales\",
            data: data,
            lines: { show: true },
            points: { show: true }
            

        }], { xaxis: { mode: \"time\", timeformat: \"%y-%m-%d %H:%M:%S\"} });

";
*/

}
echo '</div>';
if(empty($Config['_HideFrame'])) {
    endinfobox();
}
if(is_admin()) {
    //$SharedSecret = md5($Media['ID']).md5(serialize($Config));
    //echo '<div class="list_row1">API Key: <input type="text" value="'.$SharedSecret.'" style="width:98%;" onclick="jQuery(this).select();" onchange="jQuery(this).val(\''.$SharedSecret.'\');" /></div>';
    //echo '<div class="list_row2">Channel: '.$Media['ID'].'</div>';
}
?>
