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

                        $Return .= '<div class="input-append">';
			$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" type="textfield" value="'.$Date.'" class="'.$Req.' input-small " />';
			$Return .= '<span class="add-on"><i id="entry_'.$Element['ID'].'_'.$Field.'_trigger" class="icon-calendar"></i></span>';
			$Return .= '</div>';
                        $_SESSION['dataform']['OutScripts'] .= "
                            
					jQuery(\"#entry_".$Element['ID']."_".$Field."\").datepicker({
						".$MinMax."
						dateFormat: 'yy-mm-dd',
						changeMonth: true,
						changeYear: true,
												
						duration: 50
						
					});
					jQuery('.ui-datepicker').css('z-index','9999');
			";

}
if($FieldSet[1] == 'datetime'){
	$DateVal = date('Y-m-d H:i');
	$DateTime = explode(' ', $DateVal);
		if(!empty($Defaults[$Field])){
			$DateTime = explode(' ', $Defaults[$Field]);
		}
	$Return = '<input name="dataForm['.$Element['ID'].']['.$Field.'][date]" id="entry_'.$Element['ID'].'_'.$Field.'_date" type="textfield" value="'.$DateTime[0].'" class="'.$Req.' input-small " placeholder="Date" /> ';


        $Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.'][time]" id="entry_'.$Element['ID'].'_'.$Field.'_time" type="textfield" value="'.$DateTime[1].'" class="'.$Req.' input-small " placeholder="Time" />';

	$_SESSION['dataform']['OutScripts'] .= "	
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
        $Return = '<div class="input-append">';
	$Return .= '<input name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" type="textfield" value="'.$DateVal.'" class="'.$Req.' input-small" placeholder="Time" />';
        $Return .= '<span class="add-on"><i id="entry_'.$Element['ID'].'_'.$Field.'_trigger" class="icon-time"></i></span></div>';
}

echo $Return;

?>