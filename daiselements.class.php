<?php
// File Selection stuff
function dais_page_selector($Type = 's', $DefaultList = false, $callback = false, $Name = 'PageList'){
	$Tree =	dais_pagetree(strtoupper($Type), $DefaultList, 'Site pages', $callback, $Name);
	//$Tree = ob_get_clean();
	$Return = '<div style="padding:3px; overflow:auto;" id="page_list">';
	return $Return.$Tree.'</div>';
}

function tempdais_fetchPageTreeArray($ID = 0, $Level = 1, $Default = false){
    global $wpdb;
	//$Query = "SELECT ID, Title, PageType, MettaDesc FROM dais_documents WHERE `ParentID` = '".$ID."' ORDER BY `Title` ASC";
    $Result = $wpdb->get_results( "SELECT ID FROM wp_posts WHERE post_parent = $ID AND post_type='page'" );
	$Tree = '';
	//while($Page = mysql_fetch_assoc($Result)){
    foreach($Result as $Page){
	switch ($Page['PageType']){
		case 1:
			$Icon = 'page.png';
			break;
		case 2:
			$Icon = 'page_link.png';
			break;
		case 4:
			$Icon = 'userpage.png';
			break;
	}
	
	if($Page['ID'] == $_SESSION['settings'][$_SESSION['key']]['daisDefault']){
		$Icon = 'page_red.png';
	}
	
	//$Row = dais_rowswitch($Row);
	$Sel = '';
	if($Page['ID'] == $Default){
		$Sel .= ' checked="checked"';
	}
	if(!empty($Default)){
	$Path = dais_inPath($Default);
		$Path = explode(',', $Path);
		//dump($Path);
		$Path[] = $_SESSION['DocumentLoaded'];
		if(in_array($Page['ID'], $Path)){
			$Sel .= ' disabled="disabled"';		
		}
	}else{
		if(!empty($_SESSION['DocumentLoaded'])){
			if($_SESSION['DocumentLoaded'] == $Page['ID']){
				$Sel .= ' disabled="disabled"';		
			}
		}
	}
		$Tree .= '<div style="padding:3px 3px 3px '.(3*($Level*4)).'px;" class="list_row4"><label for="pageNo'.$Page['ID'].'"><img src="system/dais/images/'.$Icon.'" width="16" height="16" border="0" align="absmiddle" /><input type="radio" value="'.$Page['ID'].'" name="LoadDocument" id="pageNo'.$Page['ID'].'" '.$Sel.' /> '.$Page['Title'].'</label>';
		//$Tree .= '<div style="padding:3px 3px 3px 38px; display:none;" class="page_descriptions">'.$Page['MettaDesc'].'</div>';
		$Tree .= '</div>';
		
		$Tree .= dais_fetchPageTreeArray($Page['ID'], $Level+1, $Default);
	}
return $Tree;
}


function dais_pagetree($SelectType = 'S', $Default=0, $Name = 'My Pages', $callback = false, $ValueName, $ID = 0, $Level = 1){
global $wpdb;
if($SelectType == 'M'){
		$SelType = '[]';
		$InputType = 'checkbox';
		$SelectLight = 'selector';
	}else{
		$SelType = '';
		$InputType = 'radio';
		$SelectLight = 'bglight';
	}
        $Result = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_parent = $ID AND post_type='page'" );
	//$Result = mysql_query($Query);
    $Row = 'list_row2';
	$Tree = '';
    foreach($Result as $Page){
    
	$Icon = 'page.png';

	$Script = '';
	if(is_array($Default)){
		if(in_array($Page->ID, $Default)){
			$Script = 'checked="checked"';
		}
	}
	$callbackscript = '';
	if(!empty($callback)){
		$callbackscript = 'onchange="'.$callback.'(this.value);"';
	}
	
	$Row = dais_rowswitch($Row);
		//$Tree .= '<div style="padding:3px;" class="'.$Row.'"><label for="pageNo'.$Page['ID'].'"><img src="system/dais/images/'.$Icon.'" width="16" height="16" border="0" align="middle" /><input name="Data[Content][PageList]'.$SelType.'" type="'.$InputType.'" id="pageNo'.$Page['ID'].'" value="'.$Page['ID'].'" '.$Script.'  '.$callbackscript.' >'.$Page['Title'].'</label></div>';
		$Tree .= '<div style="padding:3px 3px 3px '.(3*($Level*4)).'px;" class="list_row4"><label for="'.$ValueName.'_'.$Page->ID.'"><img src="'.WP_PLUGIN_URL.'/db-toolkit/images/'.$Icon.'" width="16" height="16" border="0" align="absmiddle" /><input name="Data[Content]['.$ValueName.']'.$SelType.'" type="'.$InputType.'" id="'.$ValueName.'_'.$Page->ID.'" value="'.$Page->ID.'" '.$Script.'  '.$callbackscript.' > '.$Page->post_title.'</label>';
		$Tree .= '</div>';
		$Tree .= dais_pagetree($SelectType, $Default, $Name, $callback,$ValueName, $Page->ID, $Level+1);
	}
return $Tree;
}
/// End File Selection Stuff

function dais_standardsetupbuttons($Element){

return '<input name="Data[ID]" type="hidden" id="Data[Element]" value="'.$Element['ID'].'">
<input name="Data[Element]" type="hidden" id="Data[Element]" value="'.$Element['Element'].'">
<input name="Data[Position]" type="hidden" id="Data[Position]" value="'.$Element['Position'].'">
<input name="Data[Type]" type="hidden" id="Type" value="plugin">
<input name="Save" type="submit" class="button-primary" id="Save" value="Save" />
<a class="button-primary" href="'.str_replace('&interface='.$Element['ID'], '', str_replace('&dt_newInterface=true', '', $_SERVER['REQUEST_URI'])).'">Close</a>';

}


function dais_customfield($Type, $Title, $Name, $ID = false, $Row = 'list_row1' , $Default = '', $Att = ''){
	$Default = htmlentities($Default);
if($ID === false){
	$ID = $Name;
}
if($Type == 'textarea'){
	$Return = '<div style="padding:3px" class="'.$Row.'">';
	$Return .= ''.$Title.':<br /> <textarea name="Data[Content]['.$Name.']" rows="8" id="'.$ID.'" '.$Att.' />'.$Default.'</textarea>';
	$Return .= '</div>';
}else{
	$Return = '<div style="padding:3px" class="'.$Row.'">';
	$Return .= ''.$Title.':<input type="'.$Type.'" name="Data[Content]['.$Name.']" id="'.$ID.'" value="'.$Default.'" '.$Att.' />';
	$Return .= '</div>';
}
return $Return;
}



?>