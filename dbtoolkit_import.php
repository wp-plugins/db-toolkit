<?php
if(!empty($_SESSION['appInstall'])){
    if(!file_exists($_SESSION['appInstall'])){
        unset($_SESSION['appInstall']);
    }
}
if(!empty($_FILES['itfInstaller']['size'])){
    $overrides = array('action'=>'itf_install', 'mimes'=> array('itf' => 'application/itf'));
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

            $data = file_get_contents($_SESSION['appInstall']);
            if($predata = @gzinflate($data)){
                $data = $predata;
            }

            $data = unserialize(base64_decode($data));

            //vardump($data);
            //die;

            $exisits = get_option('_'.sanitize_title($data['appInfo']['name']).'_app');
            if(!empty($exisits)){                
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
        }else{
        ?>
        <form name="importApplication" enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h4>Install a DB-Toolkit Application in .itf format</h4>
        <p class="install-help">If you have an application in a .itf format, you may install it by uploading it here.</p>
	<input type="file" name="itfInstaller">
	<input type="submit" value="Install Now" class="button">
        </form>
        <?php
        }
        ?>
    </div>
</div>