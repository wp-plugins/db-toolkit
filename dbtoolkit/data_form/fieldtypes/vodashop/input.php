<?php

if($FieldSet[1] == 'msisdn'){
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
	//if($Element['_ActiveProcess'] == 'insert'){
	//	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'"  value="'.$Val.'" style="width:50%;" class="'.$Req.' text " onchange="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';// <input type="button" name="button" id="button" value="Sync" style="padding:0.2em 0.6em 0.3em;" class="ui-button ui-state-default ui-corner-all ui-state-hover" onclick="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';
	//}else{
		echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'"  value="'.$Val.'" style="width:50%;" class="'.$Req.' text " />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="button" id="button" value="Sync" class="ui-button ui-state-default ui-corner-all ui-state-hover" onclick="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';
	//}
}
if($FieldSet[1] == 'scanner'){
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
	//if($Element['_ActiveProcess'] == 'insert'){
	//	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'"  value="'.$Val.'" style="width:50%;" class="'.$Req.' text " onchange="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';// <input type="button" name="button" id="button" value="Sync" style="padding:0.2em 0.6em 0.3em;" class="ui-button ui-state-default ui-corner-all ui-state-hover" onclick="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';
	//}else{
		echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'"  value="'.$Val.'" style="width:50%;" class="'.$Req.' text " /> <input type="button" name="button" id="button" value="Sync" class="ui-button ui-state-default ui-corner-all ui-state-hover" onclick="vodashop_sync_Fetch(\'entry_'.$Element['ID'].'\', \''.$Field.'\', \''.$Element['_ActiveProcess'].'\')" />';
	//}
}
if($FieldSet[1] == 'allocatebutton'){
    $Sel = '';
    if($Defaults[$Field] == 1){
	$Sel = 'checked="checked"';
    }
    echo '<input type="checkbox" id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" value="1" '.$Sel.' />';

    $_SESSION['dataform']['OutScripts'] .="
        $('#entry_".$Element['ID']."_".$Field."').bind('click', function(p){
            if(this.checked == false){
                if(confirm('Are you sure you want to un-allocate this phone?')){
                    $('#entry_212_ClientID_view').val('');
                    $('#entry_212_ClientID').val('');
                }else{
                    this.checked=true;
                }
            }
        });
    ";


}
if($FieldSet[1] == 'autosubmit'){
	$_SESSION['dataform']['OutScripts'] .="
	
		//alert('data_form_".$Element['ID']."');
		$('#data_form_".$Element['ID']."').submit();
	
	";
}
?>