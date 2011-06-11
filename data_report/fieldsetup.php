<h2>Field Setup</h2>
<?php
if (!empty($Element['_Application'])) {
    $Application = $Element['_Application'];
} else {
    $Application = 'Base';
    if (!empty($_SESSION['activeApp'])) {
        $Application = $_SESSION['activeApp'];
    }
}
?>
<div class="section">    
    <div class="option">
        <div class="title">Application</div>
        <div class="controls">            
            <?php
                echo dt_listApplications($Application);
            ?><input type="text" value="" style="padding: 5px; display:none;" id="_Application_New" name="Data[Content][_Application]" disabled="disabled">
            <a id="addAppB" href="#" onclick="return dt_addNewApp();">Add New</a>
            <div class="clear"></div>
            <div class="clear"></div>
        </div>
        <div class="explain">Select an application to add the interface to, or create a new application.</div>
        <div class="clear"></div>
    </div>
</div>

<?php
                    /*
                      <div class="list_row1" style="padding: 3px;">
                      <table width="100%" cellspacing="2" cellpadding="2" border="0">
                      <tbody>
                      <tr>
                      <td width="150" align="" class="">
                      <label for="_iconSelect">Icon</label>
                      </td>
                      <td class="">
                      <?php
                      echo '<img id="interfaceIconPreview" src="'.WP_PLUGIN_URL.'/db-toolkit/images/icons/app_window.png" width="16" height="16" align="absmiddle" />';
                      ?>
                      <input type="hidden" value="app_window.png" style="padding: 5px;" id="_Application_Icon" name="Data[Content][_Icon]" >
                      <a id="changeIcon" href="#" onclick="return dt_iconChooser();">Change</a>
                      </td>
                      </tr>
                      </tbody>
                      </table>
                      </div>
                      <?php
                     */
                    echo dais_customfield('text', 'Menu Group', '_ItemGroup', '_ItemGroup', 'list_row2', $Element['_ItemGroup'], '', 'Sets the menu group on the left navigation bar within Wordpress admin.');
                    $Sel = '';
                    if (!empty($Element['Content']['_SetAdminMenu'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Admin Menu', '_SetAdminMenu', '_SetAdminMenu', 'list_row1', 1, $Sel, 'Sets the interface to the admin bar. (Requires a menu group)');
                    echo dais_customfield('text', 'Menu Label', '_ReportTitle', '_ReportTitle', 'list_row1', $Element['_interfaceName'], '', 'Sets the title of the menu link.');
                    echo dais_customfield('text', 'Interface Title', '_ReportDescription', '_ReportDescription', 'list_row2', $Element['_ReportDescription'], '','Sets the title of the interface which is whoen when using the interface.');
                    echo dais_customfield('text', 'Interface Description', '_ReportExtendedDescription', '_ReportExtendedDescription', 'list_row1', $Element['_ReportExtendedDescription'], '','Give the interface a description to quickly identify it\'s function.');
                    $Sel = '';
                    if (!empty($Element['Content']['_SetDashboard'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Dashboard Item', '_SetDashboard', '_SetDashboard', 'list_row1', 1, $Sel, 'Place the interface as a Dashboard widget.');

                    if (empty($Element['Content']['_menuAccess'])) {
                        $Element['Content']['_menuAccess'] = 'read';
                    }
                    global $wp_roles;
?>

<?php
                    echo '<h2>Table Selection</h2>';
?>
<?php
                    echo df_listTables('_main_table', 'dr_fetchPrimSetup', $Element['Content']['_main_table']);
                    //EndInfoBox();
?>

                    <div id="col-container" >
    <?php
                    echo '<h2>Report Setup</h2>';
//dump($Element);
                    if ($_GET['page'] != 'Add_New') {
    ?>
                        <div style="width:565px;">

        <?php echo '<h2>Advanced Field Types</h2>'; ?>
                        <div class="list_row3"><input type="button" class="button" value="Add Clone Field" onclick="dr_addLinking('<?php echo $Element['Content']['_main_table']; ?>')" /></div>
                        <div class="columnSorter" id="drToolBox">
            <?php
//echo df_tableReportSetup($Element['Content']['_main_table'], $Element, false, 'C');
            ?>
                    </div>
        

                    </div>
    <?php
                    }
    ?>
                    <div style="">
                        <div id="referenceSetup"></div>
                        <div style="overflow:auto;">
                            <table width="" border="0" cellspacing="2" cellpadding="2">
                                <tr>
                                    <td valign="top" class="columnSorter" id="FieldList_Main"><?php
                    echo df_tableReportSetup($Element['Content']['_main_table'], 'false', $Element);
    ?></td>
                </tr>
            </table>
        </div>
    </div>

                </div>
<?php
                    echo '<h2>Passback Field</h2>';
?>
                    <div style="padding:3px;">
                        <input type="button" name="button" id="button" class="button" value="Add Passback Field" onclick="dr_addPassbackField();"/></div>
                    <div id="PassBack_FieldSelect">
    <?php
                    echo dr_loadPassbackFields($Element['Content']['_main_table'], $Element['Content']['_ReturnFields'], $Element['Content']);
    ?></div>
<?php

                    echo '<h2>Sort Field</h2>';
?>
                    <div id="sortFieldSelect">
    <?php
                    if ($_GET['page'] != 'Add_New') {
                        echo df_loadSortFields($Element['Content']['_main_table'], $Element['Content']['_SortField'], $Element['Content']['_SortDirection']);
                    }
    ?>
                </div>
