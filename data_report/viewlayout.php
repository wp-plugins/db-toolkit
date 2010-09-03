<div id="tabs-2b">
    <div id="viewtabs">
        <ul class="content-box-tabs">
            <li><a href="#gridview">Grid View</a></li>
            <li><a href="#templateview">Template</a></li>
        </ul>
        <div id="gridview">
            <?php
            InfoBox('View Layout');
            ?>
            <div style="margin-top: 20px; padding: 5px;" class="ui-state-highlight ui-corner-all">
                <input type="button" value="Sync Fields" onclick="viewsSetup_getFields();" />
                &nbsp;
                <input type="button" value="Insert Row" onclick="viewSetup_AddRow();" />
                <input type="button" id="AddSection" value="Add Section Break" onclick="dr_addSectionBreak('view');" />
				Popup View Width:
                <input type="text" id="_popupWidth" name="Data[Content][_popupWidthview]" value="<?php if(!empty($Element['Content']['_popupWidthview'])) {
                    echo $Element['Content']['_popupWidthview'];
                }else {
                    echo '450';
                       } ?>" size="5" maxlength="4" />
				px
                <input type="checkbox" id="_modalPopup" name="Data[Content][_popupTypeView]" value="modal" <?php if(!empty($Element['Content']['_popupTypeView'])) {
                    echo 'checked="checked"';
                       } ?> /> <label for="_modalPopup">Modal</label>

            </div>
            <div id="viewGridview">
                <?php

                parse_str($Element['Content']['_gridLayoutView'], $LayoutView);
                $view = '';
                $Hidden = '';
                $CurrRow = '0';
                $CurrCol = '0';
                $Index = 1;
                $SubIndex = 0;
                foreach($LayoutView as $LayoutViewField => $Grid) {
                    $Grid = explode('_', $Grid);
                    if(substr($Grid[0],0,7) == 'viewrow') {
                        $SetupView[$Grid[0]][$Grid[1]]['Fields'][]['Name'] = str_replace('Field_','',$LayoutViewField);
                        $SetupView[$Grid[0]][$Grid[1]]['Width'] = $Grid[2];
                        $SubIndex++;
                    }else {
                        $SetupView[$Grid[0]][] = $LayoutViewField;
                    }
                }
                if(!empty($SetupView)) {
                    foreach($SetupView as $preRow=>$Columns) {
                        $Row = 'void';
                        if(substr($preRow,0,7) == 'viewrow') {
                            $Row = 'viewrow'.$Index;
                            $view .= '<div class="rowWrapperView">';
                            $view .= '<div style="clear:both; width:90%; float:left;" class="viewRow" id="'.$Row.'">';
                            foreach($Columns as $Column=>$Field) {
                                $view .= '<div style="padding:0; margin:0; width:'.$Field['Width'].'; float:left;" id="'.$Row.'_'.$Column.'" class="column">';
                                $view .= '<div class="ui-state-error viewGridview viewColumn" style="padding:10px; margin:10px;">';
                                foreach($Field['Fields'] as $FieldSet) {
                                    $Title = df_parseCamelCase($FieldSet['Name']);
                                    if(!empty($Element['Content']['_FieldTitle'][$FieldSet['Name']])) {
                                        if(substr($FieldSet['Name'], 0, 2) == '__') {
                                            $Title = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/copy.png" width="16" height="16" align="absmiddle" /> '.$Element['Content']['_FieldTitle'][$FieldSet['Name']];
                                        }else {
                                            $Title = $Element['Content']['_FieldTitle'][$FieldSet['Name']];
                                        }
                                    }
                                    if(substr($FieldSet['Name'],0,13) != '_SectionBreak') {
                                        $FieldName = 'Field_'.$FieldSet['Name'];
                                        $view .= '<div class="viewportlet" id="viewportlet_Field_'.$FieldSet['Name'].'"><div class="viewportlet-header">'.$Title.'<input class="layOutview positioning" type="hidden" name="'.$FieldName.'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/><span class="ui-icon ui-icon-close"></span></div></div>';
                                    }else {
                                        //dump($Element['Content']['_SectionBreak'][$FieldSet['Name']]['Title']);
                                        $FieldName = $Element['Content']['_SectionBreak'][$FieldSet['Name']]['Title'];
                                        $FieldCaption = $Element['Content']['_SectionBreak'][$FieldSet['Name']]['Caption'];
                                        //$view .= '<div class="viewportlet" id="viewportlet_Field_'.$FieldSet['Name'].'"><div class="viewportlet-header">'.$Title.'<input class="layOutview positioning" type="hidden" name="'.$FieldName.'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/><span class="ui-icon ui-icon-close"></span></div></div>';
                                        $view .= '<div style="padding: 3px;" class="list_row4 table_sorter sectionBreak viewportlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="SectionBreak_SectionBreak\'+number+\'"><div class="viewportlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/arrow_out.png" style="-moz-user-select: none;"/>&nbsp;<input type="text" class="sectionTitle" name="Data[Content][_SectionBreak]['.$FieldSet['Name'].'][Title]" value="'.$FieldName.'" /><span class="ui-icon ui-icon-close"></span></div><div style="padding:3px;">Caption: <input type="text" class="sectionTitle" name="Data[Content][_SectionBreak]['.$FieldSet['Name'].'][Caption]" value="'.$FieldCaption.'" /></div><input class="layOutview positioning" type="hidden" name="'.$FieldSet['Name'].'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/></div>';
                                    }
                                }
                                $view .= '</div>';
                                $view .= '</div>';
                            }
                            $view .= '</div>';
                            $view .= ' <div style="width:10%; padding-top:12px; float:left;" class="viewRow" id="'.$Row.'Control">';
                            $view .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/cog.png" style="cursor:pointer;" width="16" height="16" onclick="viewSetupColumns(\''.$Row.'\');" /><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/add.png" style="cursor:pointer;" width="16" height="16" onclick="viewAddColumn(\''.$Row.'\');" /><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/delete.png" style="cursor:pointer;" width="16" height="16" onclick="viewSubtractColumn(\''.$Row.'\');" />';
                            if($Index > 1) {
                                $view .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/no.png" style="cursor:pointer;" width="16" height="16" onclick="viewRemoveColumns(\''.$Row.'\');" />';
                            }
                            $view .= '</div>';

                            $view .= '</div>';
                        }else {
                            foreach($Columns as $HiddenFields) {
                                $FieldName = str_replace('Field_', '', $HiddenFields);
                                $Title = df_parseCamelCase($FieldName);
                                $Hidden .= '<div class="viewportlet" id="viewportlet_Field_'.$FieldName.'"><div class="viewportlet-header">'.$Title.'<input class="layOutview positioning" type="hidden" name="Field_'.$FieldName.'" id="field_'.$FieldName.'" value="'.$Row.'_hidden"/><span class="ui-icon ui-icon-close"></span></div></div>';
                            }
                        }
                        $Index++;
                    }
                }else {
                    echo '<p>Running in auto generation</p>';
                }
                echo $view;

                ?>
            </div>
            <div style="clear:both; width:350px;"><br />
                <br />
                <?php echo InfoBox('Available Fields'); ?>
                <div style="padding:10px;" class="viewGridview" id="fieldTrayview">
                    <?php
                    echo $Hidden;
                    ?>
                </div>
                <?php
                EndInfoBox();
                ?>
            </div>
            <?php
            EndInfoBox();
            ?>
            <input type="checkbox" name="Data[Content][_disableLayoutEngineview]" id="disableLayoutEngineview" <?php if(!empty($Element['Content']['_disableLayoutEngineview'])) {
                echo 'checked="checked"';
                   } ?>/>
            <label for="disableLayoutEngineview"> Disable Layout Engine</label>
            <input name="Data[Content][_gridLayoutView]" type="hidden" id="gridLayoutBoxView" value="<?php echo $Element['Content']['_gridLayoutView']; ?>" size="100" <?php if(!empty($element['content']['_disablelayoutengine'])) {
                echo 'disabled="disabled"';
                   } ?>="<?php if(!empty($Element['Content']['_disableLayoutEngine'])) {
                       echo 'disabled="disabled"';
                   } ?>" />
        </div>
        <div id="templateview">

            <?php

            $Sel = '';
            if(!empty($Element['Content']['_UseViewTemplate'])) {
                $Sel = 'checked="checked"';
            }
            echo dais_customfield('checkbox', 'Use View Template', '_UseViewTemplate', '_UseViewTemplate', 'list_row1' , 1, $Sel);

            echo dais_customfield('textarea', 'Content Wrapper Start', '_ViewTemplateContentWrapperStart', '_ViewTemplateContentWrapperStart', 'list_row2' , $Element['Content']['_ViewTemplateContentWrapperStart'], '');
            echo dais_customfield('textarea', 'PreContent', '_ViewTemplatePreContent', '_ViewTemplatePreContent', 'list_row2' , $Element['Content']['_ViewTemplatePreContent'], '');
            echo dais_customfield('textarea', 'Content', '_ViewTemplateContent', '_ViewTemplateContent', 'list_row2' , $Element['Content']['_ViewTemplateContent'], '');
            InfoBox('Useable Keys');
            ?>
            <pre>
{{_PageID}}		: Page ID
{{_PageName}}		: Page Name
{{_EID}}		: Element ID
{{_<i>Fieldname</i>_name}}	: Field Title
{{<i>Fieldname</i>}}		: Field Data
{{_return_<i><b>Fieldname</b></i>}}	: Return Field
            </pre>

            Field Keys:
            <?php
            foreach($Element['Content']['_FieldTitle'] as $FieldKey=>$Val) {
                echo $Val.' = {{'.$FieldKey.'}}<br />';
            }
            EndInfoBox();
            echo dais_customfield('textarea', 'PostContent', '_ViewTemplatePostContent', '_ViewTemplatePostContent', 'list_row2' , $Element['Content']['_ViewTemplatePostContent'], '');
            echo dais_customfield('textarea', 'Content Wrapper End', '_ViewTemplateContentWrapperEnd', '_ViewTemplateContentWrapperEnd', 'list_row2' , $Element['Content']['_ViewTemplateContentWrapperEnd'], '');



            ?>



        </div>
    </div>
</div>
<script>
    jQuery(function() {
        jQuery("#viewtabs").tabs();
        jQuery('#disableLayoutEngineview').bind('change', function(ui, e){

            if(jQuery(this).attr('checked') == true){
                jQuery('#gridLayoutBoxView').attr('disabled', 'disabled');
            }else{
                jQuery('#gridLayoutBoxView').removeAttr('disabled');
            }

        });

        jQuery(".columnSorter").sortable({
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            connectWith: '.columnSorter',
            stop: function(p){
                //alert(columns);
                viewSetup_columSave();
            }

        });
        jQuery(".viewportlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all").find(".viewportlet-header").addClass("ui-widget-header ui-corner-all");
        jQuery(".viewportlet-header .ui-icon").click(function() {
            jQuery(this).toggleClass("ui-icon-minusthick");
            jQuery(this).parents(".viewportlet:first").remove();
            viewSetup_columSave();
        });

        viewSetup_columSave();
        jQuery(".viewGridview").sortable({
            connectWith: '.viewGridview',
            update: function(event, ui){
                jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
                viewSetup_columSave();
            }
        });
        jQuery(".viewGridview").disableSelection();

    });

    function viewsSetup_getFields(){

        //jQuery('#fieldTrayview').html('');
        //jQuery('.viewColumn').html('');
        jQuery('#FieldList_Main .table_sorter').each(function(){
            if(jQuery('#viewportlet_'+this.id).length == 0){
                title = jQuery(this).find('h3').html();
                jQuery('#fieldTrayview').append('<div class="viewportlet" id="viewportlet_'+this.id+'"><div class="viewportlet-header">'+title+'<input class="layOutview positioning" type="hidden" name="'+this.id+'" id="field_'+this.id+'" value="1"/><span class="ui-icon ui-icon-close"></span></div></div>');
            }
        });
        jQuery(".viewportlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
        .find(".viewportlet-header").addClass("ui-widget-header ui-corner-all");
        jQuery(".viewportlet-header .ui-icon").click(function() {
            jQuery(this).toggleClass("ui-icon-minusthick");
            jQuery(this).parents(".viewportlet:first").remove();
        });
    }

    function viewSetup_columSave(){
        jQuery('#gridLayoutBoxView').val(jQuery('.layOutview').serialize());
    }

    function viewSetup_AddRow(){
        rownum = jQuery('.rowWrapperView').length+1;
        //alert(rownum);
        //.each(function(){
        //	alert(jQuery(this).length);
        //});


        jQuery('#viewGridview').append('<div class="rowWrapperView"><div style="clear:both; width:90%; float:left;" class="viewRow" id="viewrow'+rownum+'"><div style="padding:0; margin:0; width:100%; float:left;" id="viewrow'+rownum+'_col1" class="column"><div class="ui-state-error viewGridview viewColumn" style="padding:10px; margin:10px;"></div></div></div><div style="width:10%; padding-top:12px; float:left;" class="viewRow" id="viewrow'+rownum+'Control"><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/cog.png" style="cursor:pointer;" width="16" height="16" onclick="viewSetupColumns(\'viewrow'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/add.png" style="cursor:pointer;" width="16" height="16" onclick="viewAddColumn(\'viewrow'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/delete.png" style="cursor:pointer;" width="16" height="16" onclick="viewSubtractColumn(\'viewrow'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/no.png" style="cursor:pointer;" width="16" height="16" onclick="viewRemoveColumns(\'viewrow'+rownum+'\');" /></div></div>');
        jQuery(".viewGridview").sortable({
            connectWith: '.viewGridview',
            update: function(event, ui){
                jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
                viewSetup_columSave();
            }
        });
        jQuery(".viewGridview").disableSelection();
    }

    function viewSetupColumns(row){
        jQuery('body').prepend('<div id="ui-jsDialog-'+row+'" title="Row Config"><p>Loading Entry</p></div>');
        jQuery("#ui-jsDialog-"+row+"").dialog({
            position: 'center',
            autoResize: true,
            minWidth: 200,
            buttons: {
                'Save': function() {jQuery(this).dialog("close"); }
            },
            open: function(event, ui) {
                jQuery("#ui-jsDialog-"+row+"").dialog('option', 'title', row);
                jQuery("#ui-jsDialog-"+row+"").html('');
                jQuery('#'+row+' .column').each(function(){
                    jQuery("#ui-jsDialog-"+row+"").append('<div><input type="text" class="setting_'+row+'" ref="'+this.id+'" id="column234234" value="'+jQuery(this).css('width')+'" /></div>');
                });
            },
            close: function(event, ui) {
                jQuery('.setting_'+row).each(function(){
                    jQuery('#'+jQuery(this).attr('ref')).css('width', this.value);
                    jQuery('#'+jQuery(this).attr('ref')).each(function(){
                        jQuery(this).find(".positioning").val(jQuery(this).attr('id')+'_'+jQuery(this).css('width'));
                    });
                });
                viewSetup_columSave();
                jQuery("#ui-jsDialog-"+row+"").remove();
            }
        });
    }

    function viewAddColumn(row){
        cols = jQuery('#'+row+' .column').length+1;
        width = 100/cols;
        //jQuery('#'+row).html('');
        //for(i=1; i<= cols; i++){
        //}
        //jQuery('#view'+row+' .column').each(function(){
        jQuery('#'+row+' .column').animate({'width' : width+'%'},1, function(){
            //alert('ping');
            if(this.id == row+'_col'+(cols-1)){
                jQuery('#'+row).append('<div style="padding:0; margin:0; width:'+width+'%; float:left;" id="'+row+'_col'+cols+'" class="column"><div class="ui-state-error viewGridview viewColumn" style="padding:10px; margin:10px;"></div></div>');
                jQuery(".viewGridview").sortable({
                    connectWith: '.viewGridview',
                    update: function(event, ui){
                        jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
                        viewSetup_columSave();
                    }
                });
                jQuery(".viewGridview").disableSelection();
            }
            jQuery(this).find(".positioning").val(jQuery(this).attr('id')+'_'+jQuery(this).css('width'));
            viewSetup_columSave();
        });

    }
    function viewSubtractColumn(row){
        cols = jQuery('#'+row+' .column').length-1;
        if(cols <= 0){
            return false;
        }
        width = 100/cols;
        //jQuery('#'+row).html('');
        //for(i=1; i<= cols; i++){
        //jQuery('#'+row).append('<div style="padding:0; margin:0; width:'+width+'%; float:left;" id="'+row+'_col'+i+'" class="column"><div class="ui-state-error viewGridview viewColumn" style="padding:10px; margin:10px;"></div></div>');
        jQuery('#'+row+'_col'+(cols+1)+' .viewportlet').appendTo('#'+row+'_col'+cols+' .viewColumn');
        //jQuery('#view'+row+'_col'+(cols+1)).fadeOut(100, function(){
        jQuery('#'+row+'_col'+(cols+1)).remove();
        jQuery('#'+row+' .column').animate({'width' : width+'%'}, 1, function(){
            jQuery(this).find(".positioning").val(jQuery(this).attr('id')+'_'+jQuery(this).css('width'));
            viewSetup_columSave();
        });
        //});
        //}
        jQuery(".viewGridview").sortable({
            connectWith: '.viewGridview',
            update: function(event, ui){
                jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
                viewSetup_columSave();
            }
        });
        jQuery(".viewGridview").disableSelection();
    }
    function viewRemoveColumns(row){
        if(jQuery('#view'+row+' .viewportlet').length != 0){
            alert('Cannot remove. Row not empty.');
        }else{
            jQuery('#'+row).fadeOut(200, function(){
                jQuery(this).parent().remove();
            });
        }
    }

</script>
