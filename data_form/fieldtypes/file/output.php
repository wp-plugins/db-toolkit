<?php
switch($Types[1]) {
    case 'image':

        $Value = explode('?', $Data[$Field]);

        $Vars = array();
        $Vars['q'] = '75';
        $ClassName = '';
        if(!empty($Config['_ImageClassName'][$Field])){
            $ClassName = $Config['_ImageClassName'][$Field];
        }
        if(!empty($Config['_ImageCompression'][$Field])){
            $Vars['q'] = $Config['_ImageCompression'][$Field];
        }
        if(!empty($Config['_ImageSizeY'][$Field])){
            if($Config['_ImageSizeY'][$Field] != 'auto'){
                $Vars['h'] = $Config['_ImageSizeY'][$Field];
            }
        }
        if(!empty($Config['_ImageSizeX'][$Field])){
            if($Config['_ImageSizeX'][$Field] != 'auto'){
                $Vars['w'] = $Config['_ImageSizeX'][$Field];
            }
        }

        $Vars = build_query($Vars);

        $Source = WP_PLUGIN_URL.'/db-toolkit/libs/timthumb.php?src='.urlencode($Value[0]).'&'.$Vars;
        if(!empty($Config['_ImageURLOnly'][$Field])){
            return $Source;
        }

        $Out .= '<img src="'.$Source.'" class="'.$ClassName.'">';


        break;
    case 'file':
        if(!empty($Data[$Field])) {
            
            $File = explode('?', $Data[$Field]);
            
            $Dets = pathinfo($File[1]);            
            $ext = strtolower($Dets['extension']);
            if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')){
                    $Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" align="absmiddle" />&nbsp;';
            }else{
                    $Icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" align="absmiddle" />&nbsp;';
            }
            //vardump($Data[$Field]);
            $FileSrc = str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $File[0]);
            //echo filesize($File[0]);
            $Size = file_return_bytes(filesize($FileSrc));
            
            //$Out .= $Data[$Field];
            //$Out .= '<div class="captions">'.df_parsecamelcase($Image[1]).'</div>';
            $Out .= $Icon.'<a href="'.$File[0].'" target="_blank" >'.$File[1].'</a> ('.$Size.')';
        }else {
            $Out .= 'No file uploaded.';
        }
        break;
    case 'multi';
        if(empty($Data[$Field])) {
            break;
        }
        $Out = $Data[$Field];
        if($Values = unserialize($Data[$Field])) {
            if(!empty($Values['Files'])) {
                $Out = false;
                foreach($Values['Files'] as $File) {

                    $Value = $File['StoredFilename'];
                    $Row = dais_rowswitch($Row);
                    //$File = explode('|', $Value);
                    $Dets = pathinfo($File['StoredFilename']);
                    $ext = strtolower($Dets['extension']);
                    if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')) {
                        $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" border="0" align="absmiddle" title="'.$File['OriganalFileName'].'" /> ';
                    }else {
                        $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" border="0" align="absmiddle" title="'.$File['OriganalFileName'].'" /> ';
                    }
                    $Out .= '<div class="'.$Row.'"><a href="'.$File['StoredFilename'].'" target="_blank">'.$Icon.' '.$File['OriganalFileName'].'</a></div>';

                }

                break;
            }
            //$Values = $Data[$Field];
            $Out = '';
            $Row = 'list_row2';
            foreach($Values as $Value) {
                $Row = dais_rowswitch($Row);
                $File = explode('|', $Value);
                $Dets = pathinfo($File[1]);
                $ext = strtolower($Dets['extension']);
                if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')) {
                    $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" border="0" align="absmiddle" title="'.$File[1].'" /> ';
                }else {
                    $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" border="0" align="absmiddle" title="'.$File[1].'" /> ';
                }
                $Out .= '<div class="'.$Row.'"><a href="'.$File[0].'" target="_blank">'.$Icon.' '.$Dets['basename'].'</a></div>';
            }
            break;
        }else {
            $File = explode('|', $Value);
            $Dets = pathinfo($File[1]);
            $ext = strtolower($Dets['extension']);
            if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif')) {
                $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/'.$ext.'.gif" align="absmiddle" /> ';
            }else {
                $Icon = '<img src="'.WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/file/icons/file.gif" align="absmiddle" /> ';
            }
        }

        $Out = $Icon.$File[1];
        break;

}
?>