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
    //vardump($_POST);
    if(!empty($_POST['Data']['Content']['_customJSLibrary'])){
        $newCFG['_CustomJSLibraries'] = $_POST['Data']['Content']['_customJSLibrary'];
    }
    if(!empty($_POST['Data']['Content']['_customCSSSource'])){
        $newCFG['_CustomCSSSource'] = $_POST['Data']['Content']['_customCSSSource'];
    }

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
