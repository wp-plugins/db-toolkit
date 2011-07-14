<?php
/*
 * app launcher! ye baby
 */
global $wpdb;
$user = wp_get_current_user();

$app= get_option('_'.sanitize_title($_SESSION['activeApp']).'_app');

$Len = strlen($app['name']);
$appString = 's:12:"_Application";s:'.$Len.':"'.$app['name'].'"';

$itnf = $wpdb->get_results( "SELECT option_name FROM ".$wpdb->options." WHERE `option_value` LIKE '%".$appString ."%'", ARRAY_A);

foreach($itnf as $interface){
    $cfg = get_option($interface['option_name']);
    if($cfg['_menuAccess'] == 'null'){
        $cfg['_menuAccess'] = 'read';
    }
    if(!empty($user->allcaps[$cfg['_menuAccess']])){
        if(!empty($cfg['_ItemGroup'])){
            $menus[$cfg['_ItemGroup']][$cfg['ID']] = $cfg['_interfaceName'];
        }else{
            $menus[$cfg['_ReportDescription']] = $cfg['ID'];
        }
    }
}
?>
<h2 id="appTitle"><?php echo  $app['name']; ?></h2>
<?php
    if(!empty($menus)){
        ksort($menus);
        echo '<div class="appnav_toolbar">';
            echo '<ul class="tools_widgets">';
                foreach($menus as $menu=>$group){
                    if(is_array($group)){
                        echo '<li class="root_item"><a class="parent hasSubs">'.$menu.'</a>';
                            echo '<ul id="'.sanitize_title($menu).'" style="visibility: hidden; display: block;">';
                            foreach($group as $interface=>$label){
                                echo '<li><a href="admin.php?page=app_launcher&renderinterface='.$interface.'">'.$label.'</a></li>';
                            }
                            echo '</ul>';
                        echo '</li>';
                    }else{
                        echo '<li class="root_item"><a href="admin.php?page=app_launcher&renderinterface='.$group.'" class="parent">'.$menu.'</a></li>';
                    }
                }                
            echo '</ul>';
            echo '<div style="clear:both;"></div>';
        echo '</div>';
    }
    if(!empty($areas)){
        echo '<div class="subsubsub">';
        echo implode(' | ',$areas);
        echo '</div>';
        echo '<div style="clear:both;"></div>';
    }
    /*
?>
        <div class="appnav_toolbar">
            <ul class="tools_widgets">
                <li class="root_item"><a class="parent hasSubs">Processors</a>
                    <ul id="" style="visibility: hidden; display: block;">

                        <li><a>A Link</a></li>
                        <li><a class="child HasSubs">A Link</a>
                            <ul id="" style="visibility: hidden; display: block;">
                                <li class="title"><a>A nother sub link</a></li>
                            </ul>
                        </li>
                        <li><a>A Link</a></li>
                        <li><a>A Link</a></li>
                        <li><a>A Link</a></li>

                    </ul>
                </li>
                <li class="root_item"><a class="parent">Processors</a></li>
            </ul>
            <div style="clear:both;"></div>
        </div>

<?php
     * 
     */
if(!empty($_GET['renderinterface'])){
    $noedit = true;
    include DB_TOOLKIT.'dbtoolkit_admin.php';
}
?>


<?php
$_SESSION['dataform']['OutScripts'] .= "

    // activate menus
    jQuery('.tools_widgets ul').css({
        display: \"none\"
    });
    jQuery('.tools_widgets li').hover(function(){
        jQuery(this).find('ul:first').css({
            visibility: \"visible\",
            display: \"none\"
        }).fadeIn(250);
    },function(){
        jQuery(this).find('ul:first').css({
            visibility: \"hidden\"
        });
    });
";
?>