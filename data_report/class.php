<?php

function dr_setFilters($EID, $FilterString){

    $Media = getelement($EID);
    $Config = $Media['Content'];


    if($FilterString == 'clear'){
        unset($_SESSION['reportFilters'][$EID]);
        ob_start();
            include(DB_TOOLKIT.'data_report/toolbar.php');
        $Return .= ob_get_clean();
        return $Return;
    }
    parse_str($FilterString, $filters);
    $filters = $filters['reportFilter'][$EID];
    foreach($filters as $filter=>$val){
        if(is_array($val)){
            foreach($val as $pkey=>$part){
                if(!empty($part)){
                    $_SESSION['reportFilters'][$EID][$filter][$pkey] = $part;
                }else{
                    unset($_SESSION['reportFilters'][$EID][$filter][$pkey]);
                }
            }
        }else{
            if(!empty($val)){
                $_SESSION['reportFilters'][$EID][$filter] = $val;
            }else{
                if(!empty($_SESSION['reportFilters'][$EID][$filter])){
                    unset($_SESSION['reportFilters'][$EID][$filter]);
                }
            }
        }
    }
        ob_start();
            include(DB_TOOLKIT.'data_report/toolbar.php');
        $Return .= ob_get_clean();

        return $Return;
    /*
    [EID] => dt_intfc4f28dd990c4a1
    [_keywords] => wer
    [email_address] =>
    */

}

function grid_rowswitch($Row = '') {
    if ($Row == 'odd') {
        return '';
    }
    return 'odd';
}

function report_rowswitch($Row = '') {
    if ($Row == 'list_row2') {
        return 'list_row1';
    }
    return 'list_row2';

    if ($Row == 'row_even') {
        return 'row_odd';
    }
    return 'row_even';
}

function printr($a) {
    $Return = '<pre>';
    ob_start();
    print_r($a);
    return $Return . ob_get_clean() . '</pre>';
}

function df_cleanArray($Array) {
    foreach ($Array as $Key => $Value) {
        if (is_array($Value)) {
            $temp = df_cleanArray($Value);
            if (!empty($temp)) {
                $Clean[$Key] = $temp;
            }
        } else {
            if (!empty($Value)) {
                $Clean[$Key] = $Value;
            }
        }
    }
    return $Clean;
}

if (is_admin ()) {

    // Admin Functiuons

    function dr_loadPassbackFields($Table, $Defaults = 'none', $Config = false, $remove = true) {

        if (empty($Table)) {
            return;
        }

        $result = mysql_query("SHOW COLUMNS FROM `". $Table."`");
        if (mysql_num_rows($result) > 0) {
            $TotalsField = '';
            while ($row = mysql_fetch_assoc($result)) {
                $TotalsField .= '<option value="' . $row['Field'] . '" {{' . $row['Field'] . '}}>' . $row['Field'] . '</option>';
                $FieldsClearer[] = $row['Field'];
            }

            if (!empty($Config)) {
                if (!empty($Config['_CloneField'])) {
                    $TotalsField .= '<optgroup label="Cloned Fields">';
                    foreach ($Config['_CloneField'] as $FieldKey => $Array) {
                        $Sel = '';
                        if ($Default == $FieldKey) {
                            $Sel = 'selected="selected"';
                        }
                        $TotalsField .= '<option value="' . $FieldKey . '" ' . $Sel . '>' . $Config['_FieldTitle'][$FieldKey] . '</option>';
                    }
                }
            }
        }
        if ($Defaults == 'none') {
            $ID = uniqid(rand(100, 99999));
            $Return = '<div style="padding:3px;" class="list_row3" id="ReturnFields_' . $ID . '_wrap">';
            $Return .= 'Return Field: <select class="passBackField" name="Data[Content][_ReturnFields][]" id="ReturnFields_' . $ID . '">';
            $Return .= $TotalsField;
            $Return .= '</select>&nbsp;';
            if (!empty($remove)) {
                $Return .= '<a href="#" onclick="jQuery(\'#ReturnFields_' . $ID . '_wrap\').remove(); return false;">remove</a>';
            } else {
                $Return .= '<em>Primary</em>';
                $remove = true;
            }
            $Return .= '</div>';
            foreach ($FieldsClearer as $Clear) {
                $Return = str_replace('{{' . $Clear . '}}', '', $Return);
            }
            $Return .= '</div>';
        } else {
            //dump($Defaults);
            if (is_array($Defaults)) {
                $first = false;
                foreach ($Defaults as $Default) {
                    $ID = uniqid(rand(100, 99999));
                    $Return = '<div style="padding:3px;" class="list_row3" id="ReturnFields_' . $ID . '_wrap">';
                    $Return .= 'Return Field: <select class="passBackField" name="Data[Content][_ReturnFields][]" id="ReturnFields_' . $ID . '">';
                    $Return .= $TotalsField;
                    $Return .= '</select>&nbsp;';
                    if (empty($remove)) {
                        $Return .= '<a href="#" onclick="jQuery(\'#ReturnFields_' . $ID . '_wrap\').remove(); return false;">remove</a>';
                    } else {
                        $Return .= '<em>Primary</em>';
                        $remove = false;
                    }
                    $Return .= '</div>';
                    $out[] = str_replace('{{' . $Default . '}}', 'selected="selected"', $Return);
                }
                $Return = implode('', $out);
                foreach ($FieldsClearer as $Clear) {
                    $Return = str_replace('{{' . $Clear . '}}', '', $Return);
                }
            }
        }
        return $Return;
    }

    function df_searchReferenceForm($Table) {
        return false;
    }

    function df_IconToggle($Field, $Name, $Icon, $Defaults = false){
        $ISel = 'checked="checked"';
        $IClass = 'button-highlighted';
        if (empty($Defaults['_'.$Name][$Field])) {
            $ISel = '';
            $IClass = 'button';
        }

        $return = ' &nbsp;<span class="' . $IClass . '" id="'.$Name.'_' . $Field . '" onclick="df_setToggle(\''.$Name.'_' . $Field . '\');" title="'.$Name.'"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/'.$Icon.') left center no-repeat; padding:5px 8px;"></span></span>';
        $return .= '<input style="display:none;" type="checkbox" name="Data[Content][_'.$Name.'][' . $Field . ']" id="'.$Name.'_' . $Field . '_check" ' . $ISel . ' />';
    
        return $return;

    }

    function df_makeFieldConfigBox($Field, $Config, $Defaults = false) {
        global $wpdb;


        $Table = $Config['Content']['_main_table'];
        $name = df_parseCamelCase($Field);
        $addClass= '';
        if (!empty($Config['Content']['_FieldTitle'][$Field])) {
            if (substr($Field, 0, 2) == '__') {
                $name = '<img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/copy.png" width="16" height="16" align="absmiddle" /> '.$Config['Content']['_FieldTitle'][$Field];
                if($Config['Content']['_FieldTitle'][$Field] != $Field){
                    $name .= ' ('.$Field.')';
                }
                $addClass = 'cloned';
            } else {
                $name = $Config['Content']['_FieldTitle'][$Field];
                if($Config['Content']['_FieldTitle'][$Field] != $Field){
                    $name .= ' ('.$Field.')';
                }

            }
        }
        //echo '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><input type="checkbox" name="null" id="use_'.$Field.'" checked="checked" onclick="dr_enableDisableField(this);" />&nbsp;'.ucwords($name).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';


        $PreReturn[$Field] = '<div id="Field_' . $Field . '" class="admin_list_row3 table_sorter postbox '.$addClass.'" style="width:550px;">';

        $PreReturn[$Field] .= '<img src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png" align="absmiddle" onclick="jQuery(\'#Field_' . $Field . '\').remove();" style="float:right; padding:5px;" />';

        $PreReturn[$Field] .= '<img src="' . WP_PLUGIN_URL . '/db-toolkit/images/cog.png" align="absmiddle" onclick="jQuery(\'#overide_' . $Field . '\').toggle();" style="float:right; padding:5px;" />';

        $PreReturn[$Field] .= '<h3 class="fieldTypeHandle">' . $name . '</h3>';

        // Linking Master
        if (substr($Field, 0, 2) == '__') {

            $result = mysql_query("SHOW COLUMNS FROM `" . $Config['Content']['_main_table'] . "`");
            // echo mysql_error();
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                }
            }

            $PreReturn[$Field] .= '<div class="admin_config_panel">';

            $PreReturn[$Field] .= 'Master Field: <select name="Data[Content][_CloneField][' . $Field . '][Master]" id="master_' . $Field . '">';
            foreach ($FieldList as $MasterField) {
                // add default here
                $Sel = '';
                if ($MasterField == $Config['Content']['_CloneField'][$Field]['Master']) {
                    $Sel = 'selected="selected"';
                }
                $PreReturn[$Field] .= '<option value="' . $MasterField . '" ' . $Sel . '>' . $MasterField . '</option>';
            }
            // get clones
            if (!empty($Config)) {
                if (!empty($Config['Content']['_CloneField'])) {
                    $PreReturn[$Field] .= '<optgroup label="Cloned Fields">';
                    foreach ($Config['Content']['_CloneField'] as $FieldKey => $Array) {
                        if ($FieldKey != $Field) {
                            $Sel = '';
                            if ($Config['Content']['_CloneField'][$Field]['Master'] == $FieldKey) {
                                $Sel = 'selected="selected"';
                            }
                            $PreReturn[$Field] .= '<option value="' . $FieldKey . '" ' . $Sel . '>' . $Config['Content']['_FieldTitle'][$FieldKey] . '</option>';
                        }
                    }
                }
            }

            $PreReturn[$Field] .= '</select>';

            $PreReturn[$Field] .= '</div>';
        }


        $PreReturn[$Field] .= '<div id="overide_' . $Field . '" class="admin_config_panel" style="display:none; position:reletive;">';
        //New Options
        $Width = '';
        if (!empty($Defaults['_WidthOverride'][$Field])) {
            $Width = $Defaults['_WidthOverride'][$Field];
        }
        $RSel = 'checked="checked"';
        $RClass = 'button-highlighted';
        if (empty($Config['Content']['_Required'][$Field])) {
            $RSel = '';
            $RClass = 'button';
        }
        $SSel = 'checked="checked"';
        $SClass = 'button-highlighted';
        if (empty($Config['Content']['_Sortable'][$Field])) {
            $SSel = '';
            $SClass = 'button';
        }
        $USel = 'checked="checked"';
        $UClass = 'button-highlighted';
        if (empty($Config['Content']['_Unique'][$Field])) {
            $USel = '';
            $UClass = 'button';
        }
        $Title = df_parseCamelCase($Field);
        if (!empty($Config['Content']['_FieldTitle'][$Field])) {
            $Title = $Config['Content']['_FieldTitle'][$Field];
        }
        $Caption = '';
        if (!empty($Config['Content']['_FieldCaption'][$Field])) {
            $Caption = $Config['Content']['_FieldCaption'][$Field];
        }
        $inlineSel = '';
        if (!empty($Config['Content']['_InlineEdit'][$Field])) {
            $inlineSel = 'checked="checked"';
        }
        $Justify = '';
        if (!empty($Config['Content']['_Justify'][$Field])) {
            $Justify = $Config['Content']['_Justify'][$Field];
        }
        $fieldFormWidth = '';
        if (!empty($Config['Content']['_FormFieldWidth'][$Field])) {
            $fieldFormWidth = $Config['Content']['_FormFieldWidth'][$Field];
        }
        //$PreReturn[$Field] .= '<label><strong>Lable</strong></label>';

        $PreReturn[$Field] .= '<div style="padding:3px;">Title: <input type="text" value="' . $Title . '" name="Data[Content][_FieldTitle][' . $Field . ']" /> ';
        $PreReturn[$Field] .= 'Caption: <input type="text" value="' . $Caption . '" name="Data[Content][_FieldCaption][' . $Field . ']" />';
        $PreReturn[$Field] .= df_FormWidthSetup($Field, $fieldFormWidth);
        $PreReturn[$Field] .= '</div>';
        //$PreReturn[$Field] .= '<label><strong>Alignment</strong></label>';
        $PreReturn[$Field] .= '<div style="padding:3px;">List Column Width: <input type="text" style="width:40px;" value="' . $Width . '" name="Data[Content][_WidthOverride][' . $Field . ']" /> ';
        $PreReturn[$Field] .= df_alignmentSetup($Field, $Justify).'</div>';
        
        
        
        if(empty($row))
            $row = false;
        
        $PreReturn[$Field] .= '</div><div class="admin_config_toolbar"> <div style="float:left; width:180px;">' . df_fieldTypes($Field, $Table, $row, $Defaults['_Field']) .'</div>'. dr_reportListTypes($Field, $Defaults['_IndexType'][$Field]);
        // inline settings
        //class="button-highlighted"
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $UClass . '" id="unique_' . $Field . '" onclick="df_setToggle(\'unique_' . $Field . '\');" title="Unique"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/unique.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $RClass . '" id="required_' . $Field . '" onclick="df_setToggle(\'required_' . $Field . '\');" title="Required"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/required.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $SClass . '" id="issortable_' . $Field . '" onclick="df_setToggle(\'issortable_' . $Field . '\');" title="Sortable"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/table_sort.png) left center no-repeat; padding:5px 8px;"></span></span>';
        
        //$PreReturn[$Field] .= df_IconToggle($Field, 'Indexes', 'open-bookmark.png', $Config['Content']);

        $PreReturn[$Field] .= '<div class="widefat" id="' . $Field . '_FieldTypePanel" style="display:none; text-align:left; margin:10px 0;"></div>';

        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Unique][' . $Field . ']" id="unique_' . $Field . '_check" ' . $USel . ' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Required][' . $Field . ']" id="required_' . $Field . '_check" ' . $RSel . ' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Sortable][' . $Field . ']" id="issortable_' . $Field . '_check" ' . $SSel . ' />';
        
        $PreReturn[$Field] .= '</div><div class="admin_config_panel" style="text-align:right;" id="ExtraSetting_' . $Field . '">';
        unset($Types);
        $Types = explode('_', $Defaults['_Field'][$Field]);
        if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php')) {
            include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php');
            $func = $FieldTypes[$Types[1]]['func'];
            if ($func != 'null') {
                if ($func != '') {
                    $PreReturn[$Field] .= '<div class="widefat" id="' . $Field . '_configPanel" style="display:none; text-align:left;">';
                    $PreReturn[$Field] .= '<h3>' . $Field . ' Config</h3><div class="admin_config_panel">';
                    $PreReturn[$Field] .= $func($Field, $Table, $Config);
                    $PreReturn[$Field] .= '</div></div>';
                    $PreReturn[$Field] .= '<input type="button" class="buttons" value="Setup" onclick="toggle(\'' . $Field . '_configPanel\');" />';
                }
            }
        }
        $PreReturn[$Field] .= '</div></div>';

        return $PreReturn[$Field];
    }

    function df_tableReportSetup($Table, $EID, $Config = false, $Column = 'M') {
        if (empty($Table)) {
            return;
        }
        global $wpdb;
        
        if ($Column == 'Linking') {

            $result = mysql_query("SHOW COLUMNS FROM `".$Table."`");
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                }
            }


            $Field = '__' . uniqid();
            $name = df_parseCamelCase($Field);




            $name = df_parseCamelCase($Field);
            //echo '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><input type="checkbox" name="null" id="use_'.$Field.'" checked="checked" onclick="dr_enableDisableField(this);" />&nbsp;'.ucwords($name).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';            
            $PreReturn[$Field] .= '<div id="Field_' . $Field . '" class="admin_list_row3 table_sorter postbox cloned" style="width:550px;"><img src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png" align="absmiddle" onclick="jQuery(\'#Field_' . $Field . '\').remove();" style="float:right; padding:5px;" /><img src="' . WP_PLUGIN_URL . '/db-toolkit/images/cog.png" align="absmiddle" onclick="jQuery(\'#overide_' . $Field . '\').toggle();" style="float:right; padding:5px;" /><h3 class="fieldTypeHandle">' . df_parseCamelCase($Field) . '</h3>';
            // Linking Master
            $PreReturn[$Field] .= '<div style="padding:5px;">';

            $PreReturn[$Field] .= 'Master Field: <select name="Data[Content][_CloneField][' . $Field . '][Master]" id="master_' . $Field . '">';
            foreach ($FieldList as $MasterField) {
                // add default here
                $PreReturn[$Field] .= '<option value="' . $MasterField . '">' . $MasterField . '</option>';
            }
            $PreReturn[$Field] .= '</select>';

            $PreReturn[$Field] .= '</div>';


            $PreReturn[$Field] .= '<div id="overide_' . $Field . '" class="admin_config_panel" style="display:none; position:reletive;">';
            //New Options
            $Justify = '';
            $Width = '';
            $Title = df_parseCamelCase($Field);
            $Caption = '';
            $inlineSel = '';

            $SSel = '';
            $SClass = 'button';
            $USel = '';
            $UClass = 'button';
            $RSel = '';
            $RClass = 'button';

            if (!empty($Config)) {


                if (!empty($Defaults['_WidthOverride'][$Field])) {
                    $Width = $Defaults['_WidthOverride'][$Field];
                }

                if (!empty($Config['Content']['_Required'][$Field])) {
                    $RSel = 'checked="checked"';
                    $RClass = 'button-highlighted';
                }
                if (empty($Config['Content']['_Sortable'][$Field])) {
                    $SSel = 'checked="checked"';
                    $SClass = 'button-highlighted';
                }

                if (!empty($Config['Content']['_Unique'][$Field])) {
                    $USel = 'checked="checked"';
                    $UClass = 'button-highlighted';
                }
                if (!empty($Config['Content']['_FieldTitle'][$Field])) {
                    $Title = $Config['Content']['_FieldTitle'][$Field];
                }

                if (!empty($Config['Content']['_FieldCaption'][$Field])) {
                    $Caption = $Config['Content']['_FieldCaption'][$Field];
                }

                if (!empty($Config['Content']['_InlineEdit'][$Field])) {
                    $inlineSel = 'checked="checked"';
                }

                if (!empty($Config['Content']['_Justify'][$Field])) {
                    $Justify = $Config['Content']['_Justify'][$Field];
                }
            }            
            $PreReturn[$Field] .= '<div style="padding:3px;">Title: <input type="text" value="' . $Title . '" name="Data[Content][_FieldTitle][' . $Field . ']" /> ';
            $PreReturn[$Field] .= 'Caption: <input type="text" value="' . $Caption . '" name="Data[Content][_FieldCaption][' . $Field . ']" />';
            $PreReturn[$Field] .= df_FormWidthSetup($Field, $fieldFormWidth);
            $PreReturn[$Field] .= '</div>';
            $PreReturn[$Field] .= '<div style="padding:3px;">List Column Width: <input type="text" style="width:40px;" value="' . $Width . '" name="Data[Content][_WidthOverride][' . $Field . ']" /> ';
            $PreReturn[$Field] .= df_alignmentSetup($Field, $Justify) . '</div>';
            //$PreReturn[$Field] .= '<div class="admin_list_row2">Unique: <input type="checkbox" name="Data[Content][_Unique]['.$Field.']" id="unique_'.$Field.'" '.$USel.' /></div>';
            //$PreReturn[$Field] .= '<div class="admin_list_row1">Reguired: </div>';
            //$PreReturn[$Field] .= '<div class="admin_list_row2">Sortable: </div>';
            //$PreReturn[$Field] .= '<div style="padding:3px;">Inline Editing: <input type="checkbox" name="Data[Content][_InlineEdit]['.$Field.']" id="sortable_'.$Field.'" '.$inlineSel.' /></div>';
            $PreReturn[$Field] .= '</div>';




            $PreReturn[$Field] .= '<div class="admin_config_toolbar"> <div style="float:left; width:180px;">' . df_fieldTypes($Field, $Table, $row, $Defaults['_Field']) .'</div>'. dr_reportListTypes($Field, $Defaults['_IndexType'][$Field]);

            $PreReturn[$Field] .= ' &nbsp;<span class="' . $UClass . '" id="unique_' . $Field . '" onclick="df_setToggle(\'unique_' . $Field . '\');" title="Unique"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/unique.png) left center no-repeat; padding:5px 8px;"></span></span>';
            $PreReturn[$Field] .= ' &nbsp;<span class="' . $RClass . '" id="required_' . $Field . '" onclick="df_setToggle(\'required_' . $Field . '\');" title="Required"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/required.png) left center no-repeat; padding:5px 8px;"></span></span>';
            $PreReturn[$Field] .= ' &nbsp;<span class="' . $SClass . '" id="issortable_' . $Field . '" onclick="df_setToggle(\'issortable_' . $Field . '\');" title="Sortable"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/table_sort.png) left center no-repeat; padding:5px 8px;"></span></span>';

            $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Unique][' . $Field . ']" id="unique_' . $Field . '_check" ' . $USel . ' />';
            $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Required][' . $Field . ']" id="required_' . $Field . '_check" ' . $RSel . ' />';
            $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Sortable][' . $Field . ']" id="issortable_' . $Field . '_check" ' . $SSel . ' />';

            $PreReturn[$Field] .= '<div class="widefat" id="' . $Field . '_FieldTypePanel" style="display:none; text-align:left;"></div>';
            $PreReturn[$Field] .= '</div><div class="admin_config_panel" style="text-align:right;" id="ExtraSetting_' . $Field . '">';
            unset($Types);





            $Types = explode('_', $Defaults['_Field'][$Field]);
            if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php')) {
                include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php');
                $func = $FieldTypes[$Types[1]]['func'];
                if ($func != 'null') {
                    if ($func != '') {
                        $PreReturn[$Field] .= '<div class="admin_list_row3" id="' . $Field . '_configPanel" style="display:none; text-align:left;">';
                        $PreReturn[$Field] .= '<h3>' . $Field . ' Config</h3><div class="admin_config_panel">';
                        $PreReturn[$Field] .= $func($Field, $Table, $Config);
                        $PreReturn[$Field] .= '</div></div>';
                        $PreReturn[$Field] .= '<input type="button" class="buttons" value="Setup" onclick="toggle(\'' . $Field . '_configPanel\');" />';
                    }
                }
            }
            $PreReturn[$Field] .= '</div></div>';






            return $PreReturn[$Field];
        }

        if ($EID == 'false') {

            $Defaults = $Config['Content'];
            $Config['Content']['_main_table'] = $Table;
            //dump($Defaults);
            if (!empty($Defaults['_FormLayout'])) {
                parse_str($Defaults['_FormLayout'], $Columns);
            }
            $Return = '';
            $result = mysql_query("SHOW COLUMNS FROM `".$Table."`");
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                    $Field = $row['Field'];
                    $PreReturn[$Field] = df_makeFieldConfigBox($Field, $Config, $Defaults);
                }
            }
            if (!empty($Defaults['_Field']) && $Column != 'N') {
                foreach ($Defaults['_Field'] as $Key => $Value) {
                    if (!empty($PreReturn[$Key])) {
                        $Return .= $PreReturn[$Key];
                        unset($PreReturn[$Key]);
                    } else {
                        //if(substr($Key,0,2) == '__'){
                        $Return .= df_makeFieldConfigBox($Key, $Config, $Defaults);
                        //}
                    }
                }
            }
            if (!empty($PreReturn)) {
                foreach ($PreReturn as $Key => $newFields) {
                    $Return .= $newFields;
                }
            }
        } else {

            $Ref = getelement($EID);
            $Return = '';
            $Row = 'list_row2';



            foreach ($Ref['Content']['_Field'] as $Field => $FieldSet) {
                $Row = dais_rowswitch($Row);
                //$Return .= '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/arrow_out.png" align="absmiddle" class="OrderSorter" />&nbsp;<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/tag.png" align="absmiddle" onclick="jQuery(\'#overide_'.$Field.'\').toggle();" /><input type="texfield" style="width:40px; display:none;" name="Data[Content][_WidthOverride]['.$Field.']" id="overide_'.$Field.'" /> &nbsp;'.df_parseCamelCase($Field).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';
                $Return .= '<div id="Field_' . $Field . '" class="' . $Row . ' table_sorter" style="padding:3px;"><img src="' . WP_PLUGIN_DIR . '/db-toolkit/data_report/arrow_out.png" align="absmiddle" class="OrderSorter" />';
                $Return .= '&nbsp;<img src="' . WP_PLUGIN_DIR . '/db-toolkit/data_report/tag.png" align="absmiddle" onclick="jQuery(\'#overide_' . $Field . '\').toggle();" /><span id="overide_' . $Field . '" style="display:none;">';
                //New Options
                $Return .= '&nbsp;Width: <input type="texfield" style="width:40px;" name="Data[Content][_WidthOverride][' . $Field . ']" />&nbsp;';
                $Return .= df_alignmentSetup($Field);

                $Return .= '</span> &nbsp;' . df_parseCamelCase($Field) . ' : ' . dr_reportListTypes($Field, $Ref['Content']['_IndexType']) . df_fieldTypes($Field, $Table, $row, $Ref['Content']['_Field']) . '<span id="ExtraSetting_' . $Field . '">';

                $Types = explode('_', $FieldSet);
                if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php')) {
                    include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Types[0] . '/conf.php');
                    $func = $FieldTypes[$Types[1]]['func'];
                    if ($func != 'null') {
                        $Return .= '<div style="padding:3px; text-align:right;"><input type="button" class="buttons" value="Setup" onclick="toggle(\'' . $Field . '_configPanel\');" /></div>';
                        $Return .= '<blockquote id="' . $Field . '_configPanel">';
                        $Return .= '<h3>' . $Field . ' Config</h3>';
                        $Return .= $func($Field, $Table, $Ref);
                        $Return .= '</blockquote>';
                    }
                }

                $Return .= '</span></div>';
                //df_fieldTypes($Field, $Table, $row, $Defaults)
            }







            $Return .= '<input type="hidden" id="referencePage" style="width:40px;" name="Data[Content][_ReferencePage]" value="' . $Ref['ParentDocument'] . '" />';
        }
        return $Return;
    }

    function dr_reportListTypes($Field, $Default = false) {

        
        $VClass = 'button';
        $IClass = 'button';
        $VSel = '';
        $ISel = '';

        if(!empty($Default)){
            $Part = explode('_', $Default);
            if($Part[1] == 'show'){
                $VSel = 'checked="checked"';
                $VClass = 'button-highlighted';
            }
            if($Part[0] == 'index'){
                $ISel = 'checked="checked"';
                $IClass = 'button-highlighted';
            }
        }

        
        $Return = ' &nbsp;<span class="' . $VClass . '" id="displayTypeV_' . $Field . '" onclick="df_setToggle(\'displayTypeV_' . $Field . '\');" title="Visible"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/eye.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $Return .= '<input style="display:none;" type="checkbox" name="Data[Content][_IndexType][' . $Field . '][Visibility]" value="show" id="displayTypeV_' . $Field . '_check" ' . $VSel . ' />';

        $Return .= ' &nbsp;<span class="' . $IClass . '" id="displayTypeI_' . $Field . '" onclick="df_setToggle(\'displayTypeI_' . $Field . '\');" title="Searchable"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/indexed.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $Return .= '<input style="display:none;" type="checkbox" name="Data[Content][_IndexType][' . $Field . '][Indexed]" value="index" id="displayTypeI_' . $Field . '_check" ' . $ISel . ' />';


        return $Return;

        $Return .= '<select name="Data[Content][_IndexType][' . $Field . ']" id="displayType_' . $Field . '">';
        $Sel = '';
        if ($Default == 'index_show') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="index_show" ' . $Sel . '>Shown Indexed</option>';
        $Sel = '';
        if ($Default == 'noindex_show') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="noindex_show" ' . $Sel . '>Shown Not Indexed</option>';
        $Sel = '';
        if ($Default == 'index_hide') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="index_hide" ' . $Sel . '>Hidden Indexed</option>';
        $Sel = '';
        if ($Default == 'noindex_hide') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="noindex_hide" ' . $Sel . '>Hidden Not Indexed</option>';
        $Return .= '</select>&nbsp;';
        return $Return;
    }

    // End Admin Functions
}

function dr_toolbarButton($Title, $Script = false, $Class = 'noicon', $Link = false, $Target = '_blank'){

    $onClick = '';
    if(!empty($Script)){
        $onClick = 'onClick="'.$Script.'"';
    }
    $linkStart = '';
    $linkEnd = '';
    if(!empty($Link)){
        $linkStart = '<a href="'.$Link.'" target="'.$Target.'">';
        $linkEnd = '</a>';
    }
    
    
    return '<span class="fbutton"><div class="button add-new-h2" '.$onClick.'>'.$linkStart.'<span class="'.$Class.'">'.$Title.'</span>'.$linkEnd.'</div></span>';
}
function dr_toolbarSeperator(){
    return;
    return '<div class="btnseparator"></div>';
}
function dr_lockFilters($EID) {
    //   vardump($_SESSION['reportFilters']);
    $setFilters = serialize($_SESSION['reportFilters'][$EID]);
    add_option('filter_Lock_' . $EID, $setFilters);
    return true;
}

function dr_unlockFilters($EID) {
    delete_option('filter_Lock_' . $EID);
    unset($_SESSION['reportFilters'][$EID]);
    unset($_SESSION['lockedFilters'][$EID]);
}

function dr_buildInterfaceList() {

    global $wpdb;
    $apps = get_option('dt_int_Apps');
    //vardump($apps);
    foreach($apps as $app=>$settings){

        // for each App
        $Icon = WP_PLUGIN_URL . '/db-toolkit/data_report/application-home.png';
        $Return .= '<li><a class="child"><img src="' . $Icon . '" align="absmiddle" />'.$settings['name'].'</a>';
        $Return .= "<ul id=\"\" style=\"visibility: hidden; display: block;\">";

            $appConfig = get_option('_'.$app.'_app');
            $appGroups = array();
            foreach ($appConfig['interfaces'] as $interface=>$access) {
                $cfg = get_option($interface);                
                if(empty($cfg['_ItemGroup'])){
                    $cfg['_ItemGroup'] = 'Ungrouped';
                }
                $appGroups[$cfg['_ItemGroup']][] = $cfg;
            }
            $Return .= "<li class=\"title\"><h2>Category</h2></li>";
            foreach ($appGroups as $app => $state) {

                $Icon = WP_PLUGIN_URL . '/db-toolkit/data_report/application-home.png';
                $Return .= '<li><a class="child"><img src="' . $Icon . '" align="absmiddle" /> ' . $app . '</a>';

                if (!empty($appGroups[$app])) {
                    $Return .= "<ul id=\"\" style=\"visibility: hidden; display: block;\">";
                    $Return .= "<li class=\"title\"><h2>" . $app . "</h2></li>";
                    foreach ($appGroups[$app] as $interface) {
                        //vardump($interface);
                        $IIcon = WP_PLUGIN_URL . '/db-toolkit/data_report/plus-button.png';
                        $Return .= '<li><a onclick="formSetup_InsertInterface(\'' . $interface['ID'] . '\');"><img src="' . $IIcon . '" align="absmiddle" /> ' . $interface['_ReportDescription'] . '<div><span class="description">' . $interface['_ReportExtendedDescription'] . '</span></div></a>';
                    }
                    $Return .= '</ul>';
                }

                $Return .= '</li>';
            }
        $Return .= '</ul>';
        $Return .= '</li>';
    }
    return $Return;
}

function dr_loadInsertInterfaceBox($EID) {

    $cfg = get_option($EID);
    if (empty($cfg))
        return 'Not Found';

    $Return = '';

    $Return .= '<div class="formportlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="formportlet_' . $EID . '"><div class="formportlet-header ui-corner-all"><span class="ui-icon ui-icon-close"></span><div><strong>' . $cfg['_ReportDescription'] . '</strong></div><span class="description">' . $cfg['_ReportExtendedDescription'] . '</span><input class="layOutform positioning" type="hidden" name="' . $EID . '" id="interface_' . $EID . '" value="1"/></div></div>';
    $_SESSION['dataform']['OutScripts'] .= "
        jQuery('.formportlet-header .ui-icon').click(function() {
                jQuery(this).toggleClass(\"ui-icon-minusthick\");
                jQuery(this).parents(\".formportlet:first\").remove();
                formSetup_columSave();
        });
    ";

    return $Return;
}

function df_buildSetProcessors($Config) {

    if (empty($Config['_FormProcessors'])) {
        return;
    }
    $Return = '';
    foreach ($Config['_FormProcessors'] as $processID => $process) {

        $processor = $process['_process'];
        $func = 'config_' . $processor;

        if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php')) {
            include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php');
            include_once(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/functions.php');



            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onInsert'])) {
                $class = 'button-highlighted"';
            }
            $Icons = '<span title="Run Process on Insert" onclick="df_setToggle(\'onInsert_' . $processID . '\');" id="onInsert_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-insert.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';

            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onUpdate'])) {
                $class = 'button-highlighted"';
            }
            $Icons .= '&nbsp;<span title="Run Process on Update" onclick="df_setToggle(\'onUpdate_' . $processID . '\');" id="onUpdate_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-pencil.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';

            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onDelete'])) {
                $class = 'button-highlighted"';
            }
            $Icons .= '&nbsp;<span title="Run Process on Delete" onclick="df_setToggle(\'onDelete_' . $processID . '\');" id="onDelete_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-delete.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
            if (function_exists($func)) {
                $class = 'button';
                if (!empty($Config['_FormProcessors'][$processID]['_configPanelOpen'])) {
                    $class = 'button-highlighted"';
                }
                $Icons .= '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px 5px 20px;">Settings</span></span>';
                $Icons .= '<input type="checkbox" value="1" id="configirator_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_configPanelOpen]" ' . $Sel . ' style="display: none;">';
            }

            $Sel = '';
            if (!empty($Config['_FormProcessors'][$processID]['_onInsert'])) {
                $Sel = 'checked="checked"';
            }
            $Icons .= '<input type="checkbox" value="1" id="onInsert_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onInsert]" ' . $Sel . ' style="display: none;">';

            $Sel = '';
            if (!empty($Config['_FormProcessors'][$processID]['_onUpdate'])) {
                $Sel = 'checked="checked"';
            }
            $Icons .= '<input type="checkbox" value="1" id="onUpdate_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onUpdate]" ' . $Sel . ' style="display: none;">';

            $Sel = '';
            if (!empty($Config['_FormProcessors'][$processID]['_onDelete'])) {
                $Sel = 'checked="checked"';
            }
            $Icons .= '<input type="checkbox" value="1" id="onDelete_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onDelete]" ' . $Sel . ' style="display: none;">';



            $Return .= '<div style="width: 550px; opacity: 1;" class="admin_list_row3 table_sorter postbox" id="' . $processID . '">';
            $Return .= '<input type="hidden" name="Data[Content][_FormProcessors][' . $processID . '][_process]" value="' . $processor . '" />';
            $Return .= '<img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery(\'#' . $processID . '\').remove();" src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png">';

            $Return .= '<h3 class="fieldTypeHandle">' . $Title . '</h3>';
            $Return .= '<div class="admin_config_toolbar">';

            // if there is a config
            $Return .= '<span style="float:right;"><p>' . $Icons . '</p></span>';

            $Return .= '<p>' . $Desc . '</p>';
            $Return .= '<div style="clear:right;"></div>';
            $Return .= '</div>';
            $Return .= '<div id="ExtraSetting_term_id" style="text-align: right;" class="admin_config_panel">';
            if (function_exists($func)) {

                $show = 'none';
                if (!empty($Config['_FormProcessors'][$processID]['_configPanelOpen'])) {
                    $show = 'block';
                }

                $Return .= '<div style="text-align: left; display:' . $show . ';" id="config_' . $processID . '" class="widefat">';
                $Return .= '<h3>Configuration</h3>';
                $Return .= '<div class="inside"><p>';
                $Return .= $func($processID, $Config['_main_table'], $Config);
                $Return .= '</p></div>';
                $Return .= '</div>';
            }
            $Return .= '</div>';
            $Return .= '</div>';
        }
    }


    return $Return;
}
function df_buildSetViewProcessors($Config) {
    
    if (empty($Config['_ViewProcessors'])) {
        return;
    }
    $Return = '';
    foreach ($Config['_ViewProcessors'] as $processID => $process) {

        $processor = $process['_process'];
        $func = 'config_' . $processor;

        if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php')) {
            include(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php');
            include_once(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/functions.php');


            $Icons = '';
            if (function_exists($func)) {
                $class = 'button';
                if (!empty($Config['_FormProcessors'][$processID]['_configPanelOpen'])) {
                    $class = 'button-highlighted"';
                }
                $Icons = '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px 5px 20px;">Settings</span></span>';
                $Icons .= '<input type="checkbox" value="1" id="configirator_' . $processID . '_check" name="Data[Content][_ViewProcessors][' . $processID . '][_configPanelOpen]" ' . $Sel . ' style="display: none;">';
            }

            $Return .= '<div style="width: 750px; opacity: 1;" class="admin_list_row3 table_sorter postbox" id="' . $processID . '">';
            $Return .= '<input type="hidden" name="Data[Content][_ViewProcessors][' . $processID . '][_process]" value="' . $processor . '" />';
            $Return .= '<img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery(\'#' . $processID . '\').remove();" src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png">';

            $Return .= '<h3 class="fieldTypeHandle">' . $ViewTitle . '</h3>';
            $Return .= '<div class="admin_config_toolbar">';

            // if there is a config
            $Return .= '<span style="float:right;"><p>' . $Icons . '</p></span>';

            $Return .= '<p>' . $ViewDesc . '</p>';
            $Return .= '<div style="clear:right;"></div>';
            $Return .= '</div>';
            $Return .= '<div id="ExtraSetting_term_id" style="text-align: right;" class="admin_config_panel">';
            if (function_exists($func)) {

                $show = 'none';
                if (!empty($Config['_FormProcessors'][$processID]['_configPanelOpen'])) {
                    $show = 'block';
                }

                $Return .= '<div style="text-align: left; display:' . $show . ';" id="config_' . $processID . '" class="widefat">';
                $Return .= '<h3>Configuration</h3>';
                $Return .= '<div class="inside"><p>';
                $Return .= $func($processID, $Config['_main_table'], $Config);
                $Return .= '</p></div>';
                $Return .= '</div>';
            }
            $Return .= '</div>';
            $Return .= '</div>';
        }
    }


    return $Return;
}

function df_listProcessors() {
    //return '<li><a onclick="">'.__DIR__.'</a></li>';
    $processesDirs = opendir(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors');
    $Return = '';
    while (($processor = readdir($processesDirs)) !== false) {
        if ($processor != '.' && $processor != '..' && $processor != 'index.htm') {
            if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php')) {
                include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php');
                $Icon = WP_PLUGIN_URL . '/db-toolkit/data_report/arrow_switch.png';
                $Return .= '<li><a onclick="df_addPRocess(\'' . $processor . '\');"><img src="' . $Icon . '" align="absmiddle" /> ' . $Title . '</a></li>';
            }
        }
    }
    //<li><a onclick="">WOOT</a></li>
    return $Return;
}
function df_listViewProcessors() {
    //return '<li><a onclick="">'.__DIR__.'</a></li>';
    if(!file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors')){
        return '<li><a>You dont have any view processors installed.</a></li>';
    }
    $processesDirs = opendir(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors');
    $Return = '';
    while (($processor = readdir($processesDirs)) !== false) {
        if ($processor != '.' && $processor != '..' && $processor != 'index.htm') {
            if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php')) {
                include(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php');
                $Icon = WP_PLUGIN_URL . '/db-toolkit/data_report/arrow_switch.png';
                $Return .= '<li><a onclick="df_addViewProcess(\'' . $processor . '\');"><img src="' . $Icon . '" align="absmiddle" /> ' . $ViewTitle . '</a></li>';
            }
        }
    }
    //<li><a onclick="">WOOT</a></li>
    return $Return;
}

function df_addProcess($processor, $table) {

    $Return = '';

    if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php')) {
        include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php');
        include_once(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/functions.php');
        $processID = uniqid('process_');

        $func = 'config_' . $processor;


        $Icons = '<span title="Run Process on Insert" onclick="df_setToggle(\'onInsert_' . $processID . '\');" id="onInsert_' . $processID . '" class="button-highlighted"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-insert.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        $Icons .= '&nbsp;<span title="Run Process on Update" onclick="df_setToggle(\'onUpdate_' . $processID . '\');" id="onUpdate_' . $processID . '" class="button"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-pencil.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        $Icons .= '&nbsp;<span title="Run Process on Delete" onclick="df_setToggle(\'onDelete_' . $processID . '\');" id="onDelete_' . $processID . '" class="button"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-delete.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        if (function_exists($func)) {
            $Icons .= '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="button-highlighted"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px 5px 20px;">Settings</span></span>';
            $Icons .= '<input type="checkbox" value="1" id="configirator_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_configPanelOpen]" checked="checked" style="display: none;">';
        }
        $Icons .= '<input type="checkbox" value="1" id="onInsert_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onInsert]" checked="checked" style="display: none;">';
        $Icons .= '<input type="checkbox" value="1" id="onUpdate_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onUpdate]" style="display: none;">';
        $Icons .= '<input type="checkbox" value="1" id="onDelete_' . $processID . '_check" name="Data[Content][_FormProcessors][' . $processID . '][_onDelete]" style="display: none;">';


        $Return .= '<div style="width: 550px; opacity: 1;" class="admin_list_row3 table_sorter postbox" id="' . $processID . '">';
        $Return .= '<input type="hidden" name="Data[Content][_FormProcessors][' . $processID . '][_process]" value="' . $processor . '" />';
        $Return .= '<img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery(\'#' . $processID . '\').remove();" src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png">';
        $Return .= '<h3 class="fieldTypeHandle">' . $Title . '</h3>';
        $Return .= '<div class="admin_config_toolbar">';

        $Return .= '<span style="float:right;"><p>' . $Icons . '</p></span>';
        $Return .= '<p>' . $Desc . '</p>';
        $Return .= '<div style="clear:right;"></div>';
        $Return .= '</div>';
        $Return .= '<div id="ExtraSetting_term_id" style="text-align: right;" class="admin_config_panel">';
        if (function_exists($func)) {
            $Return .= '<div style="text-align: left;" id="config_' . $processID . '" class="widefat">';
            $Return .= '<h3>Configuration</h3>';
            $Return .= '<div class="inside"><p>';
            $Return .= $func($processID, $table);
            $Return .= '</p></div>';
            $Return .= '</div>';
        }
        $Return .= '</div>';
        $Return .= '</div>';
    }

    return $Return;
}
function df_addViewProcess($processor, $table) {

    $Return = '';

    if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php')) {
        include(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/conf.php');
        include_once(WP_PLUGIN_DIR . '/db-toolkit/data_report/processors/' . $processor . '/functions.php');
        $processID = uniqid('process_');

        $func = 'config_' . $processor;


        $Return .= '<div style="width: 750px; opacity: 1;" class="admin_list_row3 table_sorter postbox" id="' . $processID . '">';
        $Return .= '<input type="hidden" name="Data[Content][_ViewProcessors][' . $processID . '][_process]" value="' . $processor . '" />';
        $Return .= '<img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery(\'#' . $processID . '\').remove();" src="' . WP_PLUGIN_URL . '/db-toolkit/images/cancel.png">';
        $Return .= '<h3 class="fieldTypeHandle">' . $ViewTitle . '</h3>';
        $Return .= '<div class="admin_config_toolbar">';
        $Icons = '';
        if (function_exists($func)) {
            $Icons = '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="button-highlighted"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px 5px 20px;">Settings</span></span>';
            $Icons .= '<input type="checkbox" value="1" id="configirator_' . $processID . '_check" name="Data[Content][_ViewProcessors][' . $processID . '][_configPanelOpen]" checked="checked" style="display: none;">';
        }

        $Return .= '<span style="float:right;"><p>' . $Icons . '</p></span>';
        $Return .= '<p>' . $ViewDesc . '</p>';
        $Return .= '<div style="clear:right;"></div>';
        $Return .= '</div>';
        $Return .= '<div id="ExtraSetting_term_id" class="admin_config_panel">';
        if (function_exists($func)) {
            $Return .= '<div style="text-align: left;" id="config_' . $processID . '" class="widefat">';
            $Return .= '<h3>Configuration</h3>';
            $Return .= '<div class="inside"><p>';
            $Return .= $func($processID, $table);
            $Return .= '</p></div>';
            $Return .= '</div>';
        }
        $Return .= '</div>';
        $Return .= '</div>';
    }

    return $Return;
}

function dr_BuildReportFilters($Config, $EID, $Defaults = false) {

    // For the HardUserBase filter that assigned to ta filed, make sure its too is hard filtered.
    //setup indexed filters
    //dump($Config['_IndexType']);
    $Return = '';
    $Keywords = '';

    if (!empty($Defaults['_keywords'])) {
        $Keywords = $Defaults['_keywords'];
    }

    if (!empty($Config['_Show_KeywordFilters'])) {

        if (empty($_SESSION['lockedFilters'][$EID]['_keywords'])) {
            $Return .= '<div class="filterField">';
            if (!empty($Config['_Keyword_Title'])) {
                $Return .= '<h2>' . $Config['_Keyword_Title'] . '</h2>';
            }
            $Return .= '<input type="text" name="reportFilter[' . $EID . '][_keywords]" id="keyWordFilter" class="filterSearch" value="' . $Keywords . '" />&nbsp;&nbsp;&nbsp;</div>';
        } else {
            if (!empty($Config['_Hide_FilterLock'])) {
                $Return .= '<span class="highlight"><div class="filterField">';
                if (!empty($Config['_Keyword_Title'])) {
                    $Return .= '<strong>' . $Config['_Keyword_Title'] . '</strong><br />';
                }
                $Return .= '<input type="text" name="reportFilter[' . $EID . '][_keywords]" id="keyWordFilter" class="filterSearch" value="' . $Keywords . '" />&nbsp;&nbsp;&nbsp;</div></span>';
            }
        }
    }
    foreach ($Config['_Field'] as $Field => $FieldType) {
        if (empty($_SESSION['lockedFilters'][$EID][$Field])) {
            $type = $FieldType;
            if (!empty($type[1])) {                
                $index = explode('_', $Config['_IndexType'][$Field]);                
                if ($index[0] == 'index') {
                    if (function_exists($type[0] . '_showFilter')) {
                        $func = $type[0] . '_showFilter';
                        $Return .= $func($Field, $type[1], $Defaults, $Config, $EID);
                    }
                }
            }
        } else {
            if (empty($Config['_Hide_FilterLock'])) {
                $type = $FieldType;
                if (!empty($type[1])) {                    
                    $index = explode('_', $Config['_IndexType'][$Field]);
                    if ($index[0] == 'index') {
                        if (function_exists($type[0] . '_showFilter')) {
                            $func = $type[0] . '_showFilter';
                            $Return .= $func($Field, $type[1], $Defaults, $Config, $EID);
                        }
                    }
                }
            }
        }
    }

    return $Return;
}

function df_alignmentSetup($id, $Default = false) {
    $Return = 'Justify: <select name="Data[Content][_Justify][' . $id . ']" id="Justify_' . $id . '_settings">';
    $Sel = '';
    if ($Default == 'Left') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="left" ' . $Sel . '>Left</option>';
    $Sel = '';
    if ($Default == 'Center') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="Center" ' . $Sel . '>Center</option>';
    $Sel = '';
    if ($Default == 'Right') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="Right" ' . $Sel . '>Right</option>';
    $Return .= '</select>';
    //'<input type="text" name="Data[Content][ImageSizeI]['.$id.']" value="100" class="textfield" size="3" maxlength="3" /> ';
    return $Return;
}

function df_FormWidthSetup($id, $Default = false) {
    if(empty($Default)){
        $Default = 'input-medium';
    }
    $Return = ' Form Field Size: <select name="Data[Content][_FormFieldWidth][' . $id . ']" id="FormFieldWidth_' . $id . '_settings">';
    $Return .= '<optgroup label="Preset Sizes">';
    $Sel = '';
    if ($Default == 'input-mini') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-mini" ' . $Sel . '>Mini</option>';

    $Sel = '';
    if ($Default == 'input-small') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-small" ' . $Sel . '>Small</option>';
    
    $Sel = '';
    if ($Default == 'input-medium') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-medium" ' . $Sel . '>Medium</option>';

    $Sel = '';
    if ($Default == 'input-large') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-large" ' . $Sel . '>Large</option>';

    $Sel = '';
    if ($Default == 'input-xlarge') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-xlarge" ' . $Sel . '>XLarge</option>';

    $Sel = '';
    if ($Default == 'input-xxlarge') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="input-xxlarge" ' . $Sel . '>XXLarge</option>';

    $Return .= '</optgroup>';
    $Return .= '<optgroup label="Incremental">';
   
        for($i=1; $i<=12; $i++){
            $Sel = '';
            if ($Default == 'span'.$i) {
                $Sel = 'selected="selected"';
            }            
            $Return .= '<option value="span'.$i.'" ' . $Sel . '>Span '.$i.'</option>';
        }
    $Return .= '</optgroup>';
    $Return .= '</select>';
    //'<input type="text" name="Data[Content][ImageSizeI]['.$id.']" value="100" class="textfield" size="3" maxlength="3" /> ';
    return $Return;
}

function dr_BuildUpDateForm($EID, $ID) {
    $Data = getelement($EID);
    $Data['_ActiveProcess'] = 'update';
    
    $Out['title'] = $Data['Content']['_EditFormText'];
    $PreOut = df_BuildCaptureForm($Data, $ID);
    if (!is_array($PreOut)) {
        return df_buildQuickCaptureForm($EID);
    }
    if (!empty($PreOut['title'])) {
        $Out['title'] = $PreOut['title'];
    }
    $Out['html'] = $PreOut['html'];
    $Out['width'] = $PreOut['width'];
    return $Out;
}

function df_buildDataSheet($EID, $ID) {

    $Data = getelement($EID);
    $Return = '<h2>Edit Entry</h2>';
    $Return .= df_BuildCaptureForm($Data, $ID);
    return $Return;
}


function dr_BuildReportGrid($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false, $wherePush = false, $getOverride = false) {

    global $wpdb;

    if(!empty($getOverride)){
        parse_str($getOverride, $_GET);
    }
    
    $queryJoin = array();
    $queryWhere = array();
    $queryJoinStruct = array();
    $queryWhereStruct = array();
    $queryLimit = '';
    $querySelects = array();
    $WhereTag = '';
    $groupBy = '';

    $Media = getelement($EID);
    $Config = $Media['Content'];


    /// Explode types for later use
    foreach ($Config['_Field'] as $Field => $Type) {
        $Config['_Field'][$Field] = explode('_', $Type);

    }
    /// Load queryfilters
    $joinIndex = 'a';
    foreach ($Config['_Field'] as $Field => $Type) {

        // Create a Join ID
        $joinIndex++;

        // SELECT: if clone find masters
        if(!empty($Config['_CloneField'][$Field])){
            $prime = $querySelects[$Config['_CloneField'][$Field]['Master']];
        }else{
            $prime = '`prim`.`'.$Field.'`';
        }
        //$prim = $Field;
        //SELECT: Add Field to SELECT if visible
        $querySelects[$Field] = $prime;

        // SELECT: Add return values select
        if(in_array($Field, $Config['_ReturnFields'])){
            $querySelects['_return_'.$Field] = '`prim`.`'.$Field.'`';
        }
        //$querySelects[$Field]
        // Run Filters that have been set through each field type
        if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Type[0] . '/queryfilter.php')) {
            include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Type[0] . '/queryfilter.php');
        }
    }    
    //Apply Ordering and sorting.
    if(empty($_SESSION['report_'.$Media['ID']]['SortDir'])){        
        $_SESSION['report_' . $Media['ID']]['SortDir'] = $Config['_SortDirection'];
    }    
    if(empty($_SESSION['report_'.$Media['ID']]['SortField'])){
        $_SESSION['report_' . $Media['ID']]['SortField'] = $Config['_SortField'];
    }
    if(!empty($SortField)){
        $_SESSION['report_' . $Media['ID']]['SortField'] = $SortField;
    }
    if(!empty($SortDir)){
        $_SESSION['report_' . $Media['ID']]['SortDir'] = $SortDir;
    }
    
    $orderStr = 'ORDER BY `'.$_SESSION['report_' . $Media['ID']]['SortField'].'` '.$_SESSION['report_' . $Media['ID']]['SortDir'];
    //vardump($_SESSION['report_' . $Media['ID']]);
    // apply AS types to selects
    // Remove hidden fields from query to save memory
    foreach($querySelects as $Field=>$Value){
        if(strpos($Config['_IndexType'][$Field],'_show') !== false || strpos($Field,'_return_') !== false){
            $querySelects[$Field] = $Value.' AS `'.$Field.'`';
        }else{
            unset($querySelects[$Field]);
        }
    }

    if(!empty($queryWhere)){        
        $WhereTag = " WHERE ";
        foreach($queryWhere as $whereType => $whereSet){
            if(is_array($whereSet)){
                $queryWhereStruct[] = '('.implode(' '.$whereType.' ', $whereSet).')';
            }else{
                $queryWhereStruct[] = $whereSet;
            }
        }
    }


    foreach($queryJoin as $Field=>$joinField){
        foreach($joinField as $joinTo){
            foreach($joinTo as $joinSet){
                $queryJoinStruct[] = implode('', $joinSet);
            }
        }
    }
    // Build Query
    $Query = "SELECT \n";
        // add selects
        $Query .= implode(", \n", $querySelects)."\n";
        // add table
        $Query .= "FROM `" . $Config['_main_table'] . "` AS prim \n";
        // add joins
        $Query .= implode(" \n", $queryJoinStruct) . " \n ";
        // add where tag if needed wheres
        $Query .= $WhereTag." \n";
        // add wheres
        $Query .= implode(" AND \n", $queryWhereStruct) . "\n ";
        // add group by
        if(!empty($groupBy)){
            $Query .= ' GROUP BY ';
            if(count($groupBy) > 1){
                $Query .= implode(", \n", $groupBy) . " \n ";
            }else{
                $Query .= implode(" \n", $groupBy) . " \n ";
            }
        }
        // add orderby
        $Query .= $orderStr . " \n ";
        // add query limits
        $Query .= $queryLimit . "\n;";

//return query structure to system.
        if(!empty($_GET['debug'])){

            echo "<textarea style=\"width:100%; height; auto;\">".$Query."</textarea>";

        }

        if(strtolower($Format) == 'sql'){
            return $Query;
        }
        if(strtolower($Format) == 'data'){
            return $wpdb->get_results($Query, ARRAY_A);
        }

    ob_start();
        include(DB_TOOLKIT.'data_report/listmode.php');
    return ob_get_clean();

}

function df_inlineedit($Entry, $ID, $Value) {

    $part = explode('_', $Entry, 3);
    $Element = getelement($part[1]);
    $Config = $Element['Content'];
    $preQuery = mysql_query("SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "'");
    $Data[$part[1]] = mysql_fetch_assoc($preQuery);
    $Data[$Config['_ReturnFields'][0]] = $ID;
    $Data[$part[1]][$part[2]] = $Value;
    $return = df_processupdate($Data, $part[1]);
    if (empty($Config['_NotificationsOff'])) {
        return $return['Message'];
    }
    return 1;
}

function df_processupdate($Data, $EID) {
    global $wpdb;
    $Element = getelement($EID);
    $Config = $Element['Content'];
    //dump($Config);
    // Load Entry's data for hidden values

    //$preQuery = mysql_query("SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'");
    //$PreData = mysql_fetch_assoc($preQuery);

    $preQuery = "SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'";
    $PreData = $wpdb->get_results($preQuery, ARRAY_A);
    $PreData = $PreData[0];

    /* Auditing Disabled for now. Need to find a way to audit dynamic and changed tables.
    if ($Config['_EnableAudit']) {
        $memberID = 0;
        if (!empty($_SESSION['UserBase']['Member']['ID'])) {
            $memberID = $_SESSION['UserBase']['Member']['ID'];
        }
        $lres = mysql_query("SHOW COLUMNS FROM " . $Config['_main_table']);
        $prerows = array();
        while ($row = mysql_fetch_assoc($lres)) {
            $prerows[] = $row['Field'];
        }
        $rows = implode(',', $prerows);
        if (mysql_query("CREATE TABLE `_audit_" . $Config['_main_table'] . "` SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "' LIMIT 1")) {
            // new entry

            mysql_query("ALTER TABLE `_audit_" . $Config['_main_table'] . "` ADD `_ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ,
						ADD `_DateInserted` DATETIME NOT NULL AFTER `_ID` ,
						ADD `_DateModified` DATETIME NOT NULL AFTER `_ID` ,
						ADD `_User` INT NOT NULL AFTER `_DateModified` ,
                                                ADD `_RawData` TEXT NOT NULL AFTER `_DateInserted`");
            mysql_query("UPDATE `_audit_" . $Config['_main_table'] . "` SET `_DateModified` = '" . date('Y-m-d H:i:s') . "', `_DateInserted` = '" . date('Y-m-d H:i:s') . "', `_User` = '" . $memberID . "', `_RawData` = '" . mysql_real_escape_string(serialize($Data)) . "';");
            mysql_query("INSERT INTO `_audit_" . $Config['_main_table'] . "` SET `_DateInserted` = '" . date('Y-m-d H:i:s') . "', `_User` = '" . $memberID . "', `_RawData` = '" . mysql_real_escape_string(serialize($Data)) . "', `" . $Config['_ReturnFields'][0] . "`, " . $OldData . " = '" . $Data[$Config['_ReturnFields'][0]] . "'  ;");
            mysql_query("INSERT INTO `_audit_" . $Config['_main_table'] . "` SET `_DateInserted` = '" . date('Y-m-d H:i:s') . "', `_User` = '" . $memberID . "', `_RawData` = '" . mysql_real_escape_string(serialize($Data)) . "', `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'  ;");
        } else {
            $predata = mysql_query("SELECT * FROM " . $Config['_main_table'] . " WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "';");
            $prerow = mysql_fetch_assoc($predata);
            $OldData = array();
            foreach ($prerow as $Field => $Value) {
                $OldData[] = "`" . $Field . "` = '" . mysql_real_escape_string($Value) . "' ";
            }
            $OldData = implode(', ', $OldData);
            $UpdateQuery = "UPDATE `_audit_" . $Config['_main_table'] . "` SET `_DateModified` = '" . date('Y-m-d H:i:s') . "', `_User` = '" . $memberID . "', `_RawData` = '" . mysql_real_escape_string(serialize($Data)) . "', " . $OldData . " WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "' ORDER BY `_ID` DESC LIMIT 1;";
            mysql_query($UpdateQuery);
            mysql_query("INSERT INTO `_audit_" . $Config['_main_table'] . "` SET `_DateInserted` = '" . date('Y-m-d H:i:s') . "', `_RawData` = '" . mysql_real_escape_string(serialize($Data)) . "', `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'  ;");
        }
    }
    //go through the Submitted Data / apply fieldtype filters and add processed value to update queue
    */

    
    foreach ($Config['_Field'] as $Field => $Type) {
        if(empty($Data[$EID][$Field]) && !empty($Config['_Required'][$Field])){
                $return['_error_'][] = $Config['_FieldTitle'][$Field].' is required.';
                $return['_fail_'][$Field] = true;                
        }
        if(isset($Data[$EID][$Field]) || isset($_FILES['dataForm']['size'][$EID][$Field])){
            $typeSet = explode('_', $Type);
            if (!empty($typeSet[1])) {
                if (function_exists($typeSet[0] . '_handleInput')) {
                    $Func = $typeSet[0] . '_handleInput';
                    if (is_array($Data[$EID][$Field])) {
                        $Data[$EID][$Field] = serialize($Data[$EID][$Field]);
                    } else {
                        if (!isset($Data[$EID][$Field])) {
                            $Data[$EID][$Field] = '';
                        }
                    }
                    $Element['_ActiveProcess'] = 'update';
                    $newValue = $Func($Field, $Data[$EID][$Field], $typeSet[1], $Element, $PreData, $Data);
                    if(is_array($newValue[$Field])){
                        if(!empty($newValue[$Field]['_fail_'])){
                            $return['_error_'][] = $newValue[$Field]['_error_'];
                            $return['_fail_'][$Field] = true;
                        }
                    }
                    //}
                } else {
                    $newValue = $Data[$EID][$Field];
                }
            } else {
                $newValue = $PreData[$Field];
            }
            if (substr($Field, 0, 2) != '__') {
                $updateData[$Field] = $newValue;
                //$updateData[] = "`".$Field."` = '".mysql_real_escape_string($newValue)."' ";
            }
        }
    }
    // return if any failed.
    if(!empty($return['_fail_'])){
        return $return;
    }
    // process update processess
    if (!empty($Config['_FormProcessors'])) {

        foreach ($Config['_FormProcessors'] as $processID => $Setup) {
            if(empty($updateData)){
                if(empty($Config['_InsertFail'])) {
                    $Return['Message'] = 'Entry Update Failed';
                }else {
                    $Return['Message'] = $Config['_UpdateFail'];
                }
                $Return['noticeType'] = 'error';
                return $Return;
            }

            if (!empty($Setup['_onUpdate'])) {
                if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php')) {
                    include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php';
                    $func = 'pre_process_' . $Setup['_process'];
                    if (function_exists($func)) {
                        $updateData = $func($updateData, $Setup, $Config);
                        if(!empty($updateData['__fail__'])){
                            $Return['noticeType'] = 'error';
                            if(!empty($updateData['__error__'])) {
                                $Return['Message'] = $updateData['__error__'];
                                return $Return;
                            }
                            if(empty($Config['_InsertFail'])) {
                                $Return['Message'] = 'Entry Insert Failed';
                            }else{
                                $Return['Message'] = $Config['_InsertFail'];
                            }

                            return $Return;
                        }

                    }
                }
            }
        }
    }


    
    // Post Process
    foreach ($Config['_Field'] as $Field => $Type) {
        $typeSet = explode('_', $Type);
        if (!empty($typeSet[1])) {
            if (function_exists($typeSet[0] . '_postProcess')) {
                $Func = $typeSet[0] . '_postProcess';
                $Element['_ActiveProcess'] = 'update';
                $Func($Field, $Data[$EID][$Field], $typeSet[1], $Element, $Data[$EID], $Data[$Config['_ReturnFields'][0]]);
            }
        }
    }

        //foreach ($updateData as $Field => $newValue) {
    //    $newData[] = "`" . $Field . "` = '" . mysql_real_escape_string($newValue) . "' ";
    //}
    //$Updates = implode(', ', $newData);
    //$Query = "UPDATE `" . $Config['_main_table'] . "` SET " . $Updates . " WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'";
    if(empty($updateData)){
        $Return['Message'] = 'umm, there was nothing to update.';
        $Return['noticeType'] = 'error';
        return $Return;
    }


    if($wpdb->update($Config['_main_table'], $updateData, array($Config['_ReturnFields'][0] => $Data[$Config['_ReturnFields'][0]]))){
        $update = true;
    }else{
        $update = false;
    }


    if (!empty($Config['_ReturnFields'][0])) {
        $ReturnVals = implode(', ', $Config['_ReturnFields']);
        //$outq = mysql_query("SELECT " . $ReturnVals . " FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "';");
        //$dta = mysql_fetch_assoc($outq);

        $dta = $wpdb->get_results("SELECT " . $ReturnVals . " FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "';", ARRAY_A);
        $dta = $dta[0];
        $outstr = array();
        foreach ($dta as $key => $val) {
            $outstr[] = $key . '=' . $val;
        }
        $Return['Value'] = implode('&', $outstr);
    } else {
        $Return['Value'] = $ID;
    }

    
    // post update processess
    if (!empty($Config['_FormProcessors'])) {
        foreach ($Config['_FormProcessors'] as $processID => $Setup) {
            if (!empty($Setup['_onUpdate'])) {
                if(empty($updateData)){
                    if(empty($Config['_InsertFail'])) {
                        $Return['Message'] = 'Entry Update Failed';
                    }else {
                        $Return['Message'] = $Config['_UpdateFail'];
                    }
                    return $Return;
                }

                if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php')) {
                    include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php';
                    $func = 'post_process_' . $Setup['_process'];
                    if (function_exists($func)) {
                        $Data = $func($updateData, $Setup, $Config);
                        if (!is_array($Data)) {
                            $Config['_UpdateSuccess'] = $Data;
                        }
                    }
                }
            }
        }
    }
    //if ($update == true) {
        if (empty($Config['_UpdateSuccess'])) {
            $Return['Message'] = 'Entry updated successfully';
        } else {
            $Return['Message'] = $Config['_UpdateSuccess'];
        }
        $Return['noticeType'] = 'success';
        return $Return;
    //}
    $Return['noticeType'] = 'error';
    $Return['Message'] = 'Nothing to update.';
    return $Return;
}

function df_deleteEntries($EID, $Data) {
    global $wpdb;
    $Data = df_cleanArray(explode('|||', $Data));
    $El = getelement($EID);
    $Config = $El['Content'];
    if(empty($Config['_Show_Delete']) && empty($Config['_Show_Delete_action'])){
        return 'Deleting is Disabled';
    }
    if (!empty($RefConfig['Field'])) {
        if (in_array('imageupload', $RefConfig['Field'])) {
            $ImagesToDelete = array_keys($RefConfig['Field'], 'imageupload');
        }
    }
    $Index = 0;
    $Return = '';
    foreach ($Data as $ID) {
        $ID = str_replace($EID . '_', '', $ID);

        //$Pre = $wpdb->escape("SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "' LIMIT 1;");
        $Pre = "SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "' LIMIT 1;";
        $OldData = $wpdb->get_row($Pre, ARRAY_A);
        dr_trackActivity('Delete', $EID, $ID);

        if (!empty($ImagesToDelete)) {
            foreach ($ImagesToDelete as $Field) {
                if (file_exists($OldData[$Field])) {
                    unlink($OldData[$Field]);
                }
            }
        }
        // post update processess
        if (!empty($Config['_FormProcessors'])) {
            foreach ($Config['_FormProcessors'] as $processID => $Setup) {
                if (!empty($Setup['_onDelete'])) {
                    if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php')) {
                        include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php';
                        $func = 'pre_process_' . $Setup['_process'];
                        if (function_exists($func)) {
                            $OldData = $func($OldData, $Setup, $Config);
                        }
                    }
                }
            }
        }
        $deleteQuery = "DELETE FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "' LIMIT 1;";
        $Rows = $wpdb->query($deleteQuery);
        // post update processess
        if (!empty($Config['_FormProcessors'])) {
            foreach ($Config['_FormProcessors'] as $processID => $Setup) {
                if (!empty($Setup['_onDelete'])) {
                    if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php')) {
                        include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php';
                        $func = 'post_process_' . $Setup['_process'];
                        if (function_exists($func)) {
                            $OldData = $func($OldData, $Setup, $Config);
                            if (!is_array($OldData)) {
                                //$Config['_UpdateSuccess'] = $OldData;
                            }
                        }
                    }
                }
            }
        }

        $Index++;
    }
    $Note = 'Item';
    if ($Rows > 1) {
        $Note = 'Items';
    }
    return $Rows . ' ' . $Note . ' Deleted<br />';
}

function dr_importer($EID) {


    $_SESSION['importKey'] = uniqid(rand(100, 999) . '_importKey_');


    $Data = getelement($EID);
    $html = '<form enctype="multipart/form-data" method="post" action="' . $_SERVER['HTTP_REFERER'] . '" class="formular" id="import_form_' . $EID . '" >';
    $html .= '<input type="hidden" name="importInterface" id="importInterface" value="' . $EID . '" />';
    $html .= '<input type="hidden" name="importKey" id="importKey" value="' . $_SESSION['importKey'] . '" />';

    $html .= '<div class="form-gen-field-wrapper" id="form-field-importer">';
    $html .= '<label class="form-gen-lable singletext" for="fileSelector" id="fileSelectorLabel">File Import <em>(required)</em></label>';
    $html .= '<input type="file" class="validate[required]" id="' . $EID . '" name="fileImport">';
    $html .= '<div class="caption" id="importCaption">Accepted Format: .CSV</div>';
    $html .= '<div style="clear: left;"></div>';
    $html .= '</div>';



    $html .= '</form>';


    $Out['title'] = 'Import';
    $Out['html'] = $html;
    $Out['width'] = 300;
    return $Out;
}

function dr_prepairImport($EID) {

    /* if (($handle = fopen($_SESSION['import_'.$EID]['import']['file'], "r")) !== FALSE) {
      $_SESSION['importKey'] = uniqid(rand(100, 999).'_importKey_');
      //while($data = fgetcsv($handle, 1024, $delim)){

      //}
      //fclose($handle);
      } */
    if (empty($_SESSION['import_' . $EID]['startpoint'])) {
        $start = 0;
        if (!empty($_SESSION['import_' . $EID]['import']['importSkipFirst'])) {
            $start = 1;
        }
        $_SESSION['import_' . $EID]['startpoint'] = $start;
    }

    $html .= '<div id="textImportResult">Starting at: ' . $_SESSION['import_' . $EID]['startpoint'] . ': <span id="import_processedCount">0</span> of ' . (count(file($_SESSION['import_' . $EID]['import']['file'])) - $start) . ' entries imported.</div>';
    $html .= '<div id="' . $EID . '_importProgress"></div>';
    //$html .= filesize($_SESSION['import_'.$EID]['import']['file']);

    $Out['title'] = 'Data Importer';
    $Out['html'] = $html;
    $Out['width'] = 300;

    return $Out;
}

function dr_processImport($EID) {

    $PreCount = file($_SESSION['import_' . $EID]['import']['file']);
    $Total = count($PreCount);
    unset($PreCount);

    if (($handle = fopen($_SESSION['import_' . $EID]['import']['file'], "r")) !== FALSE) {
        $_SESSION['importKey'] = uniqid(rand(100, 999) . '_importKey_');


        $Row = 0;
        $Processed = 1;
        //if(!empty($data)){
        $Query = '';
        //}

        while ($data = fgetcsv($handle, 0, $_SESSION['import_' . $EID]['import']['delimiter'])) {

            if ($Row == $_SESSION['import_' . $EID]['startpoint']) {
                if (empty($First)) {
                    $Query .= 'INSERT INTO `' . $_SESSION['import_' . $EID]['import']['table'] . '`';
                }
                $Fields = array();
                $Values = array();
                foreach ($_SESSION['import_' . $EID]['import']['map'] as $Field => $Value) {
                    if ($Value != 'null') {
                        $Fields[] = '`' . $Field . '`';
                        $Values[] = "'" . mysql_real_escape_string($data[$Value]) . "'";
                    }
                }
                if (empty($First)) {
                    $Query .= '(' . implode(',', $Fields) . ') VALUES ';
                    $First = true;
                }
                $QueryValues[] = "(" . implode(',', $Values) . ")";
                $_SESSION['import_' . $EID]['startpoint']++;
                if ($Processed == 25 || $_SESSION['import_' . $EID]['startpoint'] >= $Total) {
                    break;
                }
                $Processed++;
            }
            $Row++;
        }
        fclose($handle);
    }
    $Query = $Query . implode(',', $QueryValues);
    mysql_query($Query);
    //vardump($Query);
    //echo mysql_error();
    $out['error'] = mysql_error();
    $out['query'] = $Query;
    $out['p'] = round(($_SESSION['import_' . $EID]['startpoint'] / $Total) * 100, 2);
    $out['d'] = $_SESSION['import_' . $EID]['startpoint'];
    if ($_SESSION['import_' . $EID]['startpoint'] == $Total) {
        if (file_exists($_SESSION['import_' . $EID]['import']['file'])) {
            unlink($_SESSION['import_' . $EID]['import']['file']);
        }
        unset($_SESSION['import_' . $EID]);
        return 'false';
    }
    //$out = $Out.' - '.$html;
    return $out;
}

function dr_buildImportManager($EID, $delim = ',') {

    if (($handle = fopen($_SESSION['import_' . $EID]['import']['file'], "r")) !== FALSE) {
        $_SESSION['importKey'] = uniqid(rand(100, 999) . '_importKey_');

        $Row = 1;
        $head = array();
        $body = array();
        $titles = array();
        while ($data = fgetcsv($handle, 1000, $delim)) {
            //vardump($data);
            if ($Row == 1) {
                foreach ($data as $field) {
                    $head[] = '<th style="white-space: nowrap;" scope="col">' . $field . '</th>';
                    $titles[] = $field;
                }
            } elseif ($Row == 2) {
                foreach ($data as $field) {
                    $body[] = '<td style="white-space: nowrap;">' . $field . '</td>';
                }
            }
            if ($Row == 2) {
                break;
            }
            $Row++;
        }
        fclose($handle);
    } else {
        $Out['title'] = 'Error';
        $Out['html'] = 'Could not import. Please try again.';
        $Out['width'] = 250;
    }
    //vardump($data);
    // setup selector
    $Selector = '';
    foreach ($titles as $csvField => $column) {
        $Selector .= '<option value="' . $csvField . '">' . $column . '</option>';
    }
    //vardump($titles);
    //vardump($head);
    //vardump($body);
    //ob_start();
    //vardump($_SESSION['import_'.$EID]);
    $html = '<form enctype="multipart/form-data" method="post" action="' . $_SERVER['HTTP_REFERER'] . '" class="formular" id="import_form_' . $EID . '" >';

    $html .= '<input type="hidden" name="importInterface" id="importInterface" value="' . $EID . '" />';
    $html .= '<input type="hidden" name="importPrepairKey" id="importPrepairKey" value="' . $_SESSION['importKey'] . '" />';

    $html .= '<h3>';
    $html .= 'Delimiter: <input type="text" name="importDelimeter" id="importDelimeter" value="' . stripslashes_deep($delim) . '" style="width: 20px;" onkeyup="dr_reloadImport(\'' . $EID . '\', this.value);" /> ';
    $html .= '&nbsp;Skip First Row : <input type="checkbox" name="importSkipFirst" id="importSkipFirst" value="1" checked="checked" /> ';
    $html .= '</h3>';

    $html .= '<div style="width:780px; overflow:auto">';
    $html .= '<table width="100%" cellspacing="2" cellpadding="2" border="0" class="widefat">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th scope="col"></th>';
    // heading
    $html .= implode('', $head);
    $html .= '</tr>';
    $html .= '</thead>';

    $html .= '<tbody>';
    $html .= '<tr >';
    $html .= '<td>1</td>';
    // body
    $html .= implode('', $body);
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';

    $html .= '<h3>Field Mapping</h3>';

    $Element = getelement($EID);
    //vardump($Element);
    foreach ($Element['Content']['_Field'] as $Field => $Type) {

        $html .= '<div style="float: left; overflow: hidden; width: 22.5%;">';
        $html .= '<div class="form-gen-field-wrapper" id="form-field-' . $Field . '">';
        $html .= '<label>' . $Element['Content']['_FieldTitle'][$Field] . '</label>';

        $html .= '<select id="dataField_' . $Field . '" name="importMap[' . $Field . ']">';
        $html .= '<option value="null"></option>';
        $html .= $Selector;
        $html .= '</select>';
        $html .= '<div class="caption" id="caption_' . $EID . '_Name">' . $Element['Content']['_FieldCaption'][$Field] . '&nbsp;</div>';

        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '<div style="clear:both;"></div>';
    $html .= '</form>';


    $Out['title'] = 'Import Data Setup';
    $Out['html'] = $html;
    $Out['width'] = 800;
    return $Out;
}

function dr_cancelImport($EID) {

    if (file_exists($_SESSION['import_' . $EID]['import']['file'])) {
        unlink($_SESSION['import_' . $EID]['import']['file']);
    }
    unset($_SESSION['import_' . $EID]);

    return true;
}

function dt_listApps() {

    $appList = get_option('dt_int_Apps');

    ob_start();
    vardump($appList);
    $out['html'] = ob_get_clean();

    


    $Return = '<div style="float:left; width: 25%;">';
    $Return .= '<h3>Application</h3>';
    $Return .= '<select id="dbtoolkit_AppList">';
    $Return .= '<option value=""></option>';
    foreach ($appList as $app => $state) {
        $Sel = '';
        if ($default == $app)
            $Sel = 'selected="selected"';

        if ($state['state'] == 'open')
            $Return .= '<option value="' . $app . '" ' . $Sel . '>' . ucwords($state['name']) . '</option>';
    }
    $Return .= '</select>';
    $Return .= '</div>';
    $Return .= '<div style="float:left; width: 60%; padding-left:10px;" id="dbtoolkit_InterfaceList"></div>';
    //$Return .= '<div style="float:left; width: 30%;" id="dbtoolkit_ClusterList"></div>';

    $Out['html'] = $Return;
    if (!empty($default)) {
        $Out['app'] = $default;
    }

    return $Out;
}

function dt_listInterfaces($App) {
    if(empty($App))
        return '<h3>Please select an app</h3>';

    $app = get_option('_'.$App.'_app');
    //vardump($app);
    $Return = '<h3>' . $App . '</h3>';

    foreach ($app['interfaces'] as $interface=>$access) {
        $dta = get_option($interface);
        
        //if ($dta['_Application'] == $App) {
            if (empty($dta['_ItemGroup'])) {
                $Group = '<em>ungrouped</em>';
            } else {
                $Group = $dta['_ItemGroup'];
            }
            $interfaceGroups[$Group][] = $dta;
        //}
    }
    //vardump($interfaceGroups);
    if (empty($interfaceGroups)) {
        $Return .= '<div style="padding:3px;" class="highlight">No interfaces</div>';
        return $Return;
    }
    foreach ($interfaceGroups as $group => $Interface) {
    
        $Return .= '<div style="padding:3px;" class="highlight">' . $group . '</div>';
        foreach ($Interface as $dta) {
            //if ($dta['_Application'] == $App) {

                if ($GroupRun != $group) {

                    $GroupRun = $group;
                }

                $Return .= '<div style="padding:5px;">';
                $Return .= '<span class="interfaceInserter" style="cursor:pointer;" id="' . $dta['ID'] . '">' . $dta['_ReportDescription'] . '</span>';
                if (!empty($dta['_ReportExtendedDescription'])) {
                    $Return .= '<div><span class="description interfaceInserter" style="cursor:pointer;" id="' . $dta['ID'] . '">' . $dta['_ReportExtendedDescription'] . '</span></div>';
                }
                $Return .= '</div>';
            //}
        }
    }
    return $Return;
}


function dr_addListRowTemplate($Default = false){



ob_start();
                $show = 'block';
                if(!empty($Default)){
                    $show = 'block';
                }
                $rowTemplateID = uniqid('Template-');
                $Name = $rowTemplateID;
                if(!empty($Default['_name'])){
                    $Name = $Default['_name'];
                }
                $Header = '';
                if(!empty($Default['_before'])){
                    $Header = $Default['_before'];
                }
                $Content = '';
                if(!empty($Default['_content'])){
                    $Content = $Default['_content'];
                }
                $Footer = '';
                if(!empty($Default['_after'])){
                    $Footer = $Default['_after'];
                }

            ?>
            
                <div class="admin_list_row3 table_sorter postbox" id="dt_<?php echo $rowTemplateID; ?>">
                    <img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery('#dt_<?php echo $rowTemplateID; ?>').remove();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/cancel.png">
                    <img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery('.<?php echo $rowTemplateID; ?>').toggle();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/cog.png">                    
                    <h3 class="fieldTypeHandle"><?php echo $Name; ?></h3>
                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $rowTemplateID; ?>">
                        <strong>Template Name:</strong> <input type="text" name="Data[Content][_layoutTemplate][_Content][_name][]" value="<?php echo $Name; ?>" />
                    </div>


                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $rowTemplateID; ?>">
                        <strong>Before</strong> <span class="description">Placed before the content loop.</span>
                    </div>
                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel fieldBeforeAfter <?php echo $rowTemplateID; ?>">
                        <textarea id="<?php echo $rowTemplateID; ?>_before" class="layoutTextArea" name="Data[Content][_layoutTemplate][_Content][_before][]"><?php echo $Header; ?></textarea>
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_before').height('400px'); return false;">Larger</a> | 
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_before').height('80px'); return false;">Smaller</a>
                    </div>


                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $rowTemplateID; ?>">
                        <strong>Content</strong>
                        <span class="description">Repeated with every row/entry.</span>
                    </div>
                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel fieldBeforeAfter <?php echo $rowTemplateID; ?>">
                        <textarea id="<?php echo $rowTemplateID; ?>_content" class="layoutTextAreaLarge" name="Data[Content][_layoutTemplate][_Content][_content][]"><?php echo $Content; ?></textarea>
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_content').height('600px'); return false;">Larger</a> |
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_content').height('180px'); return false;">Smaller</a>
                    </div>


                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $rowTemplateID; ?>">
                        <strong>After</strong> <span class="description">Placed after the content loop.</span>
                    </div>
                    <div style="display: <?php echo $show; ?>;" class="admin_config_panel fieldBeforeAfter <?php echo $rowTemplateID; ?>">
                        <textarea id="<?php echo $rowTemplateID; ?>_after" class="layoutTextArea" name="Data[Content][_layoutTemplate][_Content][_after][]"><?php echo $Footer; ?></textarea>
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_after').height('400px'); return false;">Larger</a> |
                        <a href="#" onclick="jQuery('#<?php echo $rowTemplateID; ?>_after').height('80px'); return false;">Smaller</a>

                    </div>


                    <div style="clear:both"></div>
                </div>
            
<?php

return ob_get_clean();

}

function dr_addListFieldTemplate($Field, $Default = false){




ob_start();

    $fieldTemplateID = uniqid('Field-');
    $Name = $rowTemplateID;

    $show = 'block';
    if(!empty($Default)){
        $show = 'none';
    }
    $before = '';
    $after = '';

    if(!empty($Default['_before'])){
        $before = $Default['_before'];
    }
    if(!empty($Default['_after'])){
        $after = $Default['_after'];
    }
    

?>

    <div class="admin_list_row3 table_sorter postbox" id="dt_<?php echo $fieldTemplateID; ?>">
        <img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery('#dt_<?php echo $fieldTemplateID; ?>').remove();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/cancel.png">
        <img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery('.<?php echo $fieldTemplateID; ?>').toggle();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/cog.png">
        <h3 class="fieldTypeHandle"><?php echo $Field; ?></h3>
        <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $fieldTemplateID; ?>"></div>
        <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $fieldTemplateID; ?>"><strong>Before</strong></div>
        <div style="display: <?php echo $show; ?>;" class="admin_config_panel fieldBeforeAfter <?php echo $fieldTemplateID; ?>">
            <textarea class="layoutTextArea" name="Data[Content][_layoutTemplate][_Fields][<?php echo $Field; ?>][_before]" id="<?php echo $fieldTemplateID; ?>_before" ><?php echo $before; ?></textarea>
            <a href="#" onclick="jQuery('#<?php echo $fieldTemplateID; ?>_before').height('400px'); return false;">Larger</a> |
            <a href="#" onclick="jQuery('#<?php echo $fieldTemplateID; ?>_before').height('80px'); return false;">Smaller</a>
        </div>
        <div style="display: <?php echo $show; ?>;" class="admin_config_panel <?php echo $fieldTemplateID; ?>"><strong>After</strong></div>
        <div style="display: <?php echo $show; ?>;" class="admin_config_panel fieldBeforeAfter <?php echo $fieldTemplateID; ?>">
            <textarea class="layoutTextArea" name="Data[Content][_layoutTemplate][_Fields][<?php echo $Field; ?>][_after]" id="<?php echo $fieldTemplateID; ?>_after" ><?php echo $after; ?></textarea>
            <a href="#" onclick="jQuery('#<?php echo $fieldTemplateID; ?>_after').height('400px'); return false;">Larger</a> |
            <a href="#" onclick="jQuery('#<?php echo $fieldTemplateID; ?>_after').height('80px'); return false;">Smaller</a>

        </div>
        <div style="clear:both"></div>
    </div>



<?php

return ob_get_clean();

}


function dt_buildnodeMap($data, $Return = '', $level = 0, $path = '', $Default = false){

    $space = '';
    if($level > 0){
        for($i=0; $i<=$level; $i++){
            $space .= '--';
        }
    }
    if(is_array($data)){
        foreach($data as $node=>$value){
            if($node === 0){
                return dt_buildnodeMap($value, $Return, $level+1, $path.'['.$node.']', $Default);
            }
            $p ='';
            if($level > 0){
                if(is_array($value)){
                    $p = '&raquo;';
                }
            }
            $sel = '';
            if($Default == $path.'['.$node.']'){
                $sel = 'selected="selected"';
            }
            $Return .= '<option value="'.$path.'['.$node.']" '.$sel.'>'.$space.$p.$node.'</option>';
            if(is_array($value)){
                $Return = dt_buildnodeMap($value, $Return, $level+1, $path.'['.$node.']', $Default);
            }
        }
    }else{
        
        $Return .= '<option value="'.$path.'['.$data.']">'.$space.$p.$data.'</option>';
    }

return $Return;

}

function dr_dataSourceMapping($url, $Config = false){

    if(empty($url)){
        return 'Disabled.';
    }

    // Create a stream
    $opts = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
                  "Cookie: foo=bar\r\n"
      )
    );

    $context = stream_context_create($opts);

    // Open the file using the HTTP headers set above
    if(!$data = @file_get_contents($url, false, $context)){
        return 'Could not connect to source.';
    }

    if(strpos(substr(strtolower($data),0,1024), 'xml')){
        $data = dt_xml2array($data);
    }else{
        $data = json_decode($data, true);
    }
    //$Return = '<select>';
    $Return = dt_buildnodeMap($data, '', 0, '', $Config['_DataSourceRootNode']);
    ob_start();
    ?>

  <div class="section">
    <div class="option">
      <div class="title">
        Root Node
      </div>

      <div class="controls">        
        <select id="_DataSourceRootNode" name="Data[Content][_DataSourceRootNode]">
            <option value="">Select the root node point</option>
        <?php echo $Return; ?>
        </select>
        <div class="clear"></div>

      </div>

      <div class="explain">
        Select the starting node point. this is usually an array point. which will contain the entries you want to capture.
      </div>

      <div class="clear"></div>
    </div>
  </div>
    <div id="_dataFieldMapView">
        <?php
        if(!empty($Config['_DataSourceRootNode'])){
            echo dr_loadFieldMapping($url, $Config['_DataSourceRootNode'], $Config['_main_table'], $Config);
        }else{
        ?>
        <div class="description">select the node point to configure field mapping.</div>
        <?php
        }
        ?>
    </div>
    <?php


    $_SESSION['dataform']['OutScripts'] .= "
        jQuery('#_DataSourceRootNode').bind('change', function(){
            dr_loadFieldMapping(jQuery('#_DataSourceURL').val(), this.value, jQuery('#_main_table').val())
        });
    ";


    $Return = ob_get_clean();

return $Return;
}

function dr_loadFieldMapping($url, $root, $table, $Config = false){

   global $wpdb;
   
    $tablefields = $wpdb->get_results("SHOW COLUMNS FROM `".$table."`", ARRAY_N);

    
    $select = '<select id="Data[Content][_DataSourceFieldMap][{{Field}}]" name="Data[Content][_DataSourceFieldMap][{{Field}}]">';

    $select .= '</select>';

    $data = file_get_contents($url);

    if(strpos(substr(strtolower($data),0,1024), 'xml')){
        $data = dt_xml2array($data);
    }else{
        $data = json_decode($data, true);
    }

    preg_match_all('/\\[(.*?)\\]/', $root, $matchesarray);

    foreach($matchesarray[1] as $path){
        $data = $data[$path];
    }
        
    //vardump($select);
    $Return = '<table class="widefat">';
    $Return .= '<thead>';
    $Return .= '<tr>';
    $Return .= '<th>';
        $Return .= 'Table Field';
    $Return .= '</th>';
    $Return .= '<th>';
        $Return .= 'Source Field';
    $Return .= '</th>';
    $Return .= '</tr>';
    $Return .= '</thead>';
    $Return .= '<tbody>';

    foreach($tablefields as $column){

        $Return .= '<tr>';
        $Return .= '<td>';
            $Return .= df_parseCamelCase($column[0]);
        $Return .= '</td>';
        if(!empty($select)){
            $Return .= '<td>';
                $Return .='<select id="Data[Content][_DataSourceFieldMap]['.$column[0].']" name="Data[Content][_DataSourceFieldMap]['.$column[0].']">';
                $Return .= '<option value="NULL"></option>';
                $Return .= dt_buildnodeMap($data, false, false, false, $Config['_DataSourceFieldMap'][$column[0]]);
                $Return .= '</select>';
            $Return .= '</td>';
        }else{
            $Return .= '<td>';
                $Return .= 'Node point not an array and cannot be mapped.';
            $Return .= '</td>';            
        }

        $Return .= '</tr>';

    }
    $Return .= '</tbody>';
    $Return .= '</table>';

    return $Return;


}

function dt_runDataSourceImport($Config){
    global $wpdb;


    $data = file_get_contents($Config['_DataSourceURL']);
    
    if(strpos(substr(strtolower($data),0,1024), 'xml')){
        $data = dt_xml2array($data);
    }else{
        $data = json_decode($data, true);
    }
    
    preg_match_all('/\\[(.*?)\\]/', $Config['_DataSourceRootNode'], $matchesarray);
    
    foreach($matchesarray[1] as $path){
        $data = $data[$path];
    }
    if(empty($data)){
        echo 'Data source contained no data';
        exit;
    }
    $in = 0;
    $onot = 0;
    foreach($data as $entry){
        //vardump($entry);
        $prefix = "INSERT INTO `".$Config['_main_table']."` (";        
        $insertString = array();
        $prefixFields = array();
        foreach($Config['_DataSourceFieldMap'] as $Field=>$nodePoint){

            if($nodePoint != 'NULL'){
                $prefixFields[] = "`".$Field."`";
                //echo $nodePoint;
                preg_match_all('/\\[(.*?)\\]/', $nodePoint, $matchesarray);
                $currentEntry = $entry;                
                foreach($matchesarray[1] as $key=>$point){
                    if($key > 0 ){
                        $currentEntry = $currentEntry[$point];
                    }
                }
                $insertString[] = "'".@mysql_real_escape_string($currentEntry)."'";
            }
        }
        $query = $prefix.implode(', ',$prefixFields).") VALUES (".implode(', ', $insertString).");";
        
        if($wpdb->query($query)){
            $in++;
        }else{
            $not++;
        }
        
    }

    echo $in+$not." entries inserted.\r\n";
    echo "=========================================\r\n";
    echo $in." entries captured.\r\n";
    if(!empty($not)){
        echo $not." entries exclude. (failed or already captured)";
    }
    exit;

}

function dt_xml2array($contents, $get_attributes=1, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();

        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;

                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }

    return($xml_array);
}


?>