
// add any additional javascript required for the field type.
//<script>
function linkingtable_loadfields(table, field, maintable, loc){
	jQuery('#'+loc).html('&nbsp;Loading Config..');
	ajaxCall('linkingtable_loadfields',table, field, maintable, function(v){
		jQuery('#'+loc).html(v);
	});
}

function linkingtable_loaddestfields(table, field, maintable, loc){
	jQuery('#'+loc).html('&nbsp;Loading Config..');
	ajaxCall('linkingtable_loaddestfields',table, field, maintable, function(v){
		jQuery('#'+loc).html(v);
	});
}



