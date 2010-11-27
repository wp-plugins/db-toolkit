<?php

if(empty($_SESSION['dataform']['OutScripts'])) {
    $_SESSION['dataform']['OutScripts'] = '';
}


$FieldTypeFunctions = loadFolderContents(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes');

foreach($FieldTypeFunctions[0] as $Type) {
    if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/functions.php')) {
        include_once(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/functions.php');
    }
}


if(is_admin()) {
    // edit mode check
    function df_controlFunc($Field, $Table, $Func) {
        if(function_exists($Func)) {
            $Return = '<div class="widefat" id="'.$Field.'_configPanel" style="text-align:left;">';
            $Return .= '<h3>Fieldtype Config</h3><div class="admin_config_panel">';
            $Return .= $Func($Field, $Table);
            $Return .= '</div></div>';
            $Return .= '<input type="button" class="buttons" value="Setup" onclick="toggle(\''.$Field.'_configPanel\');" />';
            return $Return;
        }
        return 'No Config Options Available';
    }
    function df_listTables($TableReference, $JFunc = 'alert', $Default = false, $Req = false) {
        global $wpdb;

        $Data = $wpdb->get_results( "SHOW TABLES", ARRAY_N);


        //$res = mysql_list_tables($_SESSION['settings'][$_SESSION['key']]['Database']);
        $Return = '<select name="Data[Content]['.$TableReference.']" id="'.$TableReference.'" onchange="'.$JFunc.'(\''.$TableReference.'\');">';
        $Return .= '<option value="">'.$Value.'</option>';
        foreach($Data as $Tables) {
            //vardump($Tables);
            $Value = $Tables[0];
            $Sel = '';
            if($Default == $Value) {
                $Sel = 'selected="selected"';
            }
            //if(substr($Value, 0, 5) != 'dais_'){
            $List[] = $Value;
            $Return .= '<option value="'.$Value.'" '.$Sel.'>'.$Value.'</option>';
            //}

        }
        $Return .= '</select>';
        return $Return;
    }

    function df_tableFormSetup($Table, $Config = false) {
        $Defaults = false;
        if(!empty($Config)) {
            $Defaults = $Config['Content']['_Field'];
        }
        include('configs/upc.cfg.php');
        ob_start();
        echo '<span class="captions">Required</span>';
        $result = mysql_query("SHOW COLUMNS FROM ".$Table);
        if (mysql_num_rows($result) > 0) {
            $Row = 'list_row2';
            while ($row = mysql_fetch_assoc($result)) {
                $FieldSet = explode('_', $Defaults[$row['Field']]);
                $Row = dais_rowSwitch($Row);
                $Field = $row['Field'];
                $name = df_parseCamelCase($Field);
                //REquired Check
                $Sel = '';
                if(!empty($Config['Content']['_Required'][$Field])) {
                    $Sel = 'checked="checked"';
                }
                echo '<div id="Field_'.$Field.'" class="list_row1 table_sorter" style="padding:3px;"><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/arrow_out.png" align="absmiddle" class="OrderSorter" /><input type="checkbox" name="Data[Content][_Required]['.$Field.']" id="required_'.$Field.'" '.$Sel.' /> '.ucwords($name).' : '.df_fieldTypes($Field, $Table, $row, $Defaults).'<span class="list_row3" id="ExtraSetting_'.$Field.'">';
                if(!empty($FieldSet[1])) {
                    if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php')) {
                        include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php');
                        if($FieldTypes[$FieldSet[1]]['func'] != 'null') {
                            echo $FieldTypes[$FieldSet[1]]['func']($Field, $Table, $Config);
                        }
                    }
                }
                echo '</span></div>';
            }
        }
        return ob_get_clean();
    }


    function df_loadReturnFields($Table, $Default = false) {
        $Return = df_ListFields($Table, $Default, '_ReturnField');
        return $Return;
    }
    function df_loadSortFields($Table, $Default = false, $Dir = 'DESC') {
        $Return = df_ListFields($Table, $Default, '_SortField');
        $Return .= '<select name="Data[Content][_SortDirection]">';
        $Sel = '';
        if($Dir == 'ASC') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="ASC" '.$Sel.'>Ascending</option>';
        $Sel = '';
        if($Dir == 'DESC') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="DESC" '.$Sel.'>Descending</option>';
        $Return .= '</select>';
        return $Return;
    }

    function df_ListFields($Table, $Default, $Name) {
        if(empty($Table)){
            return;
        }        
        $result = mysql_query("SHOW COLUMNS FROM ".$Table);
        $Return = '<select name="Data[Content]['.$Name.']" id="Return_'.$Table.'">';
        //$Return .= '<option value="false">None</option>';
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $Sel = '';
                if($Default == $row['Field']) {
                    $Sel = 'selected="selected"';
                }
                $Return .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
            }
        }
        if(!empty($_GET['interface'])) {
            $Element = getElement($_GET['interface']);
            $Config = &$Element['Content'];
            if(!empty ($Config['_CloneField'])) {
                $Return .= '<optgroup label="Cloned Fields">';
                foreach ($Config['_CloneField'] as $Field=>$Array) {
                    $Sel = '';
                    if($Default == $Field) {
                        $Sel = 'selected="selected"';
                    }
                    $Return .= '<option value="'.$Field.'" '.$Sel.'>'.$Config['_FieldTitle'][$Field].'</option>';
                }
            }

        }
        $Return .= '</select>';

        return $Return;
    }

    function df_fieldTypes($Field, $Table, $c, $Defaults) {
        // $c = data types.
        
        $Return = '';
        $Type = explode('_', $Defaults[$Field]);
        //$Return = $Type[0];
        
        if($Defaults[$Field] == 'hidden' || empty($Defaults[$Field])){
            $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/wand.png';
            $Return .= '<span class="button" id="fieldTypeButton_'.$Field.'" onclick="bf_loadFieldTypePanel(\''.$Field.'_FieldTypePanel\');"><span style="background: url('.$Icon.') left center no-repeat; padding:5px 18px;"> Auto</span></span> <span style="display:none;" id="'.$Field.'_FieldTypePanel_status"><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/loading.gif" align="absmiddle" /></span>';
            $Return .= '<input type="hidden" name="Data[Content][_Field]['.$Field.']" id="Fieldtype_'.$Field.'" value="'.$Defaults[$Field].'" />';
            return $Return;
        }
        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/conf.php')) {
            $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtype.png';
            if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/icon.png')) {
                $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/icon.png';
            }

            include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/conf.php');
            //vardump($FieldTypes[$Type[0]]);
            $Return .= '<span class="button" id="fieldTypeButton_'.$Field.'" onclick="bf_loadFieldTypePanel(\''.$Field.'_FieldTypePanel\');"><span style="background: url('.$Icon.') left center no-repeat; padding:5px 18px;"> '.$FieldTypes[$Type[1]]['name'].'</span></span> <span style="display:none;" id="'.$Field.'_FieldTypePanel_status"><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/loading.gif" align="absmiddle" /></span>';
            $Return .= '<input type="hidden" name="Data[Content][_Field]['.$Field.']" id="Fieldtype_'.$Field.'" value="'.$Defaults[$Field].'" />';

        }
        return $Return;
    }


    function df_buildFieldTypesMenu($Field){
    

        $Types = loadFolderContents(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes');
        //$Return = '<select name="Data[Content][_Field]['.$Field.']" id="Fieldtype_'.$Field.'" >';
        //$Return = '<select name="Data[Content][_Field]['.$Field.']" id="Fieldtype_'.$Field.'" >'
        //$Return .= '<optgroup label="Auto Increment">';
        //$Return .= '<option value="hidden" onclick="df_noOptions(\''.$Field.'\');">Auto</option>';
        //$Return .= '</optgroup>';
        $Return = '';
        
        $Return .= '<div style="width:33.333333%; float:left;">';
        $Return .= '<div style="padding:3px;" class="highlight">Default / Auto Increment</div>';
            $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/wand.png';
            $Return .= '<div style="padding:3px;">';
            $Return .= '<a href="#" id="'.$Field.'_hidden" onclick="return df_setOptions(\''.$Field.'\', \'null\', \'hidden\');"><span style="background: url('.$Icon.') left center no-repeat; padding:5px 18px;"> Auto / Default</span></a>';
            $Return .= '</div>';
        $Return .= '</div>';

        foreach($Types[0] as $Type) {
            
            if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/conf.php')) {
                include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/conf.php');
                //$Return .= '<optgroup label="'.$FieldTypeTitle.'">';
                $CIcon = WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtype.png';
                if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/icon.png')) {
                    $CIcon = WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/icon.png';
                }
                $Return .= '<div style="width:33.333333%; float:left;">';
                $Return .= '<div style="padding:3px;" class="highlight"><span style="background: url('.$CIcon.') left center no-repeat; padding:5px 20px;"> '.$FieldTypeTitle.'</span></div>';
                foreach($FieldTypes as $Key=>$FieldSet) {
                    //$Return .= $FieldSet['name'].'<br />';
                    $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/insert.png';
                    if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/'.$Type[1].'_'.$Key.'.png')) {
                        $Icon = WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/'.$Type[1].'_'.$Key.'.png';
                    }
                    $Return .= '<div style="padding:4px;">';
                    $Return .= '<a href="#" id="'.$Field.'_'.$Type[1].'_'.$Key.'" onclick="return df_setOptions(\''.$Field.'\', \''.$FieldSet['func'].'\', \''.$Type[1].'_'.$Key.'\');"><span style="background: url('.$Icon.') left center no-repeat; padding:5px 20px;"> '.$FieldSet['name'].'</span></a>';
                    $Return .= '</div>';

                }
                $Return .= '</div>';
            }
        }
        $Return .= '<div style="clear:both;"></div>';
        //$Return .= '</select>';

        return $Return;
    }

    // End Edit mode check
}

function df_processAjaxForm($Input){

    ob_start();
    parse_str($Input, $Data);
    //vardump($Data);
    if(!empty($Data['processKey'])) {
        $Data = stripslashes_deep($Data);
        if($Data['processKey'] == $_SESSION['processKey']) {
            //unset($_SESSION['processKey']);



            if(!empty($Data['dr_update'])) {
                $EID = $Data['dataForm']['EID'];
                $Setup = getelement($EID);
                unset($Data['dataForm']['dr_update']);
                unset($Data['dataForm']['EID']);
                $Return = df_processUpdate($Data['dataForm'], $EID);
                dr_trackActivity('Update', $EID, $Return['Value']);
                if(empty($Setup['Content']['_NotificationsOff'])) {
                    $_SESSION['DF_Post'][] = $Return['Message'];
                }
                $_SESSION['DF_Post_returnID'] = $Return['Value'];
                $_SESSION['DF_Post_EID'] = $EID;
            }else {
                foreach($Data['dataForm'] as $EID=>$Data) {
                    $Return = df_processInsert($EID, $Data);
                    // Track Activity
                    dr_trackActivity('Insert', $EID, $Return['Value']);
                    $Setup = getelement($EID);
                    if(empty($Setup['Content']['_NotificationsOff'])) {
                        $_SESSION['dataform']['OutScripts'] .="
                            df_dialog('".$Return['Message']."');
                        ";
                    }
                }
            }
        }
    }
    //vardump($Return);
    return $Return;

    //return ob_get_clean();
}


function is_uppercase($Char) {
    $Upper = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    //$Lower = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    if(in_array($Char, $Upper)) {
        return true;
    }
    return false;
}

function df_buildQuickCaptureForm($EID) {
    $Data = getelement($EID);
    //$Return = '<h2>'.$Data['Content']['_New_Item_Title'].'</h2>';
    $Data['_ActiveProcess'] = 'insert';
    $Out = df_BuildCaptureForm($Data);
    $Out['title'] = $Data['Content']['_New_Item_Title'];
    //$Return = df_BuildCaptureForm($Data);
    return $Out;//$Return;
}
function df_buildtabsindex($Setup, $Config){
    // serach for main tabs
    //vardump($Config);
    $Return = '';
    foreach($Setup as $Row=>$Set){
        //vardump($Set);
        if(empty($Set['col2'])){
            if(!empty($Set['col1']['Fields'])) {
                foreach( $Set['col1']['Fields'] as $Column) {
                    foreach($Column as $Item) {
                        if(substr($Item,0,4) == '_tab') {
                            $maintabs[$Item] = $Config['_Tab'][$Item]['Title'];
                        }
                    }
                }
            }
        }
    }
    if(!empty($maintabs)){
        $tab_id = uniqid('tab_');
        $Return .= '<div id="'.$tab_id.'">';
        $Return .= '<ul class="content-box-tabs">';
        foreach($maintabs as $id=>$lable){
            $Return .= '<li><a href="#'.$id.'">'.$lable.'</a></li>';
        }
        $Return .= '</ul>';
        $Out['html'] = $Return;
        $Out['tabs'] = $maintabs;

        $_SESSION['dataform']['OutScripts'] .="
            jQuery('#".$tab_id."').tabs();
        ";
        return $Out;
    }
    
return false;
}
function df_checkTabState($ColSet, $Tabs){
    
    foreach($ColSet['Fields'] as $Field){        
        if(!empty($Tabs['tabs'][$Field['Name']])){
            
            return $Field['Name'];
        }
    }
return false;
    
}
function df_BuildCaptureForm($Element, $Defaults = false, $ViewOnly = false) {

    $Config = $Element['Content'];
    //vardump($Config);
    if(!empty($Config['_FormLayout'])) {
        parse_str($Config['_FormLayout'], $Columns);
        if(empty($Columns['FieldList_left'])) {
            //	unset($Columns);
            //	unset($Config['_FormLayout']);
        }
    }
    if(!empty($Config['_Edit_Element_Reference'])) {
        $Element = getelement($Config['_Edit_Element_Reference']);
        $Config = $Element['Content'];
    }
    /*
	if($Out = array_search(array('userbase', 'userbasegroup'), $Data['_Field'])){
		$AdminGroup = array($Data['_AdminGroup'][$Out]);
		if(!userbase_authenticate(false)){
			userbase_authenticate(true);
			return;
		}
	}
    */
    if(!empty($Defaults) && !empty($Config['_Show_Edit'])) {
        $EditID = $Defaults;
        $defRes = mysql_query("SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$EditID."';");
        if(mysql_num_rows($defRes) == 0) {
            return "Invalid Entry";
        }
        $Defaults = mysql_fetch_assoc($defRes);
    }else {
        unset($Defaults);
    }
    $Row = 'list_row2';
    $formID = rand(0,999);
    $SubmitURL = getdocument($Element['ParentDocument']);
    if(!empty($Config['_FormMode']) && !empty($Config['_ItemViewPage'])) {
        $SubmitURL = getdocument($Config['_ItemViewPage']);
    }
    $Hidden = '<form enctype="multipart/form-data" method="post" action="'.$_SERVER['REQUEST_URI'].'" class="formular" id="data_form_'.$Element['ID'].'" >';
    if(empty($_SESSION['processKey'])) {
        $_SESSION['processKey'] = uniqid(rand(100, 999).'_processKey_');
    }
    $Hidden .= '<input type="hidden" name="processKey" id="processKey" value="'.$_SESSION['processKey'].'" />';
    $Hidden .= '<input type="hidden" name="dataForm['.$Element['ID'].'][_control_'.uniqid().']" id="processKey" value="'.$_SESSION['processKey'].'" />';


    /// attempt to place in here
    ### DONT FORGET VALIDATION!!!!
    if(!empty($Config['_Required'])) {
        $_SESSION['dataform']['OutScripts'] .= "
			jQuery('#data_form_".$Element['ID']."').validationEngine({
			  success :  false,
			  failure : function() {}
			 });

		";
    }
    if(!empty($Config['_gridLayout'])) {
        parse_str($Config['_gridLayout'], $Layout);
        //dump($Layout);
        $Form = '';
        $CurrRow = '0';
        $CurrCol = '0';
        $Index = 0;
        foreach($Layout as $LayoutField => $Grid) {
            $Grid = explode('_', $Grid);
            if(substr($Grid[0],0,3) == 'row') {
                $Setup[$Grid[0]][$Grid[1]]['Fields'][]['Name'] = str_replace('Field_','',$LayoutField);
                $Setup[$Grid[0]][$Grid[1]]['Row'] = $Grid[2];
                $Index++;
            }else {
                $Setup[$Grid[0]][] = $LayoutField;
            }
        }
        if(!empty($Config['_EditFormText'])) {
            $newTitle = $Config['_EditFormText'];
        }
        $rowindex = 1;
        $Tabs = df_buildtabsindex($Setup, $Config);
        if(is_array($Tabs)){
            $Form .= $Tabs['html'];
        }
        foreach($Setup as $Row=>$ColSets) {
            //'single';
            //insert full row tab if row is only column.
            //if (!empty)
            if(substr($Row,0,3) == 'row') {
                if(empty($ColSets['col2'])){
                    if($tabid = df_checkTabState($ColSets['col1'], $Tabs)){
                        if(!empty($tabopen)){
                            $Form .= '</div>';
                            unset($tabopen);
                        }

                        $Form .= '<div id="'.$tabid.'">';
                        $tabopen = true;
                    }
                }else{

                    foreach($ColSets as $Column=>$Set){
                        //vardump($Set);
                        foreach($Set['Fields'] as $Item){
                            if(substr($Item['Name'], 0, 4) == '_tab'){
                                $subtabs[$Column][$Item['Name']] = $Item['Name'];
                            }
                        }
                        //
                    }
                }
                $Form .= '<div id="pg'.$_GET['Page']['ID'].'-form-'.$Row.'" class="form-gen-row" style="clear:both;">';
                foreach($ColSets as $Col=>$FieldSet) {
                    
                    $Form .= '<div style="float: left; overflow: hidden; width: '.$FieldSet['Row'].';">';
                    $Form .= '<div id="pg'.$_GET['Page']['ID'].'-form-'.$Row.'-'.$Col.'" class="form-gen-row form-gen-col form-col-'.$Col.'">';
                    foreach($FieldSet['Fields'] as $Fields) {
                        
                        $Field = $Fields['Name'];
                        $FieldDet = $Config['_Field'][$Field];
                        $FieldSet = explode('_',$FieldDet);
                        //dump($FieldSet);
                        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php')) {
                            if(!empty($Config['_FieldTitle'][$Field])) {
                                $name = $Config['_FieldTitle'][$Field];
                            }else {
                                $name = df_parseCamelCase($Field);
                            }
                            // Validation Classes
                            $Req = false;
                            if(!empty($Config['_Required'][$Field])) {
                                $name = $name.' <em>(required)</em>';
                                $Req = 'validate[required]';
                            }
                            if(!empty($Config['_Unique'][$Field])) {
                                if(!empty($Req)) {
                                    $Req = 'validate[required, ajax[ajaxUnique]]';
                                }else {
                                    $Req = 'validate[optional, ajax[ajaxUnique]]';
                                }
                            }
                            //echo $Field;
                            include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php');
                            if(substr($Field,0, 2) != '__' || !empty($FieldTypes[$FieldSet[1]]['cloneview'])) {
                                if(!empty($FieldTypes[$FieldSet[1]]['visible'])) {
                                    $Form .= '<div id="form-field-'.$Field.'" class="form-gen-field-wrapper"><label id="lable_'.$Element['ID'].'_'.$Field.'" for="entry_'.$Element['ID'].'_'.$Field.'" class="form-gen-lable '.$FieldSet[1].'">'.$name.'</label>';
                                    $Val = '';
                                    if(!empty($Defaults[$Field])) {
                                        $Val = stripslashes($Defaults[$Field]);
                                    }
                                    //$Pre = '<tr class="'.$Row.'" style="padding:3px;">';
                                    //$Pre .= '<td id="'.$Element['ID'].'_'.$Field.'" class="'.$Row.'" nowrap="nowrap" width="30%" style="background-color:inherit; border:inherit;padding:3px;" valign="top"><div class="title"><strong>'.$name.'&nbsp;</strong></div>';//</td>';
                                    //$Pre .= '</tr>';
                                    //$Pre .= '<tr class="'.$Row.'" style="padding:3px;">';
                                    //$Pre .= '<td class="'.$Row.'" style="background-color:inherit; border:inherit;padding:3px;" valign="top">';
                                    //df_makeEnumField($Data['_main_table'], $ElementID, $Field, $Data[$Data[$Field]]['Type'], false, $Req);
                                    ob_start();
                                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php');
                                    $Pre = ob_get_clean();
                                    //$Pre .= '</td>';
                                    //$Pre .= '</tr>';
                                    $Form .= $Pre;
                                    if(empty($FieldTypes[$FieldSet[1]]['captionsOff'])) {
                                        $Form .= '<div id="caption_'.$Element['ID'].'_'.$Field.'" class="caption">';
                                        if(!empty($Config['_FieldCaption'][$Field])) {
                                            $Form .= $Config['_FieldCaption'][$Field];
                                        }else {
                                            $Form .= '&nbsp;';
                                        }
                                        $Form .= '</div>';
                                    }
                                    $Form .= '<div style="clear:left;"></div></div>';


                                }else {
                                    ob_start();
                                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php');
                                    $Hidden .= ob_get_clean();
                                }
                            }
                        }else {
                            // check for Sectionbreak;
                            if(!empty($Config['_SectionBreak'][$Fields['Name']])) {
                                // Section Break
                                $Form .= '<div class="sectionbreak">';
                                $Form .= '<h2>'.$Config['_SectionBreak'][$Fields['Name']]['Title'].'</h2>';
                                if(!empty($Config['_SectionBreak'][$Fields['Name']]['Caption'])) {
                                    $Form .= '<div id="caption_'.$Element['ID'].'_'.$Field.'" class="caption">'.$Config['_SectionBreak'][$Fields['Name']]['Caption'].'</div>';
                                }
                                $Form .= '</div>';
                            }
                            if(!empty($Config['_Tab'][$Fields['Name']])) {
                                if(!empty($subtabs[$Col][$Fields['Name']])){
                                    if(!empty($sub_tab_set)){
                                        $Form .= '</div>';
                                    }else{
                                        $sub_id = uniqid('st_');
                                        $Form .= '<div id="'.$sub_id.'">';
                                        $Form .= '<ul class="content-box-tabs">';
                                        foreach($subtabs[$Col] as $subtab){
                                            
                                            $Form .= '<li><a href="#'.$subtab.'">'.$Config['_Tab'][$subtab]['Title'].'</a></li>';
                                        }
                                        $Form .= '</ul>';
                                        $sub_tab_set = true;
                                        $_SESSION['dataform']['OutScripts'] .="
                                            jQuery('#".$sub_id."').tabs();
                                        ";
                                    }
                                    $Form .= '<div id="'.$Fields['Name'].'">';
                                }
                                //$sub_tab_open = true;
                                // Section Break
                                //$Form .= '<div id="tabs">';
                                //$Form .= '<h2>'.$Config['_Tab'][$Fields['Name']]['Title'].'</h2>';
                                //if(!empty($Config['_Tab'][$Fields['Name']]['Caption'])) {
                                //    $Form .= '<div id="caption_'.$Element['ID'].'_'.$Field.'" class="caption">'.$Config['_SectionBreak'][$Fields['Name']]['Caption'].'</div>';
                                //}
                                //$Form .= '</div>';
                            }
                        }
                        //ob_start();
                        if(!empty($Config['_EditFormText'])) {
                            //dump($Defaults);
                            if(!empty($Defaults['_outvalue'][$Field])) {
                                $newTitle = str_replace('{{'.$Field.'}}', $Defaults['_outvalue'][$Field], $newTitle);
                            }else {
                                $newTitle = str_replace('{{'.$Field.'}}', $Defaults[$Field], $newTitle);
                            }
                            $Output['title'] = $newTitle;
                        }
                    }
                    if(!empty($sub_tab_set)) {
                        $Form .= '</div>';
                         $Form .= '</div>';
                        unset($sub_tab_set);
                    }
                    $Form .= '</div>';
                    $Form .= '</div>';
                }
                $Form .= '<div style="clear:left;"></div>';
                $Form .= '</div>';
            }
        
            $rowindex++;
        }
        if(!empty($tabopen)){
            $Form .= '</div>';
            unset($tabopen);
        }
        // end tabs form
        if(!empty($Tabs)) {
            $Form .= '</div>';
            $maintabs_isset = false;
        }
        $Form .= '<div style="clear:left;"></div>';
        $Shown = '';
        
        if(!empty($Config['_FormMode']) || $Config['_ViewMode'] == 'form') {
            $ButtonText = 'Submit';
            if(!empty($Config['_SubmitButtonText'])) {
                $ButtonText = $Config['_SubmitButtonText'];
            }

            if(!empty($_GET[$Config['_ReturnFields'][0]])) {
                if(!empty($Config['_UpdateButtonText'])) {
                    $ButtonText = $Config['_UpdateButtonText'];
                }
            }
            $ButtonAlign = 'center';
            if(!empty($Config['_SubmitAlignment'])) {
                $ButtonAlign = $Config['_SubmitAlignment'];
            }

            $Shown .= '<div style="text-align: '.$ButtonAlign.';" class="buttonbar"><input type="submit" name="captureEntry" id="button" value="'.$ButtonText.'" class="button-primary" />';
            if(!empty($Config['_ShowReset'])) {
                $Shown .= '&nbsp;<input type="reset" name="Reset" id="button" class="button" value="Reset Form" />';
            }
            $Shown .= '</div>';
        }
        if(!empty($Defaults)) {
            $Hidden .= '<input type="hidden" name="processKey" id="processKey" value="'.$_SESSION['processKey'].'" />';
            $Hidden .= '<input type="hidden" name="dr_update" value="1" />';
            $Hidden .= '<input type="hidden" name="dataForm[EID]" value="'.$Element['ID'].'" />';
            $Hidden .= '<input type="hidden" name="dataForm['.$Config['_ReturnFields'][0].']" value="'.$EditID.'" />';
        }
        $Shown .= '</form>';
        if(!empty($Config['_ajaxForms'])){
        $_SESSION['dataform']['OutScripts'] .= "
        //jQuery(\"#data_form_".$Element['ID']."\").submit();
        //if(ajaxSubmit == true) {
            
            jQuery(\"#data_form_".$Element['ID']."\").bind('submit', function(){
            ajaxCall('df_processAjaxForm',jQuery(\"#data_form_".$Element['ID']."\").serialize(), function(p){
             jQuery(\"#data_form_".$Element['ID']."\")[0].reset();
                
             df_loadOutScripts();
            });
            return false;
            })
        //}
        ";
        }
        // if ajax Forms


        $Output['width'] = $Config['_popupWidth'];
        $Output['html'] = $Hidden.$Form.$Shown;
        return $Output;
    }

    $Shown = '<table width="100%" border="0" cellspacing="0" class="captureForm" cellpadding="0">';
    if(!empty($Config['_Required'])) {
        $_SESSION['dataform']['OutScripts'] .= "
			jQuery('#data_form_".$Element['ID']."').validationEngine({
			  success :  false,
			  failure : function() {}
			 });

		";
    }
    $LeftColumn = '';
    $RightColumn = '';
    $FarRightColumn = '';
    $LIndex = 0;
    $RIndex = 0;


    foreach($Config['_Field'] as $Field=>$Type) {
        $Row = dais_rowSwitch($Row);
        $name = df_parseCamelCase($Field);
        if(!empty ($Config['_FieldTitle'][$Field])){
            $name = $Config['_FieldTitle'][$Field];
        }
        $Req = false;
        if(!empty($Config['_Required'][$Field])) {
            $name = '<span style="color:#ff0000;">*</span>'.$name;
            $Req = 'validate[required]';
        }
        if(!empty($Config['_Unique'][$Field])) {
            if(!empty($Req)) {
                $Req = 'validate[required, ajax[ajaxUnique]]';
            }else {
                $Req = 'validate[optional, ajax[ajaxUnique]]';
            }
        }
        //if(file_exists(
        $FieldSet = explode('_',$Type);

        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php')) {
            include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php');
            $Val = '';
            if(!empty($Defaults[$Field])) {
                $Val = stripslashes($Defaults[$Field]);
            }
            //vardump($FieldTypes[$FieldSet[1]]);
            if(substr($Field,0, 2) != '__' || !empty($FieldTypes[$FieldSet[1]]['cloneview'])) {
                if(!empty($FieldTypes[$FieldSet[1]]['visible'])) {

                    $Pre = '<tr class="'.$Row.'" style="padding:3px;">';
                    $Pre .= '<td id="'.$Element['ID'].'_'.$Field.'" nowrap="nowrap" class="'.$Row.'" width="30%" style="text-align:right;background-color:inherit; border:inherit;padding:3px;" valign="top"><strong>'.$name.'&nbsp;</strong></td>';
                    $Pre .= '<td class="'.$Row.'" style="background-color:inherit; border:inherit;padding:3px;" valign="top">';
                    //df_makeEnumField($Data['_main_table'], $ElementID, $Field, $Data[$Data[$Field]]['Type'], false, $Req);
                    ob_start();
                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php');
                    $Pre .= ob_get_clean();
                    // Caption
                    $Pre .= '<div class="caption">';
                        if(!empty ($Config['_FieldCaption'][$Field])){
                           $Pre .= $Config['_FieldCaption'][$Field];
                        }else{
                            $Pre .= '&nbsp;';
                        }
                    $Pre .= '</div>';

                    $Pre .= '</td>';
                    $Pre .= '</tr>';

                    /// Columns
                    $RightColumn .= $Pre;
                }else {
                    $Row = dais_rowSwitch($Row);
                    ob_start();
                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php');
                    $Hidden .= ob_get_clean();
                }
            }
            unset($FieldTypes);
            //echo 'p';
        }
    }






    $Shown .= '<table width="100%" border="0" cellspacing="0" class="captureframe" cellpadding="0">
		<tr>';
    $Output['width'] = 300;

    $Shown .= '<td valign="top">';
    $Shown .= '<table width="100%" border="0" cellspacing="0" cellpadding="2">';
    $Shown .= $RightColumn;
    $Shown .= '</table>';
    $Shown .= '</td>';
    $Output['width'] = $Output['width']+100;


    $Shown .= '</tr>';
    $Shown .= '</table>';


    $Shown .= '</table>';
    if(!empty($Config['_FormMode'])) {
        $ButtonText = 'Submit';
        if(!empty($Config['_SubmitButtonText'])) {
            $ButtonText = $Config['_SubmitButtonText'];
        }
        if(!empty($_GET[$Config['_ReturnFields'][0]])) {
            if(!empty($Config['_UpdateButtonText'])) {
                $ButtonText = $Config['_UpdateButtonText'];
            }
        }
        $ButtonAlign = 'center';
        if(!empty($Config['_SubmitAlignment'])) {
            $ButtonAlign = $Config['_SubmitAlignment'];
        }

        $Shown .= '<div style="text-align: '.$ButtonAlign.';" class="list_row3"><input type="submit" name="captureEntry" id="button" value="'.$ButtonText.'" class="button" />';
        if(!empty($Config['_ShowReset'])) {
            $Shown .= '&nbsp;<input type="reset" name="Reset" id="reset" class="button"  value="Reset Form" />';
        }
        $Shown .= '</div>';
    }
    if(!empty($Defaults)) {
        $Hidden .= '<input type="hidden" name="processKey" id="processKey" value="'.$_SESSION['processKey'].'" />';
        $Hidden .= '<input type="hidden" name="dr_update" value="1" />';
        $Hidden .= '<input type="hidden" name="dataForm[EID]" value="'.$Element['ID'].'" />';
        $Hidden .= '<input type="hidden" name="dataForm['.$Config['_ReturnFields'][0].']" value="'.$EditID.'" />';
    }
    $Shown .= '</form>';
    $Output['html'] = $Hidden.$Shown;
    return $Output;
}

function df_parseCamelCase($Field) {
    $name = '';
    for($i=0; $i<strlen($Field); $i++) {
        $Char = substr($Field, $i, 1);
        if(is_uppercase($Char) && $i > 0 && strlen($Field) >1 && (!is_uppercase(substr($Field, $i+1, 1)) && substr($Field, $i+1, 1) != '' && substr($Field, $i-1, 1) != '-' )) {
            $name .= ' ';
        }
        $name .= str_replace('_', ' ', str_replace('_ ', ' ', $Char));
    }
    return trim($name);
}

function df_processInsert($EID, $Data) {
    $Setup = getelement($EID);
    $Config = $Setup['Content'];
    foreach($Config['_Field'] as $Field=>$Type) {
        $typeSet = explode('_', $Type);
        if(!empty($typeSet[1])) {
            if(function_exists($typeSet[0].'_handleInput')) {
                $Setup['_ActiveProcess'] = 'insert';
                $Func = $typeSet[0].'_handleInput';
                $Data[$Field] = $Func($Field, $Data[$Field], $typeSet[1], $Setup, $Data);
            }
        }
    }


    foreach($Config['_Field'] as $Field=>$Type) {
        if(substr($Field,0,2) != '__'){
            $Fields[] = '`'.$Field.'`';
            if(is_array($Data[$Field])) {
                $EntryData = serialize($Data[$Field]);
            }else {
                if(!empty($Files[$Field])) {
                    $EntryData = $Files[$Field];
                }else {
                    $EntryData = $Data[$Field];
                }
            }
            $Entries[$Field] = "'".mysql_real_escape_string($EntryData)."'";
        }
    }
    $Query = "INSERT INTO `".$Config['_main_table']."` (". implode(',',$Fields).") VALUES (".implode(',', $Entries).");";
    //echo $Query;
    //die;
    if(mysql_query($Query)) {
        //vardump($Config['_ReturnFields']);
        
        if(!empty($Config['_ReturnFields'][0])) {
            $ReturnVals = implode(', ', $Config['_ReturnFields']);

            if($ID = mysql_insert_id()){
                $Data[$Config['_ReturnFields'][0]] = $ID;
                $Query = "SELECT ".$ReturnVals." FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$ID."';";
            }else{
                $Wheres = array();
               // vardump($Entries);
                foreach($Entries as $Field=>$Value) {
                    $Wheres[] = "`".$Field."` = ".$Value;
                }
                $Query = "SELECT ".$ReturnVals." FROM `".$Config['_main_table']."` WHERE ".implode('&&', $Wheres)." LIMIT 1";
            }
           // echo $Query;
           // die;
            $outq = mysql_query($Query);
            $dta = mysql_fetch_assoc($outq);
            $outstr = array();
            foreach($dta as $key=>$val) {
                $outstr[] = $key.'='.$val;
            }
            $Return['Value'] = implode('&', $outstr);
            $Data[$Config['_ReturnFields'][0]] = $dta[$Config['_ReturnFields'][0]];
        }else {
            $Return['Value'] = $ID;
        }

        // post insert
        foreach($Config['_Field'] as $Field=>$Type) {
            $typeSet = explode('_', $Type);
            if(!empty($typeSet[1])) {
                if(function_exists($typeSet[0].'_postProcess')) {
                    $Setup['_ActiveProcess'] = 'insert';
                    $Func = $typeSet[0].'_postProcess';
                    $Func($Field, $Data[$Field], $typeSet[1], $Setup, $Data, $ID);
                }
            }
        }
        // Auding
        if(!empty($Config['_EnableAudit'])) {
            $memberID = 0;
            if(!empty($_SESSION['UserBase']['Member']['ID'])) {
                $memberID = $_SESSION['UserBase']['Member']['ID'];
            }
            $lres = mysql_query("SHOW COLUMNS FROM ".$Config['_main_table']);
            $prerows = array();
            while ($row = mysql_fetch_assoc($lres)) {
                $prerows[] = $row['Field'];
            }
            $rows = implode(',', $prerows);
			if(mysql_query("CREATE TABLE `_audit_".$Config['_main_table']."` SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."' LIMIT 1")){

				mysql_query("ALTER TABLE `_audit_".$Config['_main_table']."` ADD `_ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ,
							ADD `_DateInserted` DATETIME NOT NULL AFTER `_ID` ,
							ADD `_DateModified` DATETIME NOT NULL AFTER `_ID` ,
							ADD `_User` INT NOT NULL AFTER `_DateModified`  ,
                                                        ADD `_RawData` TEXT NOT NULL AFTER `_DateInserted`");
                                mysql_query("INSERT INTO `_audit_".$Config['_main_table']."` SET `_DateInserted` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', `".$Config['_ReturnFields'][0]."` = '".$ID."'  ;");
			}else{
				mysql_query("INSERT INTO `_audit_".$Config['_main_table']."` SET `_DateInserted` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', `".$Config['_ReturnFields'][0]."` = '".$ID."'  ;");
			}
        }

        if(empty($Config['_InsertSuccess'])) {
            $Return['Message'] = 'Entry inserted successfully';
        }else {
            $Return['Message'] = $Config['_InsertSuccess'];
        }
        return $Return;
    }
    return;
}

function df_loadOutScripts() {

    if(!empty($_SESSION['dataform']['OutScripts'])) {
        $Return = $_SESSION['dataform']['OutScripts'];
        unset($_SESSION['dataform']['OutScripts']);
        return $Return;
    }
    return false;
}

// Activity Tracking Functions

function df_checkActivity($Act, $create = 0) {
    global $wpdb;
    return;
    $Res = mysql_query("SELECT ID FROM `_adittrack_activities` WHERE `Activity` = '".$Act."' LIMIT 1");
    if(mysql_num_rows($Res) == 1) {
        $Activity = mysql_fetch_assoc($Res);
        return $Activity['ID'];
    }
    $InQu = "INSERT INTO `_adittrack_activities` (
						`ID` ,
						`Activity`
						)
						VALUES (
						NULL , '".$Act."'
						);";
    if(mysql_query($InQu)) {
        return mysql_insert_id();
    }
    if($create == 0) {
        mysql_query("CREATE TABLE IF NOT EXISTS `_adittrack_activities` (
		  `ID` int(11) NOT NULL auto_increment,
		  `Activity` varchar(255) NOT NULL,
		  PRIMARY KEY  (`ID`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
        return df_checkActivity($Act, 1);
    }
    return false;
}

function dr_trackActivity($Act, $EID, $ReturnValue, $Level = 0) {
    //return;
    $Table = '';
    if(!empty($EID)) {
        $Setup = getelement($EID);
        $Table = $Setup['Content']['_main_table'];
    }
    //$ReturnValue = $Config['Content']['_ReturnFields'][0];
    $Activity = df_checkActivity($Act);
    $UserID = 0;
    if(!empty($_SESSION['UserBase']['Member']['EmailAddress'])) {
        $UserID = $_SESSION['UserBase']['Member']['EmailAddress'];
    }
    if(mysql_query("INSERT INTO `_adittrack_entries` (
						`ID` ,
						`User` ,
						`Activity` ,
						`Table` ,
						`Entry` ,
						`Element`,
						`Date`
						)
						VALUES (
						NULL , '".$UserID."', '".$Activity."', '".$Table."', '".$ReturnValue."', '".$EID."', '".date('Y-m-d H:i:s')."'
						);")) {
        return true;
    }
    if($Level == 0) {
        mysql_query("CREATE TABLE IF NOT EXISTS `_adittrack_entries` (
					  `ID` int(11) NOT NULL auto_increment,
					  `User` varchar(255) NOT NULL,
					  `Activity` int(11) NOT NULL,
					  `Table` varchar(255) NOT NULL,
					  `Entry` varchar(255) NOT NULL,
					  `Element` int(11) NOT NULL,
					  `Date` datetime NOT NULL,
					  PRIMARY KEY  (`ID`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        return dr_trackActivity($Act, $EID, $ReturnValue, 1);
    }
    return false;
}




// autocomplete Processor
if(!empty($_GET['q_eid'])) {
    $Element = getelement($_GET['q_eid']);
    $Config = $Element['Content'];
    $Table = $Config[decodestring($_GET['f_i'])];
    $Setup = $Config[$Table];

    $Res = mysql_query("SELECT ".$Setup['ID'].",".$Setup['Value']." FROM `".$Table."` WHERE `".$Setup['Value']."` LIKE '%".$_GET['q']."%' OR `".$Setup['ID']."` LIKE '%".$_GET['q']."%' ORDER BY `".$Setup['Value']."` ASC;");
    while($Out = mysql_fetch_assoc($Res)) {
        echo $Out[$Setup['ID']]." (".$Out[$Setup['Value']].")|".$Out[$Setup['ID']]."\n";
    }
    die;
}





//Validator Unique Processing
if(!empty($_GET['validatorUniques'])) {
    $Part = explode('_', str_replace('entry_', '', $_POST['validateId']),3);

    $Element = getelement('dt_'.$Part[1]);
    $Config = $Element['Content'];

    $Query = "SELECT count(".$Config['_ReturnFields'][0].") as total FROM ".$Config['_main_table']." WHERE `".$Part[2]."` = '".$_POST['validateValue']."' LIMIT 1;";

    $Res = mysql_query($Query);
    $count = mysql_fetch_assoc($Res);
    if($count['total'] >= 1) {
        echo '{"jsonValidateReturn":["'.$_POST['validateId'].'","ajaxUnique","false"]}';
    }else {
        echo '{"jsonValidateReturn":["'.$_POST['validateId'].'","ajaxUnique","true"]}';
    }

    mysql_close();
    die;
}
?>