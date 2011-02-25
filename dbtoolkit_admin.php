<?php 
/*
notes:



*/




if(!empty($_GET['renderinterface'])){
    $Interface = get_option($_GET['renderinterface']);
    if($Interface['Type'] == 'Cluster'){
        dt_renderCluster($_GET['renderinterface']);
        return;
    }
    $Title = $Interface['_interfaceName'];
    if(!empty($Interface['_ReportDescription'])) {
       $Title = $Interface['_ReportDescription'];
    }

    ?>
    <div class="wrap">
    <div id="icon-themes" class="icon32"></div><h2><?php _e($Title); ?>
        <?php
            //global $user;
            $user = wp_get_current_user();
            if(!empty($user->caps['administrator'])){
        ?>
        <a class="button add-new-h2" href="admin.php?page=Database_Toolkit&interface=<?php echo $_GET['renderinterface']; ?>">Edit</a>
    <?php
            }
    ?></h2>
    <?php
    $fset = get_option('dt_set_'.$Interface['ID']);
    if(!empty($fset)){
    ?>
        <ul class="subsubsub">

                <?php
                    
                    $tablen = count($fset);
                    $index = 1;
                    $link = explode('&ftab', $_SERVER['REQUEST_URI']);
                    $class = 'class="current"';
                    if(!empty($_GET['ftab'])){
                        $class = '';
                    }
                    $total = dr_BuildReportGrid($Interface['ID'], false, false, false, 'count', true, false);
                    //unset($_SESSION['reportFilters'][$Interface['ID']]);
                    $counter = ' <span class="count">(<span class="'.$tab['code'].'">'.$total.'</span>)</span> ';
                    
                    echo '<li><a '.$class.' href="'.$link[0].'">All '.$counter.'</a> | </li>';
                    foreach($fset as $tab){
                        $break = '';
                        $counter = '';
                        $class = '';
                        if(!empty($_GET['ftab'])){                            
                            if($_GET['ftab'] == $tab['code']){
                                $class = 'class="current"';
                            }
                        }
                        if($index < $tablen){
                            $break = ' | ';
                        }
                        if($tab['ShowCount'] == 'yes'){
                            // need to do a counter only process
                            $total = dr_BuildReportGrid($Interface['ID'], false, false, false, 'count', true, $tab['Filters']);
                            //unset($_SESSION['reportFilters'][$Interface['ID']]);
                            $counter = ' <span class="count">(<span class="'.$tab['code'].'">'.$total.'</span>)</span> ';
                        }
                        $link = explode('&ftab', $_SERVER['REQUEST_URI']);
                        echo '<li><a '.$class.' href="'.$link[0].'&ftab='.$tab['code'].'">'.$tab['Title'].$counter.'</a>'.$break.'</li>';
                        $index++;
                    }
                ?>

        </ul>
<?php
}
?>

    <div class="clear"></div>
    <div id="poststuff">
    <?php
    echo dt_renderInterface($_GET['renderinterface']);
    ?>
    </div>
    </div>
<?php

 return;
}

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


    update_option($optionTitle, $newCFG);
}

        if($_GET['page'] == 'Add_New'){
            ?>
        <div class="wrap">
            <div><img src="<?php echo WP_PLUGIN_URL . '/db-toolkit/images/dbtoolkit-logo.png'; ?>" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" />Create new Interface
            <div class="clear"></div>
                <br />
            <div id="poststuff">
                    <?php
                    //$optionTitle = uniqid('dt_intfc');

//                $_POST = stripslashes_deep($_POST);
//
//		$newOption = array();
//		$newOption['_interfaceName'] = $_POST['dt_newInterface'];
//		$newOption['_interfaceType'] = 'unconfigured';
//		$newOption['_interfaceDate'] = date('Y-m-d H:i:s');
//		$newOption['ID'] = $optionTitle;
//		$newOption['Element'] = 'data_report';
//		$newOption['Content'] = base64_encode(serialize(array()));
//		$newOption['ParentDocument'] = $optionTitle;
//		$newOption['Position'] = 0;
//		$newOption['Column'] = 0;
//		$newOption['Type'] = 'Plugin';
//		$newOption['Row'] = 0;

                    //add_option($optionTitle, serialize($newOption), NULL, "no");




                    ?>
                <form name="newInterfaceForm" method="post" action="admin.php?page=Database_Toolkit">
                        <?php
                        $defaults = get_option('_dbtoolkit_defaultinterface');
                        
                        $Element['Content'] = $defaults;
                        include('data_report/setup.plug.php');
                        ?>
                </form>
            </div>
        </div>
            <?php
            return;
        }

        if(!empty($_GET['interface']) || !empty($_GET['cluster'])) {

            if(!empty($_GET['interface'])){
                $Element = get_option($_GET['interface']);
            }else{
                $Element = get_option($_GET['cluster']);
            }
            $Element['Content'] = unserialize(base64_decode($Element['Content']));
            
            ?>
        <div class="wrap">
            <img src="<?php echo WP_PLUGIN_URL . '/db-toolkit/images/dbtoolkit-logo.png'; ?>" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" />
            <?php
            
                if(!empty($Element['_ReportDescription'])){
                    echo 'Editing: '.$Element['_ReportDescription'];
                }

                if(!empty($Element['_ClusterTitle'])){
                    echo 'Editing: '.$Element['_ClusterTitle'];
                }


            ; ?>
            <br class="clear" /><br />
            <div id="poststuff">
                <?php

                    $URL = str_replace('&interface='.$Element['ID'], '', $_SERVER['REQUEST_URI']);
                    $URL = str_replace('&cluster='.$Element['ID'], '#clusters', $URL);

                ?>
                <form name="newInterfaceForm" method="post" action="<?php echo $URL; ?>">
                        <?php
                        if(!empty($_GET['interface'])){
                            include('data_report/setup.plug.php');
                        }else{
                            include('data_report/cluster.plug.php');
                        }
                        ?>
                    <input type="hidden" value="<?php echo $Element['ID']; ?>" name="Data[interfaceTitle]" />
                </form>
            </div>
        </div>
            <?php
            return;
        }

        global $wpdb;
        $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);

        if(!empty($_POST['deleteApp'])){


            $apps = get_option('dt_int_Apps');
            
            foreach($interfaces as $interface){
                $tmp = get_option($interface['option_name']);
                if($tmp['_Application'] == $_POST['application']){
                    dt_removeInterface($interface['option_name']);
                }
                
            }
            unset($apps[$_SESSION['activeApp']]);
            update_option('dt_int_Apps', $apps);
            $_SESSION['activeApp'] = 'Base';
        }

        if(!empty($_POST['loadApp'])){
            $_SESSION['activeApp'] = $_POST['application'];

        }

        if(empty($_SESSION['activeApp'])){
            $_SESSION['activeApp'] = 'Base';
        }
        $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
        $clusters = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_clstr%' ", ARRAY_A);
        ?>

        <div class="wrap">
            <div><img src="<?php echo WP_PLUGIN_URL . '/db-toolkit/images/dbtoolkit-logo.png'; ?>" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" /><a href="admin.php?page=Add_New" class="button add-new-h2">New Interface</a>
                <br />
                <span class="description">Manage Applications and Interfaces</span>
                <br class="clear" /><br />
            <?php
            if(!empty($_POST['Data'])) {


                if(empty($_POST['Data']['ID'])){
                    $LinkID = $optionTitle;
                }else{
                    $LinkID = $_POST['Data']['ID'];
                }
                if(isset($newCFG['_ReportDescription'])){
                    $Title = $newCFG['_ReportDescription'];
                }else{
                    $Title = $newCFG['_ClusterTitle'];
                }
                
                echo '<div class="updated fade" id="message"><p><strong>Interface <a href="admin.php?page=Database_Toolkit&renderinterface='.$LinkID.'">'.$Title.'</a> Updated.</strong></p></div>';
            }
            ?>
            <form method="post" action="" id="application-switcher">
            <div class="tablenav">
                <div class="alignleft actions">Application
                    <select name="application">
                    <?php

                    $appList = get_option('dt_int_Apps');
                    if(empty($appList)){
                        $appList['Base'] = 'open';
                        update_option('dt_int_Apps', $appList);
                    }
                    foreach($appList as $app=>$state){
                        if($state == 'open'){
                            $Sel = '';
                            if($app == $_SESSION['activeApp']){
                                $Sel = 'selected="selected"';
                            }
                            echo '<option '.$Sel.' value="'.$app.'">'.$app.'</option>';
                        }
                    }
                    ?>
                    </select>
                    <input type="submit" class="button-secondary action" id="doaction" name="loadApp" value="Switch">
                    <?php
                    if($_SESSION['activeApp'] != 'Base'){
                        echo '<input type="submit" class="button-secondary action" id="exportApp" name="exportApp" value="Export Application">';
                    }
                    ?>
                    
                </div>
                <div class="alignright actions">
                    <?php
                    if($_SESSION['activeApp'] != 'Base'){
                        echo '<input type="submit" class="button-primary action" id="doaction" name="deleteApp" value="Delete Application" onClick="return confirm(\'This will delete all interfaces in this Application. Data will remain intact. This cannot be undone. Continue?\');" >';
                    }
                    ?>
                </div></div>
            </form>
            <?php
            /*
            <form name="exportPublish" method="post" action="<?php echo str_replace('&interface='.$Element['ID'], '', $_SERVER['REQUEST_URI']); ?>">
                <?php
                if($_SESSION['activeApp'] != 'Base'){
                    echo '<input type="submit" class="button-secondary action" id="exportApp" name="exportApp" value="Export Application">';
                }
                ?>
            </form>
             */
            ?>
            </div>
            <?php
            

            ?>

                <script>
                    
                jQuery(document).ready(function($) {
                        jQuery("#dbToolkit_Tabs").tabs();
                });

            </script>
            <div id="dbToolkit_Tabs" class="dbtools_tabs">
              <ul>
                            <li><a href="#interfaces"><span>Interfaces</span></a></li>
                            <li><a href="#clusters"><span>Clusters</span></a></li>
              </ul>
            
            <div id="interfaces" class="setupTab">
            <table width="100%" border="0" cellspacing="2" cellpadding="2" class="widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-top"></th>
                        <th scope="col" class="manage-column" id="interface-name-top">Interface Name</th>
                        <th scope="col" class="manage-column" id="interface-table-top">Table Interfaced</th>
                        <th scope="col" class="manage-column" id="interface-date-top">Short Code</th>
                        <th scope="col" class="manage-column" id="interface-api-top">API Access (experimental)</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-bottom"></th>
                        <th scope="col" class="manage-column" id="interface-name-bottom">Interface Name</th>
                        <th scope="col" class="manage-column" id="interface-table-bottom">Table Interfaced</th>
                        <th scope="col" class="manage-column" id="interface-date-bottom">Short Code</th>
                        <th scope="col" class="manage-column" id="interface-api-bottom">API Access (experimental)</th>
                    </tr>
                </tfoot>
                <?php
                if(!empty($interfaces)) {
                    $Groups = array();
                    foreach($interfaces as $interface) {
                        //vardump($interface);
                        
                        $Iname = $interface['option_name'];
                        $cfg = get_option($Iname);
                        if(empty($cfg['_Application'])){
                            $cfg['_Application'] = 'Base';
                        }
                        if($cfg['_Application'] == $_SESSION['activeApp']){
                            $GroupName = '__Ungrouped';
                            if(!empty($cfg['_ItemGroup'])){
                                $GroupName = $cfg['_ItemGroup'];
                            }
                            $Groups[$GroupName][] = $cfg;
                        }
                    }
                    ksort($Groups);
                    foreach($Groups as $Group=>$Interfaces){
                        
                        if($Group == '__Ungrouped'){
                            $Group = '<em>Ungrouped</em>';
                        }
                        ?>
                        <tr>
                            <th scope="row" colspan="5" class="highlight"><?php echo $Group; ?></th>
                        </tr>
                        <?php




                        foreach($Interfaces as $Interface){
                        
                        $Config = unserialize(base64_decode($Interface['Content']));
                        //vardump($Config);
                        //vardump($cfg);
                        $API = str_replace('dt_intfc', '', $Interface['ID']).'_'.md5(serialize($Config));

                        $Desc = '';
                        if(!empty($Config['_ReportExtendedDescription'])) {
                           $Desc = $Config['_ReportExtendedDescription'];
                        }
                        
                        ?>

                <tr id="<?php echo $Interface['ID']; ?>">
                    <td></td>
                    <td>
                        <strong><?php
                                    $titleName = 'Untitled Interface';
                                    if(!empty($Interface['_ReportDescription'])) {
                                        $titleName = $Interface['_ReportDescription'];
                                    }
                                    _e($titleName); ?></strong>
                        <div><?php echo $Desc; ?></div>
                        <div class="row-actions"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&interface=<?php echo $Interface['ID']; ?>">Edit</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&renderinterface=<?php echo $Interface['ID']; ?>">View</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&duplicateinterface=<?php echo $Interface['ID']; ?>">Duplicate</a> | <a href="#" onclick="dt_deleteInterface('<?php echo $Interface['ID']; ?>'); return false;">Delete</a></div></div>
                    </td>
                    <td><?php echo $Config['_main_table']; ?></td>
                    <td>[interface id="<?php echo $Interface['ID']; ?>"]</td>
                    <td>
                    <?php
                    
                    if(empty($Config['_UseListViewTemplate'])){                    
                    echo 'API Key: '.$API.'<br />';
                    
                    echo '<div class="row-actions-hide">';
                    echo '<strong>List Records</strong><br />';
                    ?>
                        <div class="row-actions-show">
                            <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=xml" target="_blank">XML</a> |
                            <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=json" target="_blank">JSON</a>
                        </div>
                    <strong>Insert Records</strong><br />
                    POST URL: <input type="text" style="width: 80%;" value="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&action=insert" />
                    
                    <?php
                    $Fields = array();
                        foreach($Config['_Field'] as $Field=>$Types){
                            if(!empty($Types)){
                                $Type = explode('_', $Types);
                                if($Type[0] != 'auto'){
                                    $Fields[] = $Field;
                                }
                            }
                        }
                        echo '<div>Submitted Data: '.implode(', ', $Fields).'</div>';
                        $Fields = array();
                        if(!empty($Config['_ReturnFields'])){
                        foreach($Config['_ReturnFields'] as $Field){
                            $Fields[] = $Field;
                        }                        
                            echo '<div>Returned Fields: '.implode(', ', $Fields).'</div>';
                        }
                        echo '</div>';
                    }else{

                        
                        echo "<div class=\"row-actions\">API Only Supported in non-templated list mode</div>";
                    }
                    ?></div
                    </td>
                </tr>
                        <?php
                    }
                   }
                }
                ?>
            </table>
            </div>
            <div id="clusters" class="setupTab">


<table width="100%" border="0" cellspacing="2" cellpadding="2" class="widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-top"></th>
                        <th scope="col" class="manage-column" id="interface-name-top">Cluster Name</th>
                        <th scope="col" class="manage-column" id="interface-table-top">Interfaces</th>
                        <th scope="col" class="manage-column" id="interface-date-top">Short Code</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-bottom"></th>
                        <th scope="col" class="manage-column" id="interface-name-bottom">Cluster Name</th>
                        <th scope="col" class="manage-column" id="interface-table-bottom">Interfaces</th>
                        <th scope="col" class="manage-column" id="interface-date-bottom">Short Code</th>                        
                    </tr>
                </tfoot>
                <?php
                if(!empty($clusters)) {
                    $Groups = array();
                    foreach($clusters as $cluster) {
                        //vardump($interface);

                        $Iname = $cluster['option_name'];
                        $cfg = get_option($Iname);
                        if(empty($cfg['_Application'])){
                            $cfg['_Application'] = 'Base';
                        }
                        if($cfg['_Application'] == $_SESSION['activeApp']){
                            $GroupName = '__Ungrouped';
                            if(!empty($cfg['_ItemGroup'])){
                                $GroupName = $cfg['_ItemGroup'];
                            }
                            $Groups[$GroupName][] = $cfg;
                        }
                    }
                    ksort($Groups);
                    foreach($Groups as $Group=>$Clusters){

                        if($Group == '__Ungrouped'){
                            $Group = '<em>Ungrouped</em>';
                        }
                        ?>
                        <tr>
                            <th scope="row" colspan="5" class="highlight"><?php echo $Group; ?></th>
                        </tr>
                        <?php




                        foreach($Clusters as $Cluster){

                        $Config = unserialize(base64_decode($Cluster['Content']));
                        //vardump($cfg);
                        $API = str_replace('dt_intfc', '', $Cluster['ID']).'_'.md5(serialize($Config));

                        $Desc = '';
                        if(!empty($Config['_ClusterDescription'])) {
                           $Desc = $Config['_ClusterDescription'];
                        }

                        ?>

                <tr id="<?php echo $Cluster['ID']; ?>">
                    <td></td>
                    <td>
                        <strong><?php
                                    $titleName = 'Untitled Cluster';
                                    if(!empty($Cluster['_ClusterTitle'])) {
                                        $titleName = $Cluster['_ClusterTitle'];
                                    }
                                    _e($titleName); ?></strong>
                        <div><?php echo $Desc; ?></div>
                        <div class="row-actions"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&cluster=<?php echo $Cluster['ID']; ?>">Edit</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&renderinterface=<?php echo $Cluster['ID']; ?>">View</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&duplicateinterface=<?php echo $Cluster['ID']; ?>">Duplicate</a> | <a href="#" onclick="dt_deleteInterface('<?php echo $Cluster['ID']; ?>', 'cluster'); return false;">Delete</a></div></div>
                    </td>
                    <td><?php echo $Config['_main_table']; ?></td>
                    <td>[interface id="<?php echo $Cluster['ID']; ?>"]</td>
                </tr>
                        <?php
                    }
                   }
                }
                ?>
            </table>


            </div>
        </div>
        </div>