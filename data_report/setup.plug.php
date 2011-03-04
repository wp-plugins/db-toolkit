<style type="text/css">


</style>
<img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_form/loading.gif" width="16" height="16" alt="loading" align="absmiddle" style="display:none" /> <img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/arrow_out.png" width="16" height="16" alt="loading" align="absmiddle" style="display:none" /> <img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/tag.png" width="16" height="16" alt="loading" align="absmiddle" style="display:none" />
<input type="hidden" name="Data[Content][_FormLayout]" cols="50" rows="10" id="_FormLayout" />
<div id="dbtools_tabs" class="dbtools_tabs">
    <ul class="content-box-tabs">
        <li><a href="#tabs-1">Field Setup</a></li>
        <li><a href="#tabs-2">Form Layout</a></li>
        <li><a href="#tabs-formprocess">Form Processors</a></li>
        <li><a href="#tabs-2b">View Layout</a></li>
        <li><a href="#tabs-2c">Chart</a></li>
        <li><a href="#tabs-3">Settings</a></li>
        <li><a href="#tabs-4">New List Template</a></li>
        <li><a href="#legacy">Legacy List Template</a></li>
        <li><a href="#tabs-5">Custom Scripts</a></li>
    </ul>
    <div class="setupTab" id="tabs-1">
    <?php
    
    if(!empty($Element['_Application'])){
        $Application = $Element['_Application'];
    }else{
        $Application = 'Base';
        if(!empty($_SESSION['activeApp'])){
            $Application = $_SESSION['activeApp'];
        }
    }

   

    ?>
    <div class="list_row1" style="padding: 3px;">
        <table width="100%" cellspacing="2" cellpadding="2" border="0" class="highlight">
            <tbody>
                <tr>
                    <td width="150" align="" class="">
                        <label for="_Application">Application</label>
                    </td>
                    <td class="">
                        <?php
                        echo dt_listApplications($Application);
                        ?><input type="text" value="" style="padding: 5px; display:none;" id="_Application_New" name="Data[Content][_Application]" disabled="disabled">
                        <a id="addAppB" href="#" onclick="return dt_addNewApp();">Add New</a>
                    </td>
                </tr>
            </tbody>
        </table>
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
        echo dais_customfield('text', 'Menu Group', '_ItemGroup', '_ItemGroup', 'list_row2' , $Element['_ItemGroup'] , '');
        $Sel = '';
        if(!empty($Element['Content']['_SetAdminMenu'])) {
            $Sel = 'checked="checked"';
        }
        echo dais_customfield('checkbox', 'Admin Menu Item<div><span class="description">Requires a menu group</span></div>', '_SetAdminMenu', '_SetAdminMenu', 'list_row1' , 1 , $Sel);
        echo dais_customfield('text', 'Menu Label', '_ReportTitle', '_ReportTitle', 'list_row1' , $Element['_interfaceName'] , '');
        echo dais_customfield('text', 'Interface Title', '_ReportDescription', '_ReportDescription', 'list_row2' , $Element['_ReportDescription']  , '');
        echo dais_customfield('text', 'Interface Description', '_ReportExtendedDescription', '_ReportExtendedDescription', 'list_row1' , $Element['_ReportExtendedDescription']  , '');
        $Sel = '';
        if(!empty($Element['Content']['_SetDashboard'])) {
            $Sel = 'checked="checked"';
        }
        echo dais_customfield('checkbox', 'Set as Dashboard Item', '_SetDashboard', '_SetDashboard', 'list_row1' , 1 , $Sel);
        
        if(empty($Element['Content']['_menuAccess'])){
            $Element['Content']['_menuAccess'] = 'read';
        }

        ?>
        <div id="menuPermissions" class="list_row2" style="padding: 3px;">Effective Permission: <select name="Data[Content][_menuAccess]">
                <option value="null" <?php if($Element['Content']['_menuAccess'] == 'read'){ echo 'selected="selected"'; } ?>>All</option>
                <option value="activate_plugins" <?php if($Element['Content']['_menuAccess'] == 'activate_plugins'){ echo 'selected="selected"'; } ?>>Administrator</option>
                <option value="delete_pages" <?php if($Element['Content']['_menuAccess'] == 'delete_pages'){ echo 'selected="selected"'; } ?>>Editor</option>
                <option value="upload_files" <?php if($Element['Content']['_menuAccess'] == 'upload_files'){ echo 'selected="selected"'; } ?>>Author</option>
                <option value="edit_posts" <?php if($Element['Content']['_menuAccess'] == 'edit_posts'){ echo 'selected="selected"'; } ?>>Contributor</option>
                <option value="read" <?php if($Element['Content']['_menuAccess'] == 'read'){ echo 'selected="selected"'; } ?>>Subscriber</option>

            </select>
        </div>
        <?php
        InfoBox('Table Selection');
        ?>
        <?php
        echo df_listTables('_main_table', 'dr_fetchPrimSetup', $Element['Content']['_main_table']);
        EndInfoBox();
        ?>

        <div id="col-container" >
            <?php
            InfoBox('Report Setup');
            //dump($Element);
            if($_GET['page'] != 'Add_New'){
            ?>
            <div style="width:565px;">

                <?php InfoBox('Advanced Field Types'); ?>
                <div class="list_row3"><input type="button" class="button" value="Add Clone Field" onclick="dr_addLinking('<?php echo $Element['Content']['_main_table']; ?>')" /></div>
                <div class="columnSorter" id="drToolBox">
                    <?php
                    //echo df_tableReportSetup($Element['Content']['_main_table'], $Element, false, 'C');
                    ?>
                </div>
                <?php EndInfoBox(); ?>

            </div>
            <?php
            }
            ?>
            <div style="">
                <div id="referenceSetup"></div>
                <div style="overflow:auto;">
                    <table width="100%" border="0" cellspacing="2" cellpadding="2">
                        <tr>
                            <td valign="top" class="columnSorter" id="FieldList_Main"><?php
                                echo df_tableReportSetup($Element['Content']['_main_table'], 'false', $Element);
                                ?></td>
                        </tr>
                    </table>                   
                </div>
            </div>
            <?php
            EndInfoBox();
            ?>
        </div>
        <?php
        InfoBox('Passback Field');
        ?>
        <div style="padding:3px;">
            <input type="button" name="button" id="button" class="button" value="Add Passback Field" onclick="dr_addPassbackField();"/></div>
        <div id="PassBack_FieldSelect">
            <?php            
                echo dr_loadPassbackFields($Element['Content']['_main_table'], $Element['Content']['_ReturnFields'], $Element['Content']);
             ?></div>
        <?php
        EndInfobox();
        InfoBox('Sort Field');
        ?>
        <div id="sortFieldSelect">
        <?php
            if($_GET['page'] != 'Add_New'){
                echo df_loadSortFields($Element['Content']['_main_table'], $Element['Content']['_SortField'], $Element['Content']['_SortDirection']);
            }
        ?>
        </div>
        <?php
        EndInfobox();
        ?>
    </div>
    <?php
    //include(WP_PLUGIN_DIR.'/db-toolkit/data_report/validation.php');
    include(WP_PLUGIN_DIR.'/db-toolkit/data_report/formlayout.php');
    include(WP_PLUGIN_DIR.'/db-toolkit/data_report/process.php');
    include(WP_PLUGIN_DIR.'/db-toolkit/data_report/viewlayout.php');
    include(WP_PLUGIN_DIR.'/db-toolkit/data_report/chartlayout.php');

    ?>
    <div id="tabs-3" class="setupTab">
        <table width="100%" border="0" cellspacing="1" cellpadding="1">
            <tr>
                <td width="50%" valign="top" style="padding-right:15px;"><?php
                    InfoBox('General Settings');
                    $Sel = 'checked="checked"';
                    if(!empty($Element['Content']['_ViewMode'])) {
                        if($Element['Content']['_ViewMode']!='list'){
                            $Sel = '';
                        }
                    }
                    echo dais_customfield('radio', 'List Mode', '_ViewMode', '_ViewMode_View', 'list_row2' , 'list' , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_ViewMode'])) {
                        if($Element['Content']['_ViewMode']=='view'){
                            $Sel = 'checked="checked"';
                        }
                    }
                    echo dais_customfield('radio', 'View Mode', '_ViewMode', '_ViewMode_View', 'list_row2' , 'view' , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_ViewMode'])) {
                        if($Element['Content']['_ViewMode']=='form'){
                            $Sel = 'checked="checked"';
                        }
                    }
                    echo dais_customfield('radio', 'Form Mode', '_ViewMode', '_ViewMode_Form', 'list_row1' , 'form' , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_ViewMode'])) {
                        if($Element['Content']['_ViewMode']=='search'){
                            $Sel = 'checked="checked"';
                        }
                    }
                    echo dais_customfield('radio', 'Search Mode', '_ViewMode', '_ViewMode_Search', 'list_row2' , 'search' , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_HideFrame'])) {
                        $Sel = 'checked="checked"';
                    }

                    echo dais_customfield('checkbox', 'Hide Frame', '_HideFrame', '_HideFrame', 'list_row1' , 1 , $Sel);
                    echo dais_customfield('text', 'New Item Title', '_New_Item_Title', '_New_Item_Title', 'list_row1' , $Element['Content']['_New_Item_Title'], '');
                    $Sel = '';
                    if(!empty($Element['Content']['_New_Item_Hide'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Hide new item button', '_New_Item_Hide', '_New_Item_Hide', 'list_row2' , 1, $Sel);
                    echo dais_customfield('text', 'Items Per Page', '_Items_Per_Page', '_Items_Per_Page', 'list_row1' , $Element['Content']['_Items_Per_Page'], '');
                    echo dais_customfield('text', 'Auto Polling Rate (seconds)', '_autoPolling', '_autoPolling', 'list_row2' , $Element['Content']['_autoPolling'], '');
                    EndInfoBox();
                    InfoBox('Tool Bar Settings');
                    $Sel = '';
                    if(!empty($Element['Content']['_Hide_Toolbar'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Hide Tool Bar', '_Hide_Toolbar', '_Hide_Toolbar', 'list_row1' , 1 , $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Filters'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Filters', '_Show_Filters', '_Show_Filters', 'list_row2' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Hide_FilterLock'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Hide Filter Lock', '_Hide_FilterLock', '_Hide_FilterLock', 'list_row2' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_toggle_Filters'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Autohide Filters', '_toggle_Filters', '_toggle_Filters', 'list_row1' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_KeywordFilters'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Keyword Filter', '_Show_KeywordFilters', '_Show_Filters', 'list_row2' , 1 , $Sel);
                    echo dais_customfield('text', 'Keyword Search Title', '_Keyword_Title', '_Keyword_Title', 'list_row1' , $Element['Content']['_Keyword_Title'] , '');
                    $Sel = '';
                    if(!empty($Element['Content']['_showReload'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Reload Button', '_showReload', '_showReload', 'list_row1' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Export'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Export Button', '_Show_Export', '_Show_Export', 'list_row2' , 1, $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Import'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Import Button', '_Show_Import', '_Show_Import', 'list_row1' , 1, $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Plugins'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Plugins', '_Show_Plugins', '_Show_Plugins', 'list_row1' , 1 , $Sel);

                    echo '<div style="padding:3px;" class="list_row2"><strong>Export Orientation: </strong>';
                    echo '<select name="Data[Content][_orientation]" >';
                    $Sel = '';
                    if($Element['Content']['_orientation'] == 'P') {
                        $Sel = 'selected="selected"';
                    }
                    echo '<option value="P" '.$Sel.'>Portrait</option>';
                    $Sel = '';
                    if($Element['Content']['_orientation'] == 'L') {
                        $Sel = 'selected="selected"';
                    }
                    echo '<option value="L" '.$Sel.'>Landscape</option>';
                    echo '</select>';
                    echo '</div>';

                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Select'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Select Options', '_Show_Select', '_Show_Select', 'list_row2' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Delete'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Delete Options', '_Show_Delete', '_Show_Delete', 'list_row1' , 1, $Sel);
                    EndInfoBox();
                    InfoBox('List Settings');
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Edit'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show and Enable Edit Action', '_Show_Edit', '_Show_Edit', 'list_row2' , 1, $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_View'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show View Action', '_Show_View', '_Show_View', 'list_row1' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Delete_action'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Delete Action', '_Show_Delete_action', '_Show_Delete_action', 'list_row1' , 1, $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_popup'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Inline Actions', '_Show_popup', '_Show_popup', 'list_row2' , 1 , $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_Show_Footer'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Footer Bar', '_Show_Footer', '_Show_Footer', 'list_row1' , 1, $Sel);
                    EndInfoBox();
                    InfoBox('Notification & Buttons');
                    echo dais_customfield('text', 'Insert Success Text', '_InsertSuccess', '_InsertSuccess', 'list_row1' , $Element['Content']['_InsertSuccess'], '');
                    echo dais_customfield('text', 'Update Success Text', '_UpdateSuccess', '_UpdateSuccess', 'list_row2' , $Element['Content']['_UpdateSuccess'], '');
                    echo dais_customfield('text', 'Insert Fail Text', '_InsertFail', '_InsertFail', 'list_row1' , $Element['Content']['_InsertFail'], '');
                    echo dais_customfield('text', 'Update Fail Text', '_UpdateFail', '_UpdateFail', 'list_row2' , $Element['Content']['_UpdateFail'], '');
                    echo dais_customfield('text', 'Submit Button Text', '_SubmitButtonText', '_SubmitButtonText', 'list_row1' , $Element['Content']['_SubmitButtonText'], '');
                    echo dais_customfield('text', 'Update Button Text', '_UpdateButtonText', '_UpdateButtonText', 'list_row2' , $Element['Content']['_UpdateButtonText'], '');
                    echo dais_customfield('text', 'Edit Form Title', '_EditFormText', '_EditFormText', 'list_row1' , $Element['Content']['_EditFormText'], '');
                    echo dais_customfield('text', 'View Form Title', '_ViewFormText', '_ViewFormText', 'list_row1' , $Element['Content']['_ViewFormText'], '');
                    echo dais_customfield('text', 'No results text', '_NoResultsText', '_NoResultsText', 'list_row1' , $Element['Content']['_NoResultsText'], '');

                    $Sel = '';
                    if(!empty($Element['Content']['_NotificationsOff'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Disable Notifications', '_NotificationsOff', '_NotificationsOff', 'list_row2' , 1, $Sel);
                    $Sel = '';
                    if(!empty($Element['Content']['_ShowReset'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Reset Button', '_ShowReset', '_ShowReset', 'list_row1' , 1, $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_SubmitAlignment'])) {
                        switch($Element['Content']['_SubmitAlignment']) {
                            case 'left':
                                $Sel = 'left';
                                break;
                            case 'center':
                                $Sel = 'center';
                                break;
                            case 'right':
                                $Sel = 'right';
                                break;

                        }

                    }
                    echo '<div style="padding:3px;" class="list_row1"><strong>Button Alignment: </strong>';
                    echo '<select name="Data[Content][_SubmitAlignment]" >';
                    echo '<option value="left" ';
                    if($Sel == 'left') {
                        echo 'selected="selected"';
                    };
                    echo '>Left</option>';
                    echo '<option value="center" ';
                    if($Sel == 'center') {
                        echo 'selected="selected"';
                    };
                    echo '>Center</option>';
                    echo '<option value="right" ';
                    if($Sel == 'right') {
                        echo 'selected="selected"';
                    };
                    echo '>Right</option>';
                    echo '</select>';
                    echo '</div>';
                    EndInfoBox();
                    InfoBox('API and Audit');
                    $Sel = '';
                    if(!empty($Element['Content']['_EnableAudit'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Enable Auditing', '_EnableAudit', '_EnableAudit', 'list_row2' , 1, $Sel);
                    echo dais_customfield('text', 'API Key Seed', '_APISeed', '_APISeed', 'list_row1' , $Element['Content']['_APISeed'] , '');
                    EndInfoBox();

                    ?></td>
                <td width="50%" valign="top">
                    <?php
                    InfoBox('Redirects');
                    ?>
                    <div id="redirectTabs" class="dbtools_tabs">
                        <ul class="content-box-tabs">
                            <li><a href="#publicRedirect">Public</a></li>
                            <li><a href="#adminRedirect">Admin</a></li>
                        </ul>
                        <div class="setupTab" id="publicRedirect">
                            <?php
                            InfoBox('Page');
                            $Sel = '';
                            if(empty($Element['Content']['_ItemViewPage'])) {
                                $Sel = 'checked="checked"';
                            }
                            echo dais_customfield('radio', 'No Redirect', '_ItemViewPage', '_ItemViewPage', 'list_row1' , 0, $Sel);
                            $PageList[] = $Element['Content']['_ItemViewPage'];
                            echo dais_page_selector('s', $PageList, false, '_ItemViewPage');
                            EndInfoBox();
                            ?>
                        </div>
                        <div class="setupTab" id="adminRedirect">
                            <?php
                            InfoBox('Interface');

                            $Sel = '';
                            if(empty($Element['Content']['_ItemViewInterface'])) {
                                $Sel = 'checked="checked"';
                            }
                            echo dais_customfield('radio', 'No Redirect', '_ItemViewInterface', '_ItemViewInterface', 'list_row1' , 0, $Sel);
                            global $wpdb;
                            $Interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
                            if(!empty($Interfaces)) {
                                $Groups = array();
                                foreach($Interfaces as $Interface) {                                    
                                        $option = get_option($Interface['option_name']);
                                        if(empty($option['_ItemGroup'])){
                                            $option['_ItemGroup'] = '__Ungrouped';
                                        }
                                        $Groups[$option['_ItemGroup']][] = $option;
                                    
                                }
                                ksort($Groups);
                                foreach($Groups as $Group=>$Interfaces){
                                    if($Group == '__Ungrouped'){
                                        $Group = '<em>Ungrouped</em>';
                                    }
                                    echo '<div style="padding:5px 3px 3px;"><strong>'.$Group.'</strong></div>';
                                    foreach($Interfaces as $Interface){
                                        
                                        $Dis = '';
                                        $Cls = '';
                                        $Sel = '';
                                        if($Interface['ID'] == $_GET['interface']){
                                            $Dis = 'disabled="disabled"';
                                            $Cls = 'highlight';
                                        }
                                       if($Interface['ID'] == $Element['Content']['_ItemViewInterface']){
                                            $Sel = 'checked="checked"';
                                       }
                                      
                                        //echo dais_customfield('radio', $Interface['_interfaceName'], '_ItemViewInterface', '_ItemViewInterface', 'list_row1' , 0, $Sel);
                                        echo '<div class="list_row4 '.$Cls.'" style="padding: 3px 3px 3px 12px;">';
                                        echo '<label for="_ItemViewInterface_'.$Interface['ID'].'">';
                                            echo '<img width="16" height="16" border="0" align="absmiddle" src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/table.png">';
                                            echo '<input type="radio" value="'.$Interface['ID'].'" id="_ItemViewInterface_'.$Interface['ID'].'" name="Data[Content][_ItemViewInterface]" '.$Sel.' '.$Dis.'> '.$Interface['_interfaceName'].'<div style="padding: 3px 3px 3px 18px;" class="description">'.$Interface['_ReportDescription'].'</div></label>';
                                        echo '</div>';
                                    }
                                }
                            }

                            EndInfoBox();
                            ?>

                        </div>
                    </div>

                    <?php
                    EndInfoBox();

                    ?>
                </td>

                <td width="50%" valign="top"></td>
            </tr>
        </table>
    </div>
    <?php
        include(WP_PLUGIN_DIR.'/db-toolkit/data_report/listtemplate.php');
    ?>









    <?php
    // Legacy Templates
    ?>
<div id="legacy">
    <h3>This tab will be removed in 0.3.0.0 So start converting to the the new list templates.</h3>

        <?php
        $Sel = '';
if(!empty($Element['Content']['_UseListViewTemplate'])) {
                $Sel = 'checked="checked"';
            }
            echo dais_customfield('checkbox', 'Use Template', '_UseListViewTemplate', '_UseListViewTemplate', 'list_row1' , 1, $Sel);

            echo dais_customfield('textarea', 'Pre Header', '_ListViewTemplatePreHeader', '_ListViewTemplatePreHeader', 'list_row2' , $Element['Content']['_ListViewTemplatePreHeader'], '');
            echo dais_customfield('textarea', 'Header', '_ListViewTemplateHeader', '_ListViewTemplateHeader', 'list_row1' , $Element['Content']['_ListViewTemplateHeader'], '');
            echo dais_customfield('textarea', 'Post Header', '_ListViewTemplatePostHeader', '_ListViewTemplatePostHeader', 'list_row2' , $Element['Content']['_ListViewTemplatePostHeader'], '');

            echo dais_customfield('textarea', 'Content Wrapper Start', '_ListViewTemplateContentWrapperStart', '_ListViewTemplateContentWrapperStart', 'list_row2' , $Element['Content']['_ListViewTemplateContentWrapperStart'], '');
            echo dais_customfield('textarea', 'PreContent', '_ListViewTemplatePreContent', '_ListViewTemplatePreContent', 'list_row2' , $Element['Content']['_ListViewTemplatePreContent'], '');
            echo dais_customfield('textarea', 'Content', '_ListViewTemplateContent', '_ListViewTemplateContent', 'list_row2' , $Element['Content']['_ListViewTemplateContent'], '');
InfoBox('Useable Keys');
?>
        <pre>
{{_ViewEdit}}	: View and Edit Icons
{{_ViewLink}}	: View Item Link
{{_RowClass}}	: Row Class
{{_SelectedClass}}: Highlight if Selected
{{_RowIndex}}	: Row Index
{{_UID}}	: Unique Row ID
{{_PageID}}	: Page ID
{{_PageName}}	: Page Name
{{_EID}}	: Element ID
{{<i><b>Fieldname</b></i>}}	: Field Data
{{_<i>Fieldname</i>_name}}	: Field Name
{{_return_<i><b>Fieldname</b></i>}}	: Return Field
        </pre>
        to enable selection and deleting:
        id="row_{{_EID}}_{{_RowIndex}}"  ref="{{_return_<em><strong>Fieldname</strong></em>}} highlight" class="itemRow_{{_EID}}  report_entry"

<?php
EndInfoBox();
echo dais_customfield('textarea', 'PostContent', '_ListViewTemplatePostContent', '_ListViewTemplatePostContent', 'list_row2' , $Element['Content']['_ListViewTemplatePostContent'], '');
echo dais_customfield('textarea', 'Content Wrapper End', '_ListViewTemplateContentWrapperEnd', '_ListViewTemplateContentWrapperEnd', 'list_row2' , $Element['Content']['_ListViewTemplateContentWrapperEnd'], '');
echo dais_customfield('textarea', 'Pre Footer', '_ListViewTemplatePreFooter', '_ListViewTemplatePreFooter', 'list_row2' , $Element['Content']['_ListViewTemplatePreFooter'], '');
echo dais_customfield('textarea', 'Footer', '_ListViewTemplateFooter', '_ListViewTemplateFooter', 'list_row1' , $Element['Content']['_ListViewTemplateFooter'], '');
echo dais_customfield('textarea', 'Post Footer', '_ListViewTemplatePostFooter', '_ListViewTemplatePostFooter', 'list_row2' , $Element['Content']['_ListViewTemplatePostFooter'], '');



?>
    </div>
















    <div id="tabs-5" class="setupTab">
        <?php
        echo dais_customfield('textarea', 'Footer Scripts', '_customFooterJavaScript', '_customFooterJavaScript', 'list_row1' , $Element['Content']['_customFooterJavaScript'], 'style="height:300px;"');
        ?>
        <a href="#" onclick="dt_addLibrary(); return false;">Add Custom JS Library</a> | <a href="#" onclick="dt_addCSSLibrary(); return false;">Add Custom Style Sheet</a>
        <div id="addonLibrary">
            <?php
            if(empty($Element['Content']['_customJSLibrary'])){
                echo dais_addJSLibrary();
            }else{
                foreach($Element['Content']['_customJSLibrary'] as $jsScript){
                    if(!empty($jsScript['source'])){
                        echo dais_addJSLibrary($jsScript['source'], $jsScript['location']);
                    }

                }
            }
            ?>
        </div>
        <div id="addonCSSLibrary">
            <?php
            if(empty($Element['Content']['_customCSSSource'])){
                echo dais_addCSSLibrary();
            }else{
                foreach($Element['Content']['_customCSSSource'] as $cssScript){
                    if(!empty($cssScript['source'])){
                        echo dais_addCSSLibrary($cssScript['source']);
                    }
                }
            }
            ?>
        </div>
        
    </div>
</div><br />
<?php
echo dais_standardSetupbuttons($Element);
?>
<?php
ob_start();
?>
		jQuery('select').each(function(){
			if(this.value == 'index_hide' || this.value == 'noindex_hide'){
				jQuery(this).parent().parent().fadeTo(500, 0.5);
			}
			if(this.value == 'index_show' || this.value == 'noindex_show'){
				jQuery(this).parent().parent().fadeTo(500, 1);				
			}
		});

		jQuery("#dbtools_tabs").tabs();
jQuery("#redirectTabs").tabs();

		jQuery('select').live('change', function(){
			if(this.value == 'index_hide' || this.value == 'noindex_hide'){
				jQuery(this).parent().parent().fadeTo(500, 0.5);
			}
			if(this.value == 'index_show' || this.value == 'noindex_show'){
				jQuery(this).parent().parent().fadeTo(500, 1);				
			}
		});
<?php
$_SESSION['adminscripts'] .= ob_get_clean();
?>