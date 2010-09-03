//<script>

function text_runCode(field, EID, ID){
	if(confirm('Are you sure you want to run this code?')){
		$('#codeRun_'+ID).attr('disabled', 'disabled');
		x_text_runCode(field, EID, ID, function(x){
			$('#codeRun_'+ID).removeAttr('disabled');								   
			alert('Result\n\r------------------------------------------------------\n\r'+x);
	   });
	}
}