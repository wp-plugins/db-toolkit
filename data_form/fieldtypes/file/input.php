<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
//<input name="dataForm['.$ElementID.']['.$Field.']" type="'.$Type.'" id="entry_'.$ElementID.'_'.$Field.'" value="'.$Val.'" class="textfield '.$Req.'" />';
if($FieldSet[1] == 'image'){
	if(!empty($Defaults[$Field])){
		$im = explode('|', $Defaults[$Field]);
                if(!empty($Config['_ImageSquareM'][$Field])){
                        echo UseImage($im[0], 0, $Config['_ImageSizeM'][$Field]);
                }else{
                        echo UseImage($im[0], 6, $Config['_ImageSizeM'][$Field]);
                }//echo useimage($im[0], 5, $Element['Content']['_ImageSizeM'][$Field]);
		//echo '<div style="padding:3px;" class="list_row1"><input type="checkbox" name="deleteImage['.$Field.']" id="image_'.$Element['ID'].'_'.$Field.'" value="1" /> <label for="image_'.$Element['ID'].'_'.$Field.'">Remove Image</label></div>';
                if(@getimagesize($im[0])){
                    echo '<div style="padding:3px;" class="list_row1"><input type="checkbox" name="deleteImage['.$Field.']" id="image_'.$Element['ID'].'_'.$Field.'" value="1" /> <strong>Remove Image</strong></div>';
                }

        }
	$Return = '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" style="width:97%;" class="'.$Req.'" />';
}


if($FieldSet[1] == 'file'){
	$Return = '';
	if(!empty($Defaults[$Field])){
			$File = explode('|', $Defaults[$Field]);
			$ext = strtolower($Dets['extension']);
			$UniID = uniqid();
			
		$Return .= $Icon.'<a href="'.$File[0].'" target="_blank" >'.$File[1].'</a></div>';
		$Return .= '<div style="padding:3px;" class="list_row1"><input type="checkbox" name="deleteImage['.$Field.']" id="image_'.$Element['ID'].'_'.$Field.'" value="1" /> <label for="image_'.$Element['ID'].'_'.$Field.'">Remove File</label></div>';
		
	}
	$Return .= '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'" />';
}
if($FieldSet[1] == 'mp3'){
    $Return = '';
	if(!empty($Defaults[$Field])){
			$File = explode('|', $Defaults[$Field]);
            $UniID = uniqid($Element['ID'].'_');
            $Return .= '<span id="'.$UniID.'">'.$File[1].'</span>';
            $_SESSION['dataform']['OutScripts'] .= "
                AudioPlayer.embed(\"".$UniID."\", {
                    soundFile: \"".$File[0]."\",
                    titles: \"".$File[1]."\"
                });
            ";

		//$Out .= $Data[$Field];
		//$Out .= '<div class="captions">'.df_parsecamelcase($Image[1]).'</div>';
		$Return .= '<div style="padding:3px;" class="list_row1"><input type="checkbox" name="deleteImage['.$Field.']" id="image_'.$Element['ID'].'_'.$Field.'" value="1" /> <label for="image_'.$Element['ID'].'_'.$Field.'">Remove File</label></div>';

	}
	$Return .= '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'" />';
    
}


if($FieldSet[1] == 'multi'){

	$Return = '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'" />';
	$Return .= '<div id="'.$Element['ID'].$Field.'" class="uploadifyFrame">';
	if(!empty($Defaults[$Field])){
		$FileList = unserialize($Defaults[$Field]);
		foreach($FileList as $File){
			$FileParts = explode('|', $File);
			$Ext = pathinfo($FileParts[1]);
			$icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" width="16" height="16" align="absmiddle" />';
			if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.strtolower($Ext['extension']).'.gif')){
				$icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.strtolower($Ext['extension']).'.gif" width="16" height="16" align="absmiddle" />';
			}
			$entryID = uniqid();
			$Return .= '<div class="uploadifyQueueItem" id="box_entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'">';
			$Return .= '<div class="cancel"><img border="0" class="togglebutton" src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/cancel.png" style="cursor:pointer;" onclick="file_removeFile(\'entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'\');""><img border="0" class="togglebutton" src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/add.png" style="cursor:pointer; display:none;" onclick="file_undoRemoveFile(\'entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'\');""></div>';
			$Return .= '<span class="fileName">'.$icon.' <a href="'.$FileParts[0].'" target="_blank">'.$FileParts[1].'</a></span>';
			$Return .= '<input type="hidden" name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'" value="'.$File.'" class="'.$Req.'" />';
			$Return .= '</div>';				
		}
	}
	$Return .= '</div>';
	$Return .= '<div id="'.$Element['ID'].$Field.'_uploaded" class="uploadifyFrame"></div>';
	$_SESSION['dataform']['OutScripts'] .="
		
	var entry".$Element['ID'].$Field." = new Array();
	var uploadComplete = false;
	jQuery(\"#entry_".$Element['ID']."_".$Field."\").uploadify({
		'uploader'       : WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/scripts/uploadify.swf',
		'script'		 : '?uploadify=".urlencode(base64_encode($Req.'_'.$Element['ID'].'_'.$Field))."',
		'cancelImg'      : WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/cancel.png',
		'queueID'        : '".$Element['ID'].$Field."',
		'auto'           : false,
		'rollover'		 : true,
		'multi'          : true,
		'width'			 : '83',
		'height'		 : '21',
		'buttonImg'      : WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/select.gif',
		'wmode'          : 'transparent',
		'onSelectOnce' 	 : function(a,b){
			if(b.fileCount > 0){
				jQuery('#data_form_".$Element['ID']."').bind('submit', function(){
					jQuery('#entry_".$Element['ID']."_".$Field."').uploadifyUpload();
					if(uploadComplete == false){
						return false;
					}else{
						return true;
					}
				});
			}else{
				alert('done');	
			}
		},
		'onComplete'	 : function(e,q,f,r,d) {
				//alert(r);
				jQuery('#".$Element['ID'].$Field."_uploaded').append(r);
			},
		'onAllComplete'  : function(){
			uploadComplete = true;
			jQuery('#data_form_".$Element['ID']."').submit()
		}
	})
		
		
	";

}
echo $Return;

?>