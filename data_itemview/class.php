<?php

function di_listReports($Default = false){
	$Res = mysql_query("SELECT ID, Content, ParentDocument FROM `dais_elements` WHERE `Element` = 'data_report'");
	echo mysql_error();
	$Return .= '<div style="padding:3px;" class="list_row3">';
	$Return .= '<strong>Select Source Report</strong>: <select name="Data[Content][_source]" id="sourceSelect">';
	while($report = mysql_fetch_assoc($Res)){
		$Config = unserialize($report['Content']);
		$Sel = '';
		if($Default == $report['ID']){
			$Sel = 'selected="selected"';	
		}
		$Return .= '<option value="'.$report['ID'].'" '.$Sel.'>\''.$Config['_ReportTitle'].'\' on '.getdocument($report['ParentDocument']).'</option>';
	}
	$Return .= '</select>';
	$Return .= '&nbsp;<input type="button" name="loader" id="loader" value="Load Setup" onclick="di_loadElement(jQuery(\'#sourceSelect\').val());" />';
	$Return .= '</div>';
return $Return;
}
function di_listReference($Default = false){
	$Res = mysql_query("SELECT ID, Content, ParentDocument FROM `dais_elements` WHERE `Element` = 'data_report'");
	echo mysql_error();
	$Return .= '<div style="padding:3px;" class="list_row3">';
	$Return .= '<strong>Select Reference</strong>: <select name="Data[Content][_reference]" id="elementSelect">';
	while($report = mysql_fetch_assoc($Res)){
		$Config = unserialize($report['Content']);
		$Sel = '';
		if($Default == $report['ID']){
			$Sel = 'selected="selected"';	
		}
		$Return .= '<option value="'.$report['ID'].'" '.$Sel.'>\''.$Config['_ReportTitle'].'\' on '.getdocument($report['ParentDocument']).'</option>';
	}
	$Return .= '</select>';
	$Return .= '&nbsp;<input type="button" name="loader" id="loader" value="Load Setup" onclick="di_loadReference(jQuery(\'#elementSelect\').val());" />';
	$Return .= '</div>';
return $Return;
}

function di_sourceSetup($id, $Default = false){
	$Element = getelement($id);
	$Config = $Element['Content'];
	//dump($Config);
	return '<div style="padding:3px;" class="list_row1"><strong>Source Field</strong>:&nbsp; '.df_ListFields($Config['_main_table'], $Default, '_sourceField').'</div>';
}
function di_referenceSetup($id, $DefaultFilter = false,  $DefaultTitle = false){
	$Element = getelement($id);
	$Config = $Element['Content'];
	//dump($Config);
	$Return = '<div style="padding:3px;" class="list_row1"><strong>Filter Field</strong>:&nbsp; '.df_ListFields($Config['_main_table'], $DefaultFilter, '_filterField').'</div>';
	$Return .= '<div style="padding:3px;" class="list_row2"><strong>Title Field</strong>:&nbsp; '.df_ListFields($Config['_main_table'], $DefaultTitle, '_titleField').'</div>';
return $Return;
}


function di_showItem($EID, $Item, $Setup = false){
	//return 'pingoo';
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

	// setup columns
	if(!empty($Config['_FormLayout'])){
		parse_str($Config['_FormLayout'], $Columns);
		if(empty($Columns['FieldList_left'])){
			unset($Columns);
			unset($Config['_FormLayout']);
		}
	}



//setup Field Types
	foreach($Config['_Field'] as $Field=>$Type){
		// explodes to: 
		// [0] = Field plugin dir
		// [1] = Field plugin type
		$Config['_Field'][$Field] = explode('_', $Type);
	}


	
	// field type filters
	$joinIndex = 'a';
	foreach($Config['_IndexType'] as $Field=>$Type){
	$querySelects[$Field] = 'prim.'.$Field;
	}
	
        if(!empty($Config['_CloneField'])) {

                foreach($Config['_CloneField'] as $CloneKey=>$Clone) {
                    //echo 'BEFORE';
                    //vardump($querySelects);
                    foreach($querySelects as $selectKey=>$selectScan){
                        $queryJoin = str_replace($CloneKey, $Clone['Master'], $queryJoin);
                        $WhereTag = str_replace($CloneKey, $Clone['Master'], $WhereTag);
                        if(strstr($selectScan, " AS ") === false){
                            //echo $Clone['Master'].' - concat <br />';
                            if(strstr($selectScan, "_sourceid_") === false){
                                $querySelects[$selectKey] = str_replace($CloneKey, $Clone['Master'].' AS '.$CloneKey, $selectScan);
                            }
                        }
                    }
                    //echo 'After';
                    //vardump($querySelects);
                }
            }
            
        // Build Query
	foreach($Config['_Field'] as $Field=>$Type){
		// Run Filters that have been set through each field type
		if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/queryfilter.php')){
			include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[0].'/queryfilter.php');
		}
		//apply a generic keyword filter to each field is a key word has been sent
		if(!empty($_SESSION['reportFilters'][$EID]['_keywords'])){
			if($WhereTag == ''){
				$WhereTag = " WHERE ";	
			}
			$keyField = 'prim.'.$Field;
			if(strpos($querySelects[$Field], ' AS ') !== false){
				$keyField = strtok($querySelects[$Field], ' AS ');
			}

		$preWhere[] = $keyField." LIKE '%".$_SESSION['reportFilters'][$EID]['_keywords']."%' ";
		//dump($_SESSION['reportFilters'][$EID]);
		}
	$joinIndex++;
	}
	
	// create Query Selects and Where clause string
	$querySelect = implode(',',$querySelects);
	if(!empty($Setup)){
		$queryWhere = 'prim.'.$Setup['_filterField'].' = \''.$Item.'\'';
	}else{
		$queryWhere = 'prim.'.$Config['_ReturnFields'][0].' = \''.$Item.'\'';
	}
	if(!empty($queryWhere)){
		$WhereTag = " WHERE ";	
	}else{
		$WhereTag = "";			
	}

        if(is_array($groupBy)) {
            $groupBy = 'GROUP BY ('.implode(',', $groupBy).')';
            $countLimit = '';
            $entryCount = true;
            //add totals selects to count
            if(is_array($countSelect)) {
                $countSelect = ','.implode(',',$countSelect);
            }
        }
        
        $Query = "SELECT ".$querySelect." FROM `".$Config['_main_table']."` AS prim \n ".$queryJoin." \n ".$WhereTag." \n ".$queryWhere."\n ".$groupBy." \n ".$orderStr." \n LIMIT 1;";
	// Query Results
	$Res = mysql_query($Query);
	//echo $Query.'<br /><br /><br />';
        echo mysql_error();



	$Data = mysql_fetch_assoc($Res);
	
	if(!empty($Config['_UseViewTemplate'])){
		//dump($Config);
		$PreReturn = $Config['_ViewTemplateContentWrapperStart'];
		$PreReturn .= $Config['_ViewTemplatePreContent'];
		$PreReturn .= $Config['_ViewTemplateContent'];
		$PreReturn .= $Config['_ViewTemplatePostContent'];
		$PreReturn .= $Config['_ViewTemplateContentWrapperEnd'];
		//echo $Config['_ViewTemplateContent'];
		$newTitle = 'View Item';
		if(!empty($Config['_ViewFormText'])){
			$newTitle = $Config['_ViewFormText'];
		}
		foreach($Config['_Field'] as $Field=>$Types){
			if(!empty($Config['_ViewFormText'])){
				//dump($Data);
				if(!empty($Data['_outvalue'][$Field])){
					$newTitle = str_replace('{{'.$Field.'}}', $Data['_outvalue'][$Field], $newTitle);
				}else{
					$newTitle = str_replace('{{'.$Field.'}}', $Data[$Field], $newTitle);
				}
			}
		//	dump($Type);
			if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php')){
				if(!empty($Config['_FieldTitle'][$Field])){					
					$name = $Config['_FieldTitle'][$Field];
				}else{
					$name = df_parseCamelCase($Field);
				}
				$PreReturn = str_replace('{{_'.$Field.'_name}}', $name, $PreReturn);
				if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/output.php')){
					$Out = false;
					include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/output.php');
					$PreReturn = str_replace('{{'.$Field.'}}', $Out, $PreReturn);
				}
				$PreReturn = str_replace('{{_PageID}}', $Element['ParentDocument'], $PreReturn);
				$PreReturn = str_replace('{{_PageName}}', getdocument($Element['ParentDocument']), $PreReturn);
				$PreReturn = str_replace('{{_EID}}', $EID, $PreReturn);
			}
		}
		$Output['title'] = $newTitle;
		$Output['width'] = $Config['_popupWidthview'];
		$Output['html'] = $PreReturn;
		return $Output;
	}
	
	
	//dump($Data);
	$Row = 'list_row2';
	$LeftColumn = '';
	$RightColumn = '';
	$FarRightColumn = '';
	//dump($Config['_Field']);
	if(!empty($Config['_gridLayoutView'])){
		$Config['_gridLayoutView'] = str_replace('=viewrow', '=row', $Config['_gridLayoutView']);
		parse_str($Config['_gridLayoutView'], $Layout);
		
		$Form = '';
		$CurrRow = '0';
		$CurrCol = '0';
		$Index = 0;
		foreach($Layout as $LayoutField => $Grid){
			$Grid = explode('_', $Grid);
			if(substr($Grid[0],0,3) == 'row'){
				$Setup[$Grid[0]][$Grid[1]]['Fields'][]['Name'] = str_replace('Field_','',$LayoutField);
				$Setup[$Grid[0]][$Grid[1]]['Row'] = $Grid[2];
				$Index++;
			}else{
				$Setup[$Grid[0]][] = $LayoutField;
			}
		}
		$newTitle = 'View Item';
		if(!empty($Config['_ViewFormText'])){
			$newTitle = $Config['_ViewFormText'];
		}
		//dump($Setup);
		foreach($Setup as $Row=>$ColSets){
		//	dump($ColSets);
			if(substr($Row,0,3) == 'row'){
				$Form .= '<div id="pg'.$_GET['Page']['ID'].'-view-'.$Row.'" class="view-gen-row" style="clear:both;">';
				foreach($ColSets as $Col=>$FieldSet){
					$Form .= '<div style="float: left; overflow: hidden; width: '.$FieldSet['Row'].';">';
						$Form .= '<div id="pg'.$_GET['Page']['ID'].'-view-'.$Row.'-'.$Col.'" class="view-gen-row view-gen-col view-col-'.$Col.'">';
							foreach($FieldSet['Fields'] as $Fields){
								//dump($Config['_Field']);
								$Field = $Fields['Name'];								
								$FieldSet = $Config['_Field'][$Field];
								//$FieldSet = explode('_',$FieldDet);
									if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php')){
										if(!empty($Config['_FieldTitle'][$Field])){					
											$name = $Config['_FieldTitle'][$Field];
										}else{
											$name = df_parseCamelCase($Field);
										}
										//echo $Field;
										$Form .= '<div id="view-field-'.$Field.'" class="view-gen-field-wrapper"><label id="lable_'.$Element['ID'].'_'.$Field.'" for="entry_'.$Element['ID'].'_'.$Field.'" class="view-gen-lable '.$FieldSet[1].'">'.$name.'</label>';
										include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/conf.php');
										$Val = '';
										if(!empty($Data[$Field])){
										$Val = stripslashes($Data[$Field]);
										}
										if(!empty($FieldTypes[$FieldSet[1]]['visible'])){
											
											//$Pre = '<tr class="'.$Row.'" style="padding:3px;">';
											//$Pre .= '<td id="'.$Element['ID'].'_'.$Field.'" class="'.$Row.'" nowrap="nowrap" width="30%" style="background-color:inherit; border:inherit;padding:3px;" valign="top"><div class="title"><strong>'.$name.'&nbsp;</strong></div>';//</td>';
											//$Pre .= '</tr>';
											//$Pre .= '<tr class="'.$Row.'" style="padding:3px;">';
											//$Pre .= '<td class="'.$Row.'" style="background-color:inherit; border:inherit;padding:3px;" valign="top">';
												//df_makeEnumField($Data['_main_table'], $ElementID, $Field, $Data[$Data[$Field]]['Type'], false, $Req);
												$Types = $FieldSet;
												$Out = false;
												include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$FieldSet[0].'/output.php');
												$Pre = '<div id="view-field-'.$Field.'-data" class="view-gen-field-data-wrapper">'.$Out.'</div>';
											//$Pre .= '</td>';
											//$Pre .= '</tr>';
											$Form .= $Pre;
											if(empty($FieldTypes[$FieldSet[1]]['captionsOff'])){
												$Form .= '<div class="caption">';
												if(!empty($Config['_FieldCaption'][$Field])){
													$Form .= $Config['_FieldCaption'][$Field];
												}else{
													$Form .= '&nbsp;';
												}
												$Form .= '</div>';
											}
											$Form .= '<div style="clear:left;"></div></div>';
							
											
										}
									}else{
										// check for Sectionbreak;
										if(!empty($Config['_SectionBreak'][$Fields['Name']])){
											// Section Break
											$Form .= '<div class="sectionbreak">';
											$Form .= '<h3>'.$Config['_SectionBreak'][$Fields['Name']]['Title'].'</h3>';
											if(!empty($Config['_SectionBreak'][$Fields['Name']]['Caption'])){
												$Form .= '<div class="caption">'.$Config['_SectionBreak'][$Fields['Name']]['Caption'].'</div>';	
											}
											$Form .= '</div>';
										}
									}
								//ob_start();
								if(!empty($Config['_ViewFormText'])){
									//dump($Data);
									if(!empty($Data['_outvalue'][$Field])){
										$newTitle = str_replace('{{'.$Field.'}}', $Data['_outvalue'][$Field], $newTitle);
									}else{
										$newTitle = str_replace('{{'.$Field.'}}', $Data[$Field], $newTitle);
									}
									$Output['title'] = $newTitle;
								}else{
                                                                    $Output['title'] = 'View Item';
                                                                }
							}
						$Form .= '</div>';
					$Form .= '</div>';
				}
				$Form .= '<div style="clear:left;"></div>';
				$Form .= '</div>';
			}
		}
		$Form .= '<div style="clear:left;"></div>';
		$Shown = '';
		// add title
                if(!empty($Config['_ViewFormText'])){
                        //dump($Data);
                        if(!empty($Data['_outvalue'][$Field])){
                                $newTitle = str_replace('{{'.$Field.'}}', $Data['_outvalue'][$Field], $newTitle);
                        }else{
                                $newTitle = str_replace('{{'.$Field.'}}', $Data[$Field], $newTitle);
                        }
                        $Output['title'] = $newTitle;
                }
		$Output['width'] = $Config['_popupWidthview'];
		$Output['html'] = '<div class="formular">'.$Form.'</div>';
		if(!empty($Config['_Show_Edit'])){
			$OutPut['edit'] = true;
		}
		
		return $Output;
	}
	
	if(!empty($Config['_FormLayout'])){
		parse_str($Config['_FormLayout'], $Columns);
		if(empty($Columns['FieldList_left'])){
			unset($Columns);
			unset($Config['_FormLayout']);
		}

	}



	if(!empty($Columns)){	
		foreach($Columns as $Key=>$Side){
			if($Key == 'FieldList_Main'){
				$ColumnSet = 'LeftColumn';
			}
			if($Key == 'FieldList_left'){
				$ColumnSet = 'RightColumn';
			}
			if($Key == 'FieldList_right'){
				$ColumnSet = 'FarRightColumn';
			}
			foreach($Side as $Entry){
				if(substr($Entry,0,12) != 'SectionBreak'){
					$Row = dais_rowSwitch($Row);
					$Field = $Entry;
					$Types = $Config['_Field'][$Field];
					$$ColumnSet .= $FieldSet[1];
					//ob_start();
					//dump($Config['_Field']);
					//$$ColumnSet = ob_get_clean();
					//$$ColumnSet .= $FieldSet[0].'<br />';
					if(!empty($Config['_FieldTitle'][$Field])){
						$FieldTitle = $Config['_FieldTitle'][$Field];
					}else{
						$FieldTitle = df_parsecamelcase($Field);						
					}
					
					
					if(!empty($Types[1])){
						include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php');
						if($FieldTypes[$Types[1]]['visible'] == true){
							$Out = false;
							$Out = '<div id="lable_'.$Element['ID'].'_'.$Field.'" for="entry_'.$Element['ID'].'_'.$Field.'" class="view-gen-lable"><strong>'.$FieldTitle.'</strong></div>';
							include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/output.php');
							$$ColumnSet .= $Out;
						}
					}
				}else{
					$$ColumnSet .= '<h3>'.$Config['_SectionBreak']['_'.$Entry].'</h3>';
				}
			
			}
		}
	}else{
		foreach($Config['_Field'] as $Field=>$Types){
			$Row = dais_rowswitch($Row);
			if(!empty($Types[1])){
				if(!empty($Config['_FieldTitle'][$Field])){
					$FieldTitle = $Config['_FieldTitle'][$Field];
				}else{
					$FieldTitle = df_parsecamelcase($Field);						
				}
				include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/conf.php');
				if($FieldTypes[$Types[1]]['visible'] == true){
					$Out = false;
					$Out = '<div id="lable_'.$Element['ID'].'_'.$Field.'" for="entry_'.$Element['ID'].'_'.$Field.'" class="view-gen-lable"><strong>'.$FieldTitle.'</strong></div>';
					include(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Types[0].'/output.php');
					//echo $Out;
					if(!empty($Columns)){
						if(in_array($Field, $Columns['FieldList_Main'])){
							$LeftColumn .= $Out;
						}elseif(in_array($Field, $Columns['FieldList_left'])){
							$RightColumn .= $Out;
						}
					}else{
						$RightColumn .= $Out;
					}
				}
			}
		}
	}
	
	
	if(!empty($Config['_titleField'])){
		//infobox($Setup['_Prefix'].$Data[$Setup['_titleField']].$Setup['Suffix']);
		$OutPut['title'] = $Config['_Prefix'].$Data[$Config['_titleField']].$Config['Suffix'];
	}else{
		//echo '<h2>View Entry</h2>';
		$OutPut['title'] = 'View Entry';//$Setup['_Prefix'].$Data[$Setup['_titleField']].$Setup['Suffix'];
	}
	$Return = '<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>';
	$OutPut['width'] = 300;
	if(!empty($LeftColumn)){		
		$ColWidth = '33';
		if(empty($FarRightColumn)){
			$ColWidth = '50';
		}
		$Return .= '<td width="'.$ColWidth.'%" valign="top">'.$LeftColumn.'</td>';
		$OutPut['width'] = $OutPut['width']+100;
	}
	if(!empty($RightColumn)){
		$Return .= '<td valign="top">'.$RightColumn.'</td>';
		$OutPut['width'] = $OutPut['width']+100;
	}
	if(!empty($FarRightColumn)){		
		$Return .= '<td width="33%" valign="top">'.$FarRightColumn.'</td>';
		$OutPut['width'] = $OutPut['width']+100;
	}	
	$Return .= '</tr>';
	$Return .= '</table>';
	if(!empty($Config['_Show_Edit'])){
		$OutPut['edit'] = true;
		//$Return .= '<input type="button" value="Edit" class="close" onclick="dr_BuildUpDateForm('.$EID.', '.$Item.');" />';
	}
	if(!empty($Config['_EnableAudit'])){
		$revres = mysql_query("SELECT count(_ID) as Rev FROM `_audit_".$Config['_main_table']."` WHERE `".$Config['_ReturnFields'][0]."` = '".$Data[$Config['_ReturnFields'][0]]."';");
		if($revres){
			if(mysql_num_rows($revres) == 1){
				$R = mysql_fetch_assoc($revres);
				$Return .= '<div class="captions">Revision '.$R['Rev'].'</div>';
			}
		}
 	}
	$OutPut['html'] = $Return;
return $OutPut;
}




?>