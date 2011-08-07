<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
//<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="textfield '.$Req.'" />';
if($FieldSet[1] == 'date'){
			if(!empty($Element['Content']['_TodayDefault'][$Field])){
				$Date = date('Y-m-d');					
			}else{
				$Data = '';
			}
			if(!empty($Defaults[$Field])){
				$Date = $Defaults[$Field];
			}
			
			$MinMax = "";
			if(!empty($Element['Content']['_dateMin'][$Field])){
				$MinMax .= "minDate: '".$Element['Content']['_dateMin'][$Field]."',\n";
			}
			if(!empty($Element['Content']['_dateMax'][$Field])){
				$MinMax .= "maxDate: '".$Element['Content']['_dateMax'][$Field]."',\n";
			}
			$FieldID = uniqid($Element['ID'].'_');
			$Return = '<input name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" size="11" maxlength="10" type="text" value="'.$Date.'" class="'.$Req.' date " />';
			$_SESSION['dataform']['OutScripts'] .= "
					jQuery(\"#entry_".$Element['ID']."_".$Field."\").datepicker({
						".$MinMax."
						dateFormat: 'yy-mm-dd',
						changeMonth: true,
						changeYear: true,
						showOn: 'button',
						buttonImage: '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/date/calendar.png',
						buttonImageOnly: true,
						duration: 50
						
					});
					jQuery('.ui-datepicker').css('z-index','9999');
			";
			/*
			jQuery('#".$FieldID."').DatePicker({
					format:'Y-m-d',
					date: jQuery('#".$FieldID."').val(),
					current: jQuery('#".$FieldID."').val(),
					starts: 1,
					position: 'right',
					onBeforeShow: function(){
						jQuery('#".$FieldID."').DatePickerSetDate(jQuery('#".$FieldID."').val(), true);
					},
					onChange: function(formated, dates){
						jQuery('#".$FieldID."').val(formated);
					}
				});

				";
				*/
}
if($FieldSet[1] == 'datetime'){
	$DateVal = date('Y-m-d H:i');
	$DateTime = explode(' ', $DateVal);
		if(!empty($Defaults[$Field])){
			$DateTime = explode(' ', $Defaults[$Field]);
		}
	$Return = '<div style="float:left;"><input name="dataForm['.$Element['ID'].']['.$Field.'][date]" id="entry_'.$Element['ID'].'_'.$Field.'_date" size="11" maxlength="25" type="text" value="'.$DateTime[0].'" class="'.$Req.' date" style="float:none;" /><div class="caption">Date</div></div><div style="float:left;">&nbsp;<input name="dataForm['.$Element['ID'].']['.$Field.'][time]" id="entry_'.$Element['ID'].'_'.$Field.'_time" size="5" maxlength="25" type="text" value="'.$DateTime[1].'" class="'.$Req.' date" style="float:none;" /><div class="caption">Time</div></div>';

	$_SESSION['dataform']['OutScripts'] .= "
	//jQuery('#entry_".$Element['ID']."_".$Field."_time').timepickr({
     //   convention: 24	});	
	
	jQuery(\"#entry_".$Element['ID']."_".$Field."_date\").datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		//showOn: 'button',
		//buttonImage: WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/date/calendar.png',
		//buttonImageOnly: true,
		duration: 50
	});
	jQuery('.ui-datepicker').css('z-index','9999');

	";
}

if($FieldSet[1] == 'timepicker'){
	$DateVal = date('H:i');
		if(!empty($Defaults[$Field])){
			$DateVal = $Defaults[$Field];
		}
	$Return = '<input name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" size="5" maxlength="25" type="text" value="'.$DateVal.'" class="'.$Req.' date" />';
	$_SESSION['dataform']['OutScripts'] .= "
	jQuery('#entry_".$Element['ID']."_".$Field."').timepickr({
        convention: 24,
		trigger: 'click'
	});	
	";
}

if($FieldSet[1] == 'timestamp'){
	$Return = '';//= '<input name="dataForm['.$Element['ID'].']['.$Field.']" id="'.$FieldID.'" size="25" maxlength="25" type="text" value="'.$DateVal.'" class="'.$Req.'" />';
}


if($FieldSet[1] == 'scheduleMin'){
	$DefaultMin = array();
	if(!empty($Defaults[$Field])){
		$DefaultMin = substr($Defaults[$Field], 2, strlen($Defaults[$Field])-4);
		$DefaultMin = explode('||', $DefaultMin);
	}
	// Minutes
	$Return = '<div id="entry_'.$Element['ID'].'_'.$Field.'_min">';
		$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][all]" value="1" type="checkbox" id="'.$Field.'_mnull" /><label style="display:inline-block;" for="'.$Field.'_mnull">Every</label>';
		for($m=0; $m<=55;$m+=5){
			$Sel = '';
			if(in_array($m, $DefaultMin)){
				$Sel = 'checked="checked"';
			}
			$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][]" value="'.$m.'" type="checkbox" id="'.$Field.'_m'.$m.'" '.$Sel.' /><label style="display:inline-block;" for="'.$Field.'_m'.$m.'">'.$m.'&nbsp;</label>';
		}
		//$Return .= '<div class="caption" id="caption_'.$Element['ID'].'_'.$Field.'">Minutes to be run</div>';
	$Return .= '</div>';
	$_SESSION['dataform']['OutScripts'] .= "
		//jQuery(\"#entry_".$Element['ID']."_".$Field."_min\").buttonset();
	";
}
if($FieldSet[1] == 'scheduleHour'){
	$DefaultHour = array();
	if(!empty($Defaults[$Field])){
		$DefaultHour = substr($Defaults[$Field], 2, strlen($Defaults[$Field])-4);
		$DefaultHour = explode('||', $DefaultHour);
	}
	//Hours
	$Return = '<div id="entry_'.$Element['ID'].'_'.$Field.'_hour">';
		$Sel = '';
		if(in_array('am', $DefaultHour)){
			$Sel = 'checked="checked"';
		}
		$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][am]" value="1" type="checkbox" id="'.$Field.'_am" '.$Sel.' /><label style="display:inline-block;" for="'.$Field.'_am">am</label>';
		$Sel = '';
		if(in_array('pm', $DefaultHour)){
			$Sel = 'checked="checked"';
		}
		$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][pm]" value="1" type="checkbox" id="'.$Field.'_pm" '.$Sel.' /><label style="display:inline-block;" for="'.$Field.'_pm">pm</label>';
		
		for($h=1; $h<=12;$h++){
			$Sel = '';
			if(in_array($h, $DefaultHour)){
				$Sel = 'checked="checked"';
			}
			if(in_array('pm', $DefaultHour)){
				$Sel = '';
				$hval = $h+12;
				if($hval == 24){
					$hval = 0;	
				}
				if(in_array($hval, $DefaultHour)){
					$Sel = 'checked="checked"';
				}
			}
			$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][]" value="'.$h.'" type="checkbox" id="'.$Field.'_h'.$h.'" '.$Sel.' /><label style="display:inline-block;" for="'.$Field.'_h'.$h.'">'.$h.'&nbsp;</label>';
		}
		$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][all]" value="*" type="checkbox" id="'.$Field.'_hour" '.$Def['00'].' /><label style="display:inline-block;" for="'.$Field.'_hour">Hourly</label>';
		//$Return .= '<div class="caption" id="caption_'.$Element['ID'].'_'.$Field.'">Hours to be run</div>';
	$Return .= '</div>';
	$_SESSION['dataform']['OutScripts'] .= "
		//jQuery(\"#entry_".$Element['ID']."_".$Field."_hour\").buttonset();
	";
}
if($FieldSet[1] == 'scheduleDay'){
	$DefaultDay = array();
	if(!empty($Defaults[$Field])){
		$DefaultDay = substr($Defaults[$Field], 2, strlen($Defaults[$Field])-4);
		$DefaultDay = explode('||', $DefaultDay);
	}
	//Days
	$Return = '<div id="entry_'.$Element['ID'].'_'.$Field.'_day">';
	$Days = array(0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat');
	for($d=0; $d<=6;$d++){
		$Sel = '';
		if(in_array($d, $DefaultDay)){
			$Sel = 'checked="checked"';
		}
		$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][]" value="'.$d.'" type="checkbox" id="'.$Field.'_'.$Days[$d].'" '.$Sel.' /><label style="display:inline-block;" for="'.$Field.'_'.$Days[$d].'">'.$Days[$d].' &nbsp;</label>';
	}
	$Sel = '';
	$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][all]" value="*" type="radio" id="'.$Field.'_day" /> <label style="display:inline-block;" for="'.$Field.'_day">Every Day</label>';
	//$Return .= '<div class="caption" id="caption_'.$Element['ID'].'_'.$Field.'">Days to be run</div>';
	$Return .= '</div>';
	
	$_SESSION['dataform']['OutScripts'] .= "
		//jQuery(\"#entry_".$Element['ID']."_".$Field."_day\").buttonset();
	";

}


//$Return = '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="textfield '.$Req.'" style="width:98%;" value="'.$Val.'" />';
echo $Return;

?>