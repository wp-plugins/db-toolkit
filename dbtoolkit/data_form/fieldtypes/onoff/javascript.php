<?php 
// add any additional javascript required for the field type.
?>//<script>

function onoff_toggleInline(eid, field, id){
	ajaxCall('onoff_setValue', jQuery('#onOff_'+eid+'_'+id).attr('checked'), id, field, eid, function(o){
		dr_goToPage(eid, false);
	})
}