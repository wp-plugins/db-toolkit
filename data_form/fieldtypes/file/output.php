<?php

switch($Types[1]){
		case 'image':
		if(!empty($Data[$Field])){
            
			$Image = explode('|', $Data[$Field]);
			$Image[1] = strtok($Image[1], '.');
            
            $path = wp_upload_dir();
            
			if(!file_exists(str_replace($path['url'], $path['path'], $Image[0]))){
				$Image[0] = WP_PLUGIN_URL.'/db-toolkit/data_report/nopic.jpg';
				$Image[1] = 'image unavailable';
			}
		}else{
			$Image[0] = WP_PLUGIN_URL.'/db-toolkit/data_report/nopic.jpg';
			$Image[1] = 'image unavailable';
		}
			$Out .= '<div style="text-align:center; cursor:pointer;" class="'.$Row.'" id="'.md5($Image[0]).'_img_'.$Field.'">';
			if(!empty($Config['_ImageSquareI'][$Field])){
				$Out .= useimage($Image[0], 0, $Config['_ImageSizeM'][$Field]).'</a></div>';
			}else{
				$Out .= useimage($Image[0], 6, $Config['_ImageSizeM'][$Field]).'</a></div>';
			}
			$Out .= '<div class="caption">'.df_parsecamelcase($Image[1]).'</div>';
              
			  	$ImageHeight = GetImageDimentions ($Image[0], 'h');
                $ImageWidth = GetImageDimentions ($Image[0], 'w');
				if ( $ImageHeight > $ImageWidth ){				
					if($ImageHeight < $Config['_ImageSizeF'][$Field]){
						$Config['_ImageSizeF'][$Field] = $ImageHeight;	
					}
					$new_width = $ImageWidth * ( $Config['_ImageSizeF'][$Field] / $ImageHeight );
					$BoxSize = "
						height: ".($Config['_ImageSizeF'][$Field]+40).",
						width: ".(round($new_width)+20)."
						";
				}else{
					if($ImageWidth < $Config['_ImageSizeF'][$Field]){
						$Config['_ImageSizeF'][$Field] = $ImageWidth;	
					}
					$new_height = $ImageHeight * ( $Config['_ImageSizeF'][$Field] / $ImageWidth );
					$BoxSize = "
						height: 'auto',
						width: 'auto'
						";
				}
				
			$_SESSION['dataform']['OutScripts'] .= "
				jQuery('#".md5($Image[0])."_img_".$Field."').click(function(){
					//$.facebox('<h2>".$Image[1]."</h2><img src=\"".$Image[0]."\" />');
					//var Dialog = new Boxy('".useimage($Image[0], 6, $Config['_ImageSizeF'][$Field])."', {title: '".$Image[1]."', modal: false, unloadOnHide: true});
					if(jQuery(\"#ui-dialog-".md5($Image[0])."\").length == 1){
						jQuery(\"#ui-dialog-".md5($Image[0])."\").remove();
					}
					jQuery('body').append('<div id=\"ui-dialog-".md5($Image[0])."\" title=\"".$Image[1]."\"><div style=\"height:".$new_height."px;\">".useimage($Image[0], 6, $Config['_ImageSizeF'][$Field])."</p></div>');
					jQuery(\"#ui-dialog-".md5($Image[0])."\").dialog({
						position: 'center',
						buttons: {
							'Close': function() {
								jQuery(this).dialog(\"close\");
								}
							},
						
						".$BoxSize."
					});
					
				});
			";
			break;
		case 'file':
			if(!empty($Data[$Field])){
				$File = explode('|', $Data[$Field]);
				$Dets = pathinfo($File[1]);
				$ext = strtolower($Dets['extension']);
				if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
					$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" align="absmiddle" />&nbsp;';
				}else{
					$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" align="absmiddle" />&nbsp;';					
				}
				$Size = return_bytes(filesize($File[0]));
			//$Out .= $Data[$Field];
			//$Out .= '<div class="captions">'.df_parsecamelcase($Image[1]).'</div>';
			$Out .= $Icon.'<a href="'.$File[0].'" target="_blank" >'.$File[1].'</a> ('.$Size.')';
			}else{
				$Out .= 'No file uploaded.';
			}
			break;
		case 'multi';
			if(empty($Data[$Field])){
				break;
			}
			$Out = $Data[$Field];
			if($Values = unserialize($Data[$Field])){
				if(!empty($Values['Files'])){
					$Out = false;
					foreach($Values['Files'] as $File){
						
						$Value = $File['StoredFilename'];
						$Row = dais_rowswitch($Row);
						//$File = explode('|', $Value);
						$Dets = pathinfo($File['StoredFilename']);
						$ext = strtolower($Dets['extension']);
						if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
							$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" border="0" align="absmiddle" title="'.$File['OriganalFileName'].'" /> ';
						}else{
							$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" border="0" align="absmiddle" title="'.$File['OriganalFileName'].'" /> ';
						}
						$Out .= '<div class="'.$Row.'"><a href="'.$File['StoredFilename'].'" target="_blank">'.$Icon.' '.$File['OriganalFileName'].'</a></div>';
						
					}
					
				break;	
				}
				//$Values = $Data[$Field];
				$Out = '';
				$Row = 'list_row2';
				foreach($Values as $Value){
					$Row = dais_rowswitch($Row);
					$File = explode('|', $Value);
					$Dets = pathinfo($File[1]);
					$ext = strtolower($Dets['extension']);
					if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
						$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" border="0" align="absmiddle" title="'.$File[1].'" /> ';
					}else{
						$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" border="0" align="absmiddle" title="'.$File[1].'" /> ';
					}
					$Out .= '<div class="'.$Row.'"><a href="'.$File[0].'" target="_blank">'.$Icon.' '.$Dets['basename'].'</a></div>';
				}
				break;
			}else{
				$File = explode('|', $Value);
				$Dets = pathinfo($File[1]);
				$ext = strtolower($Dets['extension']);
				if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
					$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" align="absmiddle" /> ';
				}else{
					$Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" align="absmiddle" /> ';					
				}
			}
			
			$Out = $Icon.$File[1];
			break;
			
	}
?>