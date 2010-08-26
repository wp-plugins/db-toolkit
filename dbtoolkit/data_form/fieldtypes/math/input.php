<?php
if($FieldSet[1] == 'multiply'){
	
//	dump();
	echo '<h2 id="'.$Field.'_mult">'.$Val.'</h2>';
	
	$_SESSION['dataform']['OutScripts'] .="
	
	jQuery('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['B']."').unbind();
	jQuery('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['B']."').bind('change', function(){
		aval = this.value;
		bval = jQuery('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['A']."').val();
		newval = math_CurrencyFormatted(aval*bval);
		jQuery('#".$Field."_mult').html(newval);
	});
	
	";
}
?>