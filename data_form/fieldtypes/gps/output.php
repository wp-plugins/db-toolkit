<?php
	if(!empty($Data[$Field])){
		$Out = explode('|', $Data[$Field]);
		$Out['Lat'] = $Out[0];
		$Out['Lon'] = $Out[1];
		$marker = '';
		if(!empty($Config['_view_map']['_showMarker'][$Field])){
			$marker = '&markers='.$Out['Lat'].','.$Out['Lon'].',midred';
		}
		$Img = 'http://maps.google.com/staticmap?center='.$Out['Lat'].','.$Out['Lon'].'&zoom='.$Config['_view_map']['_zoom'][$Field].'&size='.$Config['_view_map']['_MapX'][$Field].'x'.$Config['_view_map']['_MapY'][$Field].'&maptype='.$Config['_view_map']['_mapType'][$Field].'&key='.$Config['_googleMaps_Key'].'&sensor=false'.$marker;
		$Img = '<img src="'.$Img.'" width="'.$Config['_view_map']['_MapX'][$Field].'" height="'.$Config['_view_map']['_MapY'][$Field].'" />';
		//return $Img;
		$Out .= $Img;
		//$Out .= '<div class="captions">'.$FieldTitle.'</div>';
	}else{
		$Out .= 'Not Available';
		//$Out .= '<div class="captions">'.$FieldTitle.'</div>';
	}
	return;
	//$Out .= '<div class="'.$Row.'"><strong>'.$FieldTitle.'</strong> : '.$Data[$Field].'</div>';
?>