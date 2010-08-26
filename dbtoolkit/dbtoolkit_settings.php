<?php
//vardump($_POST);
if(!empty($_POST['Data'])){
        $_POST = stripslashes_deep($_POST);
            update_option('_dbtoolkit_defaultinterface', serialize($_POST['Data']['Content']));
	}
        
   $defaults = get_option('_dbtoolkit_defaultinterface');
   $Element['Content'] = unserialize($defaults);

?>


<div class="wrap">
    <div id="icon-tools" class="icon32"></div><h2><?php _e('Database Toolkit Default Settings'); ?></h2>
    Setup defaults for new Interfaces.
    <br class="clear" /><br />
    <?php
    if(!empty($_POST['Data'])) {
        echo '<div class="updated fade" id="message"><p><strong>Settings Saved.</strong></p></div>';
    }
    ?>
    <form name="saveSettings" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr>
                    <td width="50%" valign="top"><?php
                        InfoBox('General Settings');
                        echo dais_customfield('text', 'Chat Height (px)', '_chartHeight', '_chartHeight', 'list_row1' , $Element['Content']['_chartHeight'], '');

                        $Sel = '';
                        if(!empty($Element['Content']['_ViewMode'])) {
                            $Sel = 'checked="checked"';
                        }
                        echo dais_customfield('checkbox', 'View Mode', '_ViewMode', '_ViewMode', 'list_row2' , 1 , $Sel);
                        $Sel = '';
                        if(!empty($Element['Content']['_FormMode'])) {
                            $Sel = 'checked="checked"';
                        }
                        echo dais_customfield('checkbox', 'Form Mode', '_FormMode', '_FormMode', 'list_row1' , 1 , $Sel);
                        $Sel = '';
                        if(!empty($Element['Content']['_SearchMode'])) {
                            $Sel = 'checked="checked"';
                        }
                        echo dais_customfield('checkbox', 'Search Mode', '_SearchMode', '_SearchMode', 'list_row2' , 1 , $Sel);
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
                        echo dais_customfield('checkbox', 'Popup Links', '_Show_popup', '_Show_popup', 'list_row2' , 1 , $Sel);
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

                        ?></td>

                </tr>
            </table>

        <input type="submit" class="button-primary" value="Save Settings" />


    </form>

</div>