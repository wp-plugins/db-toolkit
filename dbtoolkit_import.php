<?php
if(!empty($_SESSION['appInstall'])){
    if(!file_exists($_SESSION['appInstall'])){
        unset($_SESSION['appInstall']);
    }
}
if(!empty($_FILES['itfInstaller']['size'])){
    $overrides = array('action'=>'itf_install', 'mimes'=> array('itf' => 'application/itf', 'dbt' => 'application/dbt'));
    $_POST['action'] = 'itf_install';
    $upload = wp_handle_upload($_FILES['itfInstaller'], $overrides);
    $_SESSION['appInstall'] = $upload['file'];
}


?>


<div class="wrap ">
    <img src="<?php echo WP_PLUGIN_URL . '/db-toolkit/images/dbtoolkit-logo.png'; ?>" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" />Application Installer
   
    <br class="clear" /><br />
    <div id="poststuff">
        <?php
        if(!empty($_SESSION['appInstall'])){
            
            $pth = pathinfo($_SESSION['appInstall']);
            
            //die;

            $data = file_get_contents($_SESSION['appInstall']);
            if($predata = @gzinflate($data)){
                $data = $predata;
            }

            $data = unserialize(base64_decode($data));

            if($pth['extension'] == 'itf'){

                $exisits = get_option('_'.sanitize_title($data['appInfo']['name']).'_app');
                if(!empty($ewxisits)){
                    echo '<p><strong>ERROR: App is already installed.</strong></p>';
                    echo '<p id="returnLink" style="display:block;"><a href="'.$_SERVER['REQUEST_URI'].'">Back to installer</p>';
                    unset($_SESSION['appInstall']);
                    return;
                }

                if(!empty($data['appInfo'])){
                    $Ext = pathinfo(basename($data['appInfo']['imageFile']));
                    if(!empty($data['logo'])){
                        $filename = sanitize_file_name($data['appInfo']['name'].'.'.$Ext['extension']);
                        $src = wp_upload_bits($filename, null, base64_decode($data['logo']));
                        $data['appInfo']['imageURL'] = $src['url'];
                        $data['appInfo']['imageFile'] = $src['file'];
                    }
                    update_option('_'.sanitize_title($data['appInfo']['name']).'_app', $data['appInfo']);

                }


                if(!empty($data['application'])){
                echo '<p>Installing Application: <strong>'.$data['application'].'</strong></p>';
                echo '<p id="createingInterfaces">Creating Interfaces...</p>';
                if(!empty($data['tables'])){
                    echo '<p id="buildingTables">Building Tables...</p>';
                }
                echo '<p id="populatingApp">Populating App...</p>';
                echo '<p id="installStatus"></p>';
                echo '<p id="returnLink" style="display:none;"><a href="'.$_SERVER['REQUEST_URI'].'">Back to installer</p>';
                $_SESSION['dataform']['OutScripts'] .= "


                    ajaxCall('core_createInterfaces', '".$_SESSION['appInstall']."', function(P){
                        if(P){
                            jQuery('#createingInterfaces').html('Creating Interfaces...<strong>COMPLETE</strong>');
                            ajaxCall('core_createTables', '".$_SESSION['appInstall']."', function(T){
                                if(T){
                                    jQuery('#buildingTables').html('Building Tables...<strong>COMPLETE</strong>');
                                    ajaxCall('core_populateApp', '".$_SESSION['appInstall']."', function(E){
                                        if(E){
                                            jQuery('#populatingApp').html('Populating App...<strong>COMPLETE</strong>');
                                            jQuery('#installStatus').html('<strong>INSTALLATION COMPLETE</strong>');
                                            jQuery('#returnLink').show('slow');

                                        }
                                    });

                                }
                            });
                        }else{
                            jQuery('#createingInterfaces').html('Creating Interfaces...<strong>ERROR: Could not create interfaces.</strong>');
                        }
                    });


                ";
                }else{
                    unset($_SESSION['appInstall']);
                    echo '<p><strong>Invalid File.</strong></p>';
                    echo '<p id="returnLink" style="display:none;"><a href="'.$_SERVER['REQUEST_URI'].'">Back to installer</p>';
                    $_SESSION['dataform']['OutScripts'] .= "
                    jQuery('#returnLink').show('slow');
                    ";
                }
            }
            // New DBT format installation
            if($pth['extension'] == 'dbt'){
                echo '<h2>Installation Results</h2>';
                // Extract Main App Definition and Settings
                $data['MainApp'] = unserialize($data['MainApp']);
                $data['AppSettings'] = unserialize($data['AppSettings']);

                // Get App Key
                $appKey = sanitize_title($data['MainApp']['name']);
                // Check if its installed
                if(get_option('_'.$appKey.'_app')){
                    unset($_SESSION['appInstall']);
                    echo '<strong>'.$data['MainApp']['name'].'</strong> is already Installed';
                    echo '<p id="returnLink" style="display:block;"><a href="'.$_SERVER['REQUEST_URI'].'">Back to installer</p>';
                    echo '</div></div>';
                    exit;
                }
                // Register App
                $allApps = get_option('dt_int_Apps');
                $allApps[$appKey] = $data['MainApp'];
                update_option('dt_int_Apps', $allApps);
                unset($data['MainApp']);

                // Create Interfaces
                foreach($data['Interfaces'] as $InterfaceID=>$cfg){
                    $cfg = unserialize($cfg);
                    $cfg['Content'] = unserialize(base64_decode($cfg['Content']));
                    // Apply System Prefix
                    array_walk_recursive($cfg['Content'], 'core_applySystemTables');
                    //Save config
                    $cfg['Content'] = base64_encode(serialize($cfg['Content']));
                    update_option($InterfaceID, $cfg);
                }
                unset($data['Interfaces']);

                // Create Filter Locks if any
                if(!empty($data['FilterLocks'])){
                    foreach($data['FilterLocks'] as $InterfaceLock=>$cfg){
                        $cfg = unserialize($cfg);
                        update_option($InterfaceLock, $cfg);
                    }
                    unset($data['FilterLocks']);
                }

                // Create Clusters
                foreach($data['Clusters'] as $ClusterID=>$cfg){
                    $cfg = unserialize($cfg);                    
                    update_option($ClusterID, $cfg);                    
                }
                unset($data['Clusters']);

                // Upload Logo

                $newFileName = uniqid('dbtlgo').'.png';
                $logoFile = wp_upload_bits($newFileName, null, base64_decode($data['Logo']));
                if(!empty($logoFile)){
                    $data['AppSettings']['imageURL'] = $logoFile['url'];
                    $data['AppSettings']['imageFile'] = $logoFile['file'];
                }
                unset($data['Logo']);

                // Create Tables
                global $wpdb;
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                foreach($data['Tables'] as $tableKey=>$tableStruct){
                    
                    // decode table settings
                    $query = str_replace('{{wp_prefix}}', $wpdb->prefix, base64_decode($tableStruct));
                    dbDelta($query);
                }
                unset($data['Tables']);


                // Create Default Data
                foreach($data['Data'] as $Row){
                    $query = str_replace('{{wp_prefix}}', $wpdb->prefix, base64_decode($Row));
                    dbDelta($query);
                }
                unset($data['Data']);


                // Create Main App Settings                
                update_option('_'.$appKey.'_app', $data['AppSettings']);
                unset($data['AppSettings']);

                // Clear Session and end off.
                unset($_SESSION['appInstall']);
                echo '<p><strong>Installation Complete.</strong></p>';
                echo '<p id="returnLink" style="display:block;"><a href="'.$_SERVER['REQUEST_URI'].'">Back to installer</p>';
                

            }

        }else{
        ?>
        <form name="importApplication" enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h4>Install a DB-Toolkit Application in .itf or .dbt format</h4>
        <p class="install-help">If you have an application in .itf or .dbt format, you may install it by uploading it here.</p>
	<input type="file" name="itfInstaller">
	<input type="submit" value="Install Now" class="button">
        </form>
        <?php
        }
        ?>
    </div>
</div>