<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
//$Data['_main_table'], $ElementID, $Field, $Data[$Data[$Field]]['Type'], false, $Req

if($FieldSet[1] == 'linked'){
	switch($Config['_Linkedfields'][$Field]['Type']){
		case "checkbox":
                    
				$concatvalues = array();
				foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
					$concatvalues[] = $outValue;
				}
				$outString = 'CONCAT('.implode(',\' \',',$concatvalues).') as outValue';
				$Query = "SELECT ".$Config['_Linkedfields'][$Field]['ID'].", ".$outString." FROM `".$Config['_Linkedfields'][$Field]['Table']."` ORDER BY `".$Config['_Linkedfields'][$Field]['Value'][0]."` ASC;";
			$Res = mysql_query($Query);
			//$Return = '<select name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'">';
			//if(empty($Defaults[$Field])){
				//$Return .= '<option value=""></option>';
			//}
                        //$DefaultChecks = explode(',' $Defaults[$Field]);
			$checkindex = 0;
			$Return = '';
			while($lrow = mysql_fetch_assoc($Res)){
				$Sel = '';
				if(!empty($Defaults[$Field])){
					$DefaultArray = core_cleanArray(explode(',',$Defaults[$Field]));
					if(in_array($lrow[$Config['_Linkedfields'][$Field]['ID']], $DefaultArray)){
						$Sel = 'checked="checked"';
					}
				}
				$Return .= '<div style="width:32%; float:left;"><label for="entry_'.$Element['ID'].'_'.$Field.'_'.$checkindex.'"><input type="checkbox" value="'.$lrow[$Config['_Linkedfields'][$Field]['ID']].'" name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'_'.$checkindex.'" class="'.$Req.'" '.$Sel.' />'.$lrow['outValue'].'</label></div>';
				$checkindex++;
			}
			//$Return .= '</select>';
		$Return .= '<div style="clear:both;"></div>';	
		break;
		case "multiselect":
				$concatvalues = array();
				foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
					$concatvalues[] = $outValue;
				}
				$outString = 'CONCAT('.implode(',\' \',',$concatvalues).') as outValue';
				$Query = "SELECT ".$Config['_Linkedfields'][$Field]['ID'].", ".$outString." FROM `".$Config['_Linkedfields'][$Field]['Table']."` ORDER BY `".$Config['_Linkedfields'][$Field]['Value'][0]."` ASC;";
			$Res = mysql_query($Query);
			//$Return = '<select name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'">';
			//if(empty($Defaults[$Field])){
				//$Return .= '<option value=""></option>';
			//}
			$checkindex = 0;
			//$Return = 'Listed: <select name="multiselect_'.$Field.'" id="selector_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'">';
			$Return ='';
			while($lrow = mysql_fetch_assoc($Res)){
				$Sel = '';
				if(!empty($Defaults[$Field])){
					$DefaultArray = core_cleanArray(explode('|',$Defaults[$Field]));
					if(in_array($lrow[$Config['_Linkedfields'][$Field]['ID']], $DefaultArray)){
						$Sel = 'checked="checked"';
					}
				}
				//$Return .= '<option value="'.$lrow[$Config['_Linkedfields'][$Field]['ID']].'" '.$Sel.' >'.$lrow['outValue'].'</option>';
				$Return .= '<div style="width:200px; float:left;"><label for="entry_'.$Element['ID'].'_'.$Field.'_'.$checkindex.'"><input type="checkbox" value="'.$lrow[$Config['_Linkedfields'][$Field]['ID']].'" name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'_'.$checkindex.'" class="'.$Req.'" '.$Sel.' />'.$lrow['outValue'].'</label></div>';
				$checkindex++;
				
			}
			//$Return .= '</select> <input type="button" name="button" id="button" value="Add" onclick="linked_addOption(\''.$Element['ID'].'_'.$Field.'\')" />';
			//$Return .= 'Selected: <select name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'">';
			//$Return .= '</select>';
			
			
		break;
		case "dropdown":

				$values = array();
				foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
					$values[] = $outValue;
				}
				$outString = 'CONCAT('.implode(',\' \',',$values).') as outValue';
			$QuerySQL = "SELECT ".$Config['_Linkedfields'][$Field]['ID'].", ".$outString." FROM `".$Config['_Linkedfields'][$Field]['Table']."` ORDER BY `".$Config['_Linkedfields'][$Field]['Value'][0]."` ASC;";
			$Res = mysql_query($QuerySQL);
			//cho $QuerySQL;
			$Return = '<select name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" ref="'.$row['_return_'.$Config['_ReturnFields'][0]].'" class="'.$Req.'">';
			if(empty($Defaults[$Field])){
				$Return .= '<option value=""></option>';
			}
			while($lrow = mysql_fetch_assoc($Res)){
				$Sel = '';
				if(!empty($Defaults[$Field])){
					if($Defaults[$Field] == $lrow[$Config['_Linkedfields'][$Field]['ID']]){
						$Sel = 'selected="selected"';
						$Defaults['_outvalue'][$Field] = $lrow['outValue'];
					}
				}
				$Return .= '<option value="'.$lrow[$Config['_Linkedfields'][$Field]['ID']].'" '.$Sel.' >'.$lrow['outValue'].'</option>';
			}
			$Return .= '</select>';
                        // add insert Auto button!
                        //$Return .= '<div class="fbutton"><div class="button add-new-h2"><span onclick="df_buildQuickCaptureForm(\''.$Config['_Linkedfields'][$Field]['_addInterface'].'\', true, \''.$Element['ID'].'|'.$Field.'\', linked_reloadEntries);return false;" style="padding-left: 20px;" class="add">Add Entry</span></div></div>';

                        $_SESSION['dataform']['OutScripts'] .="

                            

                        ";






		break;
		case "autocomplete":
			$Det[$Config['_Linkedfields'][$Field]['ID']] = '';
			//$Det[$Config['_Linkedfields'][$Field]['Value']] = '';
			$VisDef = '';
			$Return = '';
			if(!empty($Defaults[$Field])){
				$values = array();
				foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
					$values[] = $outValue;
				}
				$OutValues = implode(', ',$values);
				$defQuery = "SELECT ".$Config['_Linkedfields'][$Field]['ID'].",".$OutValues." FROM `".$Config['_Linkedfields'][$Field]['Table']."` WHERE `".$Config['_Linkedfields'][$Field]['ID']."` =  '".$Defaults[$Field]."' ;";
				$Res = mysql_query($defQuery);
				$Det = mysql_fetch_assoc($Res);
				$OutString = '';
				foreach($values as $visValues){
					$OutString .= $Det[$visValues].' ';
					$Defaults['_outvalue'][$Field] = $OutString;

				}
				//ob_start();
				//echo "SELECT ".$IDField.",".$ValueField." FROM `".$Table."` WHERE `".$ValueField."` LIKE '%".$Default."%' OR `".$IDField."` LIKE '%".$Default."%' ORDER BY `".$ValueField."` ASC;";
				//dump($Det);
				//$Return .= ob_get_clean();
				$VisDef = $OutString;
			}
			//$FieldID = uniqid('check_'.$Field);
			//$Return .= '<input type="text" id="autocomplete_'.$FieldID.'" class="textfield" value="'.$Det[$IDField].' ['.$Det[$ValueField].']" /><input type="hidden" name="dataForm['.$ElementID.']['.$Field.']" id="autocomplete_'.$FieldID.'_value" value="'.$Det[$IDField].'" class="'.$Req.'" />';
			$Return .= '<input type="text" id="entry_'.$Element['ID'].'_'.$Field.'_view" class="'.$Req.' text" value="'.$VisDef.'" style="width:95%;" /><input type="hidden" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Det[$Config['_Linkedfields'][$Field]['ID']].'" />';



                       $_SESSION['dataform']['OutScripts'] .="
                        jQuery('#entry_".$Element['ID']."_".$Field."_view').autocomplete({
                                source: function( request, response ) {
                                    //alert(request.term);
                                    ajaxCall('linked_autocomplete', '".$Element['ID']."', '".$Field."', request.term, function(output){
                                        response(output);
                                    });
                                },
                                minLength: 2,
                                select: function( event, ui ) {
                                        jQuery('#entry_".$Element['ID']."_".$Field."').val(ui.item.id);
                                },
                                open: function() {
                                        jQuery( this ).removeClass( \"ui-corner-all\" ).addClass( \"ui-corner-top\" );
                                },
                                close: function() {
                                        jQuery( this ).removeClass( \"ui-corner-top\" ).addClass( \"ui-corner-all\" );
                                }
                        });
                        ";

                        /*
                        $_SESSION['dataform']['OutScripts'] .="
			
			var options = {
				script:'".getdocument($_GET['page'])."?q_eid=".$Element['ID']."&f_i=".urlencode(base64_encode($Field))."&',
				varname:'input',
				json: true,
				timeout: 5000,
				callback: function (obj) {
					document.getElementById('entry_".$Element['ID']."_".$Field."').value = obj.id;
				}
			};
			var as_json = new bsn.AutoSuggest('entry_".$Element['ID']."_".$Field."_view', options);
			*/
			
			
				//jQuery('#autocomplete_".$FieldID."').autocomplete(\"".getdocument($_GET['page'])."?q_eid=".$Element['ID']."&f_i=".encodestring($Field)."\",{width: 250, selectFirst: false});
				//jQuery('#autocomplete_".$FieldID."').result(function(event, data, formatted) {
				//	jQuery('#autocomplete_".$FieldID."_value').val(data[1]);
				//});";
		break;
	}


echo $Return;
}

if($FieldSet[1] == 'linkedfiltered'){
	//linked_makeFilterdLinkedField('".$Config['_Linkedfilterfields'][$Field]['Ref']."', '".$Config['_Linkedfilterfields'][$Field]['Value']."','".$Config['_Linkedfilterfields'][$Field]['Filter']."', this.value, '".$Config['_Linkedfilterfields'][$Field]['Table']."'
		$Ent = '<option>Select '.df_parseCamelCase($Config['_Linkedfilterfields'][$Field]['Filter']).' First</option>';
		$Dis = 'disabled="disabled"';
		if(!empty($Defaults[$Config['_Linkedfilterfields'][$Field]['Filter']])){
				$Ent = linked_makeFilterdLinkedField($Config['_Linkedfilterfields'][$Field]['Ref'], "CONCAT(".implode(",' ',",$Config['_Linkedfilterfields'][$Field]['Value']).") AS _Value_Field",$Config['_Linkedfilterfields'][$Field]['ID'], $Defaults[$Config['_Linkedfilterfields'][$Field]['Filter']], $Config['_Linkedfilterfields'][$Field]['Table'], $Defaults[$Field]);
				$Dis = '';
		}
	$Return = '<select id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" id="" class="'.$Req.'" '.$Dis.'>';
		$Return .= $Ent;
	$Return .= '</select><span id="entry_status_'.$Element['ID'].'_'.$Field.'"></span>';
	$_SESSION['dataform']['OutScripts'] .= "
	jQuery(\"#entry_".$Element['ID']."_".$Config['_Linkedfilterfields'][$Field]['Filter']."\").change(function(){
			jQuery('#entry_".$Element['ID']."_".$Field."').html('<option>Loading...</option>');
			caption = jQuery('#caption_".$Element['ID']."_".$Field."').html();
			jQuery('#caption_".$Element['ID']."_".$Field."').html('<img src=\"".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/linked/images/miniload.gif\" width=\"9\" height=\"9\" alt=\"Loading\" align=\"absmiddle\" /> loading data...');
			jQuery('#entry_".$Element['ID']."_".$Field."').attr('disabled', 'disabled');
		ajaxCall('linked_makeFilterdLinkedField', '".$Config['_Linkedfilterfields'][$Field]['Ref']."', 'CONCAT(".implode(",\' \',",$Config['_Linkedfilterfields'][$Field]['Value']).") AS _Value_Field','".$Config['_Linkedfilterfields'][$Field]['ID']."', this.value, '".$Config['_Linkedfilterfields'][$Field]['Table']."', function(x){
			jQuery('#caption_".$Element['ID']."_".$Field."').html(caption);
			jQuery('#entry_status_".$Element['ID']."_".$Field."').html('');
			jQuery('#entry_".$Element['ID']."_".$Field."').html(x);
			jQuery('#entry_".$Element['ID']."_".$Field."').removeAttr('disabled');
		});
	});\n";
echo $Return;
}

?>