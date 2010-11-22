<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
$Val = htmlentities($Val);
if($FieldSet[1] == 'singletext'){
	$WidthOverride = '';
	if(!empty($Config['_FieldLength'][$Field])){
		$Config['_FieldLength'][$Field] = 'style="width:'.$Config['_FieldLength'][$Field].';"';
	}
	echo $Config['_Prefix'][$Field].'<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />'.$Config['_Suffix'][$Field];
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
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textbox ">'.htmlentities($Val).'</textarea>';
}
if($FieldSet[1] == 'textarealarge'){
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textboxlarge ">'.htmlentities($Val).'</textarea>';
}
if($FieldSet[1] ==  'phpcodeblock'){
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textboxcode ">'.$Val.'</textarea>';
}
if($FieldSet[1] == 'wysiwyg'){
	echo '<textarea id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" class="'.$Req.' textboxlarge ">'.$Val.'</textarea>';
	$_SESSION['dataform']['OutScripts'] .="
		$('#entry_".$Element['ID']."_".$Field."').wysiwyg();
	";
}
if($FieldSet[1] == 'url'){
	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="" class="'.$Req.' text" />';
}
if($FieldSet[1] == 'colourpicker'){
    	echo '<input name="dataForm['.$Element['ID'].']['.$Field.']" type="text" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Val.'" class="'.$Req.' text" '.$WidthOverride.' />';
    $_SESSION['dataform']['OutScripts'] .="
        $('#entry_".$Element['ID']."_".$Field."').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                    $(el).val(hex);
                    $(el).ColorPickerHide();
                    $(el).css('background-color', '#'+hex);
            },
            onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
            },
            onChange: function (hsb, hex, rgb) {
                $('#entry_".$Element['ID']."_".$Field."').css('background-color', '#'+hex);
                $('#entry_".$Element['ID']."_".$Field."').val(hex);
            }

        });
    ";
}
?>