  <div id="tabs-2" class="setupTab">
	<?php
	InfoBox('Form Layout');
	?>
    <div style="margin-top: 20px; padding: 5px;" class="ui-state-highlight ui-corner-all"> 
    <input type="button" class="button" value="Sync Fields" onclick="formsSetup_getFields();" />
    <input type="button" class="button" value="Insert Row" onclick="formSetup_AddRow();" />
    <input type="button" class="button" id="AddSection" value="Add Section Break" onclick="dr_addSectionBreak('form');" />
    <input type="button" class="button" id="AddTab" value="Add Tab" onclick="dr_addTab('form');" />
    Popup Form Width: <input type="text" id="_popupWidth" name="Data[Content][_popupWidth]" value="<?php if(!empty($Element['Content']['_popupWidth'])){ echo $Element['Content']['_popupWidth'];}else{ echo '450';} ?>" size="5" maxlength="4" />px
    <input type="checkbox" id="_modalPopup" name="Data[Content][_popupTypeForm]" value="modal" <?php if(!empty($Element['Content']['_popupTypeForm'])) {
                    echo 'checked="checked"';
                       } ?> /> <label for="_modalPopup">Modal</label>
    <?php
    /*
    <input type="checkbox" id="_ajaxForms" name="Data[Content][_ajaxForms]" value="1" <?php if(!empty($Element['Content']['_ajaxForms'])) {
                    echo 'checked="checked"';
                       } ?> /> <label for="_ajaxForms">Ajax Form</label>\
     
     */
    ?>
    </div>

	
	<div id="formGridform">
    <?php
	
	parse_str($Element['Content']['_gridLayout'], $LayoutForm);
	$Form = '';
	$Hidden = '';
	$CurrRow = '0';
	$CurrCol = '0';
	$Index = 1;
	$SubIndex = 0;
	foreach($LayoutForm as $LayoutFormField => $Grid){
		$Grid = explode('_', $Grid);
		if(substr($Grid[0],0,3) == 'row'){
			$Setup[$Grid[0]][$Grid[1]]['Fields'][]['Name'] = str_replace('Field_','',$LayoutFormField);
			$Setup[$Grid[0]][$Grid[1]]['Width'] = $Grid[2];
			$SubIndex++;
		}else{
			$Setup[$Grid[0]][] = $LayoutFormField;
		}
	}
	if(!empty($Setup)){
		foreach($Setup as $preRow=>$Columns){
		$Row = 'void';
		if(substr($preRow,0,3) == 'row'){
		$Row = 'row'.$Index;
			$Form .= '<div class="rowWrapperForm">';
				$Form .= '<div style="clear:both; width:90%; float:left;" class="formRow" id="'.$Row.'">';
					foreach($Columns as $Column=>$Field){
						$Form .= '<div style="padding:0; margin:0; width:'.$Field['Width'].'; float:left;" id="'.$Row.'_'.$Column.'" class="column">';
						$Form .= '<div class="ui-state-error formGridform formColumn" style="padding:10px; margin:10px;">';
							foreach($Field['Fields'] as $FieldSet){
								$Title = df_parseCamelCase($FieldSet['Name']);
                                                                

                                                                if(!empty($Element['Content']['_FieldTitle'][$FieldSet['Name']])){
                                                                    if(substr($FieldSet['Name'], 0, 2) == '__'){
                                                                        $Title = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/copy.png" width="16" height="16" align="absmiddle" /> '.$Element['Content']['_FieldTitle'][$FieldSet['Name']];
                                                                    }else{
                                                                        $Title = $Element['Content']['_FieldTitle'][$FieldSet['Name']];
                                                                    }
                                                                }
                                                                //vardump($FieldSet);
								if(substr($FieldSet['Name'],0,13) != '_SectionBreak' && substr($FieldSet['Name'],0,4) != '_tab'){
									$FieldName = 'Field_'.$FieldSet['Name'];
									$Form .= '<div class="formportlet" id="formportlet_Field_'.$FieldSet['Name'].'"><div class="formportlet-header">'.$Title.'<input class="layOutform positioning" type="hidden" name="'.$FieldName.'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/><span class="ui-icon ui-icon-close"></span></div></div>';
								}else{
                                                                        if(substr($FieldSet['Name'],0,4) == '_tab'){
                                                                            //dump($Element['Content']['_SectionBreak'][$FieldSet['Name']]['Title']);
                                                                            $FieldName = $Element['Content']['_Tab'][$FieldSet['Name']]['Title'];
                                                                            //$Form .= '<div class="formportlet" id="formportlet_Field_'.$FieldSet['Name'].'"><div class="formportlet-header">'.$Title.'<input class="layOutform positioning" type="hidden" name="'.$FieldName.'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/><span class="ui-icon ui-icon-close"></span></div></div>';
                                                                            $Form .= '<div style="padding: 3px;" class="list_row4 table_sorter tab formportlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="tab_tab\'+number+\'"><div class="formportlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/arrow_out.png" style="-moz-user-select: none;"/>&nbsp;<input type="text" class="sectionTitle" name="Data[Content][_Tab]['.$FieldSet['Name'].'][Title]" value="'.$FieldName.'" /><span class="ui-icon ui-icon-close"></span></div><input class="layOutform positioning" type="hidden" name="'.$FieldSet['Name'].'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/></div>';
                                                                        }else{
                                                                            //dump($Element['Content']['_SectionBreak'][$FieldSet['Name']]['Title']);
                                                                            $FieldName = $Element['Content']['_SectionBreak'][$FieldSet['Name']]['Title'];
                                                                            $FieldCaption = $Element['Content']['_SectionBreak'][$FieldSet['Name']]['Caption'];
                                                                            //$Form .= '<div class="formportlet" id="formportlet_Field_'.$FieldSet['Name'].'"><div class="formportlet-header">'.$Title.'<input class="layOutform positioning" type="hidden" name="'.$FieldName.'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/><span class="ui-icon ui-icon-close"></span></div></div>';
                                                                            $Form .= '<div style="padding: 3px;" class="list_row4 table_sorter sectionBreak formportlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="SectionBreak_SectionBreak\'+number+\'"><div class="formportlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/arrow_out.png" style="-moz-user-select: none;"/>&nbsp;<input type="text" class="sectionTitle" name="Data[Content][_SectionBreak]['.$FieldSet['Name'].'][Title]" value="'.$FieldName.'" /><span class="ui-icon ui-icon-close"></span></div><div style="padding:3px;">Caption: <input type="text" class="sectionTitle" name="Data[Content][_SectionBreak]['.$FieldSet['Name'].'][Caption]" value="'.$FieldCaption.'" /></div><input class="layOutform positioning" type="hidden" name="'.$FieldSet['Name'].'" id="'.$FieldSet['Name'].'" value="'.$Row.'_'.$Column.'_'.$Field['Width'].'"/></div>';
                                                                        }
                                                                }
							}
						$Form .= '</div>';						
						$Form .= '</div>';
					}
				$Form .= '</div>';
				$Form .= ' <div style="width:10%; padding-top:12px; float:left;" class="formRow" id="'.$Row.'Control">';
					$Form .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/cog.png" style="cursor:pointer;" width="16" height="16" onclick="formSetupColumns(\''.$Row.'\');" /><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/add.png" style="cursor:pointer;" width="16" height="16" onclick="formAddColumn(\''.$Row.'\');" /><img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/delete.png" style="cursor:pointer;" width="16" height="16" onclick="formSubtractColumn(\''.$Row.'\');" />';
					if($Index > 1){
						$Form .= '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/no.png" style="cursor:pointer;" width="16" height="16" onclick="formRemoveColumns(\''.$Row.'\');" />';	
					}
				$Form .= '</div>';
				
			$Form .= '</div>';
		}else{
			foreach($Columns as $HiddenFields){
				$FieldName = str_replace('Field_', '', $HiddenFields);
				$Title = df_parseCamelCase($FieldName);
				$Hidden .= '<div class="formportlet" id="formportlet_Field_'.$FieldName.'"><div class="formportlet-header">'.$Title.'<input class="layOutform positioning" type="hidden" name="Field_'.$FieldName.'" id="field_'.$FieldName.'" value="'.$Row.'_hidden"/><span class="ui-icon ui-icon-close"></span></div></div>';
			}
		}
	$Index++;
	}
	}else{
		echo '<p>Running in auto generation</p>';
	}
	echo $Form;
	
	?>
	</div>
	<div style="clear:both; width:350px;"><br /><br />
	
        <?php echo InfoBox('Available Fields'); ?>
        <div style="padding:10px;" class="formGridform" id="fieldTrayform">
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
    <input type="checkbox" name="Data[Content][_disableLayoutEngineform]" id="disableLayoutEngineform" <?php if(!empty($Element['Content']['_disableLayoutEngineform'])){ echo 'checked="checked"';} ?>/>
    <label for="disableLayoutEngineform"> Disable Layout Engine</label>
	<input name="Data[Content][_gridLayout]" type="hidden" id="gridLayoutBoxForm" value="<?php echo $Element['Content']['_gridLayout']; ?>" size="100" <?php if(!empty($element['content']['_disablelayoutengine'])){ echo 'disabled="disabled"';} ?>="<?php if(!empty($Element['Content']['_disableLayoutEngine'])){ echo 'disabled="disabled"';} ?>" />
	</div>
<script>
	jQuery(function() {
		
		jQuery('#disableLayoutEngineform').bind('change', function(ui, e){
		
			if(jQuery(this).attr('checked') == true){
				jQuery('#gridLayoutBoxForm').attr('disabled', 'disabled');	
			}else{
				jQuery('#gridLayoutBoxForm').removeAttr('disabled');
			}
		
		});
			   
		
		jQuery(".columnSorter").sortable({
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			connectWith: '.columnSorter',
			stop: function(p){
				//alert(columns);
				formSetup_columSave();
			}
		
		});
			jQuery(".formportlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all").find(".formportlet-header").addClass("ui-widget-header ui-corner-all");
			jQuery(".formportlet-header .ui-icon").click(function() {
				jQuery(this).toggleClass("ui-icon-minusthick");
				jQuery(this).parents(".formportlet:first").remove();
			});

		formSetup_columSave();
		
		jQuery(".formGridform").sortable({
			connectWith: '.formGridform',
			update: function(event, ui){
				jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
				formSetup_columSave();
			}
		});
		jQuery(".formGridform").disableSelection();
		
	});

function formsSetup_getFields(){
	
	//jQuery('#fieldTrayform').html('');
	//jQuery('.formColumn').html('');
	jQuery('#FieldList_Main .table_sorter').each(function(){
		if(jQuery('#formportlet_'+this.id).length == 0){
			title = jQuery(this).find('h3').html();
			jQuery('#fieldTrayform').append('<div class="formportlet" id="formportlet_'+this.id+'"><div class="formportlet-header">'+title+'<input class="layOutform positioning" type="hidden" name="'+this.id+'" id="field_'+this.id+'" value="1"/><span class="ui-icon ui-icon-close"></span></div></div>');
		}
	});
	jQuery(".formportlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
		.find(".formportlet-header").addClass("ui-widget-header ui-corner-all");
		jQuery(".formportlet-header .ui-icon").click(function() {
			jQuery(this).toggleClass("ui-icon-minusthick");
			jQuery(this).parents(".formportlet:first").remove();
                        formSetup_columSave();
		});
}

function formSetup_columSave(){
	jQuery('#gridLayoutBoxForm').val(jQuery('.layOutform').serialize());
}

function formSetup_AddRow(){
	rownum = jQuery('.rowWrapperForm').length+1;
	//alert(rownum);
	//.each(function(){
	//	alert(jQuery(this).length);
	//});
	
	
	jQuery('#formGridform').append('<div class="rowWrapperForm"><div style="clear:both; width:90%; float:left;" class="formRow" id="row'+rownum+'"><div style="padding:0; margin:0; width:100%; float:left;" id="row'+rownum+'_col1" class="column"><div class="ui-state-error formGridform formColumn" style="padding:10px; margin:10px;"></div></div></div><div style="width:10%; padding-top:12px; float:left;" class="formRow" id="row1Control"><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/cog.png" style="cursor:pointer;" width="16" height="16" onclick="formSetupColumns(\'row'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/add.png" style="cursor:pointer;" width="16" height="16" onclick="formAddColumn(\'row'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/delete.png" style="cursor:pointer;" width="16" height="16" onclick="formSubtractColumn(\'row'+rownum+'\');" /><img src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/data_report/no.png" style="cursor:pointer;" width="16" height="16" onclick="formRemoveColumns(\'row'+rownum+'\');" /></div></div>');
		jQuery(".formGridform").sortable({
			connectWith: '.formGridform',
			update: function(event, ui){
				jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
				formSetup_columSave();
			}
		});
		jQuery(".formGridform").disableSelection();
}

function formSetupColumns(row){
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
				formSetup_columSave();
				jQuery("#ui-jsDialog-"+row+"").remove();
			}										
		});
}

function formAddColumn(row){
	
	cols = jQuery('#'+row+' .column').length+1;
	width = 100/cols;
	//jQuery('#'+row).html('');
	//for(i=1; i<= cols; i++){
	//}
	//jQuery('#'+row+' .column').each(function(){
		jQuery('#'+row+' .column').animate({'width' : width+'%'},1, function(){
		//alert('ping');
			if(this.id == row+'_col'+(cols-1)){
				jQuery('#'+row).append('<div style="padding:0; margin:0; width:'+width+'%; float:left;" id="'+row+'_col'+cols+'" class="column"><div class="ui-state-error formGridform formColumn" style="padding:10px; margin:10px;"></div></div>');
				jQuery(".formGridform").sortable({
					connectWith: '.formGridform',
					update: function(event, ui){
						jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
						formSetup_columSave();
					}
				});
				jQuery(".formGridform").disableSelection();
			}
			jQuery(this).find(".positioning").val(jQuery(this).attr('id')+'_'+jQuery(this).css('width'));
			formSetup_columSave();
		});
	
}
function formSubtractColumn(row){
	cols = jQuery('#'+row+' .column').length-1;
	if(cols <= 0){
		return false;	
	}
	width = 100/cols;
	//jQuery('#'+row).html('');
	//for(i=1; i<= cols; i++){
	//jQuery('#'+row).append('<div style="padding:0; margin:0; width:'+width+'%; float:left;" id="'+row+'_col'+i+'" class="column"><div class="ui-state-error formGridform formColumn" style="padding:10px; margin:10px;"></div></div>');
	jQuery('#'+row+'_col'+(cols+1)+' .formportlet').appendTo('#'+row+'_col'+cols+' .formColumn');
	//jQuery('#'+row+'_col'+(cols+1)).fadeOut(100, function(){
		jQuery('#'+row+'_col'+(cols+1)).remove();
		jQuery('#'+row+' .column').animate({'width' : width+'%'}, 1, function(){
			jQuery(this).find(".positioning").val(jQuery(this).attr('id')+'_'+jQuery(this).css('width'));
			formSetup_columSave();
		});
	//});
	//}
	jQuery(".formGridform").sortable({
		connectWith: '.formGridform',
		update: function(event, ui){
			jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
			formSetup_columSave();
		}
	});
	jQuery(".formGridform").disableSelection();
}
function formRemoveColumns(row){
	if(jQuery('#'+row+' .formportlet').length != 0){
		alert('Cannot remove. Row not empty.');	
	}else{
		jQuery('#'+row).fadeOut(200, function(){
			jQuery(this).parent().remove();
		});
	}
}	

</script>
