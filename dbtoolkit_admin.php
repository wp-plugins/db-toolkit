<?php 
/*
notes:

add_option($name, $value, $deprecated, $autoload); = for my purposes - set to "no".
update_option($name, $value); = update option
get_option('oscimp_store_url'); = get option value

*/




if(!empty($_GET['renderinterface'])){
    $Interface = get_option($_GET['renderinterface']);
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
    if(!empty($Interface['_ReportDescription'])) {
       // _e($Interface['_ReportDescription']);
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
        $newCFG['Type'] = 'Plugin';
        $newCFG['Row'] = 0;
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
    $newCFG['_ReportDescription'] = $_POST['Data']['Content']['_ReportDescription'];
    $newCFG['_ItemGroup'] = $_POST['Data']['Content']['_ItemGroup'];
    $newCFG['_menuAccess'] = $_POST['Data']['Content']['_menuAccess'];
    $newCFG['_Icon'] = $_POST['Data']['Content']['_Icon'];


    //vardump($newCFG);

    update_option($optionTitle, $newCFG);
}

        if($_GET['page'] == 'Add_New'){
            ?>
        <div class="wrap">
            <div id="icon-tools" class="icon32"></div><h2>Database Toolkit</h2>
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
                        $Element['Content'] = unserialize($defaults);
                        include('data_report/setup.plug.php');
                        ?>
                </form>
            </div>
        </div>
            <?php
            return;
        }

        if(!empty($_GET['interface'])) {
            $Element = get_option($_GET['interface']);
            $Element['Content'] = unserialize(base64_decode($Element['Content']));
            
            ?>
        <div class="wrap">
            <div id="icon-tools" class="icon32"></div><h2><?php _e('Interface: '.$Element['_interfaceName']); ?></h2>
            <br class="clear" /><br />
            <div id="poststuff">
                <form name="newInterfaceForm" method="post" action="<?php echo str_replace('&interface='.$Element['ID'], '', $_SERVER['REQUEST_URI']); ?>">
                        <?php
                        include('data_report/setup.plug.php');
                        ?>
                    <input type="hidden" value="<?php echo $Element['ID']; ?>" name="Data[interfaceTitle]" />
                </form>
            </div>
        </div>
            <?php
            return;
        }



        if(!empty($_POST['application'])){            
            $_SESSION['activeApp'] = $_POST['application'];

        }

        if(empty($_SESSION['activeApp'])){
            $_SESSION['activeApp'] = 'Base';
        }

        ?>

        <div class="wrap">
            <div id="icon-tools" class="icon32"></div><h2>Database Toolkit <a href="admin.php?page=Add_New" class="button add-new-h2">New Interface</a></h2>
            <br class="clear" /><br />
            <?php
            if(!empty($_POST['Data'])) {
                echo '<div class="updated fade" id="message"><p><strong>Interface <a href="admin.php?page=Database_Toolkit&renderinterface='.$_POST['Data']['ID'].'">'.$_POST['Data']['Content']['_ReportTitle'].'</a> Updated.</strong></p></div>';
            }
            ?>
            <form method="post" action="" id="application-switcher">
            <div class="tablenav">
                <div class="alignleft actions">Application
                    <select name="application">
                    <?php

                    $appList = get_option('dt_int_Apps');
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
                </div>
            </form>
                
            </div>
            <?php
            global $wpdb;

            $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);

            ?>
            <table width="100%" border="0" cellspacing="2" cellpadding="2" class="widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-top"></th>
                        <th scope="col" class="manage-column" id="interface-name-top">Interface Name</th>
                        <th scope="col" class="manage-column" id="interface-table-top">Table Interfaced</th>
                        <th scope="col" class="manage-column" id="interface-date-top">Short Code</th>
                        <th scope="col" class="manage-column" id="interface-api-top">API Key</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column" id="interface-spacer-bottom"></th>
                        <th scope="col" class="manage-column" id="interface-name-bottom">Interface Name</th>
                        <th scope="col" class="manage-column" id="interface-table-bottom">Table Interfaced</th>
                        <th scope="col" class="manage-column" id="interface-date-bottom">Short Code</th>
                        <th scope="col" class="manage-column" id="interface-api-bottom">API Key</th>
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
                        ?>


                


                <tr id="<?php echo $Interface['ID']; ?>">
                    <td></td>
                    <td>
                        <strong><?php
                                    $titleName = 'Untitled Interface';
                                    if(!empty($Interface['_interfaceName'])) {
                                        $titleName = $Interface['_interfaceName'];
                                    }
                                    _e($titleName); ?></strong>
                        <div class="row-actions"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&interface=<?php echo $Interface['ID']; ?>">Edit</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&renderinterface=<?php echo $Interface['ID']; ?>">View</a> | <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&duplicateinterface=<?php echo $Interface['ID']; ?>">Duplicate</a> | <a href="#" onclick="dt_deleteInterface('<?php echo $Interface['ID']; ?>'); return false;">Delete</a></div></div>
                    </td>
                    <td><?php echo $Config['_main_table']; ?></td>
                    <td>[interface id="<?php echo $Interface['ID']; ?>"]</td>
                    <td>
                    <?php echo $API; ?>
                        <div class="row-actions">
                            <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=xml" target="_blank">XML</a> |
                            <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=json" target="_blank">JSON</a>
                        </div>
                    </td>
                </tr>
                        <?php
                    }
                   }
                }
                ?>
            </table>
        </div>