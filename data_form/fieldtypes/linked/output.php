<?php
	if($Config['_Linkedfields'][$Field]['Type'] == 'checkbox'){
	
		$LinkingTable = '_linking_'.$Config['_main_table'].'_'.$Config['_Linkedfields'][$Field]['Table'];
		$outList = array();
		foreach($Config['_Linkedfields'][$Field]['Value'] as $outValue){
			$outList[] = 'too.'.$outValue;
		}
		$outString = 'CONCAT('.implode(',\' \',',$outList).') as '.$Field;
		$ourRes = mysql_query('SELECT prim.from, prim.to, '.$outString.' FROM `'.$LinkingTable.'` AS prim JOIN `'.$Config['_Linkedfields'][$Field]['Table'].'` AS too ON (prim.to = too.'.$Config['_Linkedfields'][$Field]['ID'].');');
		$outlist = array();
		while($outData = mysql_fetch_assoc($ourRes)){
			$outlist[] = $outData[$Field];
		}
		$Data[$Field] = implode(', ',$outlist);
	}

	$Out .= $Data[$Field];

?>