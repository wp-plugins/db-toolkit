<?php
if($FieldSet[1] == 'multiply'){
	
//	dump();
	echo '<h2 id="'.$Field.'_mult">'.$Val.'</h2>';
	
	$_SESSION['dataform']['OutScripts'] .="
	
	$('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['B']."').unbind();
	$('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['B']."').bind('change', function(){
		aval = this.value;
		bval = $('#entry_".$Element['ID']."_".$Config['_multiply'][$Field]['A']."').val();
		newval = math_CurrencyFormatted(aval*bval);
		$('#".$Field."_mult').html(newval);
	});
	
	";
}
?>