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



        if($_GET['page'] == 'Add_New'){
        /*    ?>
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



*/
                    ?>
                <form name="newInterfaceForm" method="post" action="admin.php?page=Database_Toolkit">
                        <?php 
                        $defaults = get_option('_dbtoolkit_defaultinterface');
                        
                        $Element['Content'] = $defaults;
                        include('data_report/setup.plug.php');
                        ?>
                </form>
<?php
                        /*
                        ?>
                </form>
            </div>
        </div>
            <?php
                         * 
                         */
            return;
        }

        if(!empty($_GET['interface']) || !empty($_GET['cluster'])) {

            if(!empty($_GET['interface'])){
                $Element = get_option($_GET['interface']);
            }else{
                $Element = get_option($_GET['cluster']);
            }
            $Element['Content'] = unserialize(base64_decode($Element['Content']));
            
            

                    $URL = str_replace('&interface='.$Element['ID'], '', $_SERVER['REQUEST_URI']);
                    $URL = str_replace('&cluster='.$Element['ID'], '#clusters', $URL);

                ?>
                <form name="newInterfaceForm" id="newInterfaceForm" method="post" action="<?php echo $URL; ?>">
                        <?php
                        if(!empty($_GET['interface'])){
                            include('data_report/setup.plug.php');
                        }else{
                            include('data_report/cluster.plug.php');
                        }
                        ?>
                    <input type="hidden" value="<?php echo $Element['ID']; ?>" name="Data[interfaceTitle]" />
                </form>

            <?php
            return;
        }



        /// Start of listing applications
        if(!empty($_FILES['appImage']['size'])){
            $path = wp_upload_dir();
            // set filename and paths
            $Ext = pathinfo($_FILES['appImage']['name']);
            $newFileName = sanitize_file_name($_POST['application'].'.'.$Ext['extension']);
            $upload = wp_upload_bits($newFileName, null, file_get_contents($_FILES['appImage']['tmp_name']));

            $appConfig = get_option('_'.sanitize_title($_SESSION['activeApp']).'_app');
            $appConfig['imageURL'] = $upload['url'];
            $appConfig['imageFile'] = $upload['file'];
            update_option('_'.sanitize_title($_POST['application']).'_app', $appConfig);
            
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
            //vardump($apps);
            //die;
            unset($apps[sanitize_title($_SESSION['activeApp'])]);
            update_option('dt_int_Apps', $apps);
            delete_option('_'.sanitize_title($_SESSION['activeApp']).'_app');
            $_SESSION['activeApp'] = 'Base';
        }

        if(!empty($_POST['loadApp'])){
            
            if(strtolower($_POST['application']) != 'base'){
                $Apps = get_option('dt_int_Apps');
                $appConfig= get_option('_'.sanitize_title($_POST['application']).'_app');
                //vardump($appConfig);
                if(empty($appConfig)){
                    // for old apps
                    unset($Apps[$_POST['application']]);
                    $Apps[sanitize_title($_POST['application'])]['state'] = 'open';
                    $Apps[sanitize_title($_POST['application'])]['name'] = $_POST['application'];
                    update_option('dt_int_Apps', $Apps);
                    //$newApp['Author'] =
                    $user = wp_get_current_user();
                    $newApp = array(
                        'state'=>'open',
                        'name'=>$_POST['application'],
                        'author'=>$user->data->first_name.' '.$user->data->last_name,
                        'author email'=>$user->data->user_email
                    );
                    update_option('_'.sanitize_title($_POST['application']).'_app', $newApp);
                }
            }
            $_SESSION['activeApp'] = sanitize_title($_POST['application']);
        }
        
        if(empty($_SESSION['activeApp'])){
            $_SESSION['activeApp'] = 'Base';
        }
        $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
        $clusters = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_clstr%' ", ARRAY_A);


        // check if there is a app config
        $appConfig = get_option('_'.sanitize_title($_SESSION['activeApp']).'_app');
        //vardump($_SESSION['activeApp']);
        //vardump($appConfig);


        ?>

        <div class="wrap">
            <div><?php
            if(!empty($appConfig['imageURL'])){
               echo '<img src="'.UseImage($appConfig['imageURL'], 7, 200, 100).'" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" />';
            }else{
                echo '<img src="'.WP_PLUGIN_URL . '/db-toolkit/images/dbtoolkit-logo.png" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" />';
            }
            ?>
                <a href="admin.php?page=Add_New" class="button">New Interface</a>&nbsp;
                <a href="admin.php?page=New_Cluster" class="button">New Cluster</a>
                <br />
                <span class="description">Manage Interfaces & Clusters</span>
                <br class="clear" /><br />
            <?php
            if(!empty($_POST['Data'])) {
                
                global $newCFG;

                

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
                

                echo '<div class="notice fade" id="message"><p><strong>Interface <a href="admin.php?page=Database_Toolkit&renderinterface='.$LinkID.'">'.$Title.'</a> Updated.</strong></p></div>';
            }
            ?>
            <form method="post" action="" enctype="multipart/form-data" id="application-switcher">
            <div class="tablenav">
                <div class="alignleft actions">Application
                    <?php
                        $appList = get_option('dt_int_Apps');
                    ?>
                    <select name="application">
                    <?php
                    if(empty($appList)){
                        $appList['Base'] = 'open';
                        update_option('dt_int_Apps', $appList);
                    }
                    foreach($appList as $app=>$state){
                        if(is_array($state)){
                            if($state['state'] == 'open'){
                                $Sel = '';
                                if($app == $_SESSION['activeApp']){
                                    $Sel = 'selected="selected"';
                                }
                                echo '<option '.$Sel.' value="'.$app.'">'.$state['name'].'</option>';
                            }
                            
                        }else{
                            if($state == 'open'){
                                $Sel = '';
                                if($app == $_SESSION['activeApp']){
                                    $Sel = 'selected="selected"';
                                }
                                echo '<option '.$Sel.' value="'.$app.'">'.$app.'</option>';
                            }
                        }
                    }
                    ?>
                    </select>
                    <input type="submit" class="button-secondary action" id="doaction" name="loadApp" value="Switch">
                    <?php
                    
                    if(strtolower($_SESSION['activeApp']) != 'base'){
                        echo '<input type="submit" class="button-secondary action" id="exportApp" name="exportApp" value="Export Application">';
                    ?>

                    App Image: <input type="file" name="appImage"><input type="submit" value="Upload" class="button">



                    <?php




                    }
                    ?>
                    
                </div>
                <div class="alignright actions">
                    <?php
                    if(strtolower($_SESSION['activeApp']) != 'base'){
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
                        <th scope="col" class="manage-column" id="interface-date-top">Interface Type</th>
                        <th scope="col" class="manage-column" id="interface-date-top">Short Code</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-bottom"></th>
                        <th scope="col" class="manage-column" id="interface-name-bottom">Interface Name</th>
                        <th scope="col" class="manage-column" id="interface-table-bottom">Table Interfaced</th>
                        <th scope="col" class="manage-column" id="interface-date-bottom">Interface Type</th>
                        <th scope="col" class="manage-column" id="interface-date-bottom">Short Code</th>                        
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
                        if(sanitize_title($cfg['_Application']) == $_SESSION['activeApp']){
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
                        $API = str_replace('dt_intfc', '', $Interface['ID']).'_'.md5(str_replace('dt_intfc', '', $Interface['ID']).$Config['_APISeed']);
                        //$API = $Interface['ID'].'_'.md5($Interface['ID'].$Config['_APISeed']);//str_replace('dt_intfc', '', $Interface['ID']).'_'.md5(serialize($Config));

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
                    <td><?php echo $Config['_ViewMode']; ?></td>
                    <td>[interface id="<?php echo $Interface['ID']; ?>"]</td>
                    
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
                        if(sanitize_title($cfg['_Application']) == $_SESSION['activeApp']){
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