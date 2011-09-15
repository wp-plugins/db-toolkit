<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting
//<input name="dataForm['.$ElementID.']['.$Field.']" type="'.$Type.'" id="entry_'.$ElementID.'_'.$Field.'" value="'.$Val.'" class="textfield '.$Req.'" />';
if($FieldSet[1] == 'image'){
        $Return = '';
	if(!empty($Defaults[$Field])){
            $Value = explode('?', $Defaults[$Field]);

            $Vars = array();
            $Vars['q'] = '75';
            $ClassName = '';
            if(!empty($Config['_IconClassName'][$Field])){
                $ClassName = $Config['_IconClassName'][$Field];
            }
            if(!empty($Config['_IconCompression'][$Field])){
                $Vars['q'] = $Config['_IconCompression'][$Field];
            }
            if(!empty($Config['_IconSizeY'][$Field])){
                if($Config['_IconSizeY'][$Field] != 'auto'){
                    $Vars['h'] = $Config['_IconSizeY'][$Field];
                }
            }
            if(!empty($Config['_IconSizeX'][$Field])){
                if($Config['_IconSizeX'][$Field] != 'auto'){
                    $Vars['w'] = $Config['_IconeSizeX'][$Field];
                }
            }

            $Vars = build_query($Vars);

            $Source = WP_PLUGIN_URL.'/db-toolkit/libs/timthumb.php?src='.urlencode($Value[0]).'&'.$Vars;
            if(!empty($Config['_IconURLOnly'][$Field])){
                return $Source;
            }

            $Return = '<img src="'.$Source.'" class="'.$ClassName.'">';

        }
	$Return .= '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" style="width:97%;" class="'.$Req.'" />';
}


if($FieldSet[1] == 'file'){
	$Return = '';
	if(!empty($Defaults[$Field])){
			$File = explode('?', $Defaults[$Field]);
			$ext = strtolower($Dets['extension']);
			$UniID = uniqid();
			
		$Return .= $Icon.'<a href="'.$File[0].'" target="_blank" >'.$File[1].'</a></div>';
		$Return .= '<div style="padding:3px;" class="list_row1"><input type="checkbox" name="deleteImage['.$Field.']" id="image_'.$Element['ID'].'_'.$Field.'" value="1" /> Remove File</div>';
		
	}
	$Return .= '<input type="file" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'" />';
        $Return .= '<div id="'.$Element['ID'].$Field.'_uploaded"></div>';

$_SESSION['dataform']['OutScripts'] .="

	var entry".$Element['ID'].$Field." = new Array();
	var uploadComplete = false;
	jQuery(\"#entry_".$Element['ID']."_".$Field."\").uploadify({
		'uploader'       : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/uploadify.swf',
		'script'	 : '?uploadify=".urlencode(base64_encode($Req.'_'.$Element['ID'].'_'.$Field))."',
		'cancelImg'      : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/icons/uploadify-cancel.png',		
		'auto'           : false,
		'multi'          : false,
		'rollover'	 : true,
		'width'          : '102',
		'height'	 : '26',
		'buttonImg'      : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/icons/select.gif',
		'onSelectOnce' 	 : function(a,b){
			if(b.fileCount > 0){

";
if(!empty($Config['_ajaxForms'])){
    $_SESSION['dataform']['OutScripts'] .="
                                jQuery('#ui-jsDialog-".$Element['ID']."').dialog('option', 'buttons', {

                                    'Close': function() {
                                        jQuery(this).dialog(\"close\");
                                    },
                                    'Save': function() {                                        
                                        jQuery('#entry_".$Element['ID']."_".$Field."').uploadifyUpload();

                                        jQuery('#data_form_".$Element['ID']."').bind('submit', function(){
                                            formData = jQuery('#data_form_".$Element['ID']."').serialize();
                                            jQuery('#ui-jsDialog-".$Element['ID']."').html('Sending...');
                                            jQuery('#ui-jsDialog-".$Element['ID']."').dialog('option', 'buttons', {});
                                            ajaxCall('df_processAjaxForm',formData, function(p){
                                                jQuery('#ui-jsDialog-".$Element['ID']."').remove();
                                                df_loadOutScripts();
                                                dr_goToPage('".$Element['ID']."', false);
                                            });
                                        });



                                    }
                                    
                                });
    ";
}else{
$_SESSION['dataform']['OutScripts'] .="
                               
				jQuery('#data_form_".$Element['ID']."').bind('submit', function(){                                
					jQuery('#entry_".$Element['ID']."_".$Field."').uploadifyUpload();
					if(uploadComplete == false){
						return false;
					}else{
						return true;
					}
				});
                                
                                ";
}
$_SESSION['dataform']['OutScripts'] .="
			}else{
				alert('done');
			}
		},
		'onComplete' : function(e,q,f,r,d) {
				//alert(r);
                                //jQuery('#entry_".$Element['ID']."_".$Field."').remove();
				jQuery('#".$Element['ID'].$Field."_uploaded').append(r);
			},
		'onAllComplete'  : function(){                
			uploadComplete = true;                        
			jQuery('#data_form_".$Element['ID']."').submit();
                        return true;
		}
	})


	";

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
			$icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" width="16" height="16" align="absmiddle" />';
			if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.strtolower($Ext['extension']).'.gif')){
				$icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/'.strtolower($Ext['extension']).'.gif" width="16" height="16" align="absmiddle" />';
			}
			$entryID = uniqid();
			$Return .= '<div class="uploadifyQueueItem" id="box_entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'">';
			$Return .= '<div class="cancel"><img border="0" class="togglebutton" src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/cancel.png" style="cursor:pointer;" onclick="file_removeFile(\'entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'\');""><img border="0" class="togglebutton" src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/add.png" style="cursor:pointer; display:none;" onclick="file_undoRemoveFile(\'entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'\');""></div>';
			$Return .= '<span class="fileName">'.$icon.' <a href="'.$FileParts[0].'" target="_blank">'.$FileParts[1].'</a></span>';
			$Return .= '<input type="hidden" name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'_'.$entryID.'" value="'.$File.'" class="'.$Req.'" />';
			$Return .= '</div>';				
		}
	}
	$Return .= '</div>';
	$Return .= '<div id="'.$Element['ID'].$Field.'_uploaded" class="uploadifyFrame"></div>';

        $_SESSION['dataform']['OutScripts'] .="
            jQuery('#".$Element['ID'].$Field."_uploaded').hide();
            jQuery('#".$Element['ID'].$Field."_uploaded').uploadify({
            'uploader' : '?uploadify=".urlencode(base64_encode($Req.'_'.$Element['ID'].'_'.$Field))."',
            'swf' : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/uploadify.swf',
            //'cancelImage' : '/uploadify/uploadify-cancel.png',
            //'auto' : true
            });

        ";

        /*$_SESSION['dataform']['OutScripts'] .="
		
	var entry".$Element['ID'].$Field." = new Array();
	var uploadComplete = false;
	jQuery(\"#entry_".$Element['ID']."_".$Field."\").uploadify({
		'uploader'       : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/scripts/uploadify.swf',
		'script'	 : '?uploadify=".urlencode(base64_encode($Req.'_'.$Element['ID'].'_'.$Field))."',
		'cancelImg'      : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/icons/cancel.png',
		'queueID'        : '".$Element['ID'].$Field."',
		'auto'           : false,
		'rollover'	 : true,
		'multi'          : true,
		'width'			 : '83',
		'height'		 : '21',
		'buttonImg'      : '".WP_PLUGIN_URL."/db-toolkit/data_form/fieldtypes/file/icons/select.gif',
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
		
		
	";*/

}
echo $Return;

?>