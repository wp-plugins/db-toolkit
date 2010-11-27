<?php
/*
Plugin Name: Database Interface Toolkit
Plugin URI: http://dbtoolkit.digilab.co.za
Description: Plugin for creating interfaces from database tables
Author: David Cramer
Version: 0.2.0.3
Author URI: http://www.digilab.co.za
*/

//init



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
}
register_activation_hook( __FILE__, 'interface_VersionCheck' );

function dbtoolkit_activate_run() {




    $defaults = unserialize('a:29:{s:12:"_chartHeight";s:3:"250";s:15:"_New_Item_Title";s:9:"Add Entry";s:15:"_Items_Per_Page";s:2:"20";s:12:"_autoPolling";s:0:"";s:13:"_Show_Filters";s:1:"1";s:15:"_toggle_Filters";s:1:"1";s:20:"_Show_KeywordFilters";s:1:"1";s:14:"_Keyword_Title";s:6:"Search";s:11:"_showReload";s:1:"1";s:12:"_Show_Export";s:1:"1";s:13:"_Show_Plugins";s:1:"1";s:12:"_orientation";s:1:"P";s:12:"_Show_Select";s:1:"1";s:12:"_Show_Delete";s:1:"1";s:10:"_Show_Edit";s:1:"1";s:10:"_Show_View";s:1:"1";s:19:"_Show_Delete_action";s:1:"1";s:12:"_Show_Footer";s:1:"1";s:14:"_InsertSuccess";s:27:"Entry inserted successfully";s:14:"_UpdateSuccess";s:26:"Entry updated successfully";s:11:"_InsertFail";s:22:"Could not insert entry";s:11:"_UpdateFail";s:22:"Could not update entry";s:17:"_SubmitButtonText";s:6:"Submit";s:17:"_UpdateButtonText";s:6:"Submit";s:13:"_EditFormText";s:10:"Edit Entry";s:13:"_ViewFormText";s:10:"View Entry";s:14:"_NoResultsText";s:13:"Nothing Found";s:10:"_ShowReset";s:1:"1";s:16:"_SubmitAlignment";s:4:"left";}');
    update_option('_dbtoolkit_defaultinterface', $defaults, NULL, 'No');
    return;
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
    }
    
    include_once('libs/lib.php');
    include_once('daiselements.class.php');
    include_once('data_form/class.php');
    include_once('data_report/class.php');
    include_once('data_itemview/class.php');

    if($_GET['activate'] == true){
        dbtoolkit_activate_run();
    }
    
    // dumplicate interface hack
    if(is_admin()) {
        if(!empty($_GET['duplicateinterface'])) {
            $dupvar = get_option($_GET['duplicateinterface']);
            $oldOption = $dupvar;

            $NewName = uniqid($oldOption['_interfaceName'].' ');
            $oldOption['_interfaceName'] = $NewName;
            $newTitle = uniqid('dt_intfc');
            $oldOption['ID'] = $newTitle;
            $oldOption['ParentDocument'] = $newTitle;
            //vardump($oldOption);
            add_option($newTitle, $oldOption, NULL, 'No');
            header( 'Location: '.$_SERVER['HTTP_REFERER']);
            die;
        }
    }


    // Ajax processing
    dt_process();
}

//Header
function dt_headers() {

    include_once('data_form/headers.php');
    include_once('data_report/headers.php');

    ?>
<script type="text/javascript" >
    <?php
    if(!is_admin()) {
        echo 'var ajaxurl = \'./\';';
    }else {
        ?>

            function dt_deleteInterface(interfaceID){

                if(confirm('Are you sure you want to delete this interface?')){

                    ajaxCall('dt_removeInterface', interfaceID, function(x){
                        if(x == true){
                            jQuery('#'+interfaceID).fadeOut('slow', function(){
                                jQuery(this).remove();
                                window.location = '<?php echo $_SERVER['REQUEST_URI']; ?>';
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
            jQuery.post(ajaxurl,vars, function(data){
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
        //vardump($post);
        $pattern = get_shortcode_regex();

        preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);
        //vardump($matches);
        if (in_array('interface', $matches[2])) {
            foreach($matches[3] as $preInterface){
                $preIs[] = substr(trim($preInterface, '"'), 5);
            }
        }
    }


    //<link type="text/css" rel="stylesheet" href="" />
    wp_register_style('jqueryUI-base-internal', WP_PLUGIN_URL . '/db-toolkit/jqueryui/jquery-ui.css');
    //wp_register_style('jqueryUI-base', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/themes/smoothness/jquery-ui.css');
    //wp_register_style('jqueryUI-base-custom', WP_PLUGIN_URL . '/db-toolkit/jqueryui/custom-theme/jquery-ui-1.7.3.custom.css');
    wp_register_style('jquery-multiselect', WP_PLUGIN_URL . '/db-toolkit/libs/ui.dropdownchecklist.css');
    wp_register_style('jquery-validate', WP_PLUGIN_URL . '/db-toolkit/libs/validationEngine.jquery.css');
    wp_enqueue_style('jqueryUI-base-internal');
    //wp_enqueue_style('jqueryUI-base');
    //wp_enqueue_style('jqueryUI-base-custom');
    wp_enqueue_style('jquery-multiselect');
    wp_enqueue_style('jquery-validate');



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
        if(!empty($preIs)){
            foreach($preIs as $interface){
               $preInterface = get_option($interface);
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
    }


    
}

//Scripts
function dt_scripts() {

    if(!is_admin()){
        global $post;
        //vardump($post);
        $pattern = get_shortcode_regex();

        preg_match_all('/'.$pattern.'/s', $post->post_content, $matches);
        //vardump($matches);
        if (in_array('interface', $matches[2])) {
            foreach($matches[3] as $preInterface){
                $preIs[] = substr(trim($preInterface, '"'), 5);
                
            }
        }
    }

    // queue & register scripts
    wp_register_script('data_report', WP_PLUGIN_URL . '/db-toolkit/data_form/javascript.php', false, false, true);
    wp_register_script('data_form', WP_PLUGIN_URL . '/db-toolkit/data_report/javascript.php', false, false, true);
    wp_register_script('jquery-ui-datepicker' , WP_PLUGIN_URL . '/db-toolkit/libs/ui.datepicker.js');
    wp_register_script('jquery-multiselect', WP_PLUGIN_URL . '/db-toolkit/libs/ui.dropdownchecklist-min.js', false, false, true);
    wp_register_script('jquery-validate', WP_PLUGIN_URL . '/db-toolkit/libs/jquery.validationEngine.js');
    wp_register_script('highcharts', WP_PLUGIN_URL . '/db-toolkit/data_report/js/highcharts.js');


    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-tabs");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script("jquery-ui-draggable");
    wp_enqueue_script("jquery-ui-droppable");
    wp_enqueue_script("jquery-ui-dialog");
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_script('jquery-multiselect');
    wp_enqueue_script('data_report');
    wp_enqueue_script('data_form');
    wp_enqueue_script('jquery-validate');
    wp_enqueue_script('swfobject');

    wp_enqueue_script('highcharts');

        $Types = loadFolderContents(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes');
	foreach($Types[0] as $Type){
		if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/javascript.php')){
                    wp_register_script('fieldType_'.$Type[1], WP_PLUGIN_URL.'/db-toolkit/data_form/fieldtypes/'.$Type[1].'/javascript.php', false, false, true);
                        wp_enqueue_script('fieldType_'.$Type[1]);
                }
	}

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
            foreach($preIs as $interface){
               $preInterface = get_option($interface);
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


        $adminPage = add_menu_page("Database Toolkit", "DB Toolkit", 'activate_plugins', "Database_Toolkit", "dbtoolkit_admin", WP_PLUGIN_URL.'/db-toolkit/data_report/cog.png');
	add_submenu_page("Database_Toolkit", 'Manage Interfaces', 'Interfaces', 'activate_plugins', "Database_Toolkit", 'dbtoolkit_admin');

        $addNew = add_submenu_page("Database_Toolkit", 'Add New Interface', 'Add New', 'activate_plugins', "Add_New", 'dbtoolkit_admin');
        $setup = add_submenu_page("Database_Toolkit", 'General Settings', 'General Settings', 'activate_plugins', "dbtools_setup", 'dbtoolkit_setup');
        //$setup = add_submenu_page("Database_Toolkit", 'Bug Report', 'Bug Report', 'activate_plugins', "dbtools_bugreport", 'dbtoolkit_bugreport');
        //$setup = add_submenu_page("Database_Toolkit", 'Documentation A', 'Documention B', 'activate_plugins', "dbtools_manual", 'dbtoolkit_manual');

            add_action('admin_print_styles-'.$adminPage, 'dt_styles');
            add_action('admin_head-'.$adminPage, 'dt_headers');
            add_action('admin_print_scripts-'.$adminPage, 'dt_scripts');
            add_action('admin_footer-'.$adminPage, 'dt_footers');

            add_action('admin_print_styles-'.$addNew, 'dt_styles');
            add_action('admin_head-'.$addNew, 'dt_headers');
            add_action('admin_print_scripts-'.$addNew, 'dt_scripts');
            add_action('admin_footer-'.$addNew, 'dt_footers');

            add_action('admin_print_styles-'.$setup, 'dt_styles');
            add_action('admin_head-'.$setup, 'dt_headers');
            add_action('admin_print_scripts-'.$setup, 'dt_scripts');
            add_action('admin_footer-'.$setup, 'dt_footers');

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

//Footers
function dt_footers() {
    include_once('footers.php');
    include_once('data_report/footers.php');
    include_once('data_form/footers.php');
}



// Ajax System
function dt_ajaxCall() {
    $ref = parse_url(basename($_SERVER['HTTP_REFERER']));

    global $wpdb;

    include_once('libs/lib.php');
    include_once('daiselements.class.php');
    include_once('data_form/class.php');
    include_once('data_report/class.php');
    include_once('data_itemview/class.php');

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

function dbtoolkit_admin() {
    global $user_ID;
    include_once('dbtoolkit_admin.php');
}

function dbtoolkit_setup() {
    global $user_ID;
    include_once('dbtoolkit_settings.php');
}
function dbtoolkit_manual() {
    global $user_ID;
    include_once('manual/index.php');
}

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

// Assign Actions

add_action('init', 'dt_start');
add_action('admin_menu', 'dt_menus');
add_action('wp_ajax_dt_ajaxCall', 'dt_ajaxCall');



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

            include_once('daiselements.class.php');
            include_once('data_form/class.php');
            include_once('data_report/class.php');

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
            if(!empty($Setup['Content']['_ItemViewPage'])) {
                $Location = get_permalink($Setup['Content']['_ItemViewPage']);
            }else {
                $location = $_SERVER['HTTP_REFERER'];
            }
           
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
                       $gets['page'] = $Setup['Content']['_ItemViewInterface'];
                    }
                    $ReturnValue = htmlspecialchars_decode(http_build_query(array_merge($gets, $returngets)));
                }
                
                $Redirect = $Location.$returntoken.$ReturnValue;
            }

            header('Location: '.$Redirect);
            die;
        }
    }

    // API Call
    if(!empty($_GET['APIKey'])) {
        include_once('libs/api_engine.php');
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

    if(!empty($exportFormat)) {

        if($exportFormat == 'pdf') {

            include_once('daiselements.class.php');
            include_once('data_form/class.php');
            include_once('data_report/class.php');
            include_once('data_itemview/class.php');

            include_once('libs/fpdf.php');
            include_once('libs/pdfexport.php');
            $input_params["return"] = isset($input_params["return"]) ? $input_params["return"] : false;
            if(empty($Config['_orientation'])) {
                $Config['_orientation'] = 'P';
            }
            $report = new PDFReport($Config['_orientation'], $Config['_ReportTitle']);

            $report->cf_report_header(date("jS F Y"), 8);
            $report->cf_report_title($Config['_ReportTitle']);

            $limit = false;
            if(!empty($_GET['limit'])) {
                $limit = 'full';
            }
            $OutData = dr_BuildReportGrid($Media['ID'], false, $_SESSION['report_'.$Media['ID']]['SortField'], $_SESSION['report_'.$Media['ID']]['SortDir'], 'pdf', 'none' );
            //dump($OutData);
            //die;
            $CountStat = array();
            //dump($OutData['filters']);
            if(!empty($OutData['filters'])) {
                $sum = 'filtered: ';
                $report->cf_report_header('', 9);
                foreach($OutData['filters'] as $Field=>$Value) {
                    if(!empty($Config['_FieldTitle'][$Field])) {
                        $FieldTitle = $Config['_FieldTitle'][$Field];
                    }else {
                        $FieldTitle = $Field;
                    }
                    //if($Field
                    $fieldset = array();
                    $index=0;
                    foreach($Value as $fil) {
                        $fieldset[] = $fil;
                        $index++;
                    }
                    sort($fieldset);
                    if(strpos($Config['_Field'][$Field], 'date_') !== false) {
                        $filterData[$FieldTitle] = $fieldset[0].' to '.$fieldset[count($fieldset)-1];
                    }else {
                        $filterData[$FieldTitle] = implode(', ',$fieldset);
                    }
                }
                $report->cf_report_datagrid($filterData);
                //$report->cf_report_summary("");
                unset($OutData['filters']);
            }
            if(!empty($OutData['Totals'])) {
                //$report->cf_report_header('Totals:', 9);
                foreach($OutData['Totals'] as $Field=>$Value) {
                    sort($fieldset);
                    $totalData[$Field] = $Value;
                }
                $report->cf_report_datagrid($totalData);
                //$report->cf_report_summary("");
                unset($OutData['Totals']);
            }

            $report->cf_report_spacer();        

            // headers//
            if(!empty($OutData[0])){
            foreach($OutData[0] as $Header=>$v) {
                if(!empty($Config['_FieldTitle'][$Header])) {
                    $Headers[] = $Config['_FieldTitle'][$Header];
                }else {
                    $Headers[] = $Header;
                }
            }
            $Total = count($OutData)-1;
            // Data
            for($i = 0; $i<= $Total; $i++) {
                foreach($OutData[$i] as $v) {
                    $Body[$i][] = $v;
                }
            }

            $options["width"] = "100%";

            $report->cf_report_data_col_grid($Headers, $Body, $options, "");
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
                                                if(file_exists('system/dais/plugins/data_form/fieldtypes/'.$FieldType[0].'/functions.php')){
                                                    include_once('system/dais/plugins/data_form/fieldtypes/'.$FieldType[0].'/functions.php');
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


        if($exportFormat != 'pdf') {
            $Element = getelement($Media['ID']);
            $Config = $Element['Content'];
            if(!empty($Config['_Show_Plugins'])) {
                // to do : configure adding plugins to the tool bar
                if(file_exists(WP_PLUGIN_DIR.'/db-toolkit/data_report/plugins/'.$exportFormat.'/functions.php')) {
                    include_once(WP_PLUGIN_DIR.'/db-toolkit/data_report/plugins/'.$exportFormat.'/functions.php');
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

// Render interface from shortcode to front end and view
function dt_renderInterface($interface) {
    
    if(is_array($interface)) {
        $ID = $interface['id'];
    }else {
        $ID = $interface;
    }
    $Media = get_option($ID);
    if(empty($Media)) {
        return;
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




    ob_start();
    include('data_report/element.def.php');
        if(empty($Config['_HideFrame'])){
            //$InfoBox()
            InfoBox($Config['_ReportTitle']);
        }
    $Return .= ob_get_clean();


    // Load ToolBar
    if($Config['_ViewMode'] == 'list'){
        ob_start();
            include('data_report/toolbar.php');
        $Return .= ob_get_clean();
    }
    

    // Determine Mode
    if(empty($Config['_ViewMode'])){
        $Config['_ViewMode'] = 'list';
    }

    switch ($Config['_ViewMode']){
        case 'list':
            ob_start();
                include('data_report/listmode.php');
            $Return .= ob_get_clean();
            break;            break;
        case 'view':
            ob_start();
                include('data_report/viewmode.php');
            $Return .= ob_get_clean();
            break;
        case 'form':
            ob_start();
                include('data_report/formmode.php');
            $Return .= ob_get_clean();
            break;
        case 'search':
            ob_start();
                include('data_report/searchmode.php');
            $Return .= ob_get_clean();
            break;
    }

    if(empty($Config['_HideFrame'])){
        //$InfoBox()
        ob_start();
        EndInfoBox();
        $Return .= ob_get_clean();
    }

    return $Return;

}

// delete interface
function dt_removeInterface($Interface) {
    
    delete_option($Interface);
    delete_option('filter_Lock_'.$Interface);
    delete_option('dt_set_'.$Interface);
    return true;
}


// Enable shortcode
add_shortcode("interface", "dt_renderInterface");
add_shortcode("visibility", "dt_publicReg");
// enable shortcode in widgets
add_filter('widget_text', 'do_shortcode');

// Add actions to front end
add_action('wp_head', 'dt_headers');
add_action('wp_print_styles', 'dt_styles');
add_action('wp_print_scripts', 'dt_scripts');

add_action('wp_footer', 'dt_footers');
add_action('wp_dashboard_setup', 'dt_dashboard_widgets' );



add_action('wp_dashboard_setup', 'dt_remove_dashboard_widgets' );


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
    include('marketplace.php');
}

// Hoook into the 'wp_dashboard_setup' action to register our function

function dbtoolkit_bugreport(){
    include('bugreport.php');
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



?>