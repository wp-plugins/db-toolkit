<script type="text/javascript">
jQuery(document).ready(function($) {

	//jQuery("#reportTable").tablesorter( {sortList: [[1,0]], headers: { 0:{sorter: false}}} );
	//jQuery('.tablesorter').dataTable({
	//	"sPaginationType": "full_numbers"
	//});
	
	//jQuery(".filterBoxes").multiSelect({ oneOrMoreSelected: '*' });
	<?php
	if(!empty($_SESSION['dataform']['OutScripts'])){
		echo $_SESSION['dataform']['OutScripts'];
		unset($_SESSION['dataform']['OutScripts']);
	}
	?>
	
	jQuery('.filterpanels').css('visibility', 'visible');
	jQuery('.tablesorter').css('visibility', 'visible');
});
</script>