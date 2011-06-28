<?php

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

        $result = mysql_query("SHOW COLUMNS FROM " . $Table);
        if (mysql_num_rows($result) > 0) {
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


        $PreReturn[$Field] .= '<div id="Field_' . $Field . '" class="admin_list_row3 table_sorter postbox '.$addClass.'" style="width:550px;">';

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
        $RClass = 'button-highlighted highlight';
        if (empty($Config['Content']['_Required'][$Field])) {
            $RSel = '';
            $RClass = 'button';
        }
        $SSel = 'checked="checked"';
        $SClass = 'button-highlighted highlight';
        if (empty($Config['Content']['_Sortable'][$Field])) {
            $SSel = '';
            $SClass = 'button';
        }
        $USel = 'checked="checked"';
        $UClass = 'button-highlighted highlight';
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
        //$PreReturn[$Field] .= '<label><strong>Lable</strong></label>';

        $PreReturn[$Field] .= '<div style="padding:3px;">Title: <input type="text" value="' . $Title . '" name="Data[Content][_FieldTitle][' . $Field . ']" /> ';
        $PreReturn[$Field] .= 'Caption: <input type="text" value="' . $Caption . '" name="Data[Content][_FieldCaption][' . $Field . ']" /></div>';
        //$PreReturn[$Field] .= '<label><strong>Alignment</strong></label>';
        $PreReturn[$Field] .= '<div style="padding:3px;">Width: <input type="text" style="width:40px;" value="' . $Width . '" name="Data[Content][_WidthOverride][' . $Field . ']" /> ';
        $PreReturn[$Field] .= df_alignmentSetup($Field, $Justify) . '</div>';

        $XAxis = '';
        if ($Config['Content']['_xaxis'] == $Field) {
            $XAxis = 'checked="checked"';
        }

        $chartValue = '';
        if (!empty($Config['Content']['_chartValue'][$Field])) {
            $chartValue = 'checked="checked"';
        }
        // Graphing x axis (single select)
        $PreReturn[$Field] .= '<label><strong>Charting</strong></label>';
        $PreReturn[$Field] .= '<div style="padding:3px;">X Axis: <input type="radio" name="Data[Content][_xaxis]" id="sortable_' . $Field . '" value="' . $Field . '" ' . $XAxis . ' /><br />';
        // Graphing legend/charted value
        $PreReturn[$Field] .= ' Chart Item: <input type="checkbox" name="Data[Content][_chartValue][' . $Field . ']" id="sortable_' . $Field . '" value="1" ' . $chartValue . ' />';


        /// charts
        $Sel = '';
        if (!empty($Config['Content']['_chartType'][$Field])) {
            switch ($Config['Content']['_chartType'][$Field]) {
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
        $PreReturn[$Field] .= '<select name="Data[Content][_chartType][' . $Field . ']" >';
        $PreReturn[$Field] .= '<option value="line" ';
        if ($Sel == 'line') {
            $PreReturn[$Field] .= 'selected="selected"';
        };
        $PreReturn[$Field] .= '>Line</option>';
        $PreReturn[$Field] .= '<option value="bar" ';
        if ($Sel == 'bar') {
            $PreReturn[$Field] .= 'selected="selected"';
        };
        $PreReturn[$Field] .= '>Bar</option>';
        $PreReturn[$Field] .= '<option value="column" ';
        if ($Sel == 'column') {
            $PreReturn[$Field] .= 'selected="selected"';
        };
        $PreReturn[$Field] .= '>Column</option>';
        $PreReturn[$Field] .= '<option value="spline" ';
        if ($Sel == 'spline') {
            $PreReturn[$Field] .= 'selected="selected"';
        };
        $PreReturn[$Field] .= '>Spline</option>';
        $PreReturn[$Field] .= '<option value="area" ';
        if ($Sel == 'area') {
            $PreReturn[$Field] .= 'selected="selected"';
        };
        $PreReturn[$Field] .= '>Area</option>';


        $PreReturn[$Field] .= '</select>';
        $PreReturn[$Field] .= '</div>';




        $PreReturn[$Field] .= '</div><div class="admin_config_toolbar"> <div style="float:left; width:180px;">' . df_fieldTypes($Field, $Table, $row, $Defaults['_Field']) .'</div>'. dr_reportListTypes($Field, $Defaults['_IndexType'][$Field]);
        // inline settings
        //class="button-highlighted"
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $UClass . '" id="unique_' . $Field . '" onclick="df_setToggle(\'unique_' . $Field . '\');" title="Unique"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/unique.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $RClass . '" id="required_' . $Field . '" onclick="df_setToggle(\'required_' . $Field . '\');" title="Required"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/required.png) left center no-repeat; padding:5px 8px;"></span></span>';
        $PreReturn[$Field] .= ' &nbsp;<span class="' . $SClass . '" id="issortable_' . $Field . '" onclick="df_setToggle(\'issortable_' . $Field . '\');" title="Sortable"><span style="background: url(' . WP_PLUGIN_URL . '/db-toolkit/data_report/table_sort.png) left center no-repeat; padding:5px 8px;"></span></span>';

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

            $result = mysql_query("SHOW COLUMNS FROM " . $Table);
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
                    $RClass = 'button-highlighted highlight';
                }
                if (empty($Config['Content']['_Sortable'][$Field])) {
                    $SSel = 'checked="checked"';
                    $SClass = 'button-highlighted highlight';
                }

                if (!empty($Config['Content']['_Unique'][$Field])) {
                    $USel = 'checked="checked"';
                    $UClass = 'button-highlighted highlight';
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
            $PreReturn[$Field] .= 'Caption: <input type="text" value="' . $Caption . '" name="Data[Content][_FieldCaption][' . $Field . ']" /></div>';
            $PreReturn[$Field] .= '<div style="padding:3px;">Width: <input type="text" style="width:40px;" value="' . $Width . '" name="Data[Content][_WidthOverride][' . $Field . ']" /> ';
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

        //vardump($Config);

        if ($EID == 'false') {

            $Defaults = $Config['Content'];
            //dump($Defaults);
            if (!empty($Defaults['_FormLayout'])) {
                parse_str($Defaults['_FormLayout'], $Columns);
            }
            $Return = '';
            $result = mysql_query("SHOW COLUMNS FROM " . $Table);
            if (mysql_num_rows($result) > 0) {
                $Row = 'list_row4';
                while ($row = mysql_fetch_assoc($result)) {
                    //$Row = dais_rowSwitch($Row);
                    $FieldList[] = $row['Field'];
                    $Field = $row['Field'];
                    $PreReturn[$Field] .= df_makeFieldConfigBox($Field, $Config, $Defaults);
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


        $Part = explode('_', $Default);
        if($Part[1] == 'show'){
            $VSel = 'checked="checked"';
            $VClass = 'button-highlighted highlight';
        }
        if($Part[0] == 'index'){
            $ISel = 'checked="checked"';
            $IClass = 'button-highlighted highlight';
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
    $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
    $apps = get_option('dt_int_Apps');
    $appGroups = array();
    $Return = '';
    foreach ($interfaces as $interface) {
        $cfg = get_option($interface['option_name']);
        $appGroups[$cfg['_Application']][] = $cfg;
    }
    //vardump($appGroups);
    foreach ($apps as $app => $state) {
        //vardump($app);
        $Icon = WP_PLUGIN_URL . '/db-toolkit/data_report/application-home.png';
        $Return .= '<li><a class="child"><img src="' . $Icon . '" align="absmiddle" /> ' . $app . '</a>';

        if (!empty($appGroups[$app])) {
            $Return .= "<ul id=\"\" style=\"visibility: hidden; display: block;\">";
            $Return .= "<li class=\"title\"><h2>" . $app . "</h2></li>";
            foreach ($appGroups[$app] as $interface) {
                //vardump($interface);
                $IIcon = WP_PLUGIN_URL . '/db-toolkit/data_report/plus-button.png';
                $Return .= '<li><a onclick="formSetup_InsertInterface(\'' . $interface['ID'] . '\');"><img src="' . $IIcon . '" align="absmiddle" /> ' . $interface['_ReportDescription'] . '</a>';
            }
            $Return .= '</ul>';
        }

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
            include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/functions.php');



            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onInsert'])) {
                $class = 'button-highlighted highlight"';
            }
            $Icons = '<span title="Run Process on Insert" onclick="df_setToggle(\'onInsert_' . $processID . '\');" id="onInsert_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-insert.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';

            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onUpdate'])) {
                $class = 'button-highlighted highlight"';
            }
            $Icons .= '&nbsp;<span title="Run Process on Update" onclick="df_setToggle(\'onUpdate_' . $processID . '\');" id="onUpdate_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-pencil.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';

            $class = 'button';
            if (!empty($Config['_FormProcessors'][$processID]['_onDelete'])) {
                $class = 'button-highlighted highlight"';
            }
            $Icons .= '&nbsp;<span title="Run Process on Delete" onclick="df_setToggle(\'onDelete_' . $processID . '\');" id="onDelete_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-delete.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
            if (function_exists($func)) {
                $class = 'button';
                if (!empty($Config['_FormProcessors'][$processID]['_configPanelOpen'])) {
                    $class = 'button-highlighted highlight"';
                }
                $Icons .= '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="' . $class . '"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
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

function df_addProcess($processor, $table) {

    $Return = '';

    if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php')) {
        include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/conf.php');
        include(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $processor . '/functions.php');
        $processID = uniqid('process_');

        $func = 'config_' . $processor;


        $Icons = '<span title="Run Process on Insert" onclick="df_setToggle(\'onInsert_' . $processID . '\');" id="onInsert_' . $processID . '" class="button-highlighted highlight"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-insert.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        $Icons .= '&nbsp;<span title="Run Process on Update" onclick="df_setToggle(\'onUpdate_' . $processID . '\');" id="onUpdate_' . $processID . '" class="button"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-pencil.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        $Icons .= '&nbsp;<span title="Run Process on Delete" onclick="df_setToggle(\'onDelete_' . $processID . '\');" id="onDelete_' . $processID . '" class="button"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/database-delete.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
        if (function_exists($func)) {
            $Icons .= '&nbsp;<span title="Show Configuration Panel" onclick="toggle(\'config_' . $processID . '\'); df_setToggle(\'configirator_' . $processID . '\');" id="configirator_' . $processID . '" class="button-highlighted highlight"><span style="background: url(\'' . WP_PLUGIN_URL . '/db-toolkit/data_report/gear.png\') no-repeat scroll left center transparent; padding: 5px 8px;"></span></span>';
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
            $Return .= '<div style="float:left; padding:2px;">';
            if (!empty($Config['_Keyword_Title'])) {
                $Return .= '<strong>' . $Config['_Keyword_Title'] . '</strong><br />';
            }
            $Return .= '<input type="text" name="reportFilter[' . $EID . '][_keywords]" id="keyWordFilter" class="filterSearch" value="' . $Keywords . '" />&nbsp;&nbsp;&nbsp;</div>';
        } else {
            if (!empty($Config['_Hide_FilterLock'])) {
                $Return .= '<span class="highlight"><div style="float:left; padding:2px;">';
                if (!empty($Config['_Keyword_Title'])) {
                    $Return .= '<strong>' . $Config['_Keyword_Title'] . '</strong><br />';
                }
                $Return .= '<input type="text" name="reportFilter[' . $EID . '][_keywords]" id="keyWordFilter" class="filterSearch" value="' . $Keywords . '" />&nbsp;&nbsp;&nbsp;</div></span>';
            }
        }
    }

    foreach ($Config['_Field'] as $Field => $FieldType) {
        if (empty($_SESSION['lockedFilters'][$EID][$Field])) {
            $type = explode('_', $FieldType);
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
                $type = explode('_', $FieldType);
                if (!empty($type[1])) {
                    $index = explode('_', $Config['_IndexType'][$Field]);
                    if ($index[0] == 'index') {
                        if (function_exists($type[0] . '_showFilter')) {
                            $func = $type[0] . '_showFilter';
                            $Return .= '<span class="highlight">' . $func($Field, $type[1], $Defaults, $Config, $EID) . '</span>';
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

function dr_BuildUpDateForm($EID, $ID) {
    $Data = getelement($EID);
    $Data['_ActiveProcess'] = 'update';
    $Out['title'] = 'Edit Entry';
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

//clone mater finder
function dr_cloneFindMater($Field, $Clones) {

    if (substr($Field, 0, 2) == '__') {
        $ReturnField = $Clones[$Field]['Master'];
        if (substr($ReturnField, 0, 2) == '__') {
            return dr_cloneFindMater($ReturnField, $Clones);
        }
        return $ReturnField;
    }
    return $Field;
}

function olddr_findCloneParent($Clone, $Clones, $querySelects){
    // Clear out _Return_
    $preParent = $Clones[$Clone]['Master'];
    //echo $Clone.' - '.$preParent.'<br>';
    if(!empty($querySelects[$Clone])){
        $preParent = $querySelects[$Clone];
    }elseif(!empty($querySelects[$preParent])){
        $preParent = $querySelects[$preParent];
    }
    $pattern = '__[a-zA-Z0-9]+';
    preg_match('/'.$pattern.'/s', $preParent, $matches);
    if(!empty($matches)){
        $preParent = dr_findCloneParent($preParent, $Clones, $querySelects);
    }
    return $preParent;
}


function dr_findCloneParent($Clone, $Clones, $querySelects){
    //echo $Clone.' - ';
    //vardump($Clones);

    if(!empty($Clones[$Clone]['Master'])){
        //echo $Clones[$Clone]['Master'].' - ';
        $Clone = $querySelects[$Clones[$Clone]['Master']];
        //echo '|'.$Clone.'|';
        if(!empty($Clones[$Clone])){
            $Clone = dr_findCloneParent($Clone, $Clones, $querySelects);
        }
    }
    //echo $Clone.'<br />';
return $Clone;
}

function dr_processQuery($Config, $querySelects) {


    //vardump($querySelects);
    //vardump($Config['_CloneField']);

    //may need a for loop rather than a foreach.
    //return $querySelects;
    foreach ($querySelects as $Field => $preSelect) {
        //$CloneOf = $querySelects[$Field];
        $Select = $preSelect;
        $pattern = '__[a-zA-Z0-9]+';
        preg_match('/' . $pattern . '/s', $Select, $matches);
        if(!empty($matches[0])){
            //vardump($matches);
            //echo $Select.' -> ';
            $Select = $matches[0];
            //echo $Select.' <br /> ';
        }
        if(!empty($Config['_CloneField'][$Select])){
            $CloneOf = $querySelects[$Select];
            if(!empty($Config['_CloneField'][$CloneOf])){
            //echo '++++ '.$Select.' ++++<br />';
            $Select = dr_findCloneParent($Config['_CloneField'][$Select]['Master'], $Config['_CloneField'], $querySelects);            
            }else{               
                $Select = $CloneOf;
            }
        }


        if(!empty($matches[0])){
            //echo $Select.' - ';
            //echo $matches[0].'<br />';
            $Select = str_replace($matches[0], $Select, $preSelect);
            //echo $Select.'<br /><br />';
        }
        if(strpos($Select , '.') <= 0){
            $pattern = '\(([a-zA-Z0-9]+)\)';
            preg_match('/' . $pattern . '/s', $Select, $matches);
            if(!empty($matches[1])){                
                $Select = str_replace($matches[1], 'prim.`'.$matches[1].'`', $Select);
            }else{                
                $Select = 'prim.`'.$Select.'`';
            }
        }
        $Processed[$Field] = $Select;
        //echo $Field.' - '.$Select.'<br />';


    }





    return $Processed;


    $pattern = '__[a-zA-Z0-9]+';
    $Selects = array();
    foreach($querySelects as $Field=>$Select){
        $returnPrefix = '';
        if(strpos($Field, '_return_') !== false){
            $subField = str_replace('_return_', '', $Field);
            if(!empty($querySelects[$subField])){
                $Select = $querySelects[$subField];
            }
        }
        preg_match('/'.$pattern.'/s', $Select, $matches);
        $preSelect = $Select;
        $copySelectes = $querySelects;
        if(!empty($matches[0])){
            unset($copySelectes[$Field]);
            $Select = dr_findCloneParent($matches[0], $Config['_CloneField'], $copySelectes);
        }
        preg_match('/[a-zA-Z0-9]+\(`(.*)`\)/s', $preSelect, $brackMatch);
        if(!empty($brackMatch[0]) && !empty($matches[0])){
            if(strpos($Select, '.') === false){
               $Select = 'prim.`'.$Select.'`';
            }
          $Select = str_replace('`'.$brackMatch[1].'`', $Select, $brackMatch[0]);
        }
        preg_match('/[a-zA-Z0-9]+\(`(.*)`\)/s', $Select, $brackMatch);
        if(!empty($brackMatch[0])){
            if(strpos($brackMatch[1], '.') === false){
               $pre = 'prim.`'.$brackMatch[1].'`';
            }
            $Select = str_replace('`'.$brackMatch[1].'`', $pre, $brackMatch[0]);
            //dump($brackMatch);
        }
        if(strpos($Select, '.') === false){
           $Select = 'prim.`'.$Select.'`';
        }
        // Find anything with a bracket

        $Selects[$Field] = $Select;
    }
return $Selects;
}

function dr_exportChartImage($chartData) {

    define('BATIK_PATH', WP_PLUGIN_DIR . '/db-toolkit/data_report/chartexport/batik-rasterizer.jar');

///////////////////////////////////////////////////////////////////////////////

    $charts = array_chunk($chartData, 4);

    foreach ($charts as $chartData) {
        $type = 'image/jpeg'; //$chartData[1]['value'];//$_POST['type'];
        $svg = (string) $chartData[3]['value']; //$_POST['svg'];
        $filename = (string) $chartData[0]['value']; //$_POST['filename'];
        $setWidth = (int) 1800; //$chartData[2]['value'];//$_POST['width'];
// prepare variables
        if (!$filename)
            $filename = 'chart';
        if (get_magic_quotes_gpc ()) {
            $svg = stripslashes($svg);
        }



        $tempName = md5(rand());

// allow no other than predefined types
        if ($type == 'image/png') {
            $typeString = '-m image/png';
            $ext = 'png';
        } elseif ($type == 'image/jpeg') {
            $typeString = '-m image/jpeg';
            $ext = 'jpg';
        } elseif ($type == 'application/pdf') {
            $typeString = '-m application/pdf';
            $ext = 'pdf';
        } elseif ($type == 'image/svg+xml') {
            $ext = 'svg';
        }
        if (!file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_report/chartexport/charts')) {
            mkdir(WP_PLUGIN_DIR . '/db-toolkit/data_report/chartexport/charts');
        }
        $outfile = WP_PLUGIN_DIR . '/db-toolkit/data_report/chartexport/charts/' . $tempName . '.' . $ext;

        if (isset($typeString)) {

            // size
            if ($setWidth) {
                $width = (int) $setWidth;
                if ($width)
                    $width = "-w $width";
            }

            // generate the temporary file
            if (!file_put_contents($tempName . '.svg', $svg)) {
                die("Couldn't create temporary file. Check that the directory permissions for
			the /temp directory are set to 777.");
            }

            // do the conversion

            $output = shell_exec("java -jar " . BATIK_PATH . " " . $typeString . " -d " . $outfile . " " . $width . " " . $tempName . ".svg");

            // catch error
            if (!is_file($outfile) || filesize($outfile) < 10) {
                echo "<pre>$output</pre>";
                echo "Error while converting SVG";
                die;
            }

            // stream it
            else {
                //header("Content-Disposition: attachment; filename=$filename.$ext");
                //header("Content-Type: $type");
                //echo file_get_contents($outfile);


                $file = fopen(WP_PLUGIN_DIR . '/db-toolkit/data_report/chartexport/charts/' . $filename . '.' . $ext, 'w+');
                fwrite($file, file_get_contents($outfile));
                fclose($file);
            }

            // delete it
            unlink($tempName . ".svg");
            unlink($outfile);

            $files[] = WP_PLUGIN_URL . '/db-toolkit/data_report/chartexport/charts/' . $filename . '.' . $ext;

            // SVG can be streamed directly back
        }
    }
    return $files;
}

function dr_BuildReportGrid($EID, $Page = false, $SortField = false, $SortDir = false, $Format = false, $limitOveride = false, $wherePush = false) {

//Filters will be picked up via Session value
// Set Vars
    if (!empty($Format)) {
        // XML Output
        if (strtolower($Format) == 'xml') {
            $apiOut = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            $apiOut .= "	<entries type=\"array\">\n";
        }
        //json output
        if (strtolower($Format) == 'json') {
            $jsonIndex = 0;
            $apiOutput = array();
            $apiOutput['entries'] = array();
        }

        if (strtolower($Format) == 'pdf') {
            $pdfIndex = 0;
            $apiOutput = array();
            if (!empty($_SESSION['reportFilters'][$EID])) {
                $apiOutput['filters'] = $_SESSION['reportFilters'][$EID];
            }
        }
    }
    $ReportReturn = '';
    $Element = getelement($EID);
    $Config = $Element['Content'];
    $queryJoin = '';
    $queryJoins = array();
    $queryWhere = array();
    $queryLimit = '';
    $querySelects = array();
    $WhereTag = '';
    $groupBy = '';
    $orderStr = '';
    $countSelect = '';
    $countLimit = 'LIMIT 1';
    $isModal = 'false';


    if (!empty($Config['_popupTypeView'])) {
        if ($Config['_popupTypeView'] == 'modal') {
            $isModal = true;
        }
    }
    // Setup Totals Fields
    if (!empty($Config['_TotalsField'])) {
        foreach ($Config['_TotalsField'] as $key => $Field) {

            if (!empty($Config['_TotalsFieldTitle'][$key])) {
                $Title = str_replace(' ', '', ucwords($Config['_TotalsFieldTitle'][$key]));
            } else {
                $Title = $Field . 'Total';
            }
            //if($Config['_TotalsFieldLocation'][$key] == 'inline'){
            $Config['_Field'][$Title] = 'totals_' . $Config['_TotalsFieldType'][$key];
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
            if (!empty($Config['_TotalsFieldTitleWidth'][$key])) {
                $Config['_WidthOverride'][$Title] = $Config['_TotalsFieldTitleWidth'][$key];
            }
            if (!empty($Config['_TotalsFieldCaption'][$key])) {
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
    if (!empty($Page)) {
        $_SESSION['report_' . $EID]['LastPage'] = $Page;
    } else {
        if (empty($_SESSION['report_' . $EID]['LastPage']) || $_SESSION['report_' . $EID]['LastPage'] == 'undefined') {
            $_SESSION['report_' . $EID]['LastPage'] = 1;
        }
        $Page = $_SESSION['report_' . $EID]['LastPage'];
    }
    if (!empty($SortDir)) {
        $_SESSION['report_' . $EID]['SortDir'] = $SortDir;
    }
    if (!empty($SortField)) {
        $_SESSION['report_' . $EID]['SortField'] = $SortField;
    }

//setup Field Types
    
    foreach ($Config['_Field'] as $Field => $Type) {
        if(empty($Type)){
            $querySelects[$Field] = $Field;
        }
        // explodes to:
        // [0] = Field plugin dir
        // [1] = Field plugin type
        $Config['_Field'][$Field] = explode('_', $Type);

    }

//SetupHeaders
    // Start Table
    // Check for template
    if (empty($Config['_UseListViewTemplate'])) {
        $tableClass = 'class="data_report_Table"';
        if (is_admin ()) {
            $tableClass = 'class="widefat"';
        }
        $ReportReturn .= '<table width="100%" border="0" cellspacing="0" cellpadding="4" ' . $tableClass . ' id="data_report_' . $EID . '" style="cursor:default;">';
        //Start Headers Row
        //$ReportReturn .= '<caption>'.$Config['_ReportTitle'].'</caption>';
        $ReportReturn .= '<thead>';
        $ReportReturn .= '<tr>';
    } else {
        if (!empty($Config['_ListViewTemplatePreHeader'])) {
            $ReportReturn .= $Config['_ListViewTemplatePreHeader'];
        }
    }

    foreach ($Config['_IndexType'] as $Field => $Type) {
        //echo 'ping';
        //Seperate Index/Display Types
        $Config['_IndexType'][$Field] = explode('_', $Type);
        // Totals Location check to see if field is inline or not.
        $Location = 'inline';
        if (!empty($Config['_TotalsFields'][$Field]['Location'])) {
            $Location = $Config['_TotalsFields'][$Field]['Location'];
        }
        if ($Config['_IndexType'][$Field][1] == 'show' && ($Location == 'inline' || $Location == 'headerinline' || $Location == 'footerinline') && empty($wherePush)) {
            //Set Widths
            $Direction = 'ASC';
            if ($_SESSION['report_' . $EID]['SortDir'] == 'ASC') {
                $Direction = 'DESC';
            }
            $sortClass = 'report_header';
            if ($_SESSION['report_' . $EID]['SortField'] == $Field) {
                $sortClass = 'sorting_' . $_SESSION['report_' . $EID]['SortDir'];
            }
            // set the column Title
            $fieldTitle = $Field;
            if (!empty($Config['_TotalsFields'][$Field]['Title'])) {
                $fieldTitle = $Config['_TotalsFields'][$Field]['Title'];
            }
            if (empty($Config['_WidthOverride'][$Field])) {
                $Config['_WidthOverride'][$Field] = '';
            }
            // Header Template Insert
            if (!empty($Config['_UseListViewTemplate'])) {
                if (!empty($Config['_ListViewTemplateHeader'])) {
                    if (!empty($Config['_FieldTitle'][$Field])) {
                        $preHeader = '<span>' . $Config['_FieldTitle'][$Field] . '</span>';
                    } else {
                        $preHeader = '<span>' . df_parseCamelCase($fieldTitle) . '</span>';
                    }
                    $PreHeader = str_replace('{{_Field}}', $preHeader, $Config['_ListViewTemplateHeader']);
                    $ReportReturn .= $PreHeader;
                }
            } else {

                $ReportReturn .= '<th nowrap="nowrap" scope="col" width="' . ($Config['_WidthOverride'][$Field] == '' ? '{{width_' . $Field . '}}px' : $Config['_WidthOverride'][$Field] . 'px') . '" ';
                if (!empty($Config['_Sortable'][$Field])) {
                    $ReportReturn .= 'onclick="dr_sortReport(\'' . $EID . '\', \'' . $Field . '\', \'' . $Direction . '\');" class="' . $sortClass . '"';
                }
                $ReportReturn .= '>';
                if (!empty($Config['_FieldTitle'][$Field])) {
                    $ReportReturn .= '<span>' . $Config['_FieldTitle'][$Field] . '</span>';
                } else {
                    $ReportReturn .= '<span>' . df_parseCamelCase($fieldTitle) . '</span>';
                }

                $ReportReturn .= '</th>';
            }
            // Preset the selects from query
            $querySelects[$Field] = $Field;// 'prim.`' . $Field . '`';
            // Set average width and min width
            $minWidth[$Field] = strlen($fieldTitle) * 8;
            $AvrageWidth[$Field] = array();
            $AvrageWidth[$Field][] = $minWidth[$Field];
        }
        if (!empty($wherePush)) {
            $querySelects[$Field] = $Field; //'prim.`' . $Field . '`';
        }
    }

    // Add the return field to select
    if (!empty($Config['_ReturnFields'])) {
        //$querySelects[$Config['_ReturnFields'][0]] = 'prim.'.$Config['_ReturnFields'][0];
        foreach ($Config['_ReturnFields'] as $Field) {
            $newField = '_return_' . $Field;
            $querySelects[$newField] = $Field;//'prim.`' . $Field . '`';
        }
    }
    if (empty($Config['_Show_popup'])) {
        if (!empty($Config['_Show_Edit']) && empty($Config['_ItemViewPage']) || !empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
            $ShowActionPanel = true;
        }

        if (!empty($ShowActionPanel)) {
            if (!empty($Config['_UseListViewTemplate'])) {
                if (!empty($Config['_ListViewTemplateHeader'])) {
                    $PreHeader = str_replace('{{_Field}}', 'Action', $Config['_ListViewTemplateHeader']);
                    $ReportReturn .= $PreHeader;
                }
            } else {
                $ReportReturn .= '<th scope="col">';
                $ReportReturn .= 'Action';
                $ReportReturn .= '</th>';
            }
        }
    }
    if (!empty($Config['_UseListViewTemplate'])) {
        if (!empty($Config['_ListViewTemplatePostHeader'])) {
            $ReportReturn .= $Config['_ListViewTemplatePostHeader'];
        }
    } else {
        $ReportReturn .= '</tr>';
        $ReportReturn .= '</thead>';
    }
    // End Headers setup
    // Build Query
    // field type filters
    $joinIndex = 'a';



    foreach ($Config['_Field'] as $Field => $Type) {

        //set filter for auto values
        if ($Type[0] == 'hidden') {            
            if (!empty($_SESSION['reportFilters'][$EID][$Field])) {
                if ($WhereTag == '') {
                    $WhereTag = " WHERE ";
                }
                $queryWhere[] = "prim.`" . $Field . "` = '" . $_SESSION['reportFilters'][$EID][$Field] . "'";
            }
        }
        // Run Filters that have been set through each field type
        if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Type[0] . '/queryfilter.php')) {
            include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $Type[0] . '/queryfilter.php');
        }

        if (!empty($_SESSION['reportFilters'][$EID]['_keywords'])) {
            if ($WhereTag == '') {
                $WhereTag = " WHERE ";
            }
            $keyField = 'prim.`' . $Field . '`';
            $preWhere[] = $keyField . " LIKE '%" . $_SESSION['reportFilters'][$EID]['_keywords'] . "%' ";
        }

        //}
        $joinIndex++;
    }

    if (!empty($_SESSION['reportFilters'][$EID]['_keywords'])) {
        if ($WhereTag == '') {
            $WhereTag = " WHERE ";
        }
        foreach ($querySelects as $Key => $Field) {
            $preWhere[] = $Field . " LIKE '%" . $_SESSION['reportFilters'][$EID]['_keywords'] . "%' ";
            // explode and serach parts
            $parts = explode(' ', $_SESSION['reportFilters'][$EID]['_keywords']);
            foreach ($parts as $Part) {
                $preWhere[] = $Field . " LIKE '%" . trim($Part) . "%' ";
            }
            // explode and serach parts
            $parts = explode(', ', $_SESSION['reportFilters'][$EID]['_keywords']);
            foreach ($parts as $Part) {
                $preWhere[] = $Field . " LIKE '%" . trim($Part) . "%' ";
            }
        }
    }

    // combine keyword search if there are any
    if (!empty($preWhere)) {
        $queryWhere[] = '(' . implode(' OR ', $preWhere) . ')';
    }
    if (!empty($wherePush)) {
        if ($WhereTag == '') {
            $WhereTag = " WHERE ";
        }
        foreach ($wherePush as $wField => $wVal) {
            $queryWhere[] = 'prim.`' . $wField . '` = \'' . $wVal . '\'';
        }
    }
    // create Query Selects and Where clause string
    
    $querySelects = dr_processQuery($Config, $querySelects);
    
    //vardump($Config['_CloneField']);    //vardump($querySelects);
    $preSelects = array();
    foreach ($querySelects as $AS => $selectField) {
        $preSelects[] = $selectField . ' AS `'.$AS.'`';
    }
    $querySelect = implode(", \n", $preSelects);
    $queryWhere = implode(' AND ', $queryWhere);
    // create sort fields
    if (!empty($Config['_SortField'])) {
        if (!empty($querySelects[$_SESSION['report_' . $EID]['SortField']])) {
            $orderStr = 'ORDER BY ' . $_SESSION['report_' . $EID]['SortField'] . ' ' . $_SESSION['report_' . $EID]['SortDir'];
        } else {
            $orderStr = 'ORDER BY prim.`' . $Config['_SortField'] . '` ' . $Config['_SortDirection'] . '';
        }
    }

    // create joins and on
    $pattern = 'prim.`(__[a-zA-Z0-9]+)`';
    preg_match_all('/' . $pattern . '/s', $queryJoin, $matches);
    foreach ($matches[0] as $key => $Match) {
        $queryJoin = str_replace($Match, $querySelects[$matches[1][$key]], $queryJoin);
    }
    // Build the grouping if ther are any
    if (is_array($groupBy)) {
        $preGroup = array();
        foreach ($groupBy as $groupField => $preField) {
            if (!empty($querySelects[$groupField])) {
                $preGroup[] = $querySelects[$groupField];
            } else {
                if (strpos($preField, '.') === false) {
                    $preField = 'prim.' . $preField . '';
                }
                $preGroup[] = $preField;
            }
        }
        $groupBy = 'GROUP BY (' . implode('),(', $preGroup) . ')';
        $countLimit = '';
        $entryCount = true;
        //add totals selects to count
        if (is_array($countSelect)) {
            $countSelect = ',' . implode(',', $countSelect);
        }
    }
    
    // Build WHERES - prim.clones
    $pattern = 'prim.`(__[a-zA-Z0-9]+)`';
    preg_match_all('/' . $pattern . '/s', $queryWhere, $matches);
    foreach ($matches[0] as $key => $Match) {
        if (!empty($querySelects['_sourceid_' . $matches[1][$key]])) {
            $queryWhere = str_replace($Match, $querySelects['_sourceid_' . $matches[1][$key]], $queryWhere);
        } else {
            $replace = dr_findCloneParent($matches[1][$key], $Config['_CloneField'], $querySelects);

            if (strpos($replace, '.') === false) {
                $replace = 'prim.`' . $replace . '`';
            }
            $queryWhere = str_replace($Match, $replace, $queryWhere);
        }
    }

    //return;
    // Totals Query & Results
    //dump($querySelects);
    if (!empty($Config['_CloneField'][$Config['_ReturnFields'][0]])) {
        $countSelect = dr_findCloneParent($Config['_ReturnFields'][0], $Config['_CloneField'], $querySelects);
    } else {
        $countSelect = $Config['_ReturnFields'][0];
    }
    if (strpos($countSelect, '.') === false) {
        $countSelect = 'prim.`' . $countSelect . '`';
    }
    // get done queries
    global $wpdb;
    //dump($Queries);
    $CountQuery = "SELECT count(" . $countSelect . ") as Total FROM `" . $Config['_main_table'] . "` AS prim \n " . $queryJoin . " \n " . $WhereTag . " \n " . $queryWhere . " \n " . $groupBy . "\n\n " . $countLimit . ";";

    $CountResult = mysql_query($CountQuery);
    if (!empty($entryCount)) {
        // Countr Rows
        while ($prCount = mysql_fetch_assoc($CountResult)) {
            $preCount[] = $prCount['Total'];
        }
        if (!empty($preCount)) {
            $Count['TotalEntries'] = array_sum($preCount);
            unset($prCount);
            unset($preCount);
        } else {
            $Count['TotalEntries'] = 0;
        }
        $Count['Total'] = mysql_num_rows($CountResult);
    } else {
        // get Count entry
        if (!empty($CountResult)) {
            $Count = mysql_fetch_assoc($CountResult);
            mysql_free_result($CountResult);
        } else {
            $Count = 0;
        }
    }

    if (!empty($limitOveride) && $limitOveride != 'full') {
        $Config['_Items_Per_Page'] = $limitOveride;
    }

    if(!empty($_SESSION['report_'.$EID]['limitOveride'])){        
            $Config['_Items_Per_Page'] = floatval($_SESSION['report_'.$EID]['limitOveride']);
    }

    if (!empty($Config['_Items_Per_Page'])) {
        $TotalPages = ceil($Count['Total'] / $Config['_Items_Per_Page']);
        $Start = ($Page * $Config['_Items_Per_Page']) - $Config['_Items_Per_Page'];
        $Offset = $Config['_Items_Per_Page'];
        if ($Page > 0) {
            if ($Page > $TotalPages) {
                $Page = $TotalPages;
                $Start = ($Page * $Config['_Items_Per_Page']) - $Config['_Items_Per_Page'];
                if ($Start < 0) {
                    $Start = 1;
                }
            }
            $queryLimit = " LIMIT " . $Start . ", " . $Offset . " ";
            //$Limit = "";
        }
    }
    if (strtolower($Format) == 'pdf' && $limitOveride != false) {
        if ($limitOveride = 'full') {
            $queryLimit = '';
        }
    }


    if (empty($Config['_Items_Per_Page'])) {
        $queryLimit = '';
    }
    // Select Query
    //$Query = "SELECT count(b.Country) as TotalCountry, ".$querySelect." FROM `".$Config['_main_table']."` AS prim \n ".$queryJoin." \n ".$WhereTag." \n ".$queryWhere."\n GROUP BY b.Country \n ".$orderStr." \n ".$queryLimit.";"

    if(!empty($Config['_useListTemplate']) && empty($Format)){
        $Media = $Element;
        ob_start();
        $Query = dr_BuildReportGrid($EID, $Page, $SortField, $SortDir, 'sql', $limitOveride, $wherePush);
        echo '<div id="reportPanel_'.$Media['ID'].'" class="interfaceWrapper">';
        include('templatemode.php');
        echo '</div>';
        return ob_get_clean();
    }

    $Query = "SELECT " . $querySelect . " FROM `" . $Config['_main_table'] . "` AS prim \n " . $queryJoin . " \n " . $WhereTag . " \n " . $queryWhere . "\n " . $groupBy . " \n " . $orderStr . " \n " . $queryLimit . ";";   
    //vardump($Query);
    // Wrap fields with ``
    //foreach($querySelects as $Field=>$FieldValue){
    // echo $Field.' = '.$FieldValue.'<br />';
    //   $Query = str_replace('.'.$Field, '.`'.$Field.'`', $Query);
    //}
    if (strtolower($Format) == 'data') {
        $dtaRes = mysql_query($Query);
        return mysql_fetch_assoc($dtaRes);
    }
    if (!empty($Config['_UseCustomQuery']) && !empty($Config['_ManualQuery'])) {
        $Query = $Config['_ManualQuery'];

        preg_match('/(LIMIT [ 0-9]+,[ 0-9]+)/', $Query, $Limits);
        if (!empty($Limits[0])) {
            $Query = str_replace($Limits[0], $queryLimit, $Query);
        } else {
            $Query .= $queryLimit;
        }
    }
    //echo $Query;

    $QueryHash = md5($Query);
    $_SESSION['queries'][$EID] = $Query;
    if (!empty($Queries[$QueryHash])) {
        $Result = $Queries[$QueryHash];
        mysql_data_seek($Result, 0);
    } else {
        $Result = mysql_query($Query);
        $Queries[$QueryHash] = $Result;
    }


    $_SESSION['queries'][$EID] = $Query;
    //vardump($Config);
    //$Config['_chartMode'] = true;
    if (!empty($Config['_chartMode']) && empty($Format)) {


        $chartRes = $Result;
        $x = array();
        $y = array();
        $xStart = '';
        $xInterval = '';
        //echo $Config['_xaxis'].'<br />';

        while ($chartData = mysql_fetch_assoc($chartRes)) {
            //vardump($chartData);
            //echo '-'.$chartData[$Config['_xaxis']].'<br>';
            $XValue = $chartData[$Config['_xaxis']];
            if ($Config['_Field'][$Config['_xaxis']][0] == 'date' && empty($xStart) && empty($Config['_disableDateTime'])) {

                $dateString = str_replace('-', ',', $chartData[$Config['_xaxis']]);
                $dateString = str_replace(':', ',', $dateString);
                $dateString = str_replace(' ', ',', $dateString);
                $dateString = explode(',', $dateString);
                $dateString[1] = $dateString[1] - 1;

                //dump($dateString);
                //echo $Config['_xAxisTimeFrame'];
                //$xStart = 'pointStart: Date.UTC(' . implode(',', $dateString) . '), ';
                //$xStartDate = date($dateFormat, strtotime($chartData[$Config['_xaxis']]));
                //$xInterval = "pointInterval: " . $xInterval . ",";
            }
            if ($Config['_Field'][$Config['_xaxis']][0] == 'date' && empty($Config['_disableDateTime'])) {
                // preX
                $xEndDate = date($dateFormat, strtotime($chartData[$Config['_xaxis']]));
                $preX[] = date($dateFormat, strtotime($chartData[$Config['_xaxis']]));
            }

            if (function_exists($Config['_Field'][$Config['_xaxis']][0] . '_processValue')) {
                $processFunc = $Config['_Field'][$Config['_xaxis']][0] . '_processValue';
                $XValue = '"' . $processFunc($XValue, $Config['_Field'][$Config['_xaxis']][1], $Config['_xaxis'], $Config, $EID, $chartData, 'internal') . '"';
            }
            $x[] = addslashes(trim($XValue, '"'));
            if(is_array($Config['_chartValue'])){
                foreach ($Config['_chartValue'] as $ChartLine => $on) {

                    $YValue = $chartData[$ChartLine];
                    if (function_exists($Config['_Field'][$ChartLine][0] . '_processValue')) {
                        $processFunc = $Config['_Field'][$ChartLine][0] . '_processValue';
                        $YValue = $processFunc($chartData[$ChartLine], $Config['_Field'][$ChartLine][1], $ChartLine, $Config, $EID, $chartData, 'internal');
                    } else {
                        //echo 'no process for '.$Config['_Field'][$ChartLine][0].'_processValue<br>';
                    }

                    $y[$ChartLine][] = $YValue;
                    //$y[$ChartLine][] = "['".$XValue."', ".$YValue."]";
                }
            }
        }

        /// Build PrePoulated Dataset and convert current series into the prepoulated set
        /// no idea how to do it yet.
        //dump($x);
        if ($Config['_Field'][$Config['_xaxis']][0] == 'date' && empty($Config['_disableDateTime'])) {

            $firstDate = $x[0];
            $currentDate = $x[0];
            $lastDate = $x[count($x)-1];

            $upindex = 0;
            $run= true;            
            while($run == true){
                if($upindex >= 1){
                    //echo $CurrentDate.' +'.$Config['_xAxisIntervalNum'].' '.$Config['_xAxisTimeFrame'].' | '.$currentDate.' = ';
                    $nextDate = date($Config['_dateFormat'][$Config['_xaxis']], strtotime($currentDate.' +'.$Config['_xAxisIntervalNum'].' '.$Config['_xAxisTimeFrame']));
                    $currentDate = $nextDate;
                    $next = array_search($nextDate, $x);
                    if(empty($next)){
                        $newX[] = $nextDate;
                        foreach($y as $field=>$item){
                            $newY[$field][$upindex] = 0;
                        }
                    }else{
                        foreach($y as $field=>$item){
                            $newY[$field][$upindex] = $y[$field][$next];
                        }
                        $newX[] = $x[$next];
                    }
                }else{
                    foreach($y as $field=>$item){
                        $newY[$field][$upindex] = $y[$field][0];
                    }
                    $newX[] = $firstDate;
                    $currentDate = $firstDate;
                }
                //echo $CurrentDate.' - '.$nextDate.' - '.$lastDate.'<br>';
                if($nextDate == $lastDate || count($newX) >= 500){
                    $run = false;
                }
                $upindex++;
            }
            $x = $newX;
            $y = $newY;






            //echo $xStartDate.'<br>-------------<br> '.$xEndDate.'<br> =-----------======-<br><br> ';
            /*
            $end = false;
            $nowTime = $xStartDate;
            $index = 0;
            //dump($y);

            while ($end === false) {
                // pre populate a dataset
                $tickTime = date($dateFormat, strtotime($nowTime));
                //dump($preX);
                foreach ($y as $ChartLine => $yValues) {
                    //echo $tickTime;
                    $key = array_search($tickTime, $preX);
                    //echo $yValues[$key].' - ';
                    if (is_int($key)) {
                        $newY[$ChartLine][] = $yValues[$key];
                    } else {
                        $newY[$ChartLine][] = 'null';
                    }
                    //echo $key;
                    //echo $yValues[]
                }
                $nowTime = $nowTime . ' +' . $Config['_xAxisIntervalNum'] . ' ' . $Config['_xAxisTimeFrame'];
                //echo $tickTime;
                //dump($preX);

                if ($tickTime == $xEndDate || $index == 100) {
                    $end = true;
                }
                $index++;
            }
            //dump($newY);
            //$y = $newY;
             * 
             */
        }
        //dump($y);











        //if ($Config['_Field'][$Config['_xaxis']][0] == 'date' && empty($Config['_disableDateTime'])) {
        //    $xType = 'type: \'datetime\'';
        //} else {
            $xType = "categories: ['" . implode('\',\'', $x) . "']";
        //}
        @mysql_data_seek($chartRes, 0);




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
        if (!empty($Config['_chartHeight'])) {
            $height = $Config['_chartHeight'];
        }

        $topPad = 60;
        $rightPad = (20 + (100 * count($Config['_chartValue'])) - 100);
        $bottomPad = 120;
        $leftPad = 80;
        $xAngle = -45;

        $toolTipTemplate = '<b>{{SeriesName}}</b><br/>{{YValue}}: {{XValue}}';
        if (!empty($Config['_yToolTipTemplate'])) {
            $toolTipTemplate = $Config['_yToolTipTemplate'];
        }

        // setup tooltip
        /*
         * '<b></b><br/>'+
          this.y +': '+ this.x
         */
        $toolTipTemplate = str_replace('{{SeriesName}}', "'+this.series.name+'", $toolTipTemplate);
        $toolTipTemplate = str_replace('{{YValue}}', "'+this.y+'", $toolTipTemplate);
        $toolTipTemplate = str_replace('{{XValue}}', "'+this.x+'", $toolTipTemplate);
        $toolTipTemplate = str_replace('{{PointName}}', "'+this.point.name+'", $toolTipTemplate);


        if (!empty($Config['_topPad'])) {
            $topPad = $Config['_topPad'];
        }
        if (!empty($Config['_rightPad'])) {
            $rightPad = $Config['_rightPad'];
        }
        if (!empty($Config['_bottomPad'])) {
            $bottomPad = $Config['_bottomPad'];
        }
        if (!empty($Config['_leftPad'])) {
            $leftPad = $Config['_leftPad'];
        }
        if (isset($Config['_xAngle'])) {
            $xAngle = $Config['_xAngle'];
        }

        //dump($Config);
        $ChartTitle = '';
        if (!empty($Config['_chartTitle'])) {
            $ChartTitle = $Config['_chartTitle'];
        }
        $ChartCaption = '';
        if (!empty($Config['_chartCaption'])) {
            $ChartCaption = $Config['_chartCaption'];
        }
        $xAlign = 'right';
        if (!empty($Config['_xAxis_Align'])) {
            $xAlign = $Config['_xAxis_Align'];
        }

        $ChartID = uniqid('chart_');
        $_SESSION['dataform']['OutScripts'] .= "
var " . $ChartID . " = new Highcharts.Chart({
   chart: {
      renderTo: 'chart_" . $ChartID . "',
      //defaultSeriesType: '" . $chartType . "',
      zoomType: 'x',
      margin: [" . $topPad . ", " . $rightPad . ", " . $bottomPad . ", " . $leftPad . "],
      height: " . $height . ",
      backgroundColor: null,
   },
   exporting:{
    buttons: {
        printButton: {
            enabled: true
        }
    },
    enabled: true,
    elementID: '" . $EID . "',
    filename: \"chart\",
    type: \"image/png\",
    url: 'system/dais/plugins/data_report/chartexport/index.php',
    width: 800
    },
   colors: [
	'#4572A7',
	'#89A54E',
	'#062F55',
	'#AA4643',
	'#3D96AE',
	'#DB843D',
	'#92A8CD',
	'#A47D7C',
	'#B5CA92'
    ],
   title: {
      align: 'left',
      text: '" . $ChartTitle . "',
      x: 80
   },
   subtitle: {
      text: '" . $ChartCaption . "',
      align: 'left',
      x : 81


   },
   xAxis: {
      " . $xType . ",
      labels: {
         rotation: $xAngle,
         align: '$xAlign',
         style: {
             font: 'normal 9px Verdana, sans-serif'
         }
      }
   },




   yAxis: [";
        $index = 1;
        $margin = 50;
        $off = '';
        $axisTitle = $Config['_FieldTitle'][$Key];
        $color = array("#4572A7", "#89A54E", "#062F55", "#AA4643", "#80699B", "#3D96AE", "#DB843D", "#92A8CD", "#A47D7C", "#B5CA92");
        foreach ($y as $Key => $Series) {
            $thisColor = $color[$index - 1];
            $op = '';
            if ($index > 1) {
                $margin = 360;
                $op = "opposite: true,";
            } else {
                $axisTitle = '';
                $thisColor = $thisColor;
            }
            if ($index >= 2) {
                $off = 'offset: ' . (40 * ($index - 2));
                $margin = 60;
            }
            $axis[] = "
            {
              labels: {
                 style: {
                    color: '" . $thisColor . "'
                 }
              },
              title: {
                 text: '" . $axisTitle . "',
                 margin: " . $margin . ",
                 style: {
                    color: '" . $color[$index - 1] . "'
                 },
              },
              " . $op . "
              " . $off . "
           }";

            $index++;
        }
        if (!empty($axis)) {
            $_SESSION['dataform']['OutScripts'] .= implode(',', $axis);
        }

        $_SESSION['dataform']['OutScripts'] .= "
   ],
    ";
        if (empty($xInterval)) {
            $_SESSION['dataform']['OutScripts'] .= "
   tooltip: {
      formatter: function() {
            return '$toolTipTemplate';
      }
   },";
        }
        $_SESSION['dataform']['OutScripts'] .= "
   plotOptions: {
        ";
        if (!empty($Config['_yShowDataLables'])) {
            $_SESSION['dataform']['OutScripts'] .= "
      column: {
         dataLabels: {
            enabled: true,
            color: 'auto'
         },
      },
      bar: {
         dataLabels: {
            enabled: true,
            color: 'auto'
         },
      },
      line: {
         dataLabels: {
            enabled: true,
            color: 'auto'
         },
      },
    pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
           enabled: true,
           formatter: function() {
              return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
           }
        },
        showInLegend: true

     },

      ";
        }
        $_SESSION['dataform']['OutScripts'] .= "
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
      layout: 'horizontal',";
        if (!empty($Config['_hideLegend'])) {
            $_SESSION['dataform']['OutScripts'] .= "
        enabled: false,";
        }
        $_SESSION['dataform']['OutScripts'] .= "
      style: {
         left: 'auto',
         bottom: 'auto',
         right: '" . $rightPad . "px',
         top: '0px'
      },
      backgroundColor: '#ffffff'
   },

   series: [
    ";

        $Chart = array();
        $index = 0;
        foreach ($y as $Key => $Series) {

            $SeriesData = implode(', ', $Series);
            if ($Config['_chartType'][$Key] == 'pie') {
                $SeriesData = '';
                foreach ($Series as $xKey => $yVal) {
                    $SeriesData[] = "['" . $x[$xKey] . "', " . $yVal . "]";
                }
                $SeriesData = implode(',', $SeriesData);
            }
            $Line = '{';
            // datetime stuff

            //$Line .= $xStart . "\r\n";
            //$Line .= $xInterval . "\r\n";
            $Line .= 'name: "' . $Config['_FieldTitle'][$Key] . '", ';
            $Line .= 'data: [' . $SeriesData . ']';
            $Line .= ', type: \'' . $Config['_chartType'][$Key] . '\' ';
            if (!empty($Config['_multiAxis'])) {
                $Line .= ', ';
                $Line .= 'yAxis: ' . $index . '';
            }
            $Line .= '}';
            $Graph[] = $Line;
            $index++;
        }
        if (empty($Graph)) {
            $Graph = array();
        }
        $_SESSION['dataform']['OutScripts'] .= implode(',', $Graph) . "
   ]
});

";



        //$_SESSION[]
        //TODO : make the interface remember its in sqlmode;

        $ReportReturn = '<div id="chart_' . $ChartID . '" style="height:' . $height . 'px;"></div>' . $ReportReturn;
    }



    // Query Results
    //echo $Query;
    if (strtolower($Format) == 'sql') {
        return $Query;
    }

    $Result = $wpdb->get_results($Query, ARRAY_A);

    //$Result = mysql_query($Query);
    if (!empty($Config['_chartOnly'])) {
        return '<div id="chart_' . $ChartID . '" style="height:' . $height . 'px;"></div>'; //$ReportReturn;
    }
    // Build Rows
    if (!empty($Config['_UseListViewTemplate'])) {
        if (!empty($Config['_ListViewTemplateContentWrapperStart'])) {
            $ReportReturn .= $Config['_ListViewTemplateContentWrapperStart'];
        }
    } else {
        $ReportReturn .= '<tbody>';
    }
    // Row number Increment
    $rowIndex = 1;
    // Set Row Style
    $Row = 'odd';

    // add in inline editing
    if (!empty($Config['_InlineEdit'])) {
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

    if (!empty($Result)) {

        //while($row = mysql_fetch_assoc($Result)) {
        foreach ($Result as $row) {
        
            // Switch Row Style
            //$Row = dais_rowswitch($Row);
            //$Row = report_rowswitch($Row);
            $Row = grid_rowswitch($Row);
            // foreach column

            $SelectedRow = '';
            if (!empty($Config['_ReturnFields'][0])) {
                if (!empty($_GET[$Config['_ReturnFields'][0]])) {
                    if ($row['_return_' . $Config['_ReturnFields'][0]] == $_GET[$Config['_ReturnFields'][0]]) {
                        if (!empty($Config['_Show_Edit'])) {
                            $SelectedRow = 'highlight';
                        }
                        $HighlightIndex = true;
                    }
                }
            }
            if (empty($Config['_UseListViewTemplate'])) {
                $ReportReturn .= '<tr class="' . $Row . ' itemRow_' . $EID . ' ' . $SelectedRow . ' report_entry" ref="' . $row['_return_' . $Config['_ReturnFields'][0]] . ' highlight" id="row_' . $EID . '_' . $rowIndex . '" >';
            }
            // API Output
            if (!empty($Format)) {
                // XML Output
                if (strtolower($Format) == 'xml') {
                    $apiOut .= "		<entry>\n";
                }
                // json Output
                if (strtolower($Format) == 'json') {
                    $apiOutput['entries'][$jsonIndex] = array();
                }
                if (strtolower($Format) == 'pdf') {
                    //$apiOutput[$jsonIndex][] = array();
                    if (!empty($_SESSION['reportFilters'][$EID])) {
                        //$apiOutput['filters'] = array();
                    }
                }
            }
            // Output each Row
            //dump($row);
            if (!empty($Config['_UseListViewTemplate'])) {
                if (!empty($Config['_ListViewTemplatePreContent'])) {
                    $ReportReturn .= $Config['_ListViewTemplatePreContent'];
                }
            }

            if (!empty($Config['_UseListViewTemplate'])) {
                if (!empty($Config['_ListViewTemplateContent'])) {
                    $PreReturn = str_replace('{{_FieldValue}}', $PreReportReturn, $Config['_ListViewTemplateContent']);
                    foreach ($row as $fieldKey => $fieldValue) {
                        if (function_exists($Config['_Field'][$fieldKey][0] . '_processValue')) {
                            $processFunc = $Config['_Field'][$fieldKey][0] . '_processValue';
                            $fieldValue = $processFunc($fieldValue, $Config['_Field'][$fieldKey][1], $fieldKey, $Config, $EID, $row);
                        }
                        if (!empty($Config['_FieldTitle'][$fieldKey])) {
                            $name = $Config['_FieldTitle'][$fieldKey];
                        } else {
                            $name = df_parseCamelCase($fieldKey);
                        }

                        $PreReturn = str_replace('{{_' . $fieldKey . '_name}}', $name, $PreReturn);
                        $PreReturn = str_replace('{{' . $fieldKey . '}}', $fieldValue, $PreReturn);


                        //vardump($Config);
                        //echo '<br />'.$fieldKey;
                        //$PreReturn = str_replace('{{'.$fieldKey.'}}', $fieldValue, $PreReturn);
                    }

                    // substr

                    preg_match("/\{\{([A-Za-z0-9]+)\|([0-9]+)\}\}/", $PreReturn, $returnMatches);
                    if (!empty($returnMatches)) {
                        $PreReturn = str_replace($returnMatches[0], substr(strip_tags($row[$returnMatches[1]]), 0, $returnMatches[2]) . '&hellip;', $PreReturn);
                    }


                    $PreReturn = str_replace('{{_RowClass}}', $Row, $PreReturn);
                    $PreReturn = str_replace('{{_SelectedClass}}', $SelectedRow, $PreReturn);
                    $PreReturn = str_replace('{{_RowIndex}}', $rowIndex, $PreReturn);
                    $PreReturn = str_replace('{{_UID}}', uniqid(), $PreReturn);
                    $PreReturn = str_replace('{{_PageID}}', $Element['ParentDocument'], $PreReturn);
                    $PreReturn = str_replace('{{_PageName}}', getdocument($Element['ParentDocument']), $PreReturn);
                    $PreReturn = str_replace('{{_EID}}', $EID, $PreReturn);
                    //Template based view / edit
                    if (!empty($Config['_Show_View']) || !empty($Config['_Show_Edit'])) {
                        $ViewLink = '';
                        if (!empty($Config['_Show_View'])) {
                            $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry(\"" . $row['_return_' . $Config['_ReturnFields'][0]] . "\", \"" . $EID . "\", \"" . $isModal . "\"); return false;\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                            if (!empty($Config['_ItemViewPage'])) {
                                $ReportVars = array();
                                foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                    $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                }
                                // Get permalink
                                $PageLink = get_permalink($Config['_ItemViewPage']);
                                $Location = parse_url($PageLink);
                                if (!empty($Location['query'])) {
                                    $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                                    parse_str($Location['query'], $gets);
                                    $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                } else {
                                    $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                                }
                                $ViewLink = "<a href=\"" . $PageLink . "\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                            }
                        }
                        if (!empty($Config['_Show_Edit'])) {
                            if ($ViewLink != '') {
                                $ViewLink .= " ";
                            }
                            $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');"><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                        }
                        $PreReturn = str_replace('{{_ViewEdit}}', $ViewLink, $PreReturn); //'Edit | View';
                        $PreReturn = str_replace('{{_ViewLink}}', getdocument($Config['_ItemViewPage']) . "?" . $ReportReturnString, $PreReturn); //'Edit | View';
                    }
                    $ReportReturn .= $PreReturn;
                }
                if (!empty($Config['_UseListViewTemplate'])) {
                    if (!empty($Config['_ListViewTemplatePostContent'])) {
                        $ReportReturn .= $Config['_ListViewTemplatePostContent'];
                    }
                }
            } else {
                if (empty($Config['_chartOnly'])) {
                    $ColumnCounter = 0;
                    //vardump($Config);
                    // action panels

                    // Edit Functions if no popup
                    $actionPanels = '';
                    if (!empty($ShowActionPanel)) {
                        $ViewLink = '';
                        $ActionWidth = 16;
                        if (!empty($Config['_Show_View'])) {
                            $ActionWidth = $ActionWidth + 16;
                            $ViewLink .= "<span style=\"cursor:pointer;\" onclick=\"df_loadEntry('" . $row['_return_' . $Config['_ReturnFields'][0]] . "', '" . $EID . "', " . $isModal . "); return false;\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></span>";
                            if(!is_admin()){
                                if (!empty($Config['_ItemViewPage'])) {
                                    $ReportVars = array();
                                    foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                        $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                    }
                                    // Get permalink
                                    $PageLink = get_permalink($Config['_ItemViewPage']);
                                    $Location = parse_url($PageLink);
                                    if (!empty($Location['query'])) {
                                        $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                                        parse_str($Location['query'], $gets);
                                        $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                    } else {
                                        $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                                    }
                                    $ViewLink = "<a href=\"" . $PageLink . "\"><img src=\"" . WP_PLUGIN_URL . "/db-toolkit/data_report/css/images/magnifier.png\" width=\"16\" height=\"16\" alt=\"View\" title=\"View\" border=\"0\" align=\"absmiddle\" /></a>";
                                }
                            }
                        }
                        if (!empty($Config['_Show_Edit'])) {
                            $ActionWidth = $ActionWidth + 16;
                            if ($ViewLink != '') {
                                $ViewLink .= " ";
                            }
                            $ViewLink .= '<span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');"><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/edit.png" width="16" height="16" alt="Edit" title="Edit" border="0" align="absmiddle" /></span>';
                        }
                        if (!empty($Config['_Show_Delete_action'])) {
                            $ActionWidth = $ActionWidth + 16;
                            if ($ViewLink != '') {
                                $ViewLink .= " ";
                            }
                            $ViewLink .= '<span style="cursor:pointer;" onclick="dr_deleteItem(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');"><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/delete.png" width="16" height="16" alt="Delete" title="Delete" border="0" align="absmiddle" /></span>';
                        }
                        //vardump($Config);

                        $PreReportReturn = '<td class="' . $Row . ' action" width="' . $ActionWidth . '" scope="col" style="text-align:center;overflow:hidden;">';
                        $PreReportReturn .= $ViewLink; //'Edit | View';
                        $PreReportReturn .= '</td>';
                        $actionPanels .= $PreReportReturn;
                    }




                    // Show Action Panels on left
                    //$ReportReturn .= $actionPanels;


                    foreach($Config['_IndexType'] as $Field=>$Type){
                    //foreach ($row as $Field => $Data) {
                        if($Type[1] === 1)
                            break;
                        $Data = $row[$Field];

                        if (!empty($Config['_IndexType'][$Field][1])) {
                            if ($Config['_IndexType'][$Field][1] == 'show') {
                                $outData = $Data;
                                /// Capture value for Totals
                                if (!empty($Config['_TotalsFields'][$Field]['Type'])) {
                                    switch ($Config['_TotalsFields'][$Field]['Type']) {
                                        case 'count':
                                            //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].'+1<br />';
                                            $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Count['TotalEntries'];
                                            break;
                                        case 'sum':
                                            //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].' + '.$outData.'<br />';
                                            $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] + $outData;
                                            break;
                                        case 'avg':
                                            //echo $Field.' = '.$Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']].' + '.$outData.'<br />';
                                            $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] = $Config['_TotalsFields'][$Field][$Config['_TotalsFields'][$Field]['Type']] + $outData;
                                            break;
                                    }
                                }

                                if (function_exists($Config['_Field'][$Field][0] . '_processValue')) {
                                    $processFunc = $Config['_Field'][$Field][0] . '_processValue';
                                    //						Value  field type					 Field	 element config
                                    $outData = $processFunc($Data, $Config['_Field'][$Field][1], $Field, $Config, $EID, $row);
                                }
                                // Capture columns average width for ato widths
                                $AvrageWidth[$Field][] = strlen($outData) * 8;


                                // Apply keyword Fitler Highlight
                                if (!empty($_SESSION['reportFilters'][$EID]['_keywords'])) {
                                    //$outData = str_replace($_SESSION['reportFilters'][$EID]['_keywords'], '<strong>'.$_SESSION['reportFilters'][$EID]['_keywords'].'</strong>', $outData);
                                    //$outData = str_replace(ucwords($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.ucwords($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                                    //$outData = str_replace(strtoupper($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.strtoupper($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                                    //$outData = str_replace(strtolower($_SESSION['reportFilters'][$EID]['_keywords']), '<strong>'.strtolower($_SESSION['reportFilters'][$EID]['_keywords']).'</strong>', $outData);
                                }

                                // set row output
                                //Check if field is in totals and is allowed inline
                                $Location = 'inline';
                                if (!empty($Config['_TotalsFields'][$Field]['Location'])) {
                                    $Location = $Config['_TotalsFields'][$Field]['Location'];
                                }
                                if ($Location == 'inline' || $Location == 'headerinline' || $Location == 'footerinline') {
                                    // selection highlighting (experimental)
                                    $sortClass = '';
                                    if ($_SESSION['report_' . $EID]['SortField'] == $Field) {
                                        $sortClass = 'column_sorting_' . $_SESSION['report_' . $EID]['SortDir'];
                                    }
                                    $itemID = uniqid('');
                                    // Add Reload Highlighting
                                    $LiveHighlight = '';
                                    if (!empty($Config['_showReload'])) {
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
                                    $ReportReturn .= '<td class="' . $Row . ' ' . $sortClass . ' ' . $LiveHighlight . '" scope="col" id="' . $itemID . '" ref="itemRow_' . $EID . '" width="' . ($Config['_WidthOverride'][$Field] == '' ? '{{width_' . $Field . '}}px' : $Config['_WidthOverride'][$Field] . 'px') . '" style="text-align:' . $Config['_Justify'][$Field] . '; ">';
                                    //inline editing
                                    if (!empty($Config['_InlineEdit'][$Field])) {
                                        $Req = 'inlineedit';
                                        $FieldSet = $Config['_Field'][$Field];
                                        //$ReportReturn .= WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/input.php';
                                        ob_start();
                                        $Defaults[$Field] = $row['_sourceid_' . $Field];
                                        include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $FieldSet[0] . '/conf.php');
                                        include(WP_PLUGIN_DIR . '/db-toolkit/data_form/fieldtypes/' . $FieldSet[0] . '/input.php');
                                        $ReportReturn .= ob_get_clean();
                                    } else {
                                        $PreReportReturn = '';
                                        // Make View Item Link If page is set
                                        if (is_admin ()) {
                                            //vardump($Config);
                                            if (!empty($Config['_ItemViewInterface'])) {
                                                // Create return link
                                                $ReportVars = array();
                                                foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                                    $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                                }
                                                // Get permalink
                                                // interface admin.php?page=Database_Toolkit&renderinterface=dt_intfc4c04c77ed928a
                                                $PageLink = 'admin.php?page=Database_Toolkit&renderinterface=' . $Config['_ItemViewInterface'] . '&' . htmlspecialchars_decode(http_build_query($ReportVars));
                                                $PreReportReturn .= "<a href=\"" . $PageLink . "\"><strong>";
                                            }
                                        } else {
                                            if (!empty($Config['_ItemViewPage'])) {
                                                // Create return link
                                                $ReportVars = array();
                                                foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                                    $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                                }
                                                // Get permalink
                                                $PageLink = get_permalink($Config['_ItemViewPage']);
                                                $Location = parse_url($PageLink);
                                                if (!empty($Location['query'])) {
                                                    $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                                                    parse_str($Location['query'], $gets);
                                                    $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                                } else {
                                                    $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                                                }
                                                $PreReportReturn .= "<a href=\"" . $PageLink . "\"><strong>";
                                            }
                                        }
                                        $ReturnFields = array();
                                        if (is_array($Config['_ReturnFields'])) {
                                            foreach ($Config['_ReturnFields'] as $ReturnField) {
                                                $ReturnFields[] = $ReturnField . '=' . urlencode($row['_return_' . $ReturnField]);
                                            }
                                        }
                                        //$ReturnMix = implode('&', $ReturnFields);
                                        //$PreReportReturn .= '<a href="'.getdocument($_GET['PageData']['ID']).'#'.$ReturnMix.'">'.stripslashes($outData).'</a>';
                                        $PreReportReturn .= $outData;
                                        // API Output
                                        if (!empty($Format)) {
                                            // XML Output
                                            if (strtolower($Format) == 'xml') {
                                                $apiOut .= "			<" . $Field . "><![CDATA[" . stripslashes($outData) . "]]></" . $Field . ">\n";
                                            }
                                            // json Output
                                            if (strtolower($Format) == 'json') {
                                                $apiOutput['entries'][$jsonIndex][$Field] = htmlentities(stripslashes($outData));
                                            }
                                            // PDF output
                                            if (strtolower($Format) == 'pdf') {
                                                $apiOutput[$pdfIndex][$Field] = htmlentities(stripslashes($outData));
                                                if (!empty($_SESSION['reportFilters'][$EID][$Field])) {
                                                    $apiOutput['filters'][$Field][stripslashes($outData)] = stripslashes($outData);
                                                }
                                            }
                                        }
                                        // Close link
                                        if (!empty($Config['_ItemViewPage'])) {
                                            $PreReportReturn .= "</strong></a>";
                                        }
                                        if (!empty($Config['_ItemViewInterface'])) {
                                            $PreReportReturn .= "</strong></a>";
                                        }
                                    }
                                    $ReportReturn .= $PreReportReturn;
                                    if (!empty($Config['_Show_popup'])) {
                                        
                                        if ($ColumnCounter === 0) {
                                            // Add inline actions
                                            $ViewLink = '';
                                            $ActionWidth = 16;
                                            if (!empty($Config['_Show_View'])) {
                                                $ActionWidth = $ActionWidth + 16;
                                                $ViewLink['view'] = "<a href=\"#\" onclick=\"return false;\"><span style=\"cursor:pointer;\" onclick=\"df_loadEntry('" . $row['_return_' . $Config['_ReturnFields'][0]] . "', '" . $EID . "', " . $isModal . "); return false;\">View</span></a>";
                                                if (is_admin ()) {
                                                    if (!empty($Config['_ItemViewInterface'])) {
                                                        $ReportVars = array();
                                                        foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                                            $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                                        }
                                                        // Get permalink
                                                        $PageLink = 'admin.php?page=' . $Config['_ItemViewInterface'] . '&' . htmlspecialchars_decode(http_build_query($ReportVars));
                                                        $ViewLink['view'] = "<a href=\"" . $PageLink . "\">View</a>";
                                                    }
                                                } else {
                                                    if (!empty($Config['_ItemViewPage'])) {
                                                        $ReportVars = array();
                                                        foreach ($Config['_ReturnFields'] as $ReportReturnField) {
                                                            $ReportVars[$ReportReturnField] = urlencode($row['_return_' . $ReportReturnField]);
                                                        }
                                                        // Get permalink
                                                        $PageLink = get_permalink($Config['_ItemViewPage']);
                                                        $Location = parse_url($PageLink);
                                                        if (!empty($Location['query'])) {
                                                            $PageLink = str_replace('?' . $Location['query'], '', $PageLink);
                                                            parse_str($Location['query'], $gets);
                                                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query(array_merge($gets, $ReportVars)));
                                                        } else {
                                                            $PageLink = $PageLink . '?' . htmlspecialchars_decode(http_build_query($ReportVars));
                                                        }
                                                        $ViewLink['view'] = "<a href=\"" . $PageLink . "\">View</a>";
                                                    }
                                                }
                                            }
                                            if (!empty($Config['_Show_Edit'])) {
                                                $ActionWidth = $ActionWidth + 16;

                                                $ViewLink['edit'] = '<a href="#" onclick="return false;"><span style="cursor:pointer;" onclick="dr_BuildUpDateForm(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');">Edit</span></a>';
                                            }
                                            if (!empty($Config['_Show_Delete_action'])) {
                                                $ActionWidth = $ActionWidth + 16;

                                                $ViewLink['delete'] = '<a href="#" onclick="return false;"><span style="cursor:pointer;" class="delete" onclick="dr_deleteItem(\'' . $EID . '\', \'' . $row['_return_' . $Config['_ReturnFields'][0]] . '\');">Delete</span></a>';
                                            }
                                            //vardump($Config);
                                            //$PreReportReturn = '<td class="'.$Row.' action" width="'.$ActionWidth.'" scope="col" style="text-align:center;overflow:hidden;">';
                                            $ReportReturn .= '<div class="row-actions">' . implode(' | ', $ViewLink) . '</div>'; //'Edit | View';
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
                    if (!empty($Format)) {
                        // XML Output
                        if (strtolower($Format) == 'xml') {
                            $apiOut .= "		</entry>\n";
                        }
                        // json Output
                        if (strtolower($Format) == 'json') {
                            $jsonIndex++;
                        }
                        //PDF Output
                        if (strtolower($Format) == 'pdf') {
                            $pdfIndex++;
                        }
                    }

                    // Show Action Panelson right
                    $ReportReturn .= $actionPanels;

                    $ReportReturn .= '</tr>';

                    // Increment row index
                    $rowIndex++;
                }
            }
        }
        
    }
    //echo mysql_error();
    if (!empty($Config['_UseListViewTemplate'])) {
        if (!empty($Config['_ListViewTemplateContentWrapperEnd'])) {
            $ReportReturn .= $Config['_ListViewTemplateContentWrapperEnd'];
        }
    } else {
        // Close off Table end content
        $ReportReturn .= '</tbody>';
        $ReportReturn .= '</table>';
    }




// Make Scripts for deleting and select
    if (!empty($Config['_Show_Edit'])) {
        $_SESSION['dataform']['OutScripts'] .= "
		jQuery('#data_report_" . $EID . " .report_entry').bind('click', function(){
			jQuery(this).toggleClass(\"highlight\");
		});
	";
    }

// Footer
    //TODO: really need to clean up this templating. to much repetition
    if (!empty($Config['_Show_Footer'])) {

        $First = 1;
        $Prev = $Page - 1;
        $Next = $Page + 1;
        $Last = $TotalPages;
        if ($Prev <= 0) {
            $Prev = 1;
        }
        if ($Next > $TotalPages) {
            $Next = $TotalPages;
        }
        if (empty($Page)) {
            $Page = 1;
        }
        //Page Index display
        $toPos = $Page * $Offset;
        if ($toPos > $Count['Total']) {
            $toPos = $Count['Total'];
        }
        if (empty($Config['_UseListViewTemplate'])) {

            $pageLink = '';
            if(!empty($_SERVER['QUERY_STRING'])){
                $pageLink = $_SERVER['QUERY_STRING'].'&';
            }

            //$ReportReturn .= '<div style="padding:3px;" class="list_row3">';
            $ReportReturn .= '<div style="padding:3px;" class="tablenav bottom">';
            //Total pages display
            $ReportReturn .= '<div class="tablenav-pages">';
            // Check if there are any entries
            if ($Count['Total'] == 0) {
                $nothingFound = 'Nothing Found';
                if (!empty($Config['_NoResultsText'])) {
                    $nothingFound = $Config['_NoResultsText'];
                }
                $ReportReturn .= '<span class="displaying-num">' . $nothingFound . '</span>';
            } else {
                $ReportReturn .= '<span class="displaying-num">'.( $Start + 1) . ' - ' . $toPos . ' of ' . $Count['Total'] . ' Items</span>';
            }
            //$ReportReturn .= '<div class="reportFooter_totals">';
            if ($TotalPages > 1) {
                //$ReportReturn .= '<div class="fbutton" onclick="dr_goToPage('.$EID.', '.$First.');"><div><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/images/resultset_first.png" width="16" height="16" alt="First" align="absmiddle" /></div></div>';                

                $ReportReturn .= '<a href="?'.$pageLink.'npage=1" title="Go to the first page" class="first-page" onclick="dr_goToPage(\'' . $EID . '\', 1); return false;">«</a>';
                $ReportReturn .= '<a href="?'.$pageLink.'npage='.$Prev.'" title="Go to the previous page" class="prev-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $Prev . '); return false;">‹</a>';
                $ReportReturn .= '<span class="paging-input"> ' . $Page . ' of <span class="total-pages">' . $TotalPages . ' </span></span>';
                $ReportReturn .= '<a href="?'.$pageLink.'npage='.$Next.'" title="Go to the next page" class="next-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $Next . '); return false;">›</a>';
                $ReportReturn .= '<a href="?'.$pageLink.'npage='.$TotalPages.'" title="Go to the last page" class="last-page" onclick="dr_goToPage(\'' . $EID . '\', ' . $TotalPages . '); return false;">»</a>';
                //$ReportReturn .= '<div class="fbutton" onclick="dr_goToPage('.$EID.', '.$Last.');"><div><img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_report/images/resultset_last.png" width="16" height="16" alt="Last" align="absmiddle" /></div></div>';
            }


            $ReportReturn .= '<br class="clear"></div>';
            $ReportReturn .= '</div>';

            
        } else {
            if (!empty($Config['_ListViewTemplatePreFooter'])) {
                $ReportReturn .= $Config['_ListViewTemplatePreFooter'];
            }
            if (!empty($Config['_ListViewTemplateFooter'])) {

                $prevbutton = '<div class="fbutton" onclick="dr_goToPage(\'' . $EID . '\', ' . $Prev . ');"><div><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/prev.gif" width="27" height="17" alt="Previous" align="absmiddle" /></div></div>';
                $pagejump = '<div class="fpanel">Page <input type="text" name="pageJump" id="pageJump_' . $EID . '" style="width:30px; font-size:11px;" value="' . $Page . '" onkeypress="dr_pageInput(\'' . $EID . '\', this.value);" /> of ' . $TotalPages . '</div>';
                $nextbutton = '<div class="fbutton" onclick="dr_goToPage(\'' . $EID . '\', ' . $Next . ');"><div><img src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/next.gif" width="27" height="17" alt="Next" align="absmiddle" /></div></div>';

                if ($Count['Total'] == 0) {
                    $nothingFound = 'Nothing Found';
                    if (!empty($Config['_NoResultsText'])) {
                        $nothingFound = $Config['_NoResultsText'];
                    }
                    $itemcount = '';
                    $noentries = $nothingFound;
                } else {
                    $noentries = '';
                    $itemcount = ($Start + 1) . ' - ' . $toPos . ' of ' . $Count['Total'] . ' Items';
                }

                $PreReturn = $Config['_ListViewTemplateFooter'];

                $PreReturn = str_replace('{{_footer_prev}}', $prevbutton, $PreReturn);
                $PreReturn = str_replace('{{_footer_next}}', $nextbutton, $PreReturn);
                $PreReturn = str_replace('{{_footer_page_jump}}', $pagejump, $PreReturn);
                $PreReturn = str_replace('{{_footer_item_count}}', $itemcount, $PreReturn);
                $PreReturn = str_replace('{{_footer_no_entries}}', $noentries, $PreReturn);

                $ReportReturn .= $PreReturn;
            }
            if (!empty($Config['_ListViewTemplatePostFooter'])) {
                $ReportReturn .= $Config['_ListViewTemplatePostFooter'];
            }
        }
    }
    //query
    if (is_admin ()) {
        if (!empty($_GET['debug'])) {


            //$ReportReturn .= '<div id="'.$EID.'_queryDebug" class="button" style="cursor:pointer; width:100px; text-align: center;" onclick="jQuery(\'#'.$EID.'_queryDebug_panel\').toggle();">Show Query</div>';
            $ReportReturn .= '<div id="' . $EID . '_queryDebug_panel" style="display:block;">';
            $ReportReturn .= '<textarea style="width:99%; height:200px;">' . $CountQuery . '</textarea><br />';
            $ReportReturn .= '<textarea style="width:99%; height:200px;">' . $Query . '</textarea><br />';
            $ReportReturn .= 'ERRORS: ' . mysql_error();
            //$ReportReturn .= '</div>';
        }
    }
//dump($Config);
// Create Header and Footer Totals
    if (!empty($Config['_TotalsFields'])) {

        if (!empty($Format)) {
            // XML Output
            if (strtolower($Format) == 'xml') {
                //$apiOut .= "		</entry>\n";
            }
            // json Output
            if (strtolower($Format) == 'json') {
                //	$jsonIndex++;
            }
            //PDF Output
            if (strtolower($Format) == 'pdf') {
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
        foreach ($Config['_TotalsFields'] as $Field => $TotalsSet) {

            //dump($TotalsSet);


            if ($TotalsSet['Location'] == 'headerinline') {
                $TotalsSet['Location'] = 'header';
            }
            if ($TotalsSet['Location'] == 'footerinline') {
                $TotalsSet['Location'] = 'footer';
            }
            $box = '<div class="stuffbox" style="width:{{width_' . $TotalsSet['Location'] . '}}%; float:left;">';
            if (!empty($TotalsSet['Title'])) {
                $Title = df_parseCamelCase($TotalsSet['Title']);
            }
            $OutValue = round($TotalsSet[$TotalsSet['Type']], 2);
            if ($TotalsSet['Function'] == 'VAT') {
                $OutValue = totals_vat($OutValue);
            }
            if ($TotalsSet['Function'] == 'AddVAT') {
                $OutValue = totals_addvat($OutValue);
            }

            $Title = df_parseCamelCase($Field);
            $Caption = ' ';
            if (!empty($TotalsSet['Caption'])) {
                $Caption = $TotalsSet['Caption'];
            }
            /// Outputs
            if (!empty($Format)) {
                // XML Output
                if (strtolower($Format) == 'xml') {
                    //	$apiOut .= "		</entry>\n";
                }
                // json Output
                if (strtolower($Format) == 'json') {
                    //	$jsonIndex++;
                }
                //PDF Output
                if (strtolower($Format) == 'pdf') {
                    $apiOutput['Totals'][$Title] = $TotalsSet['Prefix'] . $OutValue . $TotalsSet['Suffix'] . ' ' . $Caption;
                }
            }
            $box .= '<h3>' . $Title . '</h3>';
            $box .= '<div class="inside"><h1>' . $TotalsSet['Prefix'] . $OutValue . $TotalsSet['Suffix'] . '</h1></div>';
            $box .= '<div class="inside">' . $Caption . '</div>';
            $box .= '</div>';
            if (!empty($$TotalsSet['Location'])) {
                $$TotalsSet['Location'] .= $box;
            }
            switch ($TotalsSet['Location']) {
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
    if (!empty($hcount)) {
        if ($hcount >= 1) {
            $header = str_replace('{{width_header}}', (100 / $hcount - 3), $header);
        }
    }
    if (!empty($fcount)) {
        if ($fcount >= 1) {
            $footer = str_replace('{{width_footer}}', (100 / $fcount - 3), $footer);
        }
    }
// Run Final Totals Functions on return data
    if (!empty($GLOBALS['Totals'][$EID])) {
        foreach ($GLOBALS['Totals'][$EID] as $Key => $Output) {
            $Total = $Config['_TotalsFields'][$Output['Field']][$Config['_TotalsFields'][$Output['Field']]['Type']];
            if (!empty($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage'])) {
                if (empty($AverageBar)) {
                    //foreach$Output['PreAverage']
                    $avT = 0;
                    foreach ($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage'] as $val) {
                        $avT = $avT + $val;
                    }
                    $AverageBar = ceil($avT / count($GLOBALS['TotalsAverages'][$EID][$Output['Field']]['PreAverage']));
                }
                $Total = $AverageBar;
            }
            $func = 'totals_' . $Output['Function'];
            $newValue = $func($Output['Value'], $Total);
            $ReportReturn = str_replace($Key, $newValue, $ReportReturn);
        }
    }

// Set Auto Widths to Averages
    if(!empty($AvrageWidth)){
        foreach ($AvrageWidth as $Field => $Value) {
            $Tmp = 0;
            foreach ($Value as $Num) {
                $Tmp = $Tmp + $Num;
            }
            $Av = ceil($Tmp / count($Value));
            if (!empty($minWidth[$Field])) {
                if ($Av < $minWidth[$Field]) {
                    $Av = $minWidth[$Field];
                }
            }
            $Av = '';
            $ReportReturn = str_replace('width="{{width_' . $Field . '}}px"', $Av, $ReportReturn);
        }
    }

//dump($Config);
//echo $Query;
// API Output
    if (!empty($Format)) {
        // XML Output
        if (strtolower($Format) == 'xml') {
            return $apiOut . "\n	</entries>";
        }
        // json Output
        if (strtolower($Format) == 'json') {
            //$apiOutput;
            return json_encode($apiOutput);
        }
        // PDF Output
        if (strtolower($Format) == 'pdf') {
            //$apiOutput;
            return $apiOutput;
        }
    }



    return $header . $ReportReturn . $footer;
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
    //dump($Data);
    $Element = getelement($EID);
    $Config = $Element['Content'];
    //dump($Config);
    // Load Entry's data for hidden values
    $preQuery = mysql_query("SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'");
    $PreData = mysql_fetch_assoc($preQuery);

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
    foreach ($Config['_Field'] as $Field => $Type) {
        $typeSet = explode('_', $Type);
        if (!empty($typeSet[1])) {
            if (function_exists($typeSet[0] . '_handleInput')) {
                $Func = $typeSet[0] . '_handleInput';
                if (is_array($Data[$EID][$Field])) {
                    $Data[$EID][$Field] = serialize($Data[$EID][$Field]);
                } else {
                    if (empty($Data[$EID][$Field])) {
                        $Data[$EID][$Field] = '';
                    }
                }
                $Element['_ActiveProcess'] = 'update';
                $newValue = $Func($Field, $Data[$EID][$Field], $typeSet[1], $Element, $PreData, $Data);
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
    // process update processess
    if (!empty($Config['_FormProcessors'])) {
        foreach ($Config['_FormProcessors'] as $processID => $Setup) {
            if(empty($updateData)){
                if(empty($Config['_InsertFail'])) {
                    $Return['Message'] = 'Entry Update Failed';
                }else {
                    $Return['Message'] = $Config['_UpdateFail'];
                }
                return $Return;
            }

            if (!empty($Setup['_onUpdate'])) {
                if (file_exists(WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php')) {
                    include_once WP_PLUGIN_DIR . '/db-toolkit/data_form/processors/' . $Setup['_process'] . '/functions.php';
                    $func = 'pre_process_' . $Setup['_process'];
                    if (function_exists($func)) {
                        $updateData = $func($updateData, $Setup, $Config);
                        if(!empty($updateData['__fail__'])){
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
    foreach ($updateData as $Field => $newValue) {
        $newData[] = "`" . $Field . "` = '" . mysql_real_escape_string($newValue) . "' ";
    }
    $Updates = implode(', ', $newData);
    $Query = "UPDATE `" . $Config['_main_table'] . "` SET " . $Updates . " WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "'";
    //echo $Query;
    //die;

    if (!empty($Config['_ReturnFields'][0])) {
        $ReturnVals = implode(', ', $Config['_ReturnFields']);
        $outq = mysql_query("SELECT " . $ReturnVals . " FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $Data[$Config['_ReturnFields'][0]] . "';");
        $dta = mysql_fetch_assoc($outq);
        $outstr = array();
        foreach ($dta as $key => $val) {
            $outstr[] = $key . '=' . $val;
        }
        $Return['Value'] = implode('&', $outstr);
    } else {
        $Return['Value'] = $ID;
    }
    //dump($Return);
    //die;
    //$Return['Value'] = $Data[$Config['_ReturnFields'][0]];
    
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
    if (mysql_query($Query)) {
        if (empty($Config['_UpdateSuccess'])) {
            $Return['Message'] = 'Entry updated successfully';
        } else {
            $Return['Message'] = $Config['_UpdateSuccess'];
        }
        return $Return;
    }
    return false;
}

function df_deleteEntries($EID, $Data) {
    $Data = df_cleanArray(explode('|||', $Data));
    $El = getelement($EID);
    $Config = $El['Content'];
    if (!empty($RefConfig['Field'])) {
        if (in_array('imageupload', $RefConfig['Field'])) {
            $ImagesToDelete = array_keys($RefConfig['Field'], 'imageupload');
        }
    }
    $Index = 0;
    $Return = '';
    foreach ($Data as $ID) {
        $ID = str_replace($EID . '_', '', $ID);
        $Pre = mysql_query("SELECT * FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "' LIMIT 1;");
        dr_trackActivity('Delete', $EID, $ID);
        $OldData = mysql_fetch_assoc($Pre);
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
        mysql_query("DELETE FROM `" . $Config['_main_table'] . "` WHERE `" . $Config['_ReturnFields'][0] . "` = '" . $ID . "' LIMIT 1;");

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
    if (index > 1) {
        $Note = 'Items';
    }
    return $Index . ' ' . $Note . ' Deleted<br />';
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

    $default = '';
    if (!empty($_SESSION['activeApp'])) {
        $default = $_SESSION['activeApp'];
    }

    $appList = get_option('dt_int_Apps');

    $Return = '<div style="float:left; width: 25%;">';
    $Return .= '<h3>Application</h3>';
    $Return .= '<select id="dbtoolkit_AppList">';
    $Return .= '<option value=""></option>';
    foreach ($appList as $app => $state) {
        $Sel = '';
        if ($default == $app)
            $Sel = 'selected="selected"';

        if ($state == 'open')
            $Return .= '<option value="' . $app . '" ' . $Sel . '>' . ucwords($app) . '</option>';
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
    global $wpdb;
    $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);

    $Return = '<h3>' . $App . '</h3>';

    foreach ($interfaces as $interface) {
        $dta = get_option($interface['option_name']);
        if ($dta['_Application'] == $App) {
            if (empty($dta['_ItemGroup'])) {
                $Group = '<em>ungrouped</em>';
            } else {
                $Group = $dta['_ItemGroup'];
            }
            $interfaceGroups[$Group][] = $dta;
        }
    }
    if (empty($interfaceGroups)) {
        $Return .= '<div style="padding:3px;" class="highlight">No interfaces</div>';
        return $Return;
    }
    foreach ($interfaceGroups as $group => $Interface) {

        $Return .= '<div style="padding:3px;" class="highlight">' . $group . '</div>';
        foreach ($Interface as $dta) {
            if ($dta['_Application'] == $App) {

                if ($GroupRun != $group) {

                    $GroupRun = $group;
                }

                $Return .= '<div style="padding:5px;">';
                $Return .= '<span class="interfaceInserter" style="cursor:pointer;" id="' . $dta['ID'] . '">' . $dta['_ReportDescription'] . '</span>';
                if (!empty($dta['_ReportExtendedDescription'])) {
                    $Return .= '<div><span class="description interfaceInserter" style="cursor:pointer;" id="' . $dta['ID'] . '">' . $dta['_ReportExtendedDescription'] . '</span></div>';
                }
                $Return .= '</div>';
            }
        }
    }
    return $Return;
}


function dr_addListRowTemplate($Default = false){



ob_start();
                $show = 'block';
                if(!empty($Default)){
                    $show = 'none';
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


?>