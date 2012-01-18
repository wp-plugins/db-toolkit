<?php
/// This creates the actual input fields for capturing.
// output is echoed not returned.

/* 

 Available Variables:
 
 $Config - Element Config
 $FieldSet - array(0=>fieldfolder, 1=>fieldtype);
 $Defaults - array with all fields and all data for each field
 $Field - the current field name
 $Element - Element data
 $Req - the form validation class, sets the field to required if configured to do so
 
 field names are : name="dataForm['.$Element['ID'].']['.$Field.']"
 fieled ID's are : id="entry_'.$Element['ID'].'_'.$Field.'"
*/


$Form = str_replace("<label class=\"form-gen-lable singletext\" for=\"entry_".$Element['ID']."_".$Field."\" id=\"lable_".$Element['ID']."_".$Field."\">".$name."</label>", "", $Form);

$Sel = '';
if($Defaults[$Field] == 1){
	$Sel = 'checked="checked"';
}
echo '<div style="padding:3px;"><input type="checkbox" id="entry_'.$Element['ID'].'_'.$Field.'" name="dataForm['.$Element['ID'].']['.$Field.']" value="1" '.$Sel.' /> ';

echo "<label class=\"form-gen-lable entrylabel\" style=\"display:inline !important\" for=\"entry_".$Element['ID']."_".$Field."\" id=\"lable_".$Element['ID']."_".$Field."\">".$name."</label>";

echo '</div>';
$Config['_FieldCaption'][$Field] = '';
?>