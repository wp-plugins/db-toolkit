<?php



function grid_rowswitch($Row = '') {
    if($Row == 'odd') {
        return '';
    }
    return 'odd';
}

function report_rowswitch($Row = '') {
    if($Row == 'list_row2') {
        return 'list_row1';
    }
    return 'list_row2';

    if($Row == 'row_even') {
        return 'row_odd';
    }
    return 'row_even';
}

function printr($a) {
    $Return = '<pre>';
    ob_start();
    print_r($a);
    return $Return.ob_get_clean().'</pre>';
}

function df_cleanArray($Array) {
    foreach($Array as $Key=>$Value) {
        if(is_array($Value)) {
            $temp = df_cleanArray($Value);
            if(!empty($temp)) {
                $Clean[$Key] = $temp;
            }
        }else {
            if(!empty($Value)) {
                $Clean[$Key] = $Value;
            }
        }
    }
    return $Clean;
}

if(is_admin()) {
    // Admin Functiuons

    function dr_loadPassbackFields($Table, $Defaults = 'none', $Config = false) {

        if(empty($Table)) {
            return;
        }

        $result = mysql_query("SHOW COLUMNS FROM ".$Table);
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $TotalsField .= '<option value="'.$row['Field'].'" {{'.$row['Field'].'}}>'.$row['Field'].'</option>';
                $FieldsClearer[] = $row['Field'];
            }

            if(!empty($Config)) {
                if(!empty ($Config['_CloneField'])) {
                    $TotalsField .= '<optgroup label="Cloned Fields">';
                    foreach ($Config['_CloneField'] as $FieldKey=>$Array) {                        
                        $Sel = '';
                        if($Default == $FieldKey) {
                            $Sel = 'selected="selected"';
                        }
                        $TotalsField .= '<option value="'.$FieldKey.'" '.$Sel.'>'.$Config['_FieldTitle'][$FieldKey].'</option>';
                    }

                }
            }
        }
        if($Defaults == 'none') {
            $Return = '<div style="padding:3px;" class="list_row3" id="ReturnFields_'.$ID.'_wrap">';
            $ID = uniqid(rand(100, 99999));
            $Return .= 'Return Field: <select name="Data[Content][_ReturnFields][]" id="ReturnFields_'.$ID.'">';
            $Return .= $TotalsField;
            $Return .= '</select>&nbsp;';
            $Return .= '<a href="#" onclick="jQuery(\'#ReturnFields_'.$ID.'_wrap\').remove(); return false;">remove</a></div>';
            foreach($FieldsClearer as $Clear) {
                $Return = str_replace('{{'.$Clear.'}}', '', $Return);
            }
            $Return .= '</div>';
        }else {
            //dump($Defaults);
            if(is_array($Defaults)) {
                foreach($Defaults as $Default) {
                    $ID = uniqid(rand(100, 99999));
                    $Return = '<div style="padding:3px;" class="list_row3" id="ReturnFields_'.$ID.'_wrap">';
                    $Return .= 'Return Field: <select name="Data[Content][_ReturnFields][]" id="ReturnFields_'.$ID.'">';
                    $Return .= $TotalsField;
                    $Return .= '</select>&nbsp;';
                    $Return .= '<a href="#" onclick="jQuery(\'#ReturnFields_'.$ID.'_wrap\').remove(); return false;">remove</a>';
                    $Return .= '</div>';
                    $out[] = str_replace('{{'.$Default.'}}', 'selected="selected"', $Return);
                }
                $Return = implode('', $out);
                foreach($FieldsClearer as $Clear) {
                    $Return = str_replace('{{'.$Clear.'}}', '', $Return);
                }
            }
        }
        return $Return;
    }


    function df_searchReferenceForm($Table) {
        return false;
    }

    function dr_addTotalsField($Table, $Defaults = false, $Key = false) {
        //dump($Defaults);
        $ID = uniqid(rand(100, 999));
        $result = mysql_query("SHOW COLUMNS FROM ".$Table);
        $TotalsField = '';
        $GroupingField = '';
        if (mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $Sel = '';
                if(!empty($Defaults['_TotalsField'][$Key])) {
                    if($Defaults['_TotalsField'][$Key] == $row['Field']) {
                        $Sel = 'selected=selected';
                    }
                }
                $TotalsField .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
                $Sel = '';
                if(!empty($Defaults['_TotalsGroupingField'][$Key])) {
                    if($Defaults['_TotalsGroupingField'][$Key] == $row['Field']) {
                        $Sel = 'selected=selected';
                    }
                }
                $GroupingField .= '<option value="'.$row['Field'].'" '.$Sel.'>'.$row['Field'].'</option>';
            }
        }
        $Return = 'Totals Field: <select name="Data[Content][_TotalsField][]" id="totalsField_'.$ID.'">';
        $Return .= $TotalsField;
        $Return .= '</select>&nbsp;';

        $Return .= 'Grouping Field: <select name="Data[Content][_TotalsGroupingField][]" id="groupingField_'.$ID.'">';
        $Return .= $GroupingField;
        $Return .= '</select>&nbsp;';

        $Return .= 'Grouping: <select name="Data[Content][_TotalsFieldType][]" id="totalsFieldType_'.$ID.'">';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldType'][$Key])) {
            if($Defaults['_TotalsFieldType'][$Key] == 'count') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="count" '.$Sel.'>Count</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldType'][$Key])) {
            if($Defaults['_TotalsFieldType'][$Key] == 'sum') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="sum" '.$Sel.'>Sum</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldType'][$Key])) {
            if($Defaults['_TotalsFieldType'][$Key] == 'avg') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="avg" '.$Sel.'>Average</option>';
        //$Return .= '<option value="average">average</option>';
        //$Return .= '<option value="percentage">percentage</option>';
        $Return .= '</select>&nbsp;';

        $Return .= 'Location: <select name="Data[Content][_TotalsFieldLocation][]" id="totalsFieldLocation_'.$ID.'">';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldLocation'][$Key])) {
            if($Defaults['_TotalsFieldLocation'][$Key] == 'header') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="header" '.$Sel.'>Header</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldLocation'][$Key])) {
            if($Defaults['_TotalsFieldLocation'][$Key] == 'footer') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="footer" '.$Sel.'>Footer</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldLocation'][$Key])) {
            if($Defaults['_TotalsFieldLocation'][$Key] == 'inline') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="inline" '.$Sel.'>Inline</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldLocation'][$Key])) {
            if($Defaults['_TotalsFieldLocation'][$Key] == 'headerinline') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="headerinline" '.$Sel.'>Header + Inline</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldLocation'][$Key])) {
            if($Defaults['_TotalsFieldLocation'][$Key] == 'footerinline') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="footerinline" '.$Sel.'>Footer + Inline</option>';
        //$Return .= '<option value="average">average</option>';
        //$Return .= '<option value="percentage">percentage</option>';
        $Return .= '</select>&nbsp;';

        $Return .= 'Justify: <select name="Data[Content][_TotalsFieldJustify][]" id="totalsFieldJustify_'.$ID.'">';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldJustify'][$Key])) {
            if($Defaults['_TotalsFieldJustify'][$Key] == 'left') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="left" '.$Sel.'>Left</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldJustify'][$Key])) {
            if($Defaults['_TotalsFieldJustify'][$Key] == 'Center') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="Center" '.$Sel.'>Center</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldJustify'][$Key])) {
            if($Defaults['_TotalsFieldJustify'][$Key] == 'Right') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="Right" '.$Sel.'>Right</option>';
        $Return .= '</select>&nbsp;';

        $title = '';
        if(!empty($Defaults['_TotalsFieldTitle'][$Key])) {
            $title = $Defaults['_TotalsFieldTitle'][$Key];
        }
        $Return .= 'Title: <input type="texfield" name="Data[Content][_TotalsFieldTitle][]" value="'.$title.'" />&nbsp;';
        $caption = '';
        if(!empty($Defaults['_TotalsFieldCaption'][$Key])) {
            $caption = $Defaults['_TotalsFieldCaption'][$Key];
        }
        $Return .= 'Caption: <input type="texfield" name="Data[Content][_TotalsFieldCaption][]" value="'.$caption.'" />&nbsp;';

        $Return .= 'Inline Function: <select name="Data[Content][_TotalsFieldFunction][]" id="totalsFieldFunction_'.$ID.'">';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldFunction'][$Key])) {
            if($Defaults['_TotalsFieldFunction'][$Key] == 'none') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="none" '.$Sel.'>none</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldFunction'][$Key])) {
            if($Defaults['_TotalsFieldFunction'][$Key] == 'percent') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="percent" '.$Sel.'>Percentage</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldFunction'][$Key])) {
            if($Defaults['_TotalsFieldFunction'][$Key] == 'VAT') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="VAT" '.$Sel.'>VAT (14%)</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldFunction'][$Key])) {
            if($Defaults['_TotalsFieldFunction'][$Key] == 'AddVAT') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="AddVAT" '.$Sel.'>Add VAT (14%)</option>';
        $Sel = '';
        if(!empty($Defaults['_TotalsFieldFunction'][$Key])) {
            if($Defaults['_TotalsFieldFunction'][$Key] == 'averages') {
                $Sel = 'selected=selected';
            }
        }
        $Return .= '<option value="averages" '.$Sel.'>Average Compare</option>';
        $Return .= '</select>';
        $Width = '';
        if(!empty($Defaults['_TotalsFieldTitleWidth'][$Key])) {
            $Width = $Defaults['_TotalsFieldTitleWidth'][$Key];
        }
        $Return .= '&nbsp;Inline Width: <input type="texfield" style="width:40px;" value="'.$Width.'" name="Data[Content][_TotalsFieldTitleWidth][]" />&nbsp;';
        $Prefix = '';
        if(!empty($Defaults['_TotalsFieldPrefix'][$Key])) {
            $Width = $Defaults['_TotalsFieldPrefix'][$Key];
        }
        $Return .= '&nbsp;Header/Footer Prefix: <input type="texfield" style="width:40px;" value="'.$Prefix.'" name="Data[Content][_TotalsFieldPrefix][]" />&nbsp;';
        $Suffix = '';
        if(!empty($Defaults['_TotalsFieldSuffix'][$Key])) {
            $Width = $Defaults['_TotalsFieldSuffix'][$Key];
        }
        $Return .= '&nbsp;Header/Footer Suffix: <input type="texfield" style="width:40px;" value="'.$Suffix.'" name="Data[Content][_TotalsFieldSuffix][]" />&nbsp;';


        $Return .= '<a href="#" onclick="jQuery(\'#totalsField_'.$ID.'\').remove(); return false;">remove</a>';


        return layout_listOption('totalsField_'.$ID, false, 'Field: '.$Return, false, 'list_row3', false);

    }

    function df_makeFieldConfigBox($Field, $Config, $Defaults = false) {
        global $wpdb;


        $Table = $Config['Content']['_main_table'];
        $name = df_parseCamelCase($Field);
        if(!empty($Config['Content']['_FieldTitle'][$Field])) {
            if(substr($Field, 0, 2) == '__') {
                $name = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/copy.png" width="16" height="16" align="absmiddle" /> '.$Config['Content']['_FieldTitle'][$Field];
            }else {
                $name = $Config['Content']['_FieldTitle'][$Field];
            }
        }
        //echo '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><input type="checkbox" name="null" id="use_'.$Field.'" checked="checked" onclick="dr_enableDisableField(this);" />&nbsp;'.ucwords($name).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';

        
        $PreReturn[$Field] .= '<div id="Field_'.$Field.'" class="admin_list_row3 table_sorter postbox" style="width:550px;">';

        $PreReturn[$Field] .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/images/cancel.png" align="absmiddle" onclick="jQuery(\'#Field_'.$Field.'\').remove();" style="float:right; padding:5px;" />';

        $PreReturn[$Field] .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/images/cog.png" align="absmiddle" onclick="jQuery(\'#overide_'.$Field.'\').toggle();" style="float:right; padding:5px;" />';

        $PreReturn[$Field] .= '<h3>'.$name.'</h3>';
        
        // Linking Master
        if(substr($Field,0, 2) == '__') {

            $result = mysql_query("SHOW COLUMNS FROM `".$Config['Content']['_main_table']."`");
            // echo mysql_error();
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                }
            }

            $PreReturn[$Field] .= '<div class="admin_config_panel">';

            $PreReturn[$Field] .= 'Master Field: <select name="Data[Content][_CloneField]['.$Field.'][Master]" id="master_'.$Field.'">';
            foreach($FieldList as $MasterField) {
                // add default here
                $Sel = '';
                if($MasterField == $Config['Content']['_CloneField'][$Field]['Master']) {
                    $Sel = 'selected="selected"';
                }
                $PreReturn[$Field] .= '<option value="'.$MasterField.'" '.$Sel.'>'.$MasterField.'</option>';
            }
            // get clones
            if(!empty($Config)) {
                if(!empty ($Config['Content']['_CloneField'])) {
                    $PreReturn[$Field] .= '<optgroup label="Cloned Fields">';
                    foreach ($Config['Content']['_CloneField'] as $FieldKey=>$Array) {
                        if($FieldKey != $Field){
                            $Sel = '';
                            if($Config['Content']['_CloneField'][$Field]['Master'] == $FieldKey) {
                                $Sel = 'selected="selected"';
                            }
                            $PreReturn[$Field] .= '<option value="'.$FieldKey.'" '.$Sel.'>'.$Config['Content']['_FieldTitle'][$FieldKey].'</option>';
                        }
                    }
                }

            }

            $PreReturn[$Field] .= '</select>';

            $PreReturn[$Field] .= '</div>';
        }


        $PreReturn[$Field] .= '<div id="overide_'.$Field.'" class="admin_config_panel" style="display:none; position:reletive;">';
        //New Options
        $Width = '';
        if(!empty($Defaults['_WidthOverride'][$Field])) {
            $Width = $Defaults['_WidthOverride'][$Field];
        }
        $RSel = 'checked="checked"';
        $RClass = 'button-highlighted highlight';
        if(empty($Config['Content']['_Required'][$Field])) {
            $RSel = '';
            $RClass = 'button';
        }
        $SSel = 'checked="checked"';
        $SClass = 'button-highlighted highlight';
        if(empty($Config['Content']['_Sortable'][$Field])) {
            $SSel = '';
            $SClass = 'button';
        }
        $USel = 'checked="checked"';
        $UClass = 'button-highlighted highlight';
        if(empty($Config['Content']['_Unique'][$Field])) {
            $USel = '';
            $UClass = 'button';
        }
        $Title = df_parseCamelCase($Field);
        if(!empty($Config['Content']['_FieldTitle'][$Field])) {
            $Title = $Config['Content']['_FieldTitle'][$Field];
        }
        $Caption = '';
        if(!empty($Config['Content']['_FieldCaption'][$Field])) {
            $Caption = $Config['Content']['_FieldCaption'][$Field];
        }
        $inlineSel = '';
        if(!empty($Config['Content']['_InlineEdit'][$Field])) {
            $inlineSel = 'checked="checked"';
        }
        $Justify = '';
        if(!empty($Config['Content']['_Justify'][$Field])) {
            $Justify = $Config['Content']['_Justify'][$Field];
        }
        $PreReturn[$Field] .= '<label>Lable</label>';

        $PreReturn[$Field] .= '<div style="padding:3px;">Title: <input type="text" value="'.$Title.'" name="Data[Content][_FieldTitle]['.$Field.']" /> ';
        $PreReturn[$Field] .= 'Caption: <input type="text" value="'.$Caption.'" name="Data[Content][_FieldCaption]['.$Field.']" /></div>';
        $PreReturn[$Field] .= '<label>Alignment</label>';
        $PreReturn[$Field] .= '<div style="padding:3px;">Width: <input type="text" style="width:40px;" value="'.$Width.'" name="Data[Content][_WidthOverride]['.$Field.']" /> ';
        $PreReturn[$Field] .= df_alignmentSetup($Field, $Justify).'</div>';
        
        //$PreReturn[$Field] .= '<div style="padding:3px;">Inline Editing: <input type="checkbox" name="Data[Content][_InlineEdit]['.$Field.']" id="sortable_'.$Field.'" '.$inlineSel.' /></div>';


        // Graphing
        // X AXIS
        $XAxis = '';
        if($Config['Content']['_xaxis'] == $Field) {
            $XAxis = 'checked="checked"';
        }

        $chartValue = '';
        if(!empty($Config['Content']['_chartValue'][$Field])) {
            $chartValue = 'checked="checked"';
        }
        // Graphing x axis (single select)
        $PreReturn[$Field] .= '<label>Charting</label>';
        $PreReturn[$Field] .= '<div style="padding:3px;">X Axis: <input type="radio" name="Data[Content][_xaxis]" id="sortable_'.$Field.'" value="'.$Field.'" '.$XAxis.' />';
        // Graphing legend/charted value
        $PreReturn[$Field] .= ' Chart Item: <input type="checkbox" name="Data[Content][_chartValue]['.$Field.']" id="sortable_'.$Field.'" value="1" '.$chartValue.' />';


                    /// charts

                    $Sel = '';
                    if(!empty($Config['Content']['_chartType'][$Field])) {
                        switch($Config['Content']['_chartType'][$Field]) {
                            case 'line':
                                $Sel = 'line';
                                break;
                            case 'bar':
                                $Sel = 'bar';
                                break;
                            case 'column':
                                $Sel = 'column';
                                break;
                            case 'spline':
                                $Sel = 'spline';
                                break;
                            case 'area':
                                $Sel = 'area';
                                break;

                        }

                    }
                    $PreReturn[$Field] .= ' Chart Type: ';
                    $PreReturn[$Field] .= '<select name="Data[Content][_chartType]['.$Field.']" >';
                    $PreReturn[$Field] .= '<option value="line" ';
                    if($Sel == 'line') {
                        $PreReturn[$Field] .= 'selected="selected"';
                    };
                    $PreReturn[$Field] .= '>Line</option>';
                    $PreReturn[$Field] .= '<option value="bar" ';
                    if($Sel == 'bar') {
                        $PreReturn[$Field] .= 'selected="selected"';
                    };
                    $PreReturn[$Field] .= '>Bar</option>';
                    $PreReturn[$Field] .= '<option value="column" ';
                    if($Sel == 'column') {
                        $PreReturn[$Field] .= 'selected="selected"';
                    };
                    $PreReturn[$Field] .= '>Column</option>';
                    $PreReturn[$Field] .= '<option value="spline" ';
                    if($Sel == 'spline') {
                        $PreReturn[$Field] .= 'selected="selected"';
                    };
                    $PreReturn[$Field] .= '>Spline</option>';
                    $PreReturn[$Field] .= '<option value="area" ';
                    if($Sel == 'area') {
                        $PreReturn[$Field] .= 'selected="selected"';
                    };
                    $PreReturn[$Field] .= '>Area</option>';


                    $PreReturn[$Field] .= '</select>';
                    $PreReturn[$Field] .= '</div>';





        $PreReturn[$Field] .= '</div><div class="admin_config_toolbar"> '.df_fieldTypes($Field, $Table, $row, $Defaults['_Field']).dr_reportListTypes($Field, $Defaults['_IndexType'][$Field]);
        // inline settings
        //class="button-highlighted"
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$UClass.'" id="unique_'.$Field.'" onclick="df_setToggle(\'unique_'.$Field.'\');" title="Unique"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/unique.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$RClass.'" id="required_'.$Field.'" onclick="df_setToggle(\'required_'.$Field.'\');" title="Required"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/required.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$SClass.'" id="issortable_'.$Field.'" onclick="df_setToggle(\'issortable_'.$Field.'\');" title="Sortable"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/table_sort.png) left center no-repeat; padding:5px 8px;"></span></span>';

        $PreReturn[$Field] .= '<div class="widefat" id="'.$Field.'_FieldTypePanel" style="display:none; text-align:left;"></div>';

        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Unique]['.$Field.']" id="unique_'.$Field.'_check" '.$USel.' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Required]['.$Field.']" id="required_'.$Field.'_check" '.$RSel.' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Sortable]['.$Field.']" id="issortable_'.$Field.'_check" '.$SSel.' />';
        $PreReturn[$Field] .= '</div><div class="admin_config_panel" style="text-align:right;" id="ExtraSetting_'.$Field.'">';
        unset($Types);
        $Types = explode('_', $Defaults['_Field'][$Field]);
        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php')) {
            include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php');
            $func = $FieldTypes[$Types[1]]['func'];
            if($func != 'null') {
                if($func != '') {
                    $PreReturn[$Field] .= '<div class="widefat" id="'.$Field.'_configPanel" style="display:none; text-align:left;">';
                    $PreReturn[$Field] .= '<h3>'.$Field.' Config</h3><div class="admin_config_panel">';
                    $PreReturn[$Field] .= $func($Field, $Table,$Config);
                    $PreReturn[$Field] .= '</div></div>';
                    $PreReturn[$Field] .= '<input type="button" class="buttons" value="Setup" onclick="toggle(\''.$Field.'_configPanel\');" />';
                }
            }
        }
        $PreReturn[$Field] .= '</div></div>';
       
        return $PreReturn[$Field];
    }

    function df_tableReportSetup($Table, $EID, $Config = false, $Column = 'M') {
        if(empty($Table)) {
            return;
        }
        global $wpdb;

        if($Column == 'Linking') {

            $result = mysql_query("SHOW COLUMNS FROM ".$Table);
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                }
            }


            $Field = '__'.uniqid();
            $name = df_parseCamelCase($Field);




            $name = df_parseCamelCase($Field);
            //echo '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><input type="checkbox" name="null" id="use_'.$Field.'" checked="checked" onclick="dr_enableDisableField(this);" />&nbsp;'.ucwords($name).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';
            $PreReturn[$Field] .= '<div id="Field_'.$Field.'" class="admin_list_row3 table_sorter postbox" style="width:550px;"><img src="'.WP_PLUGIN_URL.'/db-toolkit/images/cog.png" align="absmiddle" onclick="jQuery(\'#overide_'.$Field.'\').toggle();" style="float:right; padding:5px;" /><h3>'.df_parseCamelCase($Field).'</h3>';
            // Linking Master
            $PreReturn[$Field] .= '<div style="padding:5px;">';

            $PreReturn[$Field] .= 'Master Field: <select name="Data[Content][_CloneField]['.$Field.'][Master]" id="master_'.$Field.'">';
            foreach($FieldList as $MasterField) {
                // add default here
                $PreReturn[$Field] .= '<option value="'.$MasterField.'">'.$MasterField.'</option>';
            }
            $PreReturn[$Field] .= '</select>';

            $PreReturn[$Field] .= '</div>';


            $PreReturn[$Field] .= '<div id="overide_'.$Field.'" class="admin_config_panel" style="display:none; position:reletive;">';
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

            if(!empty($Config)) {


                if(!empty($Defaults['_WidthOverride'][$Field])) {
                    $Width = $Defaults['_WidthOverride'][$Field];
                }

                if(!empty($Config['Content']['_Required'][$Field])) {
                    $RSel = 'checked="checked"';
                    $RClass = 'button-highlighted highlight';
                }
                if(empty($Config['Content']['_Sortable'][$Field])) {
                    $SSel = 'checked="checked"';
                    $SClass = 'button-highlighted highlight';
                }

                if(!empty($Config['Content']['_Unique'][$Field])) {
                    $USel = 'checked="checked"';
                    $UClass = 'button-highlighted highlight';
                }
                if(!empty($Config['Content']['_FieldTitle'][$Field])) {
                    $Title = $Config['Content']['_FieldTitle'][$Field];
                }

                if(!empty($Config['Content']['_FieldCaption'][$Field])) {
                    $Caption = $Config['Content']['_FieldCaption'][$Field];
                }

                if(!empty($Config['Content']['_InlineEdit'][$Field])) {
                    $inlineSel = 'checked="checked"';
                }

                if(!empty($Config['Content']['_Justify'][$Field])) {
                    $Justify = $Config['Content']['_Justify'][$Field];
                }
            }
            $PreReturn[$Field] .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/images/cancel.png" align="absmiddle" onclick="jQuery(\'#Field_'.$Field.'\').remove();" style="float:right; padding:5px;" />';
        $PreReturn[$Field] .= '<div style="padding:3px;">Title: <input type="text" value="'.$Title.'" name="Data[Content][_FieldTitle]['.$Field.']" /> ';
        $PreReturn[$Field] .= 'Caption: <input type="text" value="'.$Caption.'" name="Data[Content][_FieldCaption]['.$Field.']" /></div>';
        $PreReturn[$Field] .= '<div style="padding:3px;">Width: <input type="text" style="width:40px;" value="'.$Width.'" name="Data[Content][_WidthOverride]['.$Field.']" />';
        $PreReturn[$Field] .= df_alignmentSetup($Field, $Justify).'</div>';
        //$PreReturn[$Field] .= '<div class="admin_list_row2">Unique: <input type="checkbox" name="Data[Content][_Unique]['.$Field.']" id="unique_'.$Field.'" '.$USel.' /></div>';
        //$PreReturn[$Field] .= '<div class="admin_list_row1">Reguired: </div>';
        //$PreReturn[$Field] .= '<div class="admin_list_row2">Sortable: </div>';
        $PreReturn[$Field] .= '<div style="padding:3px;">Inline Editing: <input type="checkbox" name="Data[Content][_InlineEdit]['.$Field.']" id="sortable_'.$Field.'" '.$inlineSel.' /></div>';
        $PreReturn[$Field] .= '</div>';




            $PreReturn[$Field] .= '<div class="admin_config_toolbar"> '.df_fieldTypes($Field, $Table, $row, $Defaults['_Field']).dr_reportListTypes($Field, $Defaults['_IndexType'][$Field]);
            
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$UClass.'" id="unique_'.$Field.'" onclick="df_setToggle(\'unique_'.$Field.'\');" title="Unique"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/unique.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$RClass.'" id="required_'.$Field.'" onclick="df_setToggle(\'required_'.$Field.'\');" title="Required"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/required.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="'.$SClass.'" id="issortable_'.$Field.'" onclick="df_setToggle(\'issortable_'.$Field.'\');" title="Sortable"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/table_sort.png) left center no-repeat; padding:5px 8px;"></span></span>';

        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Unique]['.$Field.']" id="unique_'.$Field.'_check" '.$USel.' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Required]['.$Field.']" id="required_'.$Field.'_check" '.$RSel.' />';
        $PreReturn[$Field] .= '<input style="display:none;" type="checkbox" name="Data[Content][_Sortable]['.$Field.']" id="issortable_'.$Field.'_check" '.$SSel.' />';

        $PreReturn[$Field] .= '<div class="widefat" id="'.$Field.'_FieldTypePanel" style="display:none; text-align:left;"></div>';
        $PreReturn[$Field] .= '</div><div class="admin_config_panel" style="text-align:right;" id="ExtraSetting_'.$Field.'">';
        unset($Types);





            $Types = explode('_', $Defaults['_Field'][$Field]);
            if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php')) {
                include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php');
                $func = $FieldTypes[$Types[1]]['func'];
                if($func != 'null') {
                    if($func != '') {
                        $PreReturn[$Field] .= '<div class="admin_list_row3" id="'.$Field.'_configPanel" style="display:none; text-align:left;">';
                        $PreReturn[$Field] .= '<h3>'.$Field.' Config</h3><div class="admin_config_panel">';
                        $PreReturn[$Field] .= $func($Field, $Table,$Config);
                        $PreReturn[$Field] .= '</div></div>';
                        $PreReturn[$Field] .= '<input type="button" class="buttons" value="Setup" onclick="toggle(\''.$Field.'_configPanel\');" />';
                    }
                }
            }
            $PreReturn[$Field] .= '</div></div>';






            return $PreReturn[$Field];
        }

        //vardump($Config);

        if($EID == 'false') {

            $Defaults = $Config['Content'];
            //dump($Defaults);
            if(!empty($Defaults['_FormLayout'])) {
                parse_str($Defaults['_FormLayout'], $Columns);
            }
            $Return = '';
            $result = mysql_query("SHOW COLUMNS FROM ".$Table);
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                    $Field = $row['Field'];
                    $PreReturn[$Field] .= df_makeFieldConfigBox($Field, $Config, $Defaults);
                }
            }
            if(!empty($Defaults['_Field']) && $Column != 'N') {
                foreach($Defaults['_Field'] as $Key=>$Value) {
                    if(!empty($PreReturn[$Key])) {
                        $Return .= $PreReturn[$Key];
                        unset($PreReturn[$Key]);
                    }else {
                        //if(substr($Key,0,2) == '__'){
                            $Return .= df_makeFieldConfigBox($Key, $Config, $Defaults);
                        //}
                    }
                }
            }
            if(!empty($PreReturn)) {
                foreach($PreReturn as $Key=>$newFields) {
                    $Return .= $newFields;
                }
            }
        }else {

            $Ref = getelement($EID);
            $Return = '';
            $Row = 'list_row2';



            foreach($Ref['Content']['_Field'] as $Field=>$FieldSet) {
                $Row = dais_rowswitch($Row);
                //$Return .= '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/arrow_out.png" align="absmiddle" class="OrderSorter" />&nbsp;<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/tag.png" align="absmiddle" onclick="jQuery(\'#overide_'.$Field.'\').toggle();" /><input type="texfield" style="width:40px; display:none;" name="Data[Content][_WidthOverride]['.$Field.']" id="overide_'.$Field.'" /> &nbsp;'.df_parseCamelCase($Field).' : '.df_FilterTypes($Field, $Table, $row).'<span id="ExtraSetting_'.$Field.'"></span></div>';
                $Return .= '<div id="Field_'.$Field.'" class="'.$Row.' table_sorter" style="padding:3px;"><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/arrow_out.png" align="absmiddle" class="OrderSorter" />';
                $Return .= '&nbsp;<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/tag.png" align="absmiddle" onclick="jQuery(\'#overide_'.$Field.'\').toggle();" /><span id="overide_'.$Field.'" style="display:none;">';
                //New Options
                $Return .= '&nbsp;Width: <input type="texfield" style="width:40px;" name="Data[Content][_WidthOverride]['.$Field.']" />&nbsp;';
                $Return .= df_alignmentSetup($Field);

                $Return .= '</span> &nbsp;'.df_parseCamelCase($Field).' : '.dr_reportListTypes($Field, $Ref['Content']['_IndexType']).df_fieldTypes($Field, $Table, $row, $Ref['Content']['_Field']).'<span id="ExtraSetting_'.$Field.'">';

                $Types = explode('_', $FieldSet);
                if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php')) {
                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php');
                    $func = $FieldTypes[$Types[1]]['func'];
                    if($func != 'null') {
                        $Return .= '<div style="padding:3px; text-align:right;"><input type="button" class="buttons" value="Setup" onclick="toggle(\''.$Field.'_configPanel\');" /></div>';
                        $Return .= '<blockquote id="'.$Field.'_configPanel">';
                        $Return .= '<h3>'.$Field.' Config</h3>';
                        $Return .= $func($Field, $Table,$Ref);
                        $Return .= '</blockquote>';
                    }
                }

                $Return .= '</span></div>';
                //df_fieldTypes($Field, $Table, $row, $Defaults)
            }







            $Return .= '<input type="hidden" id="referencePage" style="width:40px;" name="Data[Content][_ReferencePage]" value="'.$Ref['ParentDocument'].'" />';
        }
        return $Return;
    }

    function dr_reportListTypes($Field, $Default = false) {


    $VClass = 'button';
    $IClass = 'button';



    $Return = ' &nbsp;<span class="'.$VClass.'" id="displayType_'.$Field.'_show" onclick="" title="Visible"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/eye.png) left center no-repeat; padding:5px 8px;"></span></span>';
    $Return .= '<input style="display:none;" type="checkbox" name="Data[Content][_IndexType]['.$Field.']" id="unique_'.$Field.'_check" '.$USel.' />';

    $Return .= ' &nbsp;<span class="'.$IClass.'" id="displayType_'.$Field.'_show" onclick="" title="Searchable"><span style="background: url('.WP_PLUGIN_URL.'/db-toolkit/data_report/indexed.png) left center no-repeat; padding:5px 8px;"></span></span>';
    $Return .= '<input style="display:none;" type="checkbox" name="Data[Content][_IndexType]['.$Field.']" id="unique_'.$Field.'_check" '.$USel.' />';

   // return $Return;

        $Return = '<select name="Data[Content][_IndexType]['.$Field.']" id="displayType_'.$Field.'">';
        $Sel = '';
        if($Default == 'index_show') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="index_show" '.$Sel.'>Shown Indexed</option>';
        $Sel = '';
        if($Default == 'noindex_show') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="noindex_show" '.$Sel.'>Shown Not Indexed</option>';
        $Sel = '';
        if($Default == 'index_hide') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="index_hide" '.$Sel.'>Hidden Indexed</option>';
        $Sel = '';
        if($Default == 'noindex_hide') {
            $Sel = 'selected="selected"';
        }
        $Return .= '<option value="noindex_hide" '.$Sel.'>Hidden Not Indexed</option>';
        $Return .= '</select>&nbsp;';
        return $Return;
    }

    // End Admin Functions
}
function dr_lockFilters($EID) {
    //   vardump($_SESSION['reportFilters']);
    $setFilters = serialize($_SESSION['reportFilters'][$EID]);
    add_option('filter_Lock_'.$EID, $setFilters);
    return true;
}
function dr_unlockFilters($EID) {
    delete_option('filter_Lock_'.$EID);
    unset($_SESSION['reportFilters'][$EID]);
    unset($_SESSION['lockedFilters'][$EID]);

}
function dr_readSetFilters($EID) {
    return false;
    $selQuery = "SELECT FilterSet FROM `report_filterlocks` WHERE `EID` = '".$EID."' LIMIT 1;";
    $res = mysql_query($selQuery);
    if(mysql_num_rows($res) == 1) {
        $data = mysql_fetch_assoc($res);
        if($out = unserialize($data['FilterSet'])) {
            //$out = core_cleanarray($out);
            //dump($out);
            foreach($out as $key=>$value) {
                if(!empty($value)) {
                    $_SESSION['reportFilters'][$EID][$key] = $value;
                    $_SESSION['lockedFilters'][$EID][$key] = true;
                }
            }
            //unset($_SESSION['reportFilters'][$EID]);
        }
    }
}

function dr_BuildReportFilters($Config, $EID, $Defaults = false) {

    // For the HardUserBase filter that assigned to ta filed, make sure its too is hard filtered.

    //setup indexed filters
    //dump($Config['_IndexType']);
    $Return = '';
    $Keywords = '';

    if(!empty($Defaults['_keywords'])) {
        $Keywords = $Defaults['_keywords'];
    }

    if(!empty($Config['_Show_KeywordFilters'])) {

        if(empty($_SESSION['lockedFilters'][$EID]['_keywords'])){
            $Return .= '<div style="float:left; padding:2px;">';
            if(!empty($Config['_Keyword_Title'])) {
                $Return .= '<strong>'.$Config['_Keyword_Title'].'</strong><br />';
            }
            $Return .= '<input type="text" name="reportFilter['.$EID.'][_keywords]" id="keyWordFilter" class="filterSearch" value="'.$Keywords.'" />&nbsp;&nbsp;&nbsp;</div>';
        }else{
            if(empty($Config['_Hide_FilterLock'])){
            $Return .= '<span class="highlight"><div style="float:left; padding:2px;">';
            if(!empty($Config['_Keyword_Title'])) {
                $Return .= '<strong>'.$Config['_Keyword_Title'].'</strong><br />';
            }
            $Return .= '<input type="text" name="reportFilter['.$EID.'][_keywords]" id="keyWordFilter" class="filterSearch" value="'.$Keywords.'" />&nbsp;&nbsp;&nbsp;</div></span>';
            }
        }
    
        
    }

    foreach($Config['_Field'] as $Field=>$FieldType) {
        if(empty($_SESSION['lockedFilters'][$EID][$Field])) {
            $type = explode('_', $FieldType);
            if(!empty($type[1])) {
                $index = explode('_', $Config['_IndexType'][$Field]);
                if($index[0] == 'index') {
                    if(function_exists($type[0].'_showFilter')) {
                        $func = $type[0].'_showFilter';
                        $Return .= $func($Field, $type[1], $Defaults, $Config, $EID);
                    }
                }
            }
        }else{
            if(empty($Config['_Hide_FilterLock'])){
                $type = explode('_', $FieldType);
                if(!empty($type[1])) {
                    $index = explode('_', $Config['_IndexType'][$Field]);
                    if($index[0] == 'index') {
                        if(function_exists($type[0].'_showFilter')) {
                            $func = $type[0].'_showFilter';
                            $Return .= '<span class="highlight">'.$func($Field, $type[1], $Defaults, $Config, $EID).'</span>';
                        }
                    }
                }
            }
        }
    }

    return $Return;
}


function df_alignmentSetup($id, $Default = false) {
    $Return = 'Justify: <select name="Data[Content][_Justify]['.$id.']" id="Justify_'.$id.'_settings">';
    $Sel = '';
    if($Default == 'Left') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="left" '.$Sel.'>Left</option>';
    $Sel = '';
    if($Default == 'Center') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="Center" '.$Sel.'>Center</option>';
    $Sel = '';
    if($Default == 'Right') {
        $Sel = 'selected="selected"';
    }
    $Return .= '<option value="Right" '.$Sel.'>Right</option>';
    $Return .= '</select>';
    //'<input type="text" name="Data[Content][ImageSizeI]['.$id.']" value="100" class="textfield" size="3" maxlength="3" /> ';
    return $Return;
}

function dr_BuildUpDateForm($EID, $ID){
    $Data = getelement($EID);
    $Data['_ActiveProcess'] = 'update';
    $Out['title'] ='Edit Entry';
    $PreOut = df_BuildCaptureForm($Data, $ID);
    if(!is_array($PreOut)) {
        return df_buildQuickCaptureForm($EID);
    }
    if(!empty($PreOut['title'])) {
        $Out['title'] = $PreOut['title'];
    }
    $Out['html'] = $PreOut['html'];
    $Out['width'] = $PreOut['width'];
    return $Out;
}


function dr_loadreportElements($ID) {

    $Res = mysql_query("SELECT ID, Content FROM `dais_elements` WHERE `ParentDocument` = '".$ID."' && `Element` = 'data_report';");
    if(mysql_num_rows($Res) == 0) {
        return 'No reference insert forms found';
    }
    $Return = 'Reference From: <select name="Data[Content][_Report_Element_Reference]" id="edit_reference_'.$ID.'" >';
    while($Data = mysql_fetch_assoc($Res)) {
        $Out = unserialize($Data['Content']);
        $Return .= '<option value="'.$Data['ID'].'">'.$Out['_ReportTitle'].'</option>';
    }
    $Return .= '</select>';
    return $Return;
}


function df_buildDataSheet($EID, $ID) {

    $Data = getelement($EID);
    $Return = '<h2>Edit Entry</h2>';
    $Return .= df_BuildCaptureForm($Data, $ID);
    return $Return;

}

//clone mater finder
function dr_cloneFindMater($Field, $Clones){

    if(substr($Field,0,2) == '__'){
        $ReturnField = $Clones[$Field]['Master'];
        if(substr($ReturnField,0,2) == '__'){
            return dr_cloneFindMater($ReturnField, $Clones);
        }
        return $ReturnField;
    }
return $Field;
}

//* new report Grid

function dr_BuildReportGrid($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false) {

//Filters will be picked up via Session value
// Set Vars
    if(!empty($Format)) {
        // XML Output
        if(strtolower($Format) == 'xml') {
            $apiOut = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            $apiOut .= "	<entries type=\"array\">\n";
        }
        //json output
        if(strtolower($Format) == 'json') {
            $jsonIndex = 0;
            $apiOutput = array();
            $apiOutput['entries'] = array();
        }

        if(strtolower($Format) == 'pdf') {
            $pdfIndex = 0;
            $apiOutput = array();
        }

    }
    $ReportReturn = '';
    $Element = getelement($EID);
    $Config = $Element['Content'];
    $queryJoin = '';
    $queryWhere = array();
    $queryLimit = '';
    $querySelects = array();
    $WhereTag = '';
    $groupBy = '';
    $orderStr = '';
    $countSelect = '';
    $countLimit = 'LIMIT 1';
    $isModal = 'false';
    if(!empty($Config['_popupTypeView'])){
        if($Config['_popupTypeView'] == 'modal'){
            $isModal = true;
        }
    }
    // Setup Totals Fields
    if(!empty($Config['_TotalsField'])) {
        foreach($Config['_TotalsField'] as $key => $Field) {

            if(!empty($Config['_TotalsFieldTitle'][$key])) {
                $Title = str_replace(' ', '', ucwords($Config['_TotalsFieldTitle'][$key]));
            }else {
                $Title = $Field.'Total';
            }
            //if($Config['_TotalsFieldLocation'][$key] == 'inline'){
            $Config['_Field'][$Title] = 'totals_'.$Config['_TotalsFieldType'][$key];
            $Config['_IndexType'][$Title] = 'index_show';
            $Config['_Justify'][$Title] = $Config['_TotalsFieldJustify'][$key];
            //}else{
            $Config['_TotalsFields'][$Title][$Config['_TotalsFieldType'][$key]] = 0;
            //}
            // Create easy sorting array
            $Config['_TotalsFields'][$Title]['Type'] = $Config['_TotalsFieldType'][$key];
            $Config['_TotalsFields'][$Title]['Grouping'] = $Config['_TotalsGroupingField'][$key];
            $Config['_TotalsFields'][$Title]['Location'] = $Config['_TotalsFieldLocation'][$key];
            $Config['_TotalsFields'][$Title]['Function'] = $Config['_TotalsFieldFunction'][$key];
            $Config['_TotalsFields'][$Title]['Prefix'] = $Config['_TotalsFieldPrefix'][$key];
            $Config['_TotalsFields'][$Title]['Suffix'] = $Config['_TotalsFieldSuffix'][$key];
            $Config['_TotalsFields'][$Title]['PrimField'] = $Field;
            //dump($Config['_TotalsFieldTitleWidth']);
            if(!empty($Config['_TotalsFieldTitleWidth'][$key])) {
                $Config['_WidthOverride'][$Title] = $Config['_TotalsFieldTitleWidth'][$key];
            }
            if(!empty($Config['_TotalsFieldCaption'][$key])) {
                $Config['_TotalsFields'][$Title]['Caption'] = $Config['_TotalsFieldCaption'][$key];
            }

            $Config['_TotalsFields'][$Title]['Title'] = $Title;
            unset($Title);
        }
        //unset($Config['_TotalsFieldType']);
        //unset($Config['_TotalsFieldLocation']);
        //unset($Config['_TotalsFieldTitle']);
        //unset($Config['_TotalsField']);
    }
    if(!empty($Page)) {
        $_SESSION['report_'.$EID]['LastPage'] = $Page;
    }else {
        if(empty($_SESSION['report_'.$EID]['LastPage']) || $_SESSION['report_'.$EID]['LastPage'] == 'undefined') {
            $_SESSION['report_'.$EID]['LastPage'] = 1;
        }
        $Page = $_SESSION['report_'.$EID]['LastPage'];
    }
    if(!empty($SortDir)) {
        $_SESSION['report_'.$EID]['SortDir'] = $SortDir;
    }
    if(!empty($SortField)) {
        $_SESSION['report_'.$EID]['SortField'] = $SortField;
    }

//setup Field Types
    foreach($Config['_Field'] as $Field=>$Type) {
        // explodes to:
        // [0] = Field plugin dir
        // [1] = Field plugin type
        $Config['_Field'][$Field] = explode('_', $Type);
    }

//SetupHeaders
    // Start Table
    // Check for template
    
    if(empty($Config['_UseListViewTemplate'])) {
        $tableClass = '';
        if(is_admin()) {
            $tableClass = 'class="widefat"';
        }
        $ReportReturn .= '<table width="100%" border="0" cellspacing="0" cellpadding="4" '.$tableClass.' id="data_report_'.$EID.'" style="cursor:default;">';
        //Start Headers Row
        //$ReportReturn .= '<caption>'.$Config['_ReportTitle'].'</caption>';
        $ReportReturn .= '<thead>';
        $ReportReturn .= '<tr>';
    }else {
        if(!empty($Config['_ListViewTemplatePreHeader'])) {
            $ReportReturn .= $Config['_ListViewTemplatePreHeader'];
        }
    }
    foreach($Config['_IndexType'] as $Field=>$Type) {
        //Seperate Index/Display Types
        $Config['_IndexType'][$Field] = explode('_', $Type);
        // Totals Location check to see if field is inline or not.
        $Location = 'inline';
        if(!empty($Config['_TotalsFields'][$Field]['Location'])) {
            $Location = $Config['_TotalsFields'][$Field]['Location'];
        }
        if($Config['_IndexType'][$Field][1] == 'show' && ($Location == 'inline' || $Location == 'headerinline' || $Location == 'footerinline')) {
            //Set Widths
            $Direction = 'ASC';
            if($_SESSION['report_'.$EID]['SortDir'] == 'ASC') {
                $Direction = 'DESC';
            }
            $sortClass = 'report_header';
            if($_SESSION['report_'.$EID]['SortField'] == $Field) {
                $sortClass = 'sorting_'.$_SESSION['report_'.$EID]['SortDir'];
            }
            // set the column Title
            $fieldTitle = $Field;
            if(!empty($Config['_TotalsFields'][$Field]['Title'])) {
                $fieldTitle = $Config['_TotalsFields'][$Field]['Title'];
            }
            if(empty($Config['_WidthOverride'][$Field])) {
                $Config['_WidthOverride'][$Field] = '';
            }
            // Header Template Insert
            if(!empty($Config['_UseListViewTemplate'])) {
                if(!empty($Config['_ListViewTemplateHeader'])) {
                    if(!empty($Config['_FieldTitle'][$Field])) {
                        $preHeader = '<span>'.$Config['_FieldTitle'][$Field].'</span>';
                    }else {
                        $preHeader = '<span>'.df_parseCamelCase($fieldTitle).'</span>';
                    }
                    $PreHeader = str_replace('{{_Field}}', $preHeader, $Config['_ListViewTemplateHeader']);
                    $ReportReturn .= $PreHeader;
                }
            }else {
                $ReportReturn .= '<th nowrap="nowrap" scope="col" width="'.($Config['_WidthOverride'][$Field] == '' ? '{{width_'.$Field.'}}px' : $Config['_WidthOverride'][$Field].'px').'" ';
                if(!empty($Config['_Sortable'][$Field])) {
                    $ReportReturn .= 'onclick="dr_sortReport(\''.$EID.'\', \''.$Field.'\', \''.$Direction.'\');" class="'.$sortClass.'"';
                }
                $ReportReturn .= '>';
                if(!empty($Config['_FieldTitle'][$Field])) {
                    $ReportReturn .= '<span>'.$Config['_FieldTitle'][$Field].'</span>';
                }else {
                    $ReportReturn .= '<span>'.df_parseCamelCase($fieldTitle).'</span>';
                }

                $ReportReturn .= '</th>';
            }
            // Preset the selects from query
            $querySelects[$Field] = 'prim.'.$Field;
            // Set average width and min width
            $minWidth[$Field] = strlen($fieldTitle)*8;
            $AvrageWidth[$Field] = array();
            $AvrageWidth[$Field][] = $minWidth[$Field];

        }
    }
    
    // Add the return field to select
    if(!empty($Config['_ReturnFields'])) {
        //$querySelects[$Config['_ReturnFields'][0]] = 'prim.'.$Config['_ReturnFields'][0];
        foreach($Config['_ReturnFields'] as $Field) {
            $newField = '_return_'.$Field;
            $querySelects[$newField] = 'prim.'.$Field.' AS '.$newField.' ';
        }
    }
    if(empty($Config['_Show_popup'])) {
        if(!empty($Config['_Show_Edit']) && empty($Config['_ItemViewPage']) || !empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
            $ShowActionPanel=true;
        }

        if(!empty($ShowActionPanel)) {
            if(!empty($Config['_UseListViewTemplate'])) {
                if(!empty($Config['_ListViewTemplateHeader'])) {
                    $PreHeader = str_replace('{{_Field}}', 'Action', $Config['_ListViewTemplateHeader']);
                    $ReportReturn .= $PreHeader;
                }
            }else {
                $ReportReturn .= '<th scope="col">';
                $ReportReturn .= 'Action';
                $ReportReturn .= '</th>';
            }
        }
    }
    if(!empty($Config['_UseListViewTemplate'])) {
        if(!empty($Config['_ListViewTemplatePostHeader'])) {
            $ReportReturn .= $Config['_ListViewTemplatePostHeader'];
        }
    }else {
        $ReportReturn .= '</tr>';
        $ReportReturn .= '</thead>';
    }
    // End Headers setup

    // Build Query

    // field type filters
    $joinIndex = 'a';

    // Remove headers for chart only
    if(!empty($Config['_chartOnly'])){
      $ReportReturn = '';
    }

   // Linkup CLoned Fields
    //vardump($Config);
    if(!empty($Config['_CloneField'])) {
        //vardump($Config['_CloneField']);
        $querySelectsPre = $querySelects;
        foreach($Config['_CloneField'] as $CloneKey=>$Clone) {
            //echo 'BEFORE';
            //vardump($querySelects);
            foreach($querySelects as $selectKey=>$selectScan){
                $queryJoin = str_replace($CloneKey, $Clone['Master'], $queryJoin);
                $WhereTag = str_replace($CloneKey, $Clone['Master'], $WhereTag);
                if(strstr($selectScan, " AS ") === false){
                    //echo $Clone['Master'].' - concat <br />';
                    if(strstr($selectScan, "_sourceid_") === false){
                        //echo $Clone['Master'];
                        $querySelects[$selectKey] = str_replace($CloneKey, $Clone['Master'].' AS '.$CloneKey, $selectScan);
                    }
                }
            }
            //echo 'After';
            //vardump($querySelects);
        }
        
    }
    
    foreach($Config['_Field'] as $Field=>$Type) {
        // Run Filters that have been set through each field type
        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/queryfilter.php')) {
            include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/queryfilter.php');
        }
        //apply a generic keyword filter to each field is a key word has been sent
        if(($Config['_IndexType'][$Field][0]) == 'index'){
            if(!empty($_SESSION['reportFilters'][$EID]['_keywords'])) {
                if($WhereTag == '') {
                    $WhereTag = " WHERE ";
                }
                 if(!empty($Config['_CloneField'][$Field])){
                    $keyField = 'prim.'.$Field;                    
                }else{
                    if(!empty($Config['_CloneField'][$Field]['Master'])){
                        $keyField = $Config['_CloneField'][$Field]['Master'];
                    }else{
                        $keyField = $Field;
                    }
                }
                if(strstr($querySelects[$Field], ' AS ') !== false) {
                    $preKeyField = explode(' AS ', $querySelects[$Field]);
                    $keyField = $preKeyField[0];
                    //$keyField = strtok($querySelects[$Field], ' AS ');
                }
                $preWhere[] = $keyField." LIKE '%".$_SESSION['reportFilters'][$EID]['_keywords']."%' ";
                //echo $keyField." LIKE '%".$_SESSION['reportFilters'][$EID]['_keywords']."%' <br />";
                //dump($_SESSION['reportFilters'][$EID]);
            }
        }
        $joinIndex++;
    }
    //post clone fixes
    foreach($querySelects as $fieldToFix=>$select){
        if(!empty($Config['_CloneField'][$fieldToFix])){
            $cloneReturns[$fieldToFix] = explode(' AS ', $select);            
        }
    }
    if(!empty($cloneReturns)){
    foreach($cloneReturns as $cloneKey=>$cloneField){
        $pureName = str_replace('prim.','',$cloneField[0]);
        if(!empty($cloneReturns[$pureName])){
           $cloneReturns[$cloneKey][0] = $cloneReturns[$pureName][0];
           $querySelects[$cloneKey] = implode(' AS ', $cloneReturns[$cloneKey]);
        }
    }
    }
// combine keyword search if there are any
    if(!empty($preWhere)) {
        $queryWhere[] = '('.implode(' OR ', $preWhere).')';
    }

    // create Query Selects and Where clause string
    $querySelect = implode(",\n\t",$querySelects);
    $queryWhere = implode("\n AND ", $queryWhere);


    // build the ordering
    //dump($querySelects[$_SESSION['report_'.$EID]['SortField']]);
    if(!empty($Config['_SortField'])) {
        $OrderField = $querySelects[$_SESSION['report_'.$EID]['SortField']];
        if(strpos($querySelects[$_SESSION['report_'.$EID]['SortField']], ' AS ') !== false) {
            $OrderField = explode(' AS ', $querySelects[$_SESSION['report_'.$EID]['SortField']]);
            $OrderField = $OrderField[1];
        }
        if(!empty($OrderField)) {
            $orderStr = 'ORDER BY '.$OrderField.' '.$_SESSION['report_'.$EID]['SortDir'];
        }else {
            $orderStr = 'ORDER BY '.$Config['_SortField'].' '.$Config['_SortDirection'].'';
        }
    }
   // echo $orderStr;
    
    // Build the grouping if ther are any
    if(is_array($groupBy)) {
        $groupBy = 'GROUP BY ('.implode(',', $groupBy).')';
        $countLimit = '';
        $entryCount = true;
        //add totals selects to count
        if(is_array($countSelect)) {
            $countSelect = ','.implode(',',$countSelect);
        }
    }
    /// clones where here 

    //queryJoin

    global $wpdb;

    // Totals Query & Results
    $CountQuery = "SELECT count(prim.".$Config['_ReturnFields'][0].") as Total FROM `".$Config['_main_table']."` AS prim \n ".$queryJoin." \n ".$WhereTag." \n ".$queryWhere." \n ".$groupBy." ".$countLimit.";";
    // Wrap fields with ``
    foreach($querySelects as $Field=>$FieldValue){       
       $CountQuery = str_replace($Field, '`'.$Field.'`', $CountQuery);
    }



    //$CountrResult = $wpdb->get_results($CountQuery, ARRAY_A);
    //vardump($CountResult);
    $CountResult = mysql_query($CountQuery);    
    if(!empty($entryCount)) {        
        // Countr Rows
        while($prCount = mysql_fetch_assoc($CountResult)) {
            $preCount[] = $prCount['Total'];
        }
        if(!empty($preCount)) {
            $Count['TotalEntries'] = array_sum($preCount);
            unset($prCount);
            unset($preCount);
        }else {
            $Count['TotalEntries'] = 0;
        }
        $Count['Total'] = mysql_num_rows($CountResult);
    }else {
        // get Count entry
        if(!empty($CountResult)) {
            $Count = mysql_fetch_assoc($CountResult);
            mysql_free_result($CountResult);
        }else {
            $Count = 0;
        }
    }
    $TotalPages = ceil($Count['Total']/$Config['_Items_Per_Page']);
    $Start = ($Page*$Config['_Items_Per_Page'])-$Config['_Items_Per_Page'];
    $Offset = $Config['_Items_Per_Page'];
    if($Page > 0) {
        if($Page > $TotalPages) {
            $Page = $TotalPages;
            $Start = ($Page*$Config['_Items_Per_Page'])-$Config['_Items_Per_Page'];
            if($Start < 0) {
                $Start = 1;
            }
        }
        $queryLimit = " LIMIT ".$Start.", ".$Offset." ";
        //$Limit = "";
    }
    if(strtolower($Format) == 'pdf' && $limitOveride != false) {
        if($limitOveride = 'full') {
            $queryLimit = '';
        }
    }
    // Select Query
    //$Query = "SELECT count(b.Country) as TotalCountry, ".$querySelect." FROM `".$Config['_main_table']."` AS prim \n ".$queryJoin." \n ".$WhereTag." \n ".$queryWhere."\n GROUP BY b.Country \n ".$orderStr." \n ".$queryLimit.";";
    
    $Query = "SELECT ".$querySelect." FROM `".$Config['_main_table']."` AS prim \n ".$queryJoin." \n ".$WhereTag." \n ".$queryWhere."\n ".$groupBy." \n ".$orderStr." \n ".$queryLimit.";";
    // Wrap fields with ``
    foreach($querySelects as $Field=>$FieldValue){
       // echo $Field.' = '.$FieldValue.'<br />';
       $Query = str_replace('.'.$Field, '.`'.$Field.'`', $Query);
    }
    $_SESSION['queries'][$EID] = $Query;

    if(!empty($Config['_chartMode']) && empty($Format)) {
        $chartRes = mysql_query($Query);
        //dump($Config);
        $x = array();
        $y = array();
        while($chartData=mysql_fetch_assoc($chartRes)) {
            // dump($chartData);
            $XValue = $chartData[$Config['_xaxis']];
            if(function_exists($Config['_Field'][$Config['_xaxis']][0].'_processValue')) {
                $processFunc = $Config['_Field'][$Config['_xaxis']][0].'_processValue';
                $XValue = '"'.$processFunc($XValue, $Config['_Field'][$Config['_xaxis']][1], $Config['_xaxis'], $Config, $EID, 'Graph').'"';
            }
            $x[] = $XValue;
            foreach($Config['_chartValue'] as $ChartLine => $on) {

                $YValue = $chartData[$ChartLine];
                if(function_exists($Config['_Field'][$ChartLine][0].'_processValue')) {
                    $processFunc = $Config['_Field'][$ChartLine][0].'_processValue';
                    $YValue = $processFunc($chartData[$ChartLine], $Config['_Field'][$ChartLine][1], $ChartLine, $Config, $EID, 'Graph');

                }


                $y[$ChartLine][] = $YValue;

                //$y[$ChartLine][] = "['".$XValue."', ".$YValue."]";
            }
        }




        //dump($Config);
        /*
            $_SESSION['dataform']['OutScripts'] .= "
                $(function () {
                    var datasets = [
                         {
                         ";
            foreach($y as $key=>$data){
                $Line[] = "label: \"".$Config['_FieldTitle'][$key]."\",
                        data: [".implode(', ', $data)."]
                    ";
            }
           $_SESSION['dataform']['OutScripts'] .= implode('}, {', $Line);
           $_SESSION['dataform']['OutScripts'] .= "
                         }
                    ];

                    $.plot($(\"#graph_".$EID."\"), datasets,{
                        xaxis: { mode: 'time', timeformat: '%d/%m/%y' },
                        lines: { show: true },
                        points: { show: true },
                        legend: {position: \"ne\"}

                    });
                });
            ";

        */
        //$chartType = 'line';
        //if(!empty($Config['_chartType'])) {
        //    $chartType = $Config['_chartType'];
        //}
        //if($Config['chartType'] == 'bar') {
        //    $height = (mysql_num_rows($chartRes)*45)+50;
        //}else {
        //    $height = '300';
        //}
//dump($Config);
        if(!empty($Config['_chartHeight'])) {
            $height = $Config['_chartHeight'];
        }
        $rightPad = (20+(100*count($Config['_chartValue']))-100);
        //dump($Config);
        $ChartTitle = '';
        if(!empty($Config['_chartTitle'])){
            $ChartTitle = $Config['_chartTitle'];
        }
        $ChartCaption = '';
        if(!empty($Config['_chartCaption'])){
            $ChartCaption = $Config['_chartCaption'];
        }
        $ChartID = uniqid('chart_');
        $_SESSION['dataform']['OutScripts'] .= "
var ".$ChartID." = new Highcharts.Chart({
   chart: {
      renderTo: 'chart_".$ChartID."',
      //defaultSeriesType: '".$chartType."',
      zoomType: 'x',
      margin: [60, ".$rightPad.", 120, 80],
      height: ".$height.",
      backgroundColor: null,
   },
   title: {
      align: 'left',
      text: '".$ChartTitle."',
      x: 80
   },
   subtitle: {
      text: '".$ChartCaption."',
      align: 'left',
      x : 81


   },
   xAxis: {
      categories: [".implode(', ', $x)."],
      labels: {
         rotation: -45,
         align: 'right',
         style: {
             font: 'normal 9px Verdana, sans-serif'
         }
      },
      tickInterval: 'auto'
   },

   yAxis: [";
        $index = 1;
        $margin = 50;
        $off = '';
        $color = array("#4572A7","#AA4643","#89A54E","#80699B","#3D96AE","#DB843D","#92A8CD","#A47D7C","#B5CA92");
        foreach($y as $Key=>$Series) {
            $op = '';
            if($index > 1) {
                $margin = 60;
                $op = "opposite: true";
            }
            if($index > 2) {
                $off = ', offset: '.(40*($index-1));
                $margin = 60;
            }
            $axis[] = "
    {
      labels: {
         style: {
            color: '".$color[$index-1]."'
         }
      },
      title: {
         text: '".$Config['_FieldTitle'][$Key]."',
         margin: ".$margin.",
         style: {
            color: '".$color[$index-1]."'
         },
      },
      ".$op."
      ".$off."
   }";

            $index++;
        }
        $_SESSION['dataform']['OutScripts'] .= implode(',', $axis);


        $_SESSION['dataform']['OutScripts'] .= "
   ],
   tooltip: {
      formatter: function() {
            return '<b>'+ this.series.name +'</b><br/>'+
            this.y +': '+ this.x;
      }
   },
   plotOptions: {
      bar: {
         dataLabels: {
            enabled: true,
            color: 'auto'
         }
      },
      marker: {
            enabled: false,
            states: {
               hover: {
                  enabled: true,
                  radius: 3
               }
            }
         }

   },
   legend: {
      layout: 'horizontal',
      style: {
         left: 'auto',
         bottom: 'auto',
         right: '180px',
         top: '0px'
      },
      backgroundColor: '#ffffff'
   },

   series: [";
        $Chart = array();
        $index = 0;
        foreach($y as $Key=>$Series) {

            $Line = '{';
            $Line .= 'name: "'.$Config['_FieldTitle'][$Key].'", ';
            $Line .= 'data: ['.implode(', ', $Series).']';
            $Line .= ', type: \''.$Config['_chartType'][$Key].'\' ';
            if($index >= 1) {
                $Line .= ', ';
                $Line .= 'yAxis: '.$index.'';
            }
            $Line .= '}';
            $Graph[] = $Line;
            $index++;
        }
        $_SESSION['dataform']['OutScripts'] .= implode(',', $Graph)."
   ]
});

";



        //$_SESSION[]
        //TODO : make the interface remember its in sqlmode;

        $ReportReturn = '<div id="chart_'.$ChartID.'" style="height:'.$height.'px;"></div>'.$ReportReturn;
        
    }



    // Query Results
    //echo $Query;
    if(strtolower($Format) == 'sql'){
        return $Query;
    }
    
    $Result = $wpdb->get_results($Query, ARRAY_A);
    
    //$Result = mysql_query($Query);

    // Build Rows
    if(!empty($Config['_UseListViewTemplate'])) {
        if(!empty($Config['_ListViewTemplateContentWrapperStart'])) {
            $ReportReturn .= $Config['_ListViewTemplateContentWrapperStart'];
        }
    }else {
        $ReportReturn .= '<tbody>';
    }
    // Row number Increment
    $rowIndex = 1;
    // Set Row Style
    $Row = 'odd';

    // add in inline editing
    if(!empty($Config['_InlineEdit'])) {
        $_SESSION['dataform']['OutScripts'] .= "
			jQuery('.inlineedit').bind('change', function(t){
				ajaxCall('df_inlineedit', this.id, jQuery(this).attr('ref'), this.value, function(f){
					if(f != '1'){
						df_dialog(f, jQuery(this).attr('ref'), '0');	
					}
				});
				//alert(this.id+' - '+jQuery(this).attr('ref')+' - '+this.value);
			});
		";
    }

    if(!empty($Result)) {
        
        //while($row = mysql_fetch_assoc($Result)) {
        foreach ($Result as $row) {

            // Switch Row Style
            //$Row = dais_rowswitch($Row);
            //$Row = report_rowswitch($Row);
            $Row = grid_rowswitch($Row);
            // foreach column

            $SelectedRow = '';
            if(!empty($Config['_ReturnFields'][0])) {
                if(!empty($_GET[$Config['_ReturnFields'][0]])) {
                    if($row['_return_'.$Config['_ReturnFields'][0]] == $_GET[$Config['_ReturnFields'][0]]) {
                        if(!empty($Config['_Show_Edit'])) {
                            $SelectedRow = 'highlight';
                        }
                        $HighlightIndex = true;
                    }
                }
            }
            if(empty($Config['_UseListViewTemplate'])) {
                $ReportReturn .= '<tr class="'.$Row.' itemRow_'.$EID.' '.$SelectedRow.' report_entry" ref="'.$row['_return_'.$Config['_ReturnFields'][0]].' highlight" id="row_'.$EID.'_'.$rowIndex.'" >';
            }
            // API Output
            if(!empty($Format)) {
                // XML Output
                if(strtolower($Format) == 'xml') {
                    $apiOut .= "		<entry>\n";
                }
                // json Output
                if(strtolower($Format) == 'json') {
                    $apiOutput['entries'][$jsonIndex] = array();
                }
                if(strtolower($Format) == 'pdf') {
                    //$apiOutput[$jsonIndex][] = array();
                    if(!empty($_SESSION['reportFilters'][$EID])) {
                        //$apiOutput['filters'] = array();
                    }
                }
            }
            // Output each Row
            //dump($row);
            if(!empty($Config['_UseListViewTemplate'])) {
                if(!empty($Config['_ListViewTemplatePreContent'])) {
                    $ReportReturn .= $Config['_ListViewTemplatePreContent'];
                }
            }

            if(!empty($Config['_UseListViewTemplate'])) {
                if(!empty($Config['_ListViewTemplateContent'])) {
                    $PreReturn = str_replace('{{_FieldValue}}', $PreReportReturn, $Config['_ListViewTemplateContent']);
                    foreach($row as $fieldKey=>$fieldValue) {
                        if(function_exists($Config['_Field'][$fieldKey][0].'_processValue')) {
                            $processFunc = $Config['_Field'][$fieldKey][0].'_processValue';
                            $fieldValue = $processFunc($fieldValue, $Config['_Field'][$fieldKey][1], $fieldKey, $Config, $EID, $row);
                        }
                        if(!empty($Config['_FieldTitle'][$fieldKey])) {
                            $name = $Config['_FieldTitle'][$fieldKey];
                        }else {
                            $name = df_parseCamelCase($fieldKey);
                        }

                        $PreReturn = str_replace('{{_'.$fieldKey.'_name}}', $name, $PreReturn);
                        $PreReturn = str_replace('{{'.$fieldKey.'}}', $fieldValue, $PreReturn);


                        //vardump($Config);

                        //echo '<br />'.$fieldKey;
                        //$PreReturn = str_replace('{{'.$fieldKey.'}}', $fieldValue, $PreReturn);
                    }

                    // substr

                    preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)\}\}/", $PreReturn, $returnMatches);
                    if(!empty($returnMatches)) {
                        $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]),0,$returnMatches[2]).'&hellip;', $PreReturn);
                    }


                    $PreReturn = str_replace('{{_RowClass}}', $Row, $PreReturn);
                    $PreReturn = str_replace('{{_RowIndex}}', $rowIndex, $PreReturn);
                    $PreReturn = str_replace('{{_UID}}', uniqid(), $PreReturn);
                    $PreReturn = str_replace('{{_PageID}}', $Element['ParentDocument'], $PreReturn);
                    $PreReturn = str_replace('{{_PageName}}', getdocument($Element['ParentDocument']), $PreReturn);
                    $PreReturn = str_replace('{{_EID}}', $EID, $PreReturn);
                    //Template based view / edit
                    if(!empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
                        $ViewLink = '';
                        if(!empty($Config['_Show_View'])) {
                            $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry(".$row['_return_'.$Config['_ReturnFields'][0]].", ".$EID.", ".$isModal."); return false;\"><img src=\"".WP_PLUGIN_URL."/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                            if(!empty($Config['_ItemViewPage'])) {
                                $ReportVars = array();
                                foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                    $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                                }
                                // Get permalink
                                $PageLink = get_permalink($Config['_ItemViewPage']);
                                $Location = parse_url($PageLink);
                                if(!empty($Location['query'])) {
                                    $PageLink = str_replace('?'.$Location['query'], '', $PageLink);
                                    parse_str($Location['query'], $gets);
                                    $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                }else {
                                    $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query($ReportVars));
                                }
                                $ViewLink = "<a href=\"".$PageLink."\"><img src=\"".WP_PLUGIN_URL."/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                            }
                        }
                        if(!empty($Config['_Show_Edit'])) {
                            if($ViewLink != '') {
                                $ViewLink .= " ";
                            }
                            $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm('.$EID.', '.$row['_return_'.$Config['_ReturnFields'][0]].');"><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                        }
                        $PreReturn = str_replace('{{_ViewEdit}}', $ViewLink, $PreReturn);//'Edit | View';
                        $PreReturn = str_replace('{{_ViewLink}}', getdocument($Config['_ItemViewPage'])."?".$ReportReturnString, $PreReturn);//'Edit | View';
                    }
                    $ReportReturn .= $PreReturn;
                }
                if(!empty($Config['_UseListViewTemplate'])) {
                    if(!empty($Config['_ListViewTemplatePostContent'])) {
                        $ReportReturn .= $Config['_ListViewTemplatePostContent'];
                    }
                }
            }else {
                if(empty($Config['_chartOnly'])){
                $ColumnCounter = 0;
                foreach($row as $Field=>$Data) {
                    if(!empty($Config['_IndexType'][$Field][1])) {
                        if($Config['_IndexType'][$Field][1] == 'show') {
                            $outData = $Data;
                            /// Capture value for Totals
                            if(!empty($Config['_TotalsFields'][$Field]['Type'])) {
                                switch($Config['_TotalsFields'][$Field]['Type']) {
                                    case 'count':
                                    //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].'+1<br />';
                                        $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Count['TotalEntries'];
                                        break;
                                    case 'sum':
                                    //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].' + '.$outData.'<br />';
                                        $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']]+$outData;
                                        break;
                                    case 'avg':
                                    //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].' + '.$outData.'<br />';
                                        $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']]+$outData;
                                        break;
                                }
                            }

                            if(function_exists($Config['_Field'][$Field][0].'_processValue')) {
                                $processFunc = $Config['_Field'][$Field][0].'_processValue';
                                //						Value  field type					 Field	 element config
                                $outData = $processFunc($Data, $Config['_Field'][$Field][1], $Field, $Config, $EID, $row);
                            }
                            // Capture columns average width for ato widths
                            $AvrageWidth[$Field][] = strlen($outData)*8;


                            // Apply keyword Fitler Highlight
                            if(!empty($_SESSION['reportFilters'][$EID]['_keywords'])) {
                                //$outData = str_replace($_SESSION['reportFilters'][$EID]['_keywords'], '<strong>'.$_SESSION['reportFilters'][$EID]['_keywords'].'</strong>', $outData);
                                //$outData = str_replace(ucwords($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.ucwords($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                                //$outData = str_replace(strtoupper($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.strtoupper($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                                //$outData = str_replace(strtolower($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.strtolower($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                            }

                            // set row output
                            //Check if field is in totals and is allowed inline
                            $Location = 'inline';
                            if(!empty($Config['_TotalsFields'][$Field]['Location'])) {
                                $Location = $Config['_TotalsFields'][$Field]['Location'];
                            }
                            if($Location == 'inline' || $Location == 'headerinline' || $Location == 'footerinline') {
                                // selection highlighting (experimental)
                                $sortClass = '';
                                if($_SESSION['report_'.$EID]['SortField'] == $Field) {
                                    $sortClass = 'column_sorting_'.$_SESSION['report_'.$EID]['SortDir'];
                                }
                                $itemID = uniqid('');
                                // Add Reload Highlighting
                                $LiveHighlight = '';
                                if(!empty($Config['_showReload'])) {
                                    /*
							if(empty($_SESSION['liveLoad'][$EID][$row['_return_'.$Config['_ReturnFields'][0]]][$Field])){
								$_SESSION['liveLoad'][$EID][$row['_return_'.$Config['_ReturnFields'][0]]][$Field] = md5(stripslashes($outData));
									$_SESSION['liveLoad'][$EID][$row['_return_'.$Config['_ReturnFields'][0]]][$Field] = md5(stripslashes($outData));
									$_SESSION['dataform']['OutScripts'] .= "
											jQuery('#".$itemID."').animate({opacity: 0.9}, 1000, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50);
										});
										});
										});
										});
										});
										});
									";
							}else{
								if($_SESSION['liveLoad'][$EID][$row['_return_'.$Config['_ReturnFields'][0]]][$Field] != md5(stripslashes($outData))){
									//echo $Field.' - Different<br />';
									//$LiveHighlight = ' liveChange ';
									$_SESSION['liveLoad'][$EID][$row['_return_'.$Config['_ReturnFields'][0]]][$Field] = md5(stripslashes($outData));
									$_SESSION['dataform']['OutScripts'] .= "
											jQuery('#".$itemID."').animate({opacity: 0.9}, 1000, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 0}, 50, function(){
											jQuery('#".$itemID."').animate({opacity: 1}, 50);
										});
										});
										});
										});
										});
										});
									";
								}
							}
                                    */
                                }
                                $ReportReturn .= '<td class="'.$Row.' '.$sortClass.' '.$LiveHighlight.'" scope="col" id="'.$itemID.'" ref="itemRow_'.$EID.'" width="'.($Config['_WidthOverride'][$Field] == '' ? '{{width_'.$Field.'}}px' : $Config['_WidthOverride'][$Field].'px').'" style="text-align:'.$Config['_Justify'][$Field].'; ">';
                                //inline editing
                                if(!empty($Config['_InlineEdit'][$Field])) {
                                    $Req = 'inlineedit';
                                    $FieldSet = $Config['_Field'][$Field];
                                    //$ReportReturn .= WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php';
                                    ob_start();
                                    $Defaults[$Field] = $row['_sourceid_'.$Field];
                                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php');
                                    include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php');
                                    $ReportReturn .= ob_get_clean();
                                }else {
                                    $PreReportReturn = '';
                                    // Make View Item Link If page is set
                                    if(is_admin()) {
                                        //vardump($Config);
                                        if(!empty($Config['_ItemViewInterface'])) {
                                            // Create return link
                                            $ReportVars = array();
                                            foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                                $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                                            }
                                            // Get permalink
                                            // interface admin.php?page=Database_Toolkit&renderinterface=dt_intfc4c04c77ed928a
                                            $PageLink = 'admin.php?page=Database_Toolkit&renderinterface='.$Config['_ItemViewInterface'].'&'.htmlspecialchars_decode(http_build_query($ReportVars));
                                            $PreReportReturn .= "<a href=\"".$PageLink."\"><strong>";
                                        }
                                    }else {
                                        if(!empty($Config['_ItemViewPage'])) {
                                            // Create return link
                                            $ReportVars = array();
                                            foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                                $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                                            }
                                            // Get permalink
                                            $PageLink = get_permalink($Config['_ItemViewPage']);
                                            $Location = parse_url($PageLink);
                                            if(!empty($Location['query'])) {
                                                $PageLink = str_replace('?'.$Location['query'], '', $PageLink);
                                                parse_str($Location['query'], $gets);
                                                $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                            }else {
                                                $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query($ReportVars));
                                            }
                                            $PreReportReturn .= "<a href=\"".$PageLink."\"><strong>";

                                        }
                                    }
                                    $ReturnFields = array();
                                    foreach($Config['_ReturnFields'] as $ReturnField) {
                                        $ReturnFields[] = $ReturnField.'='.urlencode($row['_return_'.$ReturnField]);
                                    }
                                    //$ReturnMix = implode('&', $ReturnFields);
                                    //$PreReportReturn .= '<a href="'.getdocument($_GET['PageData']['ID']).'#'.$ReturnMix.'">'.stripslashes($outData).'</a>';
                                    $PreReportReturn .= $outData;
                                    // API Output
                                    if(!empty($Format)) {
                                        // XML Output
                                        if(strtolower($Format) == 'xml') {
                                            $apiOut .= "			<".$Field.">".htmlentities(stripslashes($outData))."</".$Field.">\n";
                                        }
                                        // json Output
                                        if(strtolower($Format) == 'json') {
                                            $apiOutput['entries'][$jsonIndex][$Field] = htmlentities(stripslashes($outData));
                                        }
                                        // PDF output
                                        if(strtolower($Format) == 'pdf') {
                                            $apiOutput[$pdfIndex][$Field] = htmlentities(stripslashes($outData));
                                            if(!empty($_SESSION['reportFilters'][$EID][$Field])) {
                                                $apiOutput['filters'][$Field][stripslashes($outData)] = stripslashes($outData);
                                            }
                                        }
                                    }
                                    // Close link
                                    if(!empty($Config['_ItemViewPage'])) {
                                        $PreReportReturn .= "</strong></a>";
                                    }
                                    if(!empty($Config['_ItemViewInterface'])) {
                                        $PreReportReturn .= "</strong></a>";
                                    }
                                }
                                $ReportReturn .= $PreReportReturn;
                                if(!empty($Config['_Show_popup'])) {
                                    if($ColumnCounter===0) {
                                        // Add inline actions
                                        $ViewLink = '';
                                        $ActionWidth = 16;
                                        if(!empty($Config['_Show_View'])) {
                                            $ActionWidth = $ActionWidth+16;
                                            $ViewLink['view'] = "<a href=\"#\" onclick=\"return false;\"><span style=\"cursor:pointer;\" onclick=\"df_loadEntry('".$row['_return_'.$Config['_ReturnFields'][0]]."', '".$EID."', ".$isModal."); return false;\">View</span></a>";
                                            if(is_admin()) {
                                                if(!empty($Config['_ItemViewInterface'])) {
                                                    $ReportVars = array();
                                                    foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                                        $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                                                    }
                                                    // Get permalink
                                                    $PageLink = 'admin.php?page='.$Config['_ItemViewInterface'].'&'.htmlspecialchars_decode(http_build_query($ReportVars));
                                                    $ViewLink['view'] = "<a href=\"".$PageLink."\">View</a>";

                                                }
                                            }else {
                                                if(!empty($Config['_ItemViewPage'])) {
                                                    $ReportVars = array();
                                                    foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                                        $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                                                    }
                                                    // Get permalink
                                                    $PageLink = get_permalink($Config['_ItemViewPage']);
                                                    $Location = parse_url($PageLink);
                                                    if(!empty($Location['query'])) {
                                                        $PageLink = str_replace('?'.$Location['query'], '', $PageLink);
                                                        parse_str($Location['query'], $gets);
                                                        $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                                    }else {
                                                        $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query($ReportVars));
                                                    }
                                                    $ViewLink['view'] = "<a href=\"".$PageLink."\">View</a>";
                                                }
                                            }
                                        }
                                        if(!empty($Config['_Show_Edit'])) {
                                            $ActionWidth = $ActionWidth+16;

                                            $ViewLink['edit'] = '<a href="#" onclick="return false;"><span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\''.$EID.'\', '.$row['_return_'.$Config['_ReturnFields'][0]].');">Edit</span></a>';
                                        }
                                        if(!empty($Config['_Show_Delete_action'])) {
                                            $ActionWidth = $ActionWidth+16;

                                            $ViewLink['delete'] = '<a href="#" onclick="return false;"><span style="cursor:pointer;" class="delete" onclick="dr_deleteItem(\''.$EID.'\', '.$row['_return_'.$Config['_ReturnFields'][0]].');">Delete</span></a>';
                                        }
                                        //vardump($Config);

                                        //$PreReportReturn = '<td class="'.$Row.' action" width="'.$ActionWidth.'" scope="col" style="text-align:center;overflow:hidden;">';
                                        $ReportReturn .= '<div class="row-actions">'.implode(' | ', $ViewLink).'</div>';//'Edit | View';
                                        //$PreReportReturn .= '</td>';
                                        //$ReportReturn .= $PreReportReturn;

                                    }
                                }
                                $ReportReturn .= '</td>';
                            }
                        }
                    }
                    $ColumnCounter++;
                }

                // API Output
                if(!empty($Format)) {
                    // XML Output
                    if(strtolower($Format) == 'xml') {
                        $apiOut .= "		</entry>\n";
                    }
                    // json Output
                    if(strtolower($Format) == 'json') {
                        $jsonIndex++;
                    }
                    //PDF Output
                    if(strtolower($Format) == 'pdf') {
                        $pdfIndex++;
                    }

                }

                // Edit Functions if no popup
                if(!empty($ShowActionPanel)) {
                    $ViewLink = '';
                    $ActionWidth = 16;
                    if(!empty($Config['_Show_View'])) {
                        $ActionWidth = $ActionWidth+16;
                        $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry('".$row['_return_'.$Config['_ReturnFields'][0]]."', '".$EID."', ".$isModal."); return false;\"><img src=\"".WP_PLUGIN_URL."/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                        if(!empty($Config['_ItemViewPage'])) {
                            $ReportVars = array();
                            foreach($Config['_ReturnFields'] as $ReportReturnField) {
                                $ReportVars[$ReportReturnField] = urlencode($row['_return_'.$ReportReturnField]);
                            }
                            // Get permalink
                            $PageLink = get_permalink($Config['_ItemViewPage']);
                            $Location = parse_url($PageLink);
                            if(!empty($Location['query'])) {
                                $PageLink = str_replace('?'.$Location['query'], '', $PageLink);
                                parse_str($Location['query'], $gets);
                                $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                            }else {
                                $PageLink = $PageLink.'?'.htmlspecialchars_decode(http_build_query($ReportVars));
                            }
                            $ViewLink = "<a href=\"".$PageLink."\"><img src=\"".WP_PLUGIN_URL."/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                        }
                    }
                    if(!empty($Config['_Show_Edit'])) {
                        $ActionWidth = $ActionWidth+16;
                        if($ViewLink != '') {
                            $ViewLink .= " ";
                        }
                        $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\''.$EID.'\', '.$row['_return_'.$Config['_ReturnFields'][0]].');"><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                    }
                    if(!empty($Config['_Show_Delete_action'])) {
                        $ActionWidth = $ActionWidth+16;
                        if($ViewLink != '') {
                            $ViewLink .= " ";
                        }
                        $ViewLink .= '<span style="cursor:pointer;" onclick="dr_deleteItem(\''.$EID.'\', '.$row['_return_'.$Config['_ReturnFields'][0]].');"><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/delete.png" width="16" height="16" alt="Delete" title="Delete" border="0" align="absmiddle" /></span>';
                    }
                    //vardump($Config);

                    $PreReportReturn = '<td class="'.$Row.' action" width="'.$ActionWidth.'" scope="col" style="text-align:center;overflow:hidden;">';
                    $PreReportReturn .= $ViewLink;//'Edit | View';
                    $PreReportReturn .= '</td>';
                    $ReportReturn .= $PreReportReturn;
                }

                $ReportReturn .= '</tr>';

                // Increment row index
                $rowIndex++;
            }
            }
        }
    }
    //echo mysql_error();
    if(!empty($Config['_UseListViewTemplate'])) {
        if(!empty($Config['_ListViewTemplateContentWrapperEnd'])) {
            $ReportReturn .= $Config['_ListViewTemplateContentWrapperEnd'];
        }
    }else {
        // Close off Table end content
        $ReportReturn .= '</tbody>';
        $ReportReturn .= '</table>';
    }




// Make Scripts for deleting and select
    if(!empty($Config['_Show_Edit'])) {
        $_SESSION['dataform']['OutScripts'] .= "
		jQuery('#data_report_".$EID." .report_entry').bind('click', function(){
			jQuery(this).toggleClass(\"highlight\");
		});
	";
    }

// Footer
    //TODO: really need to clean up this templating. to much repetition
    if(!empty($Config['_Show_Footer'])) {

        $First = 1;
        $Prev = $Page-1;
        $Next = $Page+1;
        $Last = $TotalPages;
        if($Prev <= 0) {
            $Prev = 1;
        }
        if($Next > $TotalPages) {
            $Next = $TotalPages;
        }
        if(empty($Page)) {
            $Page = 1;
        }
        //Page Index display
        $toPos = $Page*$Offset;
        if($toPos > $Count['Total']) {
            $toPos = $Count['Total'];
        }
        if(empty($Config['_UseListViewTemplate'])) {
            //$ReportReturn .= '<div style="padding:3px;" class="list_row3">';
            $ReportReturn .= '<div style="padding:3px;" class="report_footer list_row3">';
            //Total pages display
            $ReportReturn .= '<div class="reportFooter_totals" style="float:left; width:47%;">';
            //$ReportReturn .= '<div class="reportFooter_totals">';
            if($TotalPages > 1) {
                //$ReportReturn .= '<div class="fbutton" onclick="dr_goToPage('.$EID.', '.$First.');"><div><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/images/resultset_first.png" width="16" height="16" alt="First" align="absmiddle" /></div></div>';
                $ReportReturn .= '<div class="fbutton" onclick="dr_goToPage(\''.$EID.'\', '.$Prev.');"><div><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/prev.gif" width="27" height="17" alt="Previous" align="absmiddle" /></div></div>';
                $ReportReturn .= '<div class="fpanel">Page <input type="text" name="pageJump" id="pageJump_'.$EID.'" style="width:30px; font-size:11px;" value="'.$Page.'" onkeypress="dr_pageInput(\''.$EID.'\', this.value);" /> of '.$TotalPages.'</div>';
                $ReportReturn .= '<div class="fbutton" onclick="dr_goToPage(\''.$EID.'\', '.$Next.');"><div><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/next.gif" width="27" height="17" alt="Next" align="absmiddle" /></div></div>';
                //$ReportReturn .= '<div class="fbutton" onclick="dr_goToPage('.$EID.', '.$Last.');"><div><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/images/resultset_last.png" width="16" height="16" alt="Last" align="absmiddle" /></div></div>';
            }


            $ReportReturn .= '</div>';


            $ReportReturn .= '<div class="reportFooter_pageIndex" style="text-align: right;">';
            // Check if there are any entries
            if($Count['Total'] == 0) {
                $nothingFound = 'Nothing Found';
                if(!empty($Config['_NoResultsText'])) {
                    $nothingFound = $Config['_NoResultsText'];
                }
                $ReportReturn	.= '<div style="padding:3px" class="noresults">'.$nothingFound.'</div>';
            }else {
                $ReportReturn .= ($Start+1).' - '.$toPos.' of '.$Count['Total'].' Items';
            }
            $ReportReturn .= '</div>';

            $ReportReturn .= '<div style="clear:both;"></div>';
            $ReportReturn .= '</div>';
        }else {
            if(!empty($Config['_ListViewTemplatePreFooter'])) {
                $ReportReturn .= $Config['_ListViewTemplatePreFooter'];
            }
            if(!empty($Config['_ListViewTemplateFooter'])) {

                $prevbutton = '<div class="fbutton" onclick="dr_goToPage(\''.$EID.'\', '.$Prev.');"><div><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/prev.gif" width="27" height="17" alt="Previous" align="absmiddle" /></div></div>';
                $pagejump = '<div class="fpanel">Page <input type="text" name="pageJump" id="pageJump_'.$EID.'" style="width:30px; font-size:11px;" value="'.$Page.'" onkeypress="dr_pageInput(\''.$EID.'\', this.value);" /> of '.$TotalPages.'</div>';
                $nextbutton = '<div class="fbutton" onclick="dr_goToPage(\''.$EID.'\', '.$Next.');"><div><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/next.gif" width="27" height="17" alt="Next" align="absmiddle" /></div></div>';

                if($Count['Total'] == 0) {
                    $nothingFound = 'Nothing Found';
                    if(!empty($Config['_NoResultsText'])) {
                        $nothingFound = $Config['_NoResultsText'];
                    }
                    $itemcount = '';
                    $noentries = $nothingFound;
                }else {
                    $noentries = '';
                    $itemcount = ($Start+1).' - '.$toPos.' of '.$Count['Total'].' Items';
                }

                $PreReturn = $Config['_ListViewTemplateFooter'];

                $PreReturn = str_replace('{{_footer_prev}}', $prevbutton, $PreReturn);
                $PreReturn = str_replace('{{_footer_next}}', $nextbutton, $PreReturn);
                $PreReturn = str_replace('{{_footer_page_jump}}', $pagejump, $PreReturn);
                $PreReturn = str_replace('{{_footer_item_count}}', $itemcount, $PreReturn);
                $PreReturn = str_replace('{{_footer_no_entries}}', $noentries, $PreReturn);

                $ReportReturn .= $PreReturn;


            }
            if(!empty($Config['_ListViewTemplatePostFooter'])) {
                $ReportReturn .= $Config['_ListViewTemplatePostFooter'];
            }

        }
    }
    //query
    if(is_admin()) {
        if(!empty($_GET['debug'])) {


            //$ReportReturn .= '<div id="'.$EID.'_queryDebug" class="button" style="cursor:pointer; width:100px; text-align: center;" onclick="jQuery(\'#'.$EID.'_queryDebug_panel\').toggle();">Show Query</div>';
            $ReportReturn .= '<div id="'.$EID.'_queryDebug_panel" style="display:block;">';
            $ReportReturn .= '<textarea style="width:99%; height:200px;">'.$CountQuery.'</textarea><br />';
            $ReportReturn .= '<textarea style="width:99%; height:200px;">'.$Query.'</textarea><br />';
            $ReportReturn .= 'ERRORS: '.mysql_error();
            //$ReportReturn .= '</div>';

        }
    }
//dump($Config);

// Create Header and Footer Totals
    if(!empty($Config['_TotalsFields'])) {

        if(!empty($Format)) {
            // XML Output
            if(strtolower($Format) == 'xml') {
                //$apiOut .= "		</entry>\n";
            }
            // json Output
            if(strtolower($Format) == 'json') {
                //	$jsonIndex++;
            }
            //PDF Output
            if(strtolower($Format) == 'pdf') {
                //	$pdfIndex++;
                $apiOutput['Totals'] = array();
                //$apiOutput = array();
                //$apiOutput['
            }

        }

        $header = '<div class="list_row1">';
        $footer = '<div class="list_row2">';
        $hcount = 0;
        $fcount = 0;
        foreach($Config['_TotalsFields'] as $Field => $TotalsSet) {

            //dump($TotalsSet);


            if($TotalsSet['Location'] == 'headerinline') {
                $TotalsSet['Location'] = 'header';
            }
            if($TotalsSet['Location'] == 'footerinline') {
                $TotalsSet['Location'] = 'footer';
            }
            $box = '<div class="stuffbox" style="width:{{width_'.$TotalsSet['Location'].'}}%; float:left;">';
            if(!empty($TotalsSet['Title'])) {
                $Title = df_parseCamelCase($TotalsSet['Title']);
            }
            $OutValue = round($TotalsSet[$TotalsSet['Type']], 2);
            if($TotalsSet['Function'] == 'VAT') {
                $OutValue = totals_vat($OutValue);
            }
            if($TotalsSet['Function'] == 'AddVAT') {
                $OutValue = totals_addvat($OutValue);
            }

            $Title = df_parseCamelCase($Field);
            $Caption = ' ';
            if(!empty($TotalsSet['Caption'])) {
                $Caption = $TotalsSet['Caption'];
            }
            /// Outputs
            if(!empty($Format)) {
                // XML Output
                if(strtolower($Format) == 'xml') {
                    //	$apiOut .= "		</entry>\n";
                }
                // json Output
                if(strtolower($Format) == 'json') {
                    //	$jsonIndex++;
                }
                //PDF Output
                if(strtolower($Format) == 'pdf') {
                    $apiOutput['Totals'][$Title] = $TotalsSet['Prefix'].$OutValue.$TotalsSet['Suffix'].' '.$Caption;
                }

            }
            $box .= '<h3>'.$Title.'</h3>';
            $box .= '<div class="inside"><h1>'.$TotalsSet['Prefix'].$OutValue.$TotalsSet['Suffix'].'</h1></div>';
            $box .= '<div class="inside">'.$Caption.'</div>';
            $box .= '</div>';
            if(!empty($$TotalsSet['Location'])) {
                $$TotalsSet['Location'] .= $box;
            }
            switch($TotalsSet['Location']) {
                case 'header':
                    $hcount++;
                    break;
                case 'footer':
                    $fcount++;
                    break;
            }
        }
        $header .= '<div style="clear:both;"></div></div>';
        $footer .= '<div style="clear:both;"></div></div>';
    }

    if($hcount >= 1) {
        $header = str_replace('{{width_header}}', (100/$hcount-3), $header);
    }
    if($fcount >= 1) {
        $footer = str_replace('{{width_footer}}', (100/$fcount-3), $footer);
    }
// Run Final Totals Functions on return data
    if(!empty($GLOBALS['Totals'][$EID])) {
        foreach($GLOBALS['Totals'][$EID] as $Key => $Output) {
            $Total = $Config['_TotalsFields'][$Output['Field']][$Config['_TotalsFields'][$Output['Field']]['Type']];
            if(!empty($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage'])) {
                if(empty($AverageBar)) {
                    //foreach$Output['PreAverage']
                    $avT = 0;
                    foreach($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage'] as $val) {
                        $avT = $avT+$val;
                    }
                    $AverageBar = ceil($avT/count($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage']));
                }
                $Total = $AverageBar;
            }
            $func = 'totals_'.$Output['Function'];
            $newValue = $func($Output['Value'], $Total);
            $ReportReturn = str_replace($Key, $newValue, $ReportReturn);
        }
    }

// Set Auto Widths to Averages
    foreach($AvrageWidth as $Field=>$Value) {
        $Tmp = 0;
        foreach($Value as $Num) {
            $Tmp = $Tmp+$Num;
        }
        $Av = ceil($Tmp/count($Value));
        if(!empty($minWidth[$Field])) {
            if($Av < $minWidth[$Field]) {
                $Av = $minWidth[$Field];
            }
        }
        $Av = '';
        $ReportReturn = str_replace('width="{{width_'.$Field.'}}px"', $Av, $ReportReturn);
    }


//dump($Config);
//echo $Query;
// API Output
    if(!empty($Format)) {
        // XML Output
        if(strtolower($Format) == 'xml') {
            return $apiOut."\n	</entries>";
        }
        // json Output
        if(strtolower($Format) == 'json') {
            //$apiOutput;
            return json_encode($apiOutput);
        }
        // PDF Output
        if(strtolower($Format) == 'pdf') {
            //$apiOutput;
            return $apiOutput;
        }
    }

    return $header.$ReportReturn.$footer;
}
function df_inlineedit($Entry, $ID, $Value) {

    $part = explode('_', $Entry, 3);
    $Element = getelement($part[1]);
    $Config = $Element['Content'];
    $preQuery = mysql_query("SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$ID."'");
    $Data[$part[1]] = mysql_fetch_assoc($preQuery);
    $Data[$Config['_ReturnFields'][0]] = $ID;
    $Data[$part[1]][$part[2]] = $Value;
    $return = df_processupdate($Data, $part[1]);
    if(empty($Config['_NotificationsOff'])) {
        return $return['Message'];
    }
    return 1;
}
function df_processupdate($Data, $EID) {
    //dump($Data);
    $Element = getelement($EID);
    $Config = $Element['Content'];
    //dump($Config);
    // Load Entry's data for hidden values
    $preQuery = mysql_query("SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."'");
    $PreData = mysql_fetch_assoc($preQuery);

    if($Config['_EnableAudit']) {
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
        if(mysql_query("CREATE TABLE `_audit_".$Config['_main_table']."` SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."' LIMIT 1")) {
            // new entry

            mysql_query("ALTER TABLE `_audit_".$Config['_main_table']."` ADD `_ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ,
						ADD `_DateInserted` DATETIME NOT NULL AFTER `_ID` ,
						ADD `_DateModified` DATETIME NOT NULL AFTER `_ID` ,
						ADD `_User` INT NOT NULL AFTER `_DateModified` ,
                                                ADD `_RawData` TEXT NOT NULL AFTER `_DateInserted`");
            mysql_query("UPDATE `_audit_".$Config['_main_table']."` SET `_DateModified` = '".date('Y-m-d H:i:s')."', `_DateInserted` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."';");
            mysql_query("INSERT INTO `_audit_".$Config['_main_table']."` SET `_DateInserted` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', `".$Config['_ReturnFields'][0]."`, ".$OldData." = '".$Data[$Config['_ReturnFields'][0]]."'  ;");
            mysql_query("INSERT INTO `_audit_".$Config['_main_table']."` SET `_DateInserted` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."'  ;");

        }else {
            $predata = mysql_query("SELECT * FROM ".$Config['_main_table']." WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."';");
            $prerow = mysql_fetch_assoc($predata);
            $OldData = array();
            foreach($prerow as $Field=>$Value) {
                $OldData[] = "`".$Field."` = '".mysql_real_escape_string($Value)."' ";
            }
            $OldData = implode(', ', $OldData);
            $UpdateQuery = "UPDATE `_audit_".$Config['_main_table']."` SET `_DateModified` = '".date('Y-m-d H:i:s')."', `_User` = '".$memberID."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', ".$OldData." WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."' ORDER BY `_ID` DESC LIMIT 1;";
            mysql_query($UpdateQuery);
            mysql_query("INSERT INTO `_audit_".$Config['_main_table']."` SET `_DateInserted` = '".date('Y-m-d H:i:s')."', `_RawData` = '".mysql_real_escape_string(serialize($Data))."', `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."'  ;");

        }
    }
    //go through the Submitted Data / apply fieldtype filters and add processed value to update queue
    foreach($Config['_Field'] as $Field=>$Type) {
        $typeSet = explode('_', $Type);
        if(!empty($typeSet[1])) {
            if(function_exists($typeSet[0].'_handleInput')) {
                $Func = $typeSet[0].'_handleInput';
                if(is_array($Data[$EID][$Field])) {
                    $Data[$EID][$Field] = serialize($Data[$EID][$Field]);
                }else {
                    if(empty($Data[$EID][$Field])) {
                        $Data[$EID][$Field] = '';
                    }
                }
                $Element['_ActiveProcess'] = 'update';
                $newValue = $Func($Field, $Data[$EID][$Field], $typeSet[1], $Element, $PreData, $Data);
                //}
            }else {
                $newValue = $Data[$EID][$Field];
            }
        }else {
            $newValue = $PreData[$Field];
        }
        if(substr($Field,0,2) != '__') {
            $updateData[] = "`".$Field."` = '".mysql_real_escape_string($newValue)."' ";
        }
    }

    $Updates = implode(', ', $updateData);
    $Query = "UPDATE `".$Config['_main_table']."` SET ".$Updates." WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."'";
    //echo $Query;
    //die;

    if(!empty($Config['_ReturnFields'][0])) {
        $ReturnVals = implode(', ', $Config['_ReturnFields']);
        $outq = mysql_query("SELECT ".$ReturnVals." FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."';");
        $dta = mysql_fetch_assoc($outq);
        $outstr = array();
        foreach($dta as $key=>$val) {
            $outstr[] = $key.'='.$val;
        }
        $Return['Value'] = implode('&', $outstr);

    }else {
        $Return['Value'] = $ID;
    }
    //dump($Return);
    //die;

    //$Return['Value'] = $Data[$Config['_ReturnFields'][0]];
    $PreDta = mysql_query($Query);

    // Post Process
    foreach($Config['_Field'] as $Field=>$Type) {
        $typeSet = explode('_', $Type);
        if(!empty($typeSet[1])) {
            if(function_exists($typeSet[0].'_postProcess')) {
                $Func = $typeSet[0].'_postProcess';
                $Element['_ActiveProcess'] = 'update';
                $Func($Field, $Data[$EID][$Field], $typeSet[1], $Element, $PreData, $Data[$Config['_ReturnFields'][0]]);
            }
        }
    }



    if(mysql_query($Query)) {
        if(empty($Config['_UpdateSuccess'])) {
            $Return['Message'] = 'Entry updated successfully';
        }else {
            $Return['Message'] = $Config['_UpdateSuccess'];
        }
        return $Return;
    }
    echo $Query;
    echo '<br /><br />';
    echo mysql_error();
    die;
    return false;
}

function df_deleteEntries($EID, $Data) {
    $Data = df_cleanArray(explode('|||', $Data));
    $El = getelement($EID);
    $Config = $El['Content'];
    if(!empty($RefConfig['Field'])) {
        if(in_array('imageupload', $RefConfig['Field'])) {
            $ImagesToDelete = array_keys($RefConfig['Field'], 'imageupload');
        }
    }
    $Index = 0;
    $Return = '';
    foreach($Data as $ID) {
        $ID = str_replace($EID.'_', '', $ID);
        $Pre = mysql_query("SELECT * FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$ID."' LIMIT 1;");
        dr_trackActivity('Delete', $EID, $ID);
        $OldData = mysql_fetch_assoc($Pre);
        if(!empty($ImagesToDelete)) {
            foreach($ImagesToDelete as $Field) {
                if(file_exists($OldData[$Field])) {
                    unlink($OldData[$Field]);
                }
            }
        }
        mysql_query("DELETE FROM `".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$ID."' LIMIT 1;") or die(mysql_error());
        $Index++;
    }
    $Note = 'Item';
    if(index > 1) {
        $Note = 'Items';
    }
    return $Index.' '.$Note.' Deleted<br />';
}

?>