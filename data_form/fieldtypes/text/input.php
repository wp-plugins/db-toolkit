<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting

if($FieldSet[1] == 'singletext'){
	$WidthOverride = '';
        
	if(!empty($Config['_FieldLength'][$Field])){
		$WidthOverride = 'style="width:'.$Config['_FieldLength'][$Field].';"';
	}
        $fieldType = 'text';
        if(!empty($Config['_fieldType'][$Field])){
            $fieldType = $Config['_fieldType'][$Field];
        }
        
	echo $Config['_Prefix'][$Field].'<input name="dataForm['.$Element['ID'].']['.$Field.']" type="'.$fieldType.'" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />'.$Config['_Suffix'][$Field];
}
if($FieldSet[1] == 'password'){

	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="password" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />';
}
if($FieldSet[1] == 'emailaddress'){
    if(!empty($Req)){
        $Req = 'validate[required,custom[email]]';
    }
	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />';
}
if($FieldSet[1] == 'telephonenumber'){
    if(!empty($Req)){
        $Req = 'validate[required,custom[telephone]]';
    }
	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />';
}
if($FieldSet[1] == 'textarea'){
        
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textbox ">'.$Val.'</textarea>';

        if(!empty($Config['_CharLength'][$Field])){
        $_SESSION['dataform']['OutScripts'] .="

            max = ".$Config['_CharLength'][$Field].";

            len = jQuery('#entry_".$Element['ID']."_".$Field."').val().length;
            jQuery('#entry_".$Element['ID']."_".$Field."').next().html(len+' Characters');

            jQuery('#entry_".$Element['ID']."_".$Field."').bind('keyup', function(h){
                len = jQuery('#entry_".$Element['ID']."_".$Field."').val().length;
                if(len <= max){
                    jQuery('#entry_".$Element['ID']."_".$Field."').next().html(len+' Characters');                    
                }else{
                    curr = jQuery('#entry_".$Element['ID']."_".$Field."').val().substr(0,max);
                    jQuery('#entry_".$Element['ID']."_".$Field."').val(curr);
                    jQuery('#entry_".$Element['ID']."_".$Field."').next().html(max+' Characters');
                }
            });
            
        ";
        }

}
if($FieldSet[1] == 'textarealarge'){
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textboxlarge ">'.$Val.'</textarea>';
}
if($FieldSet[1] ==  'phpcodeblock'){
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textboxcode ">'.$Val.'</textarea>';
}
if($FieldSet[1] == 'wysiwyg'){

    $idCount = uniqid();

    echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'_'.$idCount.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.'  textboxlarge">'.$Val.'</textarea>';
    
    
    $Buttons = implode("' , '",  $Config['_activatedButtons'][$Field]);
    
    $_SESSION['dataform']['OutScripts'] .="


        CKEDITOR.replace('entry_".$Element['ID']."_".$Field."_".$idCount."', {
            toolbar: [
                ['".$Buttons."']
            ]
        });;
        
    ";
}
if($FieldSet[1] == 'url'){
	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="" class="'.$Req.' text" />';
}
if($FieldSet[1] == 'colourpicker'){
    	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" style="width:60px;" '.$WidthOverride.' />';
    $_SESSION['dataform']['OutScripts'] .="
        jQuery('#entry_".$Element['ID']."_".$Field."').miniColors({



        });
    ";
}
?>