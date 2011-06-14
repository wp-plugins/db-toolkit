<?php
/*
 * Core Functions Library - DB Toolkit
 * (C) David Cramer 2010 - 2011
 * 
 */

require_once(DB_TOOLKIT.'libs/lib.php');
require_once(DB_TOOLKIT.'daiselements.class.php');
require_once(DB_TOOLKIT.'data_form/class.php');
require_once(DB_TOOLKIT.'data_report/class.php');
require_once(DB_TOOLKIT.'data_itemview/class.php');


function interface_VersionCheck() {
        global $wpdb;
        $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
        foreach($interfaces as $interface){
            $cfg = get_option($interface['option_name']);
            if(!is_array($cfg)){
                $cfg = unserialize($cfg);
                update_option($interface['option_name'], $cfg);
            }
        }
    $defaults = unserialize('a:51:{s:11:"_FormLayout";s:0:"";s:15:"_New_Item_Title";s:9:"Add Entry";s:15:"_Items_Per_Page";s:2:"20";s:12:"_autoPolling";s:0:"";s:13:"_Show_Filters";s:1:"1";s:15:"_toggle_Filters";s:1:"1";s:20:"_Show_KeywordFilters";s:1:"1";s:14:"_Keyword_Title";s:6:"Search";s:11:"_showReload";s:1:"1";s:12:"_Show_Export";s:1:"1";s:13:"_Show_Plugins";s:1:"1";s:12:"_orientation";s:1:"P";s:12:"_Show_Select";s:1:"1";s:12:"_Show_Delete";s:1:"1";s:10:"_Show_Edit";s:1:"1";s:10:"_Show_View";s:1:"1";s:19:"_Show_Delete_action";s:1:"1";s:12:"_Show_Footer";s:1:"1";s:14:"_InsertSuccess";s:27:"Entry inserted successfully";s:14:"_UpdateSuccess";s:26:"Entry updated successfully";s:11:"_InsertFail";s:22:"Could not insert entry";s:11:"_UpdateFail";s:22:"Could not update entry";s:17:"_SubmitButtonText";s:6:"Submit";s:17:"_UpdateButtonText";s:6:"Submit";s:13:"_EditFormText";s:10:"Edit Entry";s:13:"_ViewFormText";s:10:"View Entry";s:14:"_NoResultsText";s:13:"Nothing Found";s:10:"_ShowReset";s:1:"1";s:16:"_SubmitAlignment";s:4:"left";s:8:"_APISeed";s:0:"";s:12:"_chartHeight";s:3:"250";s:11:"_chartTitle";s:0:"";s:13:"_chartCaption";s:0:"";s:7:"_topPad";s:2:"30";s:9:"_rightPad";s:2:"50";s:10:"_bottomPad";s:3:"130";s:8:"_leftPad";s:2:"50";s:7:"_xAngle";s:3:"-45";s:12:"_xAxis_Align";s:5:"right";s:17:"_yToolTipTemplate";s:48:"<b>{{SeriesName}}</b><br/>{{YValue}}: {{XValue}}";s:26:"_ListViewTemplatePreHeader";s:0:"";s:23:"_ListViewTemplateHeader";s:0:"";s:27:"_ListViewTemplatePostHeader";s:0:"";s:36:"_ListViewTemplateContentWrapperStart";s:0:"";s:27:"_ListViewTemplatePreContent";s:0:"";s:24:"_ListViewTemplateContent";s:0:"";s:28:"_ListViewTemplatePostContent";s:0:"";s:34:"_ListViewTemplateContentWrapperEnd";s:0:"";s:26:"_ListViewTemplatePreFooter";s:0:"";s:23:"_ListViewTemplateFooter";s:0:"";s:27:"_ListViewTemplatePostFooter";s:0:"";}');
    update_option('_dbtoolkit_defaultinterface', $defaults, NULL, 'No');
}

function dt_start() {
    // I like sessions
    if(!session_id()) {
        session_start();
    }
    // Include Libraries
    if(is_admin()) {
        if(empty($_SESSION['adminscripts'])) {
            $_SESSION['adminscripts'] = "";
        }
    }  else {
        if(empty($_SESSION['dataform']['OutScripts'])){
            $_SESSION['dataform']['OutScripts'] = '';
        }
    }
    /*
    require_once(DB_TOOLKIT.'libs/lib.php');
    require_once(DB_TOOLKIT.'daiselements.class.php');
    require_once(DB_TOOLKIT.'data_form/class.php');
    require_once(DB_TOOLKIT.'data_report/class.php');
    require_once(DB_TOOLKIT.'data_itemview/class.php');
    */

    // dumplicate interface hack
    if(is_admin()) {
        if(!empty($_GET['duplicateinterface'])){
            $dupvar = get_option($_GET['duplicateinterface']);
            $oldOption = $dupvar;

            if($oldOption['Type'] == 'Cluster'){
                $NewName = uniqid($oldOption['_ClusterTitle'].' ');
                $oldOption['_ClusterTitle'] = $NewName;
                $newTitle = uniqid('dt_clstr');
                $hash = '&r=y#clusters';
            }else{
                $NewName = uniqid($oldOption['_ReportDescription'].' ');
                $oldOption['_ReportDescription'] = $NewName;
                $newTitle = uniqid('dt_intfc');
                $hash = '';
            }
            $oldOption['ID'] = $newTitle;
            $oldOption['ParentDocument'] = $newTitle;
            //vardump($oldOption);
            add_option($newTitle, $oldOption, NULL, 'No');
            header( 'Location: '.$_SERVER['HTTP_REFERER'].$hash);
            die;
        }
    }


    // Ajax processing
    dt_process();
}

//Header
function dt_headers() {

    include_once(DB_TOOLKIT.'data_form/headers.php');
    include_once(DB_TOOLKIT.'data_report/headers.php');
    ?>
<script type="text/javascript" >

    <?php
    if(!is_admin()) {
        echo 'var ajaxurl_dbt = \'./index.php?dbtoolkit\';';
    }else {
        ?>
        ajaxurl_dbt = ajaxurl;
            function dt_deleteInterface(interfaceID, type){
                hash = '';
                if(type == 'cluster'){
                    hash = '&r=y#clusters';
                }
                if(confirm('Are you sure you want to delete this interface?')){

                    ajaxCall('dt_removeInterface', interfaceID, function(x){
                        if(x == true){
                            jQuery('#'+interfaceID).fadeOut('slow', function(){
                                jQuery(this).remove();
                                window.location = '<?php echo $_SERVER['REQUEST_URI']; ?>'+hash;
                            });
                        }else{
                            alert('strange, that should have worked '+x);
                        }
                    });

                }

            }


        <?php
    }

    ?>
        function ajaxCall() {
    <?php
    if(is_admin()) {
        ?>
                var vars = { action : 'dt_ajaxCall',func: ajaxCall.arguments[0]};
        <?php
    }else {
        ?>
                var vars = { action : 'wp_dt_ajaxCall',func: ajaxCall.arguments[0]};
        <?php
    }
    ?>

            for(i=1;ajaxCall.arguments.length-1>i; i++) {
                vars['FARGS[' + i + ']'] = ajaxCall.arguments[i];
            }

            var callBack = ajaxCall.arguments[ajaxCall.arguments.length-1];
            jQuery.post(ajaxurl_dbt,vars, function(data){
                callBack(data);
            });
        }
</script>
    <?php
}

//styles
function dt_styles() {

    if(!is_admin()){
        global $post;
        $pattern = get_shortcode_regex();

        $texts = get_option('widget_text');
        $preIs = array();
        foreach($texts as $text){
            //vardump($text['text']);
            preg_match_all('/'.$pattern.'/s', $text['text'], $matches);
            if(!empty($matches[3])){                
                foreach($matches[3] as $preInterface){
                    $preIs[] = shortcode_parse_atts($preInterface);                    
                }
            }
        }


        if(!empty($post)){        
        preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);
        if (in_array('interface', $matches[2])) {
            foreach($matches[3] as $preInterface){
                $preIs[] = shortcode_parse_atts($preInterface);
            }
        }
        }



    }

    wp_register_style('jqueryUI-core', WP_PLUGIN_URL . '/db-toolkit/jqueryui/jquery-ui.css');
    //wp_register_style('jqueryUI-base', WP_PLUGIN_URL . '/db-toolkit/jqueryui/base/jquery.ui.all.css');
    //wp_register_style('jqueryUI-blitzer', WP_PLUGIN_URL . '/db-toolkit/jqueryui/blitzer/jquery-ui-1.8.13.custom.css');
    
    wp_register_style('jquery-multiselect', WP_PLUGIN_URL . '/db-toolkit/libs/ui.dropdownchecklist.css');
    wp_register_style('jquery-validate', WP_PLUGIN_URL . '/db-toolkit/libs/validationEngine.jquery.css');

    wp_enqueue_style('jqueryUI-core');
    //wp_enqueue_style('jqueryUI-base');
    //wp_enqueue_style('jqueryUI-blitzer');
    
    wp_enqueue_style('jquery-multiselect');
    wp_enqueue_style('jquery-validate');



    // load interface specifics
    if(is_admin()){

        wp_register_style('interface_setup_styles', WP_PLUGIN_URL . '/db-toolkit/data_report/css/setup.css');
        wp_enqueue_style('interface_setup_styles');

        if(!empty($_GET['page']) || !empty($_GET['renderinterface'])){
            if(!empty($_GET['page'])){
                if(substr($_GET['page'],0,8) == 'dt_intfc'){
                    $isInterface = $_GET['page'];
                }
            }
            if(!empty($_GET['renderinterface'])){
                if(substr($_GET['renderinterface'],0,8) == 'dt_intfc'){
                    $isInterface = $_GET['renderinterface'];
                }
            }

            if(!empty($isInterface)){
               $preInterface = get_option($isInterface);
               if(!empty($preInterface['_CustomCSSSource'])){
                   // load scripts
                   // setup scripts and styles
                   foreach($preInterface['_CustomCSSSource'] as $handle=>$CSS){
                       wp_register_style($handle, $CSS['source']);
                       wp_enqueue_style($handle);
                   }
               }
            }
        }
    }else{

        wp_register_style('interface_table_styles', WP_PLUGIN_URL . '/db-toolkit/data_report/css/table.css');
        wp_enqueue_style('interface_table_styles');

        if(!empty($preIs)){
            $stylesAdded = array();
            foreach($preIs as $interface){
                   $preInterface = get_option($interface['id']);
                   if(!empty($preInterface['_CustomCSSSource'])){
                       // load scripts
                       // setup scripts and styles
                       foreach($preInterface['_CustomCSSSource'] as $handle=>$CSS){
                           if(array_search($CSS['source'], $stylesAdded) === false){
                               wp_register_style($handle, $CSS['source']);
                               wp_enqueue_style($handle);
                               $stylesAdded[] = $CSS['source'];
                           }
                       }
                   }
            }
        }
    }



}

//Scripts
function dt_scripts() {

    if(!is_admin()){
        global $post;
        //$te = wp_get_sidebars_widgets();
        $pattern = get_shortcode_regex();

        $texts = get_option('widget_text');
        $preIs = array();
        foreach($texts as $text){
            //vardump($text['text']);
            preg_match_all('/'.$pattern.'/s', $text['text'], $matches);
            if(!empty($matches[3])){
                foreach($matches[3] as $preInterface){
                    $preIs[] = shortcode_parse_atts($preInterface);
                }
            }
        }

        preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);
        //vardump($matches);
        
        if (in_array('interface', $matches[2])) {
            foreach($matches[3] as $preInterface){
                $preIs[] = shortcode_parse_atts($preInterface);
                //vardump($_SERVER);
            }
        }
    }

    // queue & register scripts
    wp_register_script('data_report', WP_PLUGIN_URL . '/db-toolkit/data_form/javascript.php', false, false, true);
    wp_register_script('data_form', WP_PLUGIN_URL . '/db-toolkit/data_report/javascript.php', false, false, true);


    //wp_register_script('jquery-ui-datepicker' , WP_PLUGIN_URL . '/db-toolkit/libs/ui.datepicker.js');
    //wp_register_script('jquery-ui-progressbar' , WP_PLUGIN_URL . '/db-toolkit/libs/ui.progressbar.js', false, false, true);

    wp_register_script('jquery-ui-custom' , WP_PLUGIN_URL . '/db-toolkit/jqueryui/jquery.ui.js');


    wp_register_script('jquery-multiselect', WP_PLUGIN_URL . '/db-toolkit/libs/ui.dropdownchecklist-min.js', false, false, true);
    wp_register_script('jquery-validate', WP_PLUGIN_URL . '/db-toolkit/libs/jquery.validationEngine.js');
    wp_register_script('highcharts', WP_PLUGIN_URL . '/db-toolkit/data_report/js/highcharts.js');
    wp_register_script('highcharts-exporting', WP_PLUGIN_URL . '/db-toolkit/data_report/js/exporting.src.js');


    wp_enqueue_script("jquery");
    wp_enqueue_script("jquery-ui-custom");
    //wp_enqueue_script("jquery-ui-core");
    //wp_enqueue_script("jquery-ui-progressbar");
    //wp_enqueue_script("jquery-ui-tabs");
    //wp_enqueue_script("jquery-ui-sortable");
    //wp_enqueue_script("jquery-ui-draggable");
    //wp_enqueue_script("jquery-ui-droppable");
    //wp_enqueue_script("jquery-ui-dialog");
    //wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_script('jquery-multiselect');
    wp_enqueue_script('data_report');
    wp_enqueue_script('data_form');
    wp_enqueue_script('jquery-validate');
    wp_enqueue_script('swfobject');

    wp_enqueue_script('highcharts');
    wp_enqueue_script('highcharts-exporting');

        /*$Types = loadFolderContents(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes');
	foreach($Types[0] as $Type){
		if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/javascript.php')){
                        //wp_register_script('fieldType_'.$Type[1], WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/javascript.php', false, false, true);
                        //wp_enqueue_script('fieldType_'.$Type[1]);
                }
	}*/

    // load interface specifics

    if(is_admin()){
        if(!empty($_GET['page']) || !empty($_GET['renderinterface'])){
            if(!empty($_GET['page'])){
                if(substr($_GET['page'],0,8) == 'dt_intfc'){
                    $isInterface = $_GET['page'];
                }
            }
            if(!empty($_GET['renderinterface'])){
                if(substr($_GET['renderinterface'],0,8) == 'dt_intfc'){
                    $isInterface = $_GET['renderinterface'];
                }
            }

            if(!empty($isInterface)){
               $preInterface = get_option($isInterface);
               if(!empty($preInterface['_CustomJSLibraries'])){
                   // load scripts
                   // setup scripts and styles
                   foreach($preInterface['_CustomJSLibraries'] as $handle=>$script){
                       $in_footer = false;
                       if($script['location'] == 'foot'){
                           $in_footer = true;
                       }
                       wp_register_script($handle, $script['source'], false, false, $in_footer);
                       wp_enqueue_script($handle);
                   }
               }
            }
        }
    }else{
       
        if(!empty($preIs)){
            $scriptsAdded = array();
            foreach($preIs as $interface){
               $preInterface = get_option($interface['id']);
               if(!empty($preInterface['_CustomJSLibraries'])){
                   // load scripts
                   // setup scripts and styles
                   foreach($preInterface['_CustomJSLibraries'] as $handle=>$script){
                       if(array_search($script['location'], $scriptsAdded) === false){
                           $in_footer = false;
                           if($script['location'] == 'foot'){
                               $in_footer = true;
                           }
                           wp_register_script($handle, $script['source'], false, false, $in_footer);
                           wp_enqueue_script($handle);
                           $scriptsAdded[] = $script['location'];
                       }
                   }
               }
            }
        }

    }

}

//Menus
function dt_menus() {

    global $wpdb;
    global $menu;


    $user = wp_get_current_user();


        //vardump($menu);
        // Create the new separator
        //$menu['2.995'] = array( '', 'manage_options', 'separator-dbtoolkit', '', 'wp-menu-separator' );

        // Create the new top-level Menu
        //add_menu_page ('Application Marketplace', 'App Marketplace', 'manage_options','appmarket', 'dt_appMarket', WP_PLUGIN_URL.'/db-toolkit/images/icons/shop_cart.png', '2.996');


        add_menu_page("DB-Toolkit", "DB-Toolkit", 'activate_plugins', "Database_Toolkit_Welcome", "dbtoolkit_dashboard", WP_PLUGIN_URL.'/db-toolkit/data_report/cog.png');
	$Dashboard = add_submenu_page("Database_Toolkit_Welcome", 'Dashboard', 'Dashboard', 'activate_plugins', "Database_Toolkit_Welcome", 'dbtoolkit_dashboard');
        $adminPage = add_submenu_page("Database_Toolkit_Welcome", 'Manage Interfaces', 'Interfaces & Clusters', 'activate_plugins', "Database_Toolkit", 'dbtoolkit_admin');

        $addNew = add_submenu_page("Database_Toolkit_Welcome", 'Create New Interface', 'New Interface', 'activate_plugins', "Add_New", 'dbtoolkit_admin');
        $NewCluster = add_submenu_page("Database_Toolkit_Welcome", 'Create New Cluster Interface', 'New Cluster', 'activate_plugins', "New_Cluster", 'dbtoolkit_cluster');
        $Import = add_submenu_page("Database_Toolkit_Welcome", 'Import Application', 'Install Application', 'activate_plugins', "dbtools_importer", 'dbtoolkit_import');
        $setup = add_submenu_page("Database_Toolkit_Welcome", 'General Settings', 'General Settings', 'activate_plugins', "dbtools_setup", 'dbtoolkit_setup');



        //$setup = add_submenu_page("Database_Toolkit", 'Bug Report', 'Bug Report', 'activate_plugins', "dbtools_bugreport", 'dbtoolkit_bugreport');
        //$setup = add_submenu_page("Database_Toolkit", 'Documentation A', 'Documention B', 'activate_plugins', "dbtools_manual", 'dbtoolkit_manual');


            add_action('admin_print_styles-'.$adminPage, 'dt_styles');
            add_action('admin_head-'.$adminPage, 'dt_headers');
            add_action('admin_print_scripts-'.$adminPage, 'dt_scripts');
            add_action('admin_footer-'.$adminPage, 'dt_footers');

            add_action('admin_print_styles-'.$NewCluster, 'dt_styles');
            add_action('admin_head-'.$NewCluster, 'dt_headers');
            add_action('admin_print_scripts-'.$NewCluster, 'dt_scripts');
            add_action('admin_footer-'.$NewCluster, 'dt_footers');

            add_action('admin_print_styles-'.$addNew, 'dt_styles');
            add_action('admin_head-'.$addNew, 'dt_headers');
            add_action('admin_print_scripts-'.$addNew, 'dt_scripts');
            add_action('admin_footer-'.$addNew, 'dt_footers');

            add_action('admin_print_styles-'.$Import, 'dt_styles');
            add_action('admin_head-'.$Import, 'dt_headers');
            add_action('admin_print_scripts-'.$Import, 'dt_scripts');
            add_action('admin_footer-'.$Import, 'dt_footers');

            add_action('admin_print_styles-'.$setup, 'dt_styles');
            add_action('admin_head-'.$setup, 'dt_headers');
            add_action('admin_print_scripts-'.$setup, 'dt_scripts');
            add_action('admin_footer-'.$setup, 'dt_footers');

            add_action('admin_print_styles-'.$Dashboard, 'dt_styles');
            add_action('admin_head-'.$Dashboard, 'dt_headers');
            add_action('admin_print_scripts-'.$Dashboard, 'dt_scripts');
            add_action('admin_footer-'.$Dashboard, 'dt_footers');

	////add_submenu_page("Database_Toolkit", 'Setup', 'Setup', 'read', "General Settings", 'dbtoolkit_setup');


    $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
    if(!empty($interfaces)) {
        foreach($interfaces as $interface) {

            $cfg = get_option($interface['option_name']);
             if($cfg['_menuAccess'] == 'null'){
                $cfg['_menuAccess'] = 'read';
            }

            if(!empty($user->allcaps[$cfg['_menuAccess']])){
                if(!empty($cfg['_ItemGroup'])) {
                    $Groups[$cfg['_ItemGroup']][] = $cfg;
                }

            }

        }
        if(empty($Groups)){
            return;
        }
        foreach($Groups as $Group=>$Interfaces){
            $pageName = str_replace("'",'', '_grp_'.$Group);
            $pageName = str_replace("+",'_', $pageName);
            $pageName = str_replace(" ",'_', $pageName);

            $pageName = $Interfaces[0]['ID'];

            $groupPage = add_object_page($Group, $Group, $Interfaces[0]['_menuAccess'], $pageName, "dbtoolkit_viewinterface", WP_PLUGIN_URL.'/db-toolkit/data_report/table.png');
            add_submenu_page($pageName, $Interfaces[0]['_interfaceName'], $Interfaces[0]['_interfaceName'], $Interfaces[0]['_menuAccess'], $pageName, 'dbtoolkit_viewinterface');//admin.php?page=Database_Toolkit&renderinterface='.$interface['option_name']);
            
            for($i = 1; $i <= count($Interfaces)-1; $i++){
                
                $subPage = add_submenu_page($pageName, $Interfaces[$i]['_interfaceName'], $Interfaces[$i]['_interfaceName'], $Interfaces[$i]['_menuAccess'], $Interfaces[$i]['ID'], 'dbtoolkit_viewinterface');//admin.php?page=Database_Toolkit&renderinterface='.$interface['option_name']);

                add_action('admin_head-'.$subPage, 'dt_headers');
                add_action('admin_print_scripts-'.$subPage, 'dt_scripts');
                add_action('admin_print_styles-'.$subPage, 'dt_styles');
                add_action('admin_footer-'.$subPage, 'dt_footers');


            }

                add_action('admin_head-'.$groupPage, 'dt_headers');
                add_action('admin_print_scripts-'.$groupPage, 'dt_scripts');
                add_action('admin_print_styles-'.$groupPage, 'dt_styles');
                add_action('admin_footer-'.$groupPage, 'dt_footers');
        }
    }
}

function dt_adminMenus() {
    global $wp_admin_bar, $wpdb;

    if (!is_admin_bar_showing() )
    return;

    $user = wp_get_current_user();
    //vardump($user);



    $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
    if(!empty($interfaces)) {
        foreach($interfaces as $interface) {

            $cfg = get_option($interface['option_name']);
             if($cfg['_menuAccess'] == 'null'){
                $cfg['_menuAccess'] = 'read';
             }

            if(!empty($user->allcaps[$cfg['_menuAccess']])){
                if(!empty($cfg['_ItemGroup']) && !empty($cfg['_SetAdminMenu'])) {
                    $Groups[$cfg['_ItemGroup']][] = $cfg;
                }

            }
        }

        if(empty($Groups)){
            return;
        }
        foreach($Groups as $Group=>$Interfaces){

            // check capability
            if(current_user_can($Interfaces[0]['_menuAccess']) && !empty($Interfaces[0]['_SetAdminMenu'])){
                // group link
                //$groupPage = add_object_page($Group, $Group, $Interfaces[0]['_menuAccess'], $pageName, "dbtoolkit_viewinterface", WP_PLUGIN_URL.'/db-toolkit/data_report/table.png');
                //add_submenu_page($pageName, $Interfaces[0]['_interfaceName'], $Interfaces[0]['_interfaceName'], $Interfaces[0]['_menuAccess'], $pageName, 'dbtoolkit_viewinterface');//admin.php?page=Database_Toolkit&renderinterface='.$interface['option_name']);
                //echo $Group.' - ';
                //vardump($Interfaces);

                $wp_admin_bar->add_menu( array( 'id' => $Interfaces[0]['ID'], 'title' => $Group, 'href' => get_admin_url().'admin.php?page='.$Interfaces[0]['ID'] ) );

                for($i = 0; $i <= count($Interfaces)-1; $i++){
                    if(current_user_can($Interfaces[$i]['_menuAccess']) && !empty($Interfaces[0]['_SetAdminMenu'])){
                        $wp_admin_bar->add_menu( array( 'parent' => $Interfaces[0]['ID'], 'title' => $Interfaces[$i]['_interfaceName'], 'href' => get_admin_url().'admin.php?page='.$Interfaces[$i]['ID'] ) );
                    }
                }
            }
        }
    }

}

//Footers
function dt_footers() {
    require_once(DB_TOOLKIT.'footers.php');
    require_once(DB_TOOLKIT.'data_report/footers.php');
    require_once(DB_TOOLKIT.'data_form/footers.php');
}

// Ajax System
function dt_ajaxCall() {
    $ref = parse_url(basename($_SERVER['HTTP_REFERER']));

    global $wpdb;

    if(!empty($_POST['func'])) {
        $func = $_POST['func'];
        if (!empty($_POST['FARGS'])) {
            $func_args = $_POST['FARGS'];
        } else {
            $func_args = array();
        }
    }
    if (!empty($func) && function_exists($func)) {
        header ("Expires: Mon, 21 Nov 1997 05:00:00 GMT");    // Date in the past
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
        header ("Pragma: no-cache");                          // HTTP/1.0
        $Output = call_user_func_array($func, $func_args);
        if(is_array($Output)) {
            header('Content-type: application/json; charset=UTF-8');
            //header('Content-type: text/html; charset=UTF-8');
            echo json_encode($Output);
        }else {
            //header('Content-type: text/html; charset=UTF-8');
            echo $Output;
        }
        exit();
    }
}

// Page Loading Functions

function dbtoolkit_admin() {
    global $user_ID;
    include_once(DB_TOOLKIT.'dbtoolkit_admin.php');
}

function dbtoolkit_cluster() {
    global $user_ID;
    include_once(DB_TOOLKIT.'dbtoolkit_cluster.php');
}

function dbtoolkit_dashboard() {
    global $user_ID;
    include_once(DB_TOOLKIT.'dbtoolkit_welcome.php');
}

function dbtoolkit_setup() {
    global $user_ID;
    include_once(DB_TOOLKIT.'dbtoolkit_settings.php');
}
function dbtoolkit_import() {
    global $user_ID;
    include_once(DB_TOOLKIT.'dbtoolkit_import.php');
}
function dbtoolkit_manual() {
    global $user_ID;
    include_once(DB_TOOLKIT.'manual/index.php');
}

// Interface View Functions

function dbtoolkit_viewinterface(){
    $Interface = get_option($_GET['page']);
    $Title = $Interface['_interfaceName'];
    if(!empty($Interface['_ReportDescription'])) {
       $Title = $Interface['_ReportDescription'];
    }


    ?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div><h2><?php _e($Title); ?><a class="button add-new-h2" href="admin.php?page=Database_Toolkit&interface=<?php echo $_GET['page']; ?>">Edit</a></h2>

    <?php
    $fset = get_option('dt_set_'.$Interface['ID']);
    if(!empty($fset)){
    ?>
    	<ul class="subsubsub">

                <?php

                    $tablen = count($fset);
                    $index = 1;
                    $link = explode('&ftab', $_SERVER['REQUEST_URI']);
                    echo '<li><a href="'.$link[0].'">All</a> | </li>';
                    foreach($fset as $tab){
                        $break = '';
                        $counter = '';
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
                        echo '<li><a href="'.$link[0].'&ftab='.$tab['code'].'">'.$tab['Title'].$counter.'</a>'.$break.'</li>';
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
    echo dt_renderInterface($_GET['page']);
    echo '</div>';
    echo '</div>';

}

// Processeing and Rendering of interfaces in wp frontend
function dt_process() {


    if(!empty($_POST['func']) && !empty($_POST['action'])) {
        if($_POST['action'] == 'wp_dt_ajaxCall') {
            dt_ajaxCall();
            die;
        }
    }

    if(!empty($_POST['processKey'])) {

    $_POST = stripslashes_deep($_POST);
        if($_POST['processKey'] == $_SESSION['processKey']) {

            include_once(DB_TOOLKIT.'daiselements.class.php');
            include_once(DB_TOOLKIT.'data_form/class.php');
            include_once(DB_TOOLKIT.'data_report/class.php');

            unset($_SESSION['processKey']);
            $_SESSION['DF_Post'] = array();


            if(!empty($_POST['dr_update'])) {
                $EID = $_POST['dataForm']['EID'];
                $Setup = getelement($EID);
                unset($_POST['dataForm']['dr_update']);
                unset($_POST['dataForm']['EID']);
                $Return = df_processUpdate($_POST['dataForm'], $EID);
                dr_trackActivity('Update', $EID, $Return['Value']);
                if(empty($Setup['Content']['_NotificationsOff'])) {
                    $_SESSION['DF_Post'][] = $Return['Message'];
                }
                $_SESSION['DF_Post_returnID'] = $Return['Value'];
                $_SESSION['DF_Post_EID'] = $EID;
            }else {
                foreach($_POST['dataForm'] as $EID=>$Data) {
                    $Return = df_processInsert($EID, $Data);
                    // Track Activity
                    dr_trackActivity('Insert', $EID, $Return['Value']);
                    $Setup = getelement($EID);
                    if(empty($Setup['Content']['_NotificationsOff'])) {
                        $_SESSION['DF_Post'][] = $Return['Message'];
                    }
                }
            }
            $Redirect = $_SERVER['HTTP_REFERER'];
            if(!empty($Return['Value'])) {
                $ReturnValue = $Return['Value'];
            }
            if(is_admin()){

                if(!empty($Setup['Content']['_ItemViewInterface'])) {
                    $Location = 'admin.php?page=Database_Toolkit';
                }else{
                    $Location = $_SERVER['HTTP_REFERER'];
                }
            }else{
                if(!empty($Setup['Content']['_ItemViewPage'])) {
                    $Location = get_permalink($Setup['Content']['_ItemViewPage']);
                }else{
                    $Location = $_SERVER['HTTP_REFERER'];
                }
            }
            //echo $Location;
            //die;
            if(!empty($ReturnValue)) {
                $url = parse_url($_SERVER['HTTP_REFERER']);
                $returntoken = '?';

                if(!empty($url['query'])) {
                    if(empty($Setup['Content']['_ItemViewPage'])) {
                        $Location = str_replace('?'.$url['query'], '', $_SERVER['HTTP_REFERER']);
                    }

                    parse_str($url['query'], $gets);
                    parse_str($ReturnValue, $returngets);
                    if(!empty($Setup['Content']['_ItemViewInterface'])){
                       $RedirInterface = get_option($Setup['Content']['_ItemViewInterface']);
                       if(!empty($RedirInterface['_ItemGroup'])){
                            $gets['page'] = $Setup['Content']['_ItemViewInterface'];
                       }else{
                           $gets['page'] = 'Database_Toolkit';
                           $gets['renderinterface'] = $Setup['Content']['_ItemViewInterface'];
                       }
                    }
                    $ReturnValue = htmlspecialchars_decode(http_build_query(array_merge($gets, $returngets)));
                }

                $Redirect = $Location.$returntoken.$ReturnValue;
            }
            //echo $Redirect;
            //die;
            header('Location: '.$Redirect);
            die;
        }
    }
    //vardump($_POST);
    if(!empty($_POST['importKey'])) {

        $_POST = stripslashes_deep($_POST);
        $_SESSION['adminscripts'] .= "
          //df_buildImportManager(eid);
        ";
        if(empty($_FILES['fileImport']['size'])){
            $_SESSION['adminscripts'] .= "
              df_buildImportForm('".$_POST['importInterface']."');
            ";
            $Redirect = $_SERVER['HTTP_REFERER'];
            header('Location: '.$Redirect);
            die;
        }


        $path = wp_upload_dir();

		// set filename and paths
		$Ext = pathinfo($_FILES['fileImport']['name']);
		$newFileName = $_POST['importInterface'].'.'.$Ext['extension'];
		$newLoc = $path['path'].'/'.$newFileName;

        $_SESSION['import_'.$_POST['importInterface']]['import'] = wp_upload_bits($newFileName, null, file_get_contents($_FILES['fileImport']['tmp_name']));

        $_SESSION['adminscripts'] .= "
          df_buildImportManager('".$_POST['importInterface']."');
        ";

        $Redirect = $_SERVER['HTTP_REFERER'];
        header('Location: '.$Redirect);
        die;
    }

    if(!empty($_POST['importPrepairKey'])) {
        $Element = getelement($_POST['importInterface']);
        $_SESSION['import_'.$_POST['importInterface']]['import']['table'] = $Element['Content']['_main_table'];
        $_SESSION['import_'.$_POST['importInterface']]['import']['delimiter'] = $_POST['importDelimeter'];
        if(!empty($_POST['importSkipFirst'])){
            $_SESSION['import_'.$_POST['importInterface']]['import']['importSkipFirst'] = $_POST['importSkipFirst'];
        }
        $_SESSION['import_'.$_POST['importInterface']]['import']['map'] = $_POST['importMap'];
        $_SESSION['adminscripts'] .= "
            df_processImport('".$_POST['importInterface']."');
        ";

        $Redirect = $_SERVER['HTTP_REFERER'];
        header('Location: '.$Redirect);
        die;
    }

    // API Call
    if(!empty($_GET['APIKey'])) {
        include_once(DB_TOOLKIT.'libs/api_engine.php');
        die;
    }

/// EXPORT

    foreach($_GET as $PDFExport=>$Val) {
        if(!is_array($Val)) {
            if(strstr($PDFExport, 'format_')) {
                $export = explode('_dt_', $PDFExport);
                $exportFormat = $Val;
                $Media['ID'] = 'dt_'.$export[1];
                $Element = getElement($Media['ID']);
                $Config = $Element['Content'];
            }
        }
    }

    //error_reporting(E_ALL);
    //ini_set('display_errors','On');

//esds

    if(!empty($exportFormat)) {

        if($exportFormat == 'pdf') {

            include_once(DB_TOOLKIT.'daiselements.class.php');
            include_once(DB_TOOLKIT.'data_form/class.php');
            include_once(DB_TOOLKIT.'data_report/class.php');
            include_once(DB_TOOLKIT.'data_itemview/class.php');

            include_once(DB_TOOLKIT.'libs/fpdf.php');
            include_once(DB_TOOLKIT.'libs/pdfexport.php');


            $input_params["return"] = isset($input_params["return"]) ? $input_params["return"] : false;
            if(empty($Config['_orientation'])) {
                $Config['_orientation'] = 'P';
            }

        $report = new PDFReport($Config['_orientation'], $Config['_ReportTitle']);
        //you should use loadlib here
        //dump($_SESSION['reportFilters'][$Media['ID']]);
        if(!empty($Config['_FilterMode'])){
        $Res = mysql_query("SELECT ID, Content FROM `dais_elements` WHERE `Element` = 'data_report' AND `ParentDocument` = ".$Element['ParentDocument']." AND `ID` != '".$Media['ID']."';");
        while($element = mysql_fetch_assoc($Res)){            //dump($element);
            $eConfig = unserialize($element['Content']);
            $preReport['ID'] = $element['ID'];
            $preReport['Config'] = $eConfig;
            $reportExports[] = $preReport;
        }
        }else{
            $preReport['ID'] = $Media['ID'];
            $preReport['Config'] = $Config;
            $reportExports[] = $preReport;
        }

        $input_params["return"] = isset($input_params["return"]) ? $input_params["return"] : false;


        foreach($reportExports as $key=>$reportExport){
            //dump($_SESSION);
            $Continue = true;
            $Media['ID'] = $reportExport['ID'];
            $Config = $reportExport['Config'];

            foreach($reportExport['Config']['_Field'] as $Key=>$Value){
                if($Value == 'viewitem_filter'){
                    if(empty($_SESSION['viewSelector_'.$Media['ID']])){
                        $Continue = false;
                    }
                }
            }

            if(!empty($Continue)){
                $limit = 'full';
                if(!empty($_GET['limit'])) {
                    $limit = $_GET['limit'];
                }

                $OutData = dr_BuildReportGrid($Media['ID'], false, $_SESSION['report_'.$Media['ID']]['SortField'], $_SESSION['report_'.$Media['ID']]['SortDir'], 'pdf', $limit );
                $CountStat = array();

                if(is_array($OutData)){
                    if($key > 0){
                        $report->addPage();
                    }

                // outdata - Headings

                    $report->cf_report_headersMain($OutData, $Config);

                    if(!empty($OutData['Totals'])) {
                        foreach($OutData['Totals'] as $Field=>$Value) {
                            sort($fieldset);
                            $totalData[$Field] = $Value;
                        }
                        $report->cf_report_datagrid($totalData, 7);
                        unset($OutData['Totals']);
                    }
                    }
                    $report->cf_report_spacer();
                    $Headers = array();
                    if(!empty($OutData[0])){
                        foreach($OutData[0] as $Header=>$v) {
                            if(strpos($Config['_IndexType'][$Header], 'hide') === false){
                                if(!empty($Config['_FieldTitle'][$Header])) {
                                    $Headers[] = $Config['_FieldTitle'][$Header];
                                }else{
                                    $Headers[] = $Header;
                                }
                            }
                        }

                        $Total = count($OutData)-2;
                        $Body = array();
                        $Counter = 1;
                        for($i = 0; $i<= $Total; $i++) {
                            foreach($OutData[$i] as $Field=>$v){
                                if(strpos($Config['_IndexType'][$Field], 'hide') === false){
                                    $Body[$i][] = str_replace('&nbsp;','',html_entity_decode($v));
                                }
                            }
                        }
                    }
                    if(!empty($Config['_chartMode'])){
                        if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_report/chartexport/charts/chartImage_'.$Media['ID'].'.jpg')){
                            $report->cf_report_image(WP_PLUGIN_DIR.'/db-toolkit/data_report/chartexport/charts/chartImage_'.$Media['ID'].'.jpg');
                        }
                    }

                    $options["width"] = "100%";
                    $report->cf_report_data_col_grid($Headers, $Body, $OutData, $Config);
                    $report->cf_report_spacer();


                //break;
                }
            }
            $report->cf_report_generate_output();
            mysql_close();
            die;
        }




		if($exportFormat == 'csv'){

                $CSVout = fopen('php://output', 'w');






				$prequery = explode('LIMIT', $_SESSION['queries'][$Media['ID']]);
				$sql_query = $prequery[0];
			 	$filename = uniqid(date('mdHis')).'.csv';
                                $out = '';
				// Gets the data from the database
				$result = mysql_query($sql_query);
				$fields_cnt = mysql_num_fields($result);

                                //dump($Config['_Field']);

                                //dump($Config);
                                //die;
                                $VisibleFields = array();
                                $FieldHeaders = array();
                                foreach($Config['_Field'] as $Field=>$Value){
                                    if($Config['_IndexType'][$Field] == 'index_show' || $Config['_IndexType'][$Field] == 'noindex_show'){
                                        $VisibleFields[] = $Field;
                                        $FieldHeaders[] = $Config['_FieldTitle'][$Field];
                                    }
                                }

                                ob_start();
                                fputcsv($CSVout, $FieldHeaders, ';')."\r\n";
                                $out .= ob_get_clean();

                                while($exportData = mysql_fetch_assoc($result)){

                                    // run each field type on the result
                                    $Row = array();
                                    foreach($Config['_Field'] as $Field=>$Value){
                                        $FieldType = explode('_', $Value);

                                        if(in_array($Field, $VisibleFields)){
                                            if(count($FieldType) ==2){
                                                // include fieldtype
                                                if(file_exists(DB_TOOLKIT.'/data_form/fieldtypes/'.$FieldType[0].'/functions.php')){
                                                    include_once(DB_TOOLKIT.'/data_form/fieldtypes/'.$FieldType[0].'/functions.php');
                                                }
                                                // [type_processValue($Value, $Type, $Field, $Config, $EID, $Data)
                                                $Func = $FieldType[0].'_processvalue';
                                                //$FieldValue =
                                                $outRow = $exportData[$Field];

                                                if(function_exists($Func)){
                                                   // echo 'yes there is '.$Func.'<br>';
                                                   $Row[] = trim(strip_tags(str_replace('<br />', "\r\n", $Func($outRow, $FieldType[1], $Field, $Config, $Media['ID'], $exportData))));
                                                }else{
                                                    $Row[] = $outRow;
                                                }
                                                //dump($FieldType);
                                            }else{
                                                $Row[] = $exportData[$Field];
                                            }
                                        }
                                    }

                                    //combine row
                                    ob_start();
                                    fputcsv($CSVout, $Row, ';')."\r\n";
                                    $out .= ob_get_clean();

                                }
                                //while($export)


				// Format the data





				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				//header("Content-Length: " . strlen($out));
				// Output to browser with appropriate mime type, you choose ;)
				//header("Content-type: text/x-csv");
				//header("Content-type: text/csv");
				header("Content-type: application/csv");
				header("Content-Disposition: attachment; filename=$filename");
                                //echo '<pre>';

                                echo $out;

                                //echo '</pre>';
                                fclose($CSVout);
                                mysql_close();
				exit;


		}


                if($exportFormat == 'template'){
                    echo dt_renderInterface($Media['ID']);
                    die;
                }


        if($exportFormat != 'pdf') {
            $Element = getelement($Media['ID']);
            $Config = $Element['Content'];
            if(!empty($Config['_Show_Plugins'])) {
                // to do : configure adding plugins to the tool bar
                if(file_exists(DB_TOOLKIT.'data_report/plugins/'.$exportFormat.'/functions.php')) {
                    include_once(DB_TOOLKIT.'data_report/plugins/'.$exportFormat.'/functions.php');
                    mysql_close();
                    die;
                }
            }
        }

    }




}


function dt_listApplications($Application){

    $Apps = get_option('dt_int_Apps');
    echo '<select id="appsSelector" name="Data[Content][_Application]" >';
    foreach($Apps as $app=>$state){
        if($state == 'open'){
            $Sel = '';
            if($app == $Application){
                $Sel = 'selected="selected"';
            }
            echo '<option value="'.$app.'" '.$Sel.'>'.ucwords($app).'</option>';
        }
    }
    echo '</select>';

}



function dt_rendercluster($cluster){
    $Interface = get_option($cluster);
    $cfg = unserialize(base64_decode($Interface['Content']));
    parse_str($cfg['_clusterLayout'], $layout);

    // Build Layout Array First...

    if(is_admin ()){
    echo '<div class="wrap">';
        echo '<div id="poststuff">';
        //echo '<div class="metabox-holder">';
    }
        foreach($cfg['_grid'] as $row=>$cols){

            echo '<div id="clusterRow cluster-'.$row.'" style="width:100%; overflow:hidden;" class="formRow">';

                foreach($cols as $col=>$width){

                    echo '<div class="clusterColumn cluster-'.$row.'-'.$col.'" id="'.$row.'_'.$col.'" style="width: '.$width.'; float: left;">';
                    echo '<div class="clusterItem">';
                        $content = array_keys($layout, $row.'_'.$col);
                        if(!empty($content)){
                            $output = '';
                            foreach($content as $render){
                                $output .= dt_renderInterface($render);
                            }
                            echo $output;
                        }else{
                            echo '&nbsp;';
                        }

                    echo '</div>';
                    echo '</div>';

                }

            echo '<div style="clear:both;"></div>';
            echo '</div>';

        }
    if(is_admin ()){
            //echo '</div>';
        echo '</div>';
    echo '</div>';
    }

    return;
}



// Render interface from shortcode to front end and view
function dt_renderInterface($interface){

   

    if(is_array($interface)) {
        if(!empty($interface['id'])){
            unset($_SESSION['viewitemFilter'][$interface['id']]);
            $ID = $interface['id'];
            unset($interface['id']);
        }
        if(!empty($interface['filter']) && !empty($interface['by'])){
            $_GET[$interface['filter']] = $interface['by'];
            unset($interface['filter']);
            unset($interface['by']);
        }
    }else {
        unset($_SESSION['viewitemFilter'][$interface]);
        $ID = $interface;
    }
    $Media = get_option($ID);

    if(empty($Media)) {
        return;
    }

    if($Media['Type'] == 'Cluster'){
        ob_start();
        dt_rendercluster($ID);
        $Return = ob_get_clean();
        return do_shortcode($Return);
    }
    //echo $Media['_Icon'];    
    if($Media['_menuAccess'] != 'null'){
        $user = wp_get_current_user();
        if(empty($user->allcaps[$Media['_menuAccess']])){
            return;
        }
    }    
    $Media['Content'] = unserialize(base64_decode($Media['Content']));
    $Config = $Media['Content'];
    $Return = '';


        if(empty($_SESSION['report_'.$Media['ID']]['limitOveride'])){
            $_SESSION['report_'.$Media['ID']]['limitOveride'] = false;
        }

        if(!empty($_GET['limit'])){
            $_SESSION['report_'.$Media['ID']]['limitOveride'] = floatval($_GET['limit']);
        }

    ob_start();
        include(DB_TOOLKIT.'data_report/element.def.php');
        if(empty($Config['_HideFrame']) && ($Config['_ViewMode'] != 'search' && $Config['_ViewMode'] != 'form')){
            //$InfoBox()
            if(!is_admin()){
                InfoBox($Config['_ReportDescription']);
            }
        }
    $Return .= ob_get_clean();


    // Load ToolBar
    if($Config['_ViewMode'] == 'list'){
        ob_start();
            include(DB_TOOLKIT.'data_report/toolbar.php');
        $Return .= ob_get_clean();
    }


    // Determine Mode
    if(empty($Config['_ViewMode'])){
        $Config['_ViewMode'] = 'list';
    }

    if(!empty($_GET['npage'])){
        $newPage = floatval($_GET['npage']);
        if(is_array($interface)){
            $_SESSION['report_'.$interface['id']]['LastPage'] = $newPage;
        }else{
            $_SESSION['report_'.$interface]['LastPage'] = $newPage;
        }
    }
    
    switch ($Config['_ViewMode']){
        
        case 'list':
            ob_start();
                include(DB_TOOLKIT.'data_report/listmode.php');
            $Return .= ob_get_clean();
            break;            break;
        case 'view':
            ob_start();
                include(DB_TOOLKIT.'data_report/viewmode.php');
            $Return .= ob_get_clean();
            break;
        case 'form':
            ob_start();
                include(DB_TOOLKIT.'data_report/formmode.php');
            $Return .= ob_get_clean();
            break;
        case 'search':
            ob_start();
                include(DB_TOOLKIT.'data_report/searchmode.php');
            $Return .= ob_get_clean();
            break;
    }


    if($error = mysql_error()){
        if(is_admin()){
            
            $InterfaceData = get_option($Media['ID']);
            $InterfaceDataraw = base64_encode(serialize($InterfaceData));
            
            if(empty($_SESSION['errorReport'][$Media['ID']][md5($InterfaceDataraw)])){
                ob_start();
                echo '<h4>Error</h4>';
                echo $error;
                echo '<h4>Queries</h4>';
                vardump($_SESSION['queries'][$Media['ID']]);
                $error = ob_get_clean();
                echo '<div id="interfaceError" class="error" style="padding:5px;">An error has been detected while building this interface. Would you like to submit an error report to the developer? <input type="button" class="button" value="Send Report" onclick="dbt_sendError(\''.$Media['ID'].'\', \''.  base64_encode($error).'\');" /></div>';
            }
        }
    }


    if(empty($Config['_HideFrame']) && ($Config['_ViewMode'] != 'search' && $Config['_ViewMode'] != 'form')){
        //$InfoBox()
        ob_start();
        if(!is_admin()){
            EndInfoBox();
        }
        $Return .= ob_get_clean();
    }

    if(!empty($Config['_customFooterJavaScript'])){

        if(is_admin ()){
            $_SESSION['adminscripts'] .= $Config['_customFooterJavaScript'];
        }else{
            $_SESSION['dataform']['OutScripts'] .= $Config['_customFooterJavaScript'];
        }
    }

    $Return = do_shortcode($Return);    

    return str_replace("\r\n", '', $Return);
   
}

function dbt_sendError($Interface, $ErrorData){
    
    global $current_user;
    get_currentuserinfo();



$InterfaceData = get_option($Interface);
$InterfaceDataraw = base64_encode(serialize($InterfaceData));
$InterfaceData['Content'] = unserialize(base64_decode($InterfaceData['Content']));

    unset($_SESSION['errorReport'][$Interface]);
    $_SESSION['errorReport'][$Interface][md5($InterfaceDataraw)] = true;

    vardump($InterfaceData);
   // die;


$to = 'DB-Toolkit Support <support@dbtoolkit.co.za>';
$subject = 'DB-Toolkit Error Report';
ob_start();
echo "<h4>Wordpress Details</h4>";
vardump('Site Name:'.get_bloginfo('name'));
vardump('Site URL:'.get_bloginfo('siteurl'));
vardump('Admin Email:'.get_bloginfo('admin_email'));
vardump('Wordpress Version:'.get_bloginfo('version'));
echo "<h4>Query Error</h4>";
vardump(base64_decode($ErrorData));
echo "<h4>Config</h4>";
vardump($InterfaceData);
echo '<h4>Raw Config</h4>';
echo $InterfaceDataraw;

$message = ob_get_clean();
$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: '.$current_user->display_name.' <'.$current_user->user_email.'>' . "\r\n";


return mail($to, $subject, $message, $headers);
    
}


// delete interface
function dt_removeInterface($Interface) {

    delete_option($Interface);
    delete_option('filter_Lock_'.$Interface);
    delete_option('dt_set_'.$Interface);
    return true;
}


function dt_admin_init(){
	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		if ( in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) {
			add_filter('mce_buttons', 'dt_mce_button');
			add_filter('mce_external_plugins', 'dt_mce_plugin');
			//add_action('admin_head','add_simple_buttons');
			add_action('edit_form_advanced', 'dt_advanced_buttons');
			add_action('edit_page_form', 'dt_advanced_buttons');
		}
	}
}

function dt_mce_button($buttons) {
	array_push( $buttons, '|', 'db_toolkitInterface');

	return $buttons;
}

function dt_mce_plugin($plugins) {
	$plugins['db_toolkit'] = WP_PLUGIN_URL. '/db-toolkit/libs/editor_plugin.js';

	return $plugins;
}

function dt_advanced_buttons(){ ?>
	<script type="text/javascript">
		var defaultSettings = {},
			outputOptions = '',
			selected ='',
			content = '';

		defaultSettings['toolkitInterface'] = {
			caption: {
				name: 'Caption',
				defaultvalue: 'Caption goes here',
				description: 'Caption title goes here',
				type: 'text'
			},
			state: {
				name: 'State',
				defaultvalue: 'close',
				description: 'Select between expanded and closed state',
				type: 'select',
				options: 'open|close'
			},
			content: {
				name: 'Content',
				defaultvalue: 'Content goes here',
				description: 'Content text or html',
				type: 'textarea'
			}
		};


                function ajaxCall() {
                    ajaxurl_dbt = ajaxurl;
                    var vars = { action : 'dt_ajaxCall',func: ajaxCall.arguments[0]};
                    for(i=1;ajaxCall.arguments.length-1>i; i++) {
                        vars['FARGS[' + i + ']'] = ajaxCall.arguments[i];
                    }
                    var callBack = ajaxCall.arguments[ajaxCall.arguments.length-1];
                    jQuery.post(ajaxurl_dbt,vars, function(data){
                        callBack(data);
                    });
                }


		function insertInterface(tag){

			tbWidth = 500;
			tbHeight = 104;
                        if(jQuery('#db_tookitInsertInterface_Panel').length == 0){
                            var tbOptions = "<div id='db_tookitInsertInterface_Panel'>";
                            tbOptions += '<div id="dbToolkitInterfaceApps">Loading Apps</div>';
                            tbOptions += '<div style="clear:both;"></div>';
                            tbOptions += '</div>';

                            var form = jQuery(tbOptions);
                            var table = form.find('table');
                            form.appendTo('body').hide();
                        }else{
                            tb_show( 'Insert Interface', '#TB_inline?inlineId=db_tookitInsertInterface_Panel' );
                            return;
                        }

                    tb_show( 'Insert Interface', '#TB_inline?inlineId=db_tookitInsertInterface_Panel' );

                    ajaxCall('dt_listApps', function(d){
                        if(d.app.length >0){
                            dtLoadApp(d.app);
                        }
                        jQuery('#dbToolkitInterfaceApps').html(d.html);
                        jQuery('#dbtoolkit_AppList').change(function(){
                            jQuery('#dbtoolkit_InterfaceList').html('<h3>Loading....</h3>');
                            dtLoadApp(this.value);
                        });
                    });
		}

                function dtLoadApp(app){
                    ajaxCall('dt_listInterfaces', app, function(i){
                        jQuery('#dbtoolkit_InterfaceList').html(i);
                        jQuery('.interfaceInserter').click(function(){
                            //alert(this.value);
                            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, ' [interface id="'+this.id+'"] ');
                            tb_remove();
                        });
                    });
                }
		jQuery(document).ready(function(){

                   // alert('pong');

                });
	</script>
<?php }

function dt_publicReg($a, $b, $c){
    global $current_user;

    if(!empty($a[0])){
        // check permissions for public
        if($a[0] == 'public'){
            // check the user is not signed in
            if(empty($current_user->id)){
                return do_shortcode($b);
            }
        }
        if($a[0] == 'private'){
            // check the user is signed in
            if(!empty($current_user->id)){
                return do_shortcode($b);
            }
        }

    }
    return;
}

/// Dashboard Widgets

// Create the function to output the contents of our Dashboard Widget

function dt_renderDashboardWidget($a, $b) {
    //vardump($b);
    echo dt_renderInterface($b['id']);

}

// Create the function use in the action hook

function dt_dashboard_widgets() {
    global $wpdb;
    $dashBoardWidgets = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
    if(!empty($dashBoardWidgets)){
        add_action('admin_head', 'dt_headers');
        add_action('admin_print_scripts', 'dt_scripts');
        add_action('admin_print_styles', 'dt_styles');
        add_action('admin_footer', 'dt_footers');
    }
    foreach($dashBoardWidgets as $widget) {

        $myWidget = get_option($widget['option_name']);
        if(!empty($myWidget['_Dashboard'])) {

            $Title = $myWidget['_interfaceName'];
            $Show = true;
            if(!empty($myWidget['_ReportDescription'])) {
                $Title = $myWidget['_ReportDescription'];
            }
            if($myWidget['_menuAccess'] != 'null'){
                $user = wp_get_current_user();
                if(empty($user->allcaps[$myWidget['_menuAccess']])){
                    $Show = false;
                }
            }
            if(!empty($Show)){

                wp_add_dashboard_widget($myWidget['ID'], $Title, 'dt_renderDashboardWidget', 'alert');
            }
        }
    }

}

function dt_remove_dashboard_widgets() {
	// Globalize the metaboxes array, this holds all the widgets for wp-admin
        // chose to keep these as the user can remove the defaults if they so choose.
        // perhaps i'll make a setting to keep remove defaults

                $defaults = get_option('_dbtoolkit_defaultinterface');
                if(empty($defaults['_DisableDashboardDefaults'])){
                    return;
                }

    global $wp_meta_boxes;



        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}


function dt_iconSelector($default = false){
    $dir = WP_PLUGIN_DIR.'/db-toolkit/images/icons';
    $icons_dir = opendir($dir);
    ob_start();
    while (($icon = readdir($icons_dir)) !== false) {
        if($icon != '.' && $icon != '..') {
            $dt = pathinfo(WP_PLUGIN_DIR.'/db-toolkit/images/icons/'.$icon);

            if(strtolower($dt['extension']) == 'png'){
                $IconID = uniqid('icon_');
                $Sel = '';
                if($default == $dt['basename']){
                    $Sel = 'checked="checked"';
                }
                echo '<div style="padding:4px; float:left;">';
                echo '<label for="'.$IconID.'"><img src="'.WP_PLUGIN_URL.'/db-toolkit/images/icons/'.$dt['basename'].'" width="16" height="16" /></label>';
                echo '<input type="radio" name="selectedIcon" id="'.$IconID.'" value="'.WP_PLUGIN_URL.'/db-toolkit/images/icons/'.$dt['basename'].'" '.$Sel.' />';
                echo '</div>';
            }
        }
    }
    echo '<br class="clearfix">';
    return ob_get_clean();
}

function dt_appMarket(){
    include(DB_TOOLKIT.'marketplace.php');
}

// Hoook into the 'wp_dashboard_setup' action to register our function

function dbtoolkit_bugreport(){
    include(DB_TOOLKIT.'bugreport.php');
}

function dt_saveFilterLock($Interface, $Settings = false){

    if(!empty($Settings)){
        $Title = $Settings[0]['value'];
        $Count = 'no';
        if(!empty($Settings[1]['value'])){
            $Count = 'yes';
        }
        $Newset = get_option('filter_Lock_'.$Interface);
        $fset = get_option('dt_set_'.$Interface);
        if(!empty($Title)){
            $set['Title'] = $Title;
            $set['code'] = uniqid();
            $set['ShowCount'] = $Count;
            $set['Filters'] = $Newset;
            $fset[] = $set;
            update_option('dt_set_'.$Interface, $fset);
            return true;
        }
        ob_start();
        ?>
            <div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all">
                <p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>
                <strong>Alert:</strong> You need to provide a <strong>Set Name</strong>.</p>
            </div>
        <?php
        $error = ob_get_clean();
    }

    ob_start();
    if(!empty($error)){
        echo $error;
    }
    echo dais_customfield('text', 'Set Title', '_SetTitle', '_SetTitle', 'list_row1' , '' , false);
    echo dais_customfield('checkbox', 'Show Count', '_ShowCount', '_ShowCount', 'list_row2' , '1' , false);
    $Out = ob_get_clean();
    return $Out;

}

function core_loadSupportFeed($url){

    include_once(ABSPATH . WPINC . '/feed.php');

    // Get a SimplePie feed object from the specified feed source.
    $rss = fetch_feed($url);
    if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly
        // Figure out how many total items there are, but limit it to 5.
        $maxitems = $rss->get_item_quantity(5);

        // Build an array of all the items, starting with element 0 (first element).
        $rss_items = $rss->get_items(0, $maxitems);
    endif;
    ?>

    <ul>
        <?php if ($maxitems == 0) echo '<li>No items.</li>';
        else
        // Loop through each feed item and display each item as a hyperlink.
        foreach ( $rss_items as $item ) : ?>
        <li>
            <a class="rsswidget" href='<?php echo $item->get_permalink(); ?>'
            title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
            <?php echo $item->get_title(); ?></a><span class="rss-date"><?php echo $item->get_date('j F Y | g:i a'); ?></span><br />
            <?php echo str_replace('<hr />' , '<br />', $item->get_description()); ?>
        </li>
        <?php endforeach; ?>
    </ul>
<?php
}

/// System Utilities

function core_cleanSystemTables(&$value, $key){
    global $wpdb;
    $value = str_replace($wpdb->prefix, '{{wp_prefix}}', $value);
}

function core_applySystemTables(&$value, $key){
    global $wpdb;
    $value = str_replace('{{wp_prefix}}',$wpdb->prefix, $value);
}

/// App Exporter


function exportApp($app){
    global $wpdb;
    $Len = strlen($app);
    $appString = 's:12:"_Application";s:'.$Len.':"'.$app.'"';
    $interfaces = $wpdb->get_results( "SELECT option_name, option_value FROM ".$wpdb->options." WHERE `option_value` LIKE '%".$appString ."%'");

    $export = array();
    $export['application'] = $app;
    if(!empty($interfaces)){

        $name = uniqid('intfc');
        //$file = fopen(__DIR__.'/libs/cache/'.$name.'.itf', 'w+');
        $tables = array();
        foreach($interfaces as $interface){

            $cfg = unserialize($interface->option_value);
            $cfg = unserialize(base64_decode($cfg['Content']));
            if(!empty($cfg['_Linkedfields'])){
                foreach($cfg['_Linkedfields'] as $Field=>$Value){
                    if(empty($tables[$Value['Table']])){
                        $tables[$Value['Table']] = $Value['Table'];
                    }
                }
            }
            if(!empty($cfg['_Linkedfilterfields'])){
                foreach($cfg['_Linkedfilterfields'] as $Field=>$Value){
                    if(empty($tables[$Value['Table']])){
                        $tables[$Value['Table']] = $Value['Table'];
                    }
                }
            }
            array_walk_recursive($cfg, 'core_cleanSystemTables');

            if($wpdb->get_var("SHOW TABLES LIKE '".$cfg['_main_table']."'") != $table_name){
                $tables[$cfg['_main_table']] = $cfg['_main_table'];
            }
            //TODO: try get it to rename tables with prefixes using $wpdb->prefix;
            $export['interfaces'][$interface->option_name] = base64_encode($interface->option_value);
        }
    }
        if(!empty($tables)){

            foreach($tables as $table){
                $tableCreates = $wpdb->get_row("SHOW CREATE TABLE ".$table, ARRAY_N);
                $export['tables'][$tableCreates[0]] = base64_encode($tableCreates[1]);

                //echo $tableCreates[1];

                //export data
                $result = $wpdb->get_results("SELECT * FROM ".$table, ARRAY_A);
                foreach($result as $entries){
                    $Fields = array();
                    $Values = array();
                    foreach ($entries as $field=>$value){
                        $Fields[] = '`'.$field.'`';
                        $Values[] = "'".mysql_real_escape_string($value)."'";
                    }
                    $export['entries'][$tableCreates[0]][] = base64_encode("INSERT INTO `".$tableCreates[0]."` (".implode(',', $Fields).") VALUES (".implode(',', $Values).");");
                }
            }
        }

        //fwrite($file, gzdeflate(base64_encode(serialize($export)),9));
        //fclose($file);
        $fileName = preg_replace('/[^\w\-]+/u', '-', $app);

        $output = gzdeflate(base64_encode(serialize($export)),9);
        header ("Expires: Mon, 21 Nov 1997 05:00:00 GMT");    // Date in the past
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
        header ("Pragma: no-cache");                          // HTTP/1.0
        header('Content-type: application/itf');
        header('Content-Disposition: attachment; filename="'.$fileName.'.itf"');
        print($output);
        die;
}

function core_createInterfaces($Installer){

    $apps = get_option('dt_int_Apps');
    $data = file_get_contents($Installer);
    $data = gzinflate($data);
    $data = unserialize(base64_decode($data));
    if(empty($apps[$data['application']])){
        $apps[$data['application']] = 'open';
        update_option('dt_int_Apps', $apps);
    }
    if(!empty($data['interfaces'])){
        foreach($data['interfaces'] as $interface=>$configData){
            $Config = unserialize(base64_decode($configData));
            array_walk_recursive($Config, 'core_applySystemTables');
            update_option($interface, $Config);
        }
        return true;
    }else{
        unlink($Installer);
        unset($_SESSION['appInstall']);
        return false;
    }
    unlink($Installer);
    unset($_SESSION['appInstall']);
    return false;
    //vardump($data);
}

function core_createTables($Installer){

    global $wpdb;
    $apps = get_option('dt_int_Apps');
    $data = file_get_contents($Installer);
    $data = gzinflate($data);
    $data = unserialize(base64_decode($data));

    if(!empty($data['tables'])){
        foreach($data['tables'] as $table=>$configData){
            $Query = base64_decode($configData);
            $wpdb->query($Query);
        }
        return true;
    }else{
        return true;
    }
    unlink($Installer);
    unset($_SESSION['appInstall']);
    return false;
    //vardump($data);
}

function core_populateApp($Installer){

    global $wpdb;
    $apps = get_option('dt_int_Apps');
    $data = file_get_contents($Installer);
    $data = gzinflate($data);
    $data = unserialize(base64_decode($data));

    if(!empty($data['entries'])){
        foreach($data['entries'] as $table=>$entries){
            foreach($entries as $entry){
                $Query = base64_decode($entry);
                $wpdb->query($Query);
            }
        }
        unlink($Installer);
        unset($_SESSION['appInstall']);
        return true;
    }else{
        unlink($Installer);
        unset($_SESSION['appInstall']);
        return true;
    }
    unlink($Installer);
    unset($_SESSION['appInstall']);
    return false;
}


?>
