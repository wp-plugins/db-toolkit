<?php
/*
 * Core Admin Processors Library - DB Toolkit
 * (C) David Cramer 2010 - 2011
 *
 */

if(!empty($_POST['exportApp'])){
    exportApp($_POST['application']);
}

//// FROM dbtoolkit_admin.php
//// LINE 95

if(!empty($_POST['Data'])) {

    $_POST = stripslashes_deep($_POST);
    //vardump($_POST);

    if(!empty($_POST['Data']['ID'])){
        $optionTitle = $_POST['Data']['ID'];

        $newCFG = get_option($optionTitle);
        //vardump($newCFG);
    }else{

        $optionTitle = uniqid('dt_intfc');
        if(isset($_POST['Data']['Content']['_clusterLayout'])){
            $optionTitle = uniqid('dt_clstr');
        }

        $newOption = array();
        $newCFG['ID'] = $optionTitle;
        $newCFG['_interfaceName'] = $_POST['dt_newInterface'];
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
    // Setup Index_Show's
    //echo '<br><br><br>';
    
    $Indexes = array();
    if(!empty($_POST['Data']['Content']['_Field'])){

        

        foreach($_POST['Data']['Content']['_Field'] as $Field=>$Value){
            // Make Sure the Fields EXIST!
            $wpdb->query("SELECT `".$Field."` FROM `".$_POST['Data']['Content']['_main_table']."` LIMIT 1;");
            if(mysql_errno() == '1054'){

                $baseType = 'VARCHAR( 255 )';
                $type = explode('_', $_POST['Data']['Content']['_Field'][$Field]);
                
                if(!empty($type[1])){
                    if(file_exists(DB_TOOLKIT.'data_form/fieldtypes/'.$type[0].'/conf.php')){
                        include (DB_TOOLKIT.'data_form/fieldtypes/'.$type[0].'/conf.php');
                        if(!empty($FieldTypes[$type[1]]['baseType'])){
                            $baseType = $FieldTypes[$type[1]]['baseType'];
                        }
                    }
                }

                $wpdb->query("ALTER TABLE `".$_POST['Data']['Content']['_main_table']."` ADD `".$Field."` ".$baseType." NOT NULL ");
                echo mysql_error();
            }

            $Indexes[$Field]['Visibility'] = 'hide';
            $Indexes[$Field]['Indexed'] = 'noindex';
        }
        if(!empty($_POST['Data']['Content']['_IndexType'])){
            foreach($_POST['Data']['Content']['_IndexType'] as $Field=>$Setting){
                if(!empty($Setting['Visibility'])){
                    $Indexes[$Field]['Visibility'] = $Setting['Visibility'];
                }
                if(!empty($Setting['Indexed'])){
                    $Indexes[$Field]['Indexed'] = $Setting['Indexed'];
                }
            }
        }
        foreach($_POST['Data']['Content']['_Field'] as $Field=>$Value){
            $_POST['Data']['Content']['_IndexType'][$Field] = $Indexes[$Field]['Indexed'].'_'.$Indexes[$Field]['Visibility'];
        }
    }
    
    if(!empty($_POST['Data']['Content']['_customJSLibrary'])){
        $newCFG['_CustomJSLibraries'] = $_POST['Data']['Content']['_customJSLibrary'];
    }
    if(!empty($_POST['Data']['Content']['_customCSSSource'])){
        $newCFG['_CustomCSSSource'] = $_POST['Data']['Content']['_customCSSSource'];
    }
    // Sanatize Stuff
    $_POST['Data']['Content']['_APICallName'] = sanitize_title($_POST['Data']['Content']['_APICallName']);
    sanitize_title($title);

    $newCFG['Content'] = base64_encode(serialize($_POST['Data']['Content']));
    $newCFG['_interfaceType'] = 'Configured';
    if(!empty($_POST['Data']['Content']['_Application'])){
        $newCFG['_Application'] = $_POST['Data']['Content']['_Application'];
    }else{
        $newCFG['_Application'] = 'Base';
    }
    $Apps = get_option('dt_int_Apps');
    if(!empty($Apps[$newCFG['_Application']])){
        $Apps[$newCFG['_Application']] = 'open';
        update_option('dt_int_Apps', $Apps);
    }else{
        $Apps[$newCFG['_Application']] = 'open';
        update_option('dt_int_Apps', $Apps);
    }
    $_SESSION['activeApp'] = $newCFG['_Application'];
    $newCFG['_interfaceName'] = $_POST['Data']['Content']['_ReportTitle'];
    if(!empty($_POST['Data']['Content']['_SetMenuItem'])) {
        $newCFG['_isMenu'] = true;
    }else {
        $newCFG['_isMenu'] = false;
    }
    if(!empty($_POST['Data']['Content']['_SetDashboard'])) {
        $newCFG['_Dashboard'] = true;
    }else {
        $newCFG['_Dashboard'] = false;
    }
    if(!isset($_POST['Data']['Content']['_clusterLayout'])){
        $newCFG['_ReportDescription'] = $_POST['Data']['Content']['_ReportDescription'];
        $newCFG['_ReportExtendedDescription'] = $_POST['Data']['Content']['_ReportExtendedDescription'];
        $newCFG['Type'] = 'Plugin';
    }else{
        $newCFG['_ClusterTitle'] = $_POST['Data']['Content']['_ClusterTitle'];
        $newCFG['_ClusterDescription'] = $_POST['Data']['Content']['_ClusterDescription'];
        $newCFG['Type'] = 'Cluster';
    }


    $newCFG['_ItemGroup'] = $_POST['Data']['Content']['_ItemGroup'];
    $newCFG['_menuAccess'] = $_POST['Data']['Content']['_menuAccess'];
    $newCFG['_SetAdminMenu'] = $_POST['Data']['Content']['_SetAdminMenu'];
    $newCFG['_Icon'] = $_POST['Data']['Content']['_Icon'];

    if(!empty($_POST['Data']['Content']['_ProcessImport'])){
        $imported = unserialize(base64_decode($_POST['Data']['_SerializedImport']));
        $imported['Content'] = base64_encode(serialize($imported['Content']));
        $imported['ID'] = $optionTitle;
        $imported['_Application'] = $newCFG['_Application'];        
        update_option($optionTitle, $imported);
        header('location: '.$_SERVER['REQUEST_URI'].'&interface='.$optionTitle);
        die;
    }


    update_option($optionTitle, $newCFG);
    if(!empty($_POST['Apply'])){
        header('location: '.$_SERVER['REQUEST_URI'].'&interface='.$optionTitle);
        die;
    }
}


?>
