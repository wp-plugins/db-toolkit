<input type="hidden" name="Data[Content][_FormLayout]" cols="50" rows="10" id="_FormLayout" />
<div id="dbtools_tabs" class="dbtools_tabs">
    <ul class="content-box-tabs">
        <li><a href="#tabs-1">Setup</a></li>
        <li><a href="#tabs-2">Layout</a></li>
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

        echo dais_customfield('text', 'Menu Group', '_MenuGroup', '_MenuGroup', 'list_row2' , $Element['_MenuGroup'] , '');
        $Sel = '';
        if(!empty($Element['Content']['_SetAdminMenu'])) {
            $Sel = 'checked="checked"';
        }
        echo dais_customfield('checkbox', 'Admin Menu Item<div><span class="description">Requires a menu group</span></div>', '_SetAdminMenu', '_SetAdminMenu', 'list_row1' , 1 , $Sel);
        echo dais_customfield('text', 'Menu Label', '_MenuLable', '_MenuLable', 'list_row1' , $Element['_MenuLable'] , '');
        echo dais_customfield('text', 'Cluster Title', '_ClusterTitle', '_ClusterTitle', 'list_row2' , $Element['_ClusterTitle']  , '');
        echo dais_customfield('text', 'Cluster Description', '_ClusterDescription', '_ClusterDescription', 'list_row1' , $Element['_ClusterDescription']  , '');
        $Sel = '';
        if(!empty($Element['Content']['_DashboardItem'])) {
            $Sel = 'checked="checked"';
        }
        echo dais_customfield('checkbox', 'Set as Dashboard Item', '_DashboardItem', '_DashboardItem', 'list_row1' , 1 , $Sel);
        
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
    </div>
    <div class="setupTab" id="tabs-2">
        <?php
            include(WP_PLUGIN_DIR.'/db-toolkit/data_report/clusterlayout.php');
        ?>
    </div>
</div>
<?php
echo dais_standardSetupbuttons($Element);
?>
<?php
ob_start();
?>
    jQuery("#dbtools_tabs").tabs();
<?php
$_SESSION['adminscripts'] .= ob_get_clean();
?>