<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting

if($FieldSet[1] == 'coordinates'){
	$Out['Lat'] = '';
	$Out['Lon'] = '';
	if(!empty($Val)){
		$Out = explode('|', $Val);
		$Out['Lat'] = $Out[0];
		$Out['Lon'] = $Out[1];
	}
	echo '<div style="width:50%; float:left;"><div style="padding:0 5px 0 0;"><input name="dataForm['.$Element['ID'].']['.$Field.'][Lat]" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Out['Lat'].'" class="'.$Req.' text" />';
	echo '<div class="caption">Latitude</div></div></div>';
	echo '<div style="width:50%; float:left;"><div style="padding:0 0 0 5px ;"><input name="dataForm['.$Element['ID'].']['.$Field.'][Lon]" type="'.$Type.'" id="entry_'.$Element['ID'].'_'.$Field.'" value="'.$Out['Lon'].'" class="'.$Req.' text" />';
	echo '<div class="caption">Longitude</div></div></div><div style="clear:left;"></div>';
}
?>