<?php
// Functions

function file_imageConfig($Field, $Table, $Config = false){


        $Compression = 75;
        $Width = 'auto';
        $Height = 'auto';
	if(!empty($Config['Content']['_IconSizeX'][$Field])){
		$Width = $Config['Content']['_IconSizeX'][$Field];
	}
	if(!empty($Config['Content']['_IconSizeY'][$Field])){
		$Height = $Config['Content']['_IconSizeY'][$Field];
	}
        $Return = '<h2>List Icon</h2>';
	$Return .= '<div class="list_row1" style="padding:3px;">Icon Size: <input type="text" name="Data[Content][_IconSizeX]['.$Field.']" value="'.$Width.'" class="textfield" size="3" maxlength="4" style="width:40px;" /> X <input type="text" name="Data[Content][_IconSizeY]['.$Field.']" value="'.$Height.'" class="textfield" size="3" maxlength="4" style="width:40px;" /></div>';
        if(!empty($Config['Content']['_IconCompression'][$Field])){
		$Compression = $Config['Content']['_IconCompression'][$Field];
	}
	$Return .= '<div class="list_row1" style="padding:3px;">Icon Compression: <input type="text" name="Data[Content][_IconCompression]['.$Field.']" value="'.$Compression.'" class="textfield" size="3" maxlength="4" style="width:40px;" />%</div>';
        $Class = '';
 	if(!empty($Config['Content']['_IconClassName'][$Field])){
		$Class = $Config['Content']['_IconClassName'][$Field];
	}
        $Return .= '<div class="list_row1" style="padding:3px;">Icon Class: <input type="text" name="Data[Content][_IconClassName]['.$Field.']" value="'.$Class.'" class="textfield" /></div>';
        $Sel = '';
	if(!empty($Config['Content']['_IconURLOnly'][$Field])){
		$Sel = 'checked="checked"';//$Config['Content']['_ImageSquare'][$Field]
	}
        $Return .= '<div class="list_row2" style="padding:3px;">URL Only: <input type="checkbox" name="Data[Content][_IconURLOnly]['.$Field.']" value="1" '.$Sel.' /></div>';



        $Compression = 75;
        $Width = 'auto';
        $Height = 'auto';
	if(!empty($Config['Content']['_ImageSizeX'][$Field])){
		$Width = $Config['Content']['_ImageSizeX'][$Field];
	}
	if(!empty($Config['Content']['_ImageSizeY'][$Field])){
		$Height = $Config['Content']['_ImageSizeY'][$Field];
	}
        $Return .= '<h2>View Image</h2>';
	$Return .= '<div class="list_row1" style="padding:3px;">Image Size: <input type="text" name="Data[Content][_ImageSizeX]['.$Field.']" value="'.$Width.'" class="textfield" size="3" maxlength="4" style="width:40px;" /> X <input type="text" name="Data[Content][_ImageSizeY]['.$Field.']" value="'.$Height.'" class="textfield" size="3" maxlength="4" style="width:40px;" /></div>';
        if(!empty($Config['Content']['_ImageCompression'][$Field])){
		$Compression = $Config['Content']['_ImageCompression'][$Field];
	}
	$Return .= '<div class="list_row1" style="padding:3px;">Image Compression: <input type="text" name="Data[Content][_ImageCompression]['.$Field.']" value="'.$Compression.'" class="textfield" size="3" maxlength="4" style="width:40px;" />%</div>';
        $Class = '';
 	if(!empty($Config['Content']['_ImageClassName'][$Field])){
		$Class = $Config['Content']['_ImageClassName'][$Field];
	}
        $Return .= '<div class="list_row1" style="padding:3px;">Image Class: <input type="text" name="Data[Content][_ImageClassName]['.$Field.']" value="'.$Class.'" class="textfield" /></div>';
        $Sel = '';
	if(!empty($Config['Content']['_ImageURLOnly'][$Field])){
		$Sel = 'checked="checked"';//$Config['Content']['_ImageSquare'][$Field]
	}
        $Return .= '<div class="list_row2" style="padding:3px;">URL Only: <input type="checkbox" name="Data[Content][_ImageURLOnly]['.$Field.']" value="1" '.$Sel.' /></div>';



return $Return;

}


function file_handleInput($Field, $Input, $FieldType, $Config, $predata){
	if($FieldType == 'multi'){
		return $Input;
	}
	if(!empty($_POST['deleteImage'][$Field])){
		$FileInfo = explode('?', $predata[$Field]);
		if(file_exists($FileInfo[0])){
			unlink($FileInfo[0]);
		}
		return '';
	}
	if(empty($_FILES['dataForm']['name'][$Config['ID']][$Field])){
		return $predata[$Field];
	}
	// Create Directorys
	if(!empty($_FILES['dataForm']['size'][$Config['ID']][$Field])){

        $path = wp_upload_dir();

		// set filename and paths
		$Ext = pathinfo($_FILES['dataForm']['name'][$Config['ID']][$Field]);
		$newFileName = uniqid($Config['ID'].'_').'.'.$Ext['extension'];
		$newLoc = $path['path'].'/'.$newFileName;
		//$urlLoc = $path['url'].'/'.$newFileName;
		$GLOBALS['UploadedFile'][$Field] = $newLoc;

                $upload = wp_upload_bits($_FILES['dataForm']['name'][$Config['ID']][$Field], null, file_get_contents($_FILES['dataForm']['tmp_name'][$Config['ID']][$Field]));
		//move_uploaded_file($_FILES['dataForm']['tmp_name'][$Config['ID']][$Field], $newLoc);
		
	//return $newLoc;

        if($FieldType == 'image'){

            $imageWidth = ($Config['Content']['_ImageSizeX'][$Field] == 'auto' ? '0' : $Config['Content']['_ImageSizeX'][$Field]);
            $imageHeight = ($Config['Content']['_ImageSizeY'][$Field] == 'auto' ? '0' : $Config['Content']['_ImageSizeY'][$Field]);

            $iconWidth = ($Config['Content']['_IconSizeX'][$Field] == 'auto' ? '0' : $Config['Content']['_IconSizeX'][$Field]);
            $iconHeight = ($Config['Content']['_IconSizeY'][$Field] == 'auto' ? '0' : $Config['Content']['_IconSizeY'][$Field]);

            // crunch sizes

            $image = image_resize($upload['file'], $imageWidth, $imageHeight, true);
            $icon = image_resize($upload['file'], $iconWidth, $iconHeight, true);
        }
	return $upload['url'];
	}
	
}

function file_processValue($Value, $Type, $Field, $Config, $EID){

	if(!empty($Value)){
		//dump($Value);
		//dump($Type);
		//dump($Field);
		//dump($Config);
	//die;
		switch ($Type){
			case 'image';
                                    $Value = strtok($Value, '?');
                                    $imageWidth = ($Config['_ImageSizeX'][$Field] == 'auto' ? '0' : $Config['_ImageSizeX'][$Field]);
                                    $imageHeight = ($Config['_ImageSizeY'][$Field] == 'auto' ? '0' : $Config['_ImageSizeY'][$Field]);

                                    $iconWidth = ($Config['_IconSizeX'][$Field] == 'auto' ? '0' : $Config['_IconSizeX'][$Field]);
                                    $iconHeight = ($Config['_IconSizeY'][$Field] == 'auto' ? '0' : $Config['_IconSizeY'][$Field]);

                                    $uploadVars = wp_upload_dir();

                                    

                                    $SourceFile = str_replace($uploadVars['baseurl'], $uploadVars['basedir'], $Value);
                                    if(!file_exists($SourceFile)){
                                        return 'Image does not exists.';
                                    }
                                    $dim = getimagesize($SourceFile);                                    
                                    $newDim = image_resize_dimensions($dim[0], $dim[1], $iconWidth, $iconHeight, true);

                                    
                                    $Sourcepath = pathinfo($SourceFile);
                                    $URLpath = pathinfo($Value);
                                    $iconURL = $URLpath['dirname'].'/'.$URLpath['filename'].'-'.$newDim[4].'x'.$newDim[5].'.'.$URLpath['extension'];
                                    if(!file_exists($Sourcepath['dirname'].'/'.$Sourcepath['filename'].'-'.$newDim[4].'x'.$newDim[5].'.'.$Sourcepath['extension'])){
                                        $image = image_resize($SourceFile, $imageWidth, $imageHeight, true);
                                        $icon = image_resize($SourceFile, $iconWidth, $iconHeight, true);
                                    }
                                    $ClassName = '';
                                    if(!empty($Config['_ImageClassName'][$Field])){
                                        $ClassName = 'class="'.$Config['_ImageClassName'][$Field].'" ';
                                    }
                                    
                                    if(!empty($Config['_IconURLOnly'][$Field])){
                                        return $iconURL;
                                    }
                                    return '<img src="'.$iconURL.'" '.$ClassName.image_hwstring($iconWidth, $iconHeight).'>';


		 		break;
			case 'mp3';
					$File = explode('?', $Value);
					$UniID = uniqid($EID.'_');
				//$ReturnData = '<span id="'.$UniID.'">'.$File[1].'</span>';
                                $ReturnData = '<audio id="'.$UniID.'" src="'.$File[0].'">unavailable</audio>';
				
				$_SESSION['dataform']['OutScripts'] .= "
					AudioPlayer.embed(\"".$UniID."\", {
					";
						if(!empty($Config['_PlayerCFG']['Autoplay'][$Field])){
							$_SESSION['dataform']['OutScripts'] .= " autostart: 'yes', ";
						}
						if(!empty($Config['_PlayerCFG']['Animation'][$Field])){
							$_SESSION['dataform']['OutScripts'] .= " animation: 'yes', "; 	
						}
				$_SESSION['dataform']['OutScripts'] .= "
                                                transparentpagebg: 'yes',
						soundFile: \"".$File[0]."\",
						titles: \"".$File[1]."\"
					});

				";
                               $_SESSION['dataform']['OutScripts'] .="
                                jQuery(document).ready(function($) {
                                    AudioPlayer.setup(\"".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/player.swf\", {
                                        width: '100%',
                                        initialvolume: 100,
                                        transparentpagebg: \"yes\",
                                        left: \"000000\",
                                        lefticon: \"FFFFFF\"
                                    });
                                 });";
				return $ReturnData;
				break;
			case 'file';
			case 'multi';
				if($Data = unserialize($Value)){
					$Values = $Data;
					$Output = '';
					foreach($Values as $Value){
						$File = explode('?', $Value);
						$Dets = pathinfo($File[1]);
						$ext = strtolower($Dets['extension']);
						if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
							$Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" border="0" align="absmiddle" title="'.$File[1].'" />&nbsp;';
						}else{
							$Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" border="0" align="absmiddle" title="'.$File[1].'" />&nbsp;';
						}
						$Output .= '<a href="'.$File[0].'" target="_blank">'.$Icon.'</a>&nbsp;';
					}
					return $Output;
				}else{
                                    
                                    if(empty($Value)){
                                        return 'no file uploaded';
                                    }
					$File = explode('?', $Value);
					$Dets = pathinfo($File[1]);
					$ext = strtolower($Dets['extension']);
					if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
						$Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" align="absmiddle" />&nbsp;';
					}else{
						$Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" align="absmiddle" />&nbsp;';
					}
				}
				
				return $Icon.$File[1];
				break;
		 
		 
		}
	
	return;
	}
}





/// Uploader Processessor
if(!empty($_GET['uploadify'])){

        $string = base64_decode(urldecode($_GET['uploadify']));
        $fieldData = explode('_', $string);
        //vardump($fieldData);
        //vardump($string);
        //vardump($_FILES);

	if(!empty($_FILES['Filedata']['size'])){

        $path = wp_upload_dir();
		// set filename and paths
		$Ext = pathinfo($_FILES['Filedata']['name']);
		$newFileName = uniqid().'.'.$Ext['extension'];
		$newLoc = $path['path'].'/'.$newFileName;

        $upload = wp_upload_bits($newFileName, null, file_get_contents($_FILES['Filedata']['tmp_name']));
		//move_uploaded_file($_FILES['dataForm']['tmp_name'][$Config['ID']][$Field], $newLoc);

	//return $newLoc;
        //vardump($upload);
	//return $upload['url'].'?'.$_FILES['dataForm']['name'][$Config['ID']][$Field];

        echo '<input type="hidden" value="'.$upload['url'].'?'.$_FILES['Filedata']['name'].'" id="entry'.$string.'" name="dataForm['.$fieldData[1].'_'.$fieldData[2].']['.$fieldData[3].']">';
	}

	exit;

}


function file_playerSetup($Field, $Table, $Config = false){
	//$Return = '<div class="list_row1" style="padding:3px;">Icon Size (px): <input type="text" name="Data[Content][_ImageSizeI]['.$Field.']" value="'.$icon.'" class="textfield" size="3" maxlength="3" /> Square Crop: <input type="checkbox" name="Data[Content][_ImageSquareI]['.$Field.']" value="1" '.$Sel1.' /></div>';

	$Return = 'Player Preview<div style="padding:5px; width: 200px;" id="'.$Field.'_preview"></div>';
        $Sel = '';
        if(!empty($Config['Content']['_PlayerCFG']['Autoplay'][$Field])){
            $Sel = 'checked="checked"';
        }
	$Return .= '<div style="padding:3px;">Auto Play: <input type="checkbox" name="Data[Content][_PlayerCFG][Autoplay]['.$Field.']" id="'.$Field.'_autoPlay" '.$Sel.' value="yes" /> In a list, the last item will auto play</div>';
	//$Return .= '<div style="padding:3px;">Animation: <input type="checkbox" name="Data[Content][_PlayerCFG][Animation]['.$Field.']" id="'.$Field.'_autoPlay" value="no" /> Unchecked, the player will be open, checked minimized.</div>';
		$_SESSION['dataform']['OutScripts'] .= "
			AudioPlayer.embed(\"".$Field."_preview\", {
			";
				if(!empty($Config['Content']['_PlayerCFG']['Autoplay'][$Field])){
					$_SESSION['dataform']['OutScripts'] .= " autoplay: 'yes', "; 	
				}
				if(!empty($Config['Content']['_PlayerCFG']['Animation'][$Field])){
					$_SESSION['dataform']['OutScripts'] .= " animation: 'yes', "; 	
				}
		$_SESSION['dataform']['OutScripts'] .= "
				demomode: \"yes\"
			});
		";

return $Return;
}



function file_return_bytes($FileSize) {
   $val = trim($FileSize);
   $last = strtolower($FileSize{strlen($FileSize)-1});
   switch($last) {
       // The 'G' modifier is available since PHP 5.1.0
       case 'g':
           $FileSize *= 1024;
       case 'm':
           $FileSize *= 1024;
       case 'k':
           $FileSize *= 1024;
   }

if($FileSize < 0 || $FileSize == ''){
	$Pre = '0&nbsp;Bytes';
}elseif($FileSize < 1024 AND $FileSize > 0){
	$Pre = $FileSize.'&nbsp;Bytes';
}elseif($FileSize >= 1024 AND $FileSize < 1048576 ){
	$Pre = round(($FileSize/1024)).'&nbsp;KB';
}elseif($FileSize >= 1048576 AND $FileSize < 1073741824){
	$Pre = round(($FileSize/1048576),1).'&nbsp;MB';
}elseif($FileSize >= 1073741824){
	$Pre = round(($FileSize/1073741824),3).'&nbsp;GB';
}elseif($FileSize >= 1073741824){
	$Pre = round(($FileSize/1099511627776),3).'&nbsp;TB';
}

   return $Pre;
}








?>