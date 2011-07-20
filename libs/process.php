<?php
/*
 * Core Admin Processors Library - DB Toolkit
 * (C) David Cramer 2010 - 2011
 *
 */

function dt_saveInterface($str){
    parse_str(urldecode($str), $vars);
    return dt_saveCreateInterface($vars);
}

if(!empty($_GET['exportApp'])){
    $activeApp = get_option('_dbt_activeApp');
    if(empty($activeApp))
        return;

    $app = get_option('_'.$activeApp.'_app');
    if($app['state'] == 'open'){
        exportApp($app);
    }
    return;
}
    // dumplicate interface hack
if(is_admin()) {
    
    $activeApp = get_option('_dbt_activeApp');
    if(empty($activeApp))
        return;

    $app = get_option('_'.$activeApp.'_app');
    
    if(!empty($_GET['duplicateinterface'])){
        $dupvar = get_option($_GET['duplicateinterface']);
        $oldOption = $dupvar;
        if($oldOption['Type'] == 'Cluster'){
            $NewName = uniqid($oldOption['_ClusterTitle'].' ');
            $oldOption['_ClusterTitle'] = $NewName;
            $newTitle = uniqid('dt_clstr');
            $hash = '&r=y#clusters';
            $app['clusters'][$newTitle] = $oldOption['_menuAccess'];
        }else{
            $NewName = uniqid($oldOption['_ReportDescription'].' ');
            $oldOption['_ReportDescription'] = $NewName;
            $newTitle = uniqid('dt_intfc');
            $hash = '';
            $app['interfaces'][$newTitle] = $oldOption['_menuAccess'];
        }
        $oldOption['ID'] = $newTitle;
        $oldOption['ParentDocument'] = $newTitle;
        if(update_option($newTitle, $oldOption)){
            update_option('_'.$activeApp.'_app', $app);
        }
        header( 'Location: '.$_SERVER['HTTP_REFERER'].$hash);
        exit;
    }
}

    
//// FROM dbtoolkit_admin.php
//// LINE 95
if(!empty($_POST['Data'])) {

    dt_saveCreateInterface($_POST);
    
}

function dt_saveCreateInterface($saveData){
    global $wpdb, $user;

    $activeApp = get_option('_dbt_activeApp');
    
    if(empty($activeApp))
        return;


    $app = get_option('_'.$activeApp.'_app');
    if(empty($app))
        return;


    $saveData = stripslashes_deep($saveData);
    //vardump($saveData);

    if(!empty($saveData['Data']['ID'])){
        $optionTitle = $saveData['Data']['ID'];
        $newCFG = get_option($optionTitle);
        //vardump($newCFG);
    }else{

        $optionTitle = uniqid('dt_intfc');        
        if(isset($saveData['Data']['Content']['_clusterLayout'])){
            $optionTitle = uniqid('dt_clstr');
        }

        $newOption = array();
        $newCFG['ID'] = $optionTitle;
        if(empty($saveData['dt_newInterface']))
            $saveData['dt_newInterface'] = '';


        $newCFG['_interfaceName'] = $saveData['dt_newInterface'];
        $newCFG['_interfaceType'] = 'unconfigured';
        $newCFG['_interfaceDate'] = date('Y-m-d H:i:s');
        $newCFG['ID'] = $optionTitle;
        $newCFG['Element'] = 'data_report';
        //$newCFG['Content'] = base64_encode(serialize(array()));
        $newCFG['ParentDocument'] = $optionTitle;
        $newCFG['Position'] = 0;
        $newCFG['Column'] = 0;
        $newCFG['Row'] = 0;
    }

    $app['interfaces'][$optionTitle] = $saveData['Data']['Content']['_menuAccess'];
    // Setup Index_Show's
    //echo '<br><br><br>';
    
    $Indexes = array();
    if(!empty($saveData['Data']['Content']['_Field'])){

        

        foreach($saveData['Data']['Content']['_Field'] as $Field=>$Value){
            // Make Sure the Fields EXIST!
            $wpdb->query("SELECT `".$Field."` FROM `".$saveData['Data']['Content']['_main_table']."` LIMIT 1;");
            if(mysql_errno() == '1054'){

                $baseType = 'VARCHAR( 255 )';
                $type = explode('_', $saveData['Data']['Content']['_Field'][$Field]);
                
                if(!empty($type[1])){
                    if(file_exists(DB_TOOLKIT.'data_form/fieldtypes/'.$type[0].'/conf.php')){
                        include (DB_TOOLKIT.'data_form/fieldtypes/'.$type[0].'/conf.php');
                        if(!empty($FieldTypes[$type[1]]['baseType'])){
                            $baseType = $FieldTypes[$type[1]]['baseType'];
                        }
                    }
                }

                $wpdb->query("ALTER TABLE `".$saveData['Data']['Content']['_main_table']."` ADD `".$Field."` ".$baseType." NOT NULL ");
                echo mysql_error();
            }

            $Indexes[$Field]['Visibility'] = 'hide';
            $Indexes[$Field]['Indexed'] = 'noindex';
        }
        if(!empty($saveData['Data']['Content']['_IndexType'])){
            foreach($saveData['Data']['Content']['_IndexType'] as $Field=>$Setting){
                if(!empty($Setting['Visibility'])){
                    $Indexes[$Field]['Visibility'] = $Setting['Visibility'];
                }
                if(!empty($Setting['Indexed'])){
                    $Indexes[$Field]['Indexed'] = $Setting['Indexed'];
                }
            }
        }
        foreach($saveData['Data']['Content']['_Field'] as $Field=>$Value){
            $saveData['Data']['Content']['_IndexType'][$Field] = $Indexes[$Field]['Indexed'].'_'.$Indexes[$Field]['Visibility'];
        }
    }
    
    if(!empty($saveData['Data']['Content']['_customJSLibrary'])){
        $newCFG['_CustomJSLibraries'] = $saveData['Data']['Content']['_customJSLibrary'];
    }
    if(!empty($saveData['Data']['Content']['_customCSSSource'])){
        $newCFG['_CustomCSSSource'] = $saveData['Data']['Content']['_customCSSSource'];
    }
    // Sanatize Stuff
    $saveData['Data']['Content']['_APICallName'] = sanitize_title($saveData['Data']['Content']['_APICallName']);
    //sanitize_title($title);

    $newCFG['Content'] = base64_encode(serialize($saveData['Data']['Content']));
    $newCFG['_interfaceType'] = 'Configured';
    $newCFG['_Application'] = $activeApp;
    $newCFG['_interfaceName'] = $saveData['Data']['Content']['_ReportTitle'];
    if(!empty($saveData['Data']['Content']['_SetMenuItem'])) {
        $newCFG['_isMenu'] = true;
    }else {
        $newCFG['_isMenu'] = false;
    }
    if(!empty($saveData['Data']['Content']['_SetDashboard'])) {
        $newCFG['_Dashboard'] = true;
    }else {
        $newCFG['_Dashboard'] = false;
    }
    if(!isset($saveData['Data']['Content']['_clusterLayout'])){
        $newCFG['_ReportDescription'] = $saveData['Data']['Content']['_ReportDescription'];
        $newCFG['_ReportExtendedDescription'] = $saveData['Data']['Content']['_ReportExtendedDescription'];
        $newCFG['Type'] = 'Plugin';
    }else{
        $newCFG['_ClusterTitle'] = $saveData['Data']['Content']['_ClusterTitle'];
        $newCFG['_ClusterDescription'] = $saveData['Data']['Content']['_ClusterDescription'];
        $newCFG['Type'] = 'Cluster';
    }


    $newCFG['_ItemGroup'] = $saveData['Data']['Content']['_ItemGroup'];
    $newCFG['_menuAccess'] = $saveData['Data']['Content']['_menuAccess'];
    if(!empty($saveData['Data']['Content']['_SetAdminMenu'])){
        $newCFG['_SetAdminMenu'] = $saveData['Data']['Content']['_SetAdminMenu'];
    }
    if(!empty($saveData['Data']['Content']['_Icon'])){
        $newCFG['_Icon'] = $saveData['Data']['Content']['_Icon'];
    }
    if(!empty($saveData['Data']['Content']['_ProcessImport'])){
        $imported = unserialize(base64_decode($saveData['Data']['_SerializedImport']));
        $imported['Content'] = base64_encode(serialize($imported['Content']));
        $imported['ID'] = $optionTitle;
        $imported['_Application'] = $newCFG['_Application'];        
        update_option($optionTitle, $imported);
        header('location: '.$_SERVER['REQUEST_URI'].'&interface='.$optionTitle);
        die;
    }

    
    update_option($optionTitle, $newCFG);
    update_option('_'.$activeApp.'_app', $app);
    //vardump($app);

    return $optionTitle;
    
}


?>
