<?php

//. Implementation of UAC in dbtoolkit

    function uac_menu(){
        add_users_page("Userbase Access Control", "Access Control", 10, "access_control", "uac_admin");
    }
    function uac_admin(){
        include('access_control.php');
    }
    function uac_assign($post){
        global $wpdb;
        $defaults = array();
        $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);
        if(empty($accessGroups)){
           return;
        }
        
        if(!empty($post->ID)){
            $PostID = $post->ID;
            $defaults = get_post_meta($post->ID, '_accessControl');
            //print_r($defaults);
        }

        $isDefault = get_option('_groupAccess_isdefault');
        $isDenied = get_option('_groupAccess_isdenied');
        $sel = '';
        //echo $isDefault.'==='.$post->ID;
        if($isDefault === $PostID){
            $sel = 'checked="checked"';
        }

        echo '<div class="misc-pub-section"><input type="checkbox" id="usergroup_setLogin" name="usergroup_setLogin" value="true" '.$sel.' /> <label for="usergroup_setLogin"><strong>Set as Login Page</strong></label></div>';

        $sel = '';
        if($isDenied === $PostID){
            $sel = 'checked="checked"';
        }
        echo '<div class="misc-pub-section"><input type="checkbox" id="usergroup_setDenied" name="usergroup_setDenied" value="true" '.$sel.' /> <label for="usergroup_setDenied"><strong>Set as Denied Page</strong></label></div>';


        if(!empty($accessGroups)){
            foreach($accessGroups as $group){
                $groupData = get_option($group['option_name']);
                $groupData = unserialize($groupData);
                $sel = '';
                if(in_array($group['option_name'], $defaults)){
                    $sel = 'checked="checked"';
                }
                echo '<div id="visibility" class="misc-pub-section ">';
                echo '<input type="checkbox" id="'.$group['option_name'].'" name="usergroup_data[]" value="'.$group['option_name'].'" '.$sel.' /> <label for="'.$group['option_name'].'">'.$groupData['name'].'</label>';
                echo ' : <span class="description">'.$groupData['desc'].'</span>';
                echo '</div>';
                echo '<input type="hidden" name="usergroup_dataCheck" value="true" />';
            }
        }
    }
    function uac_metabox(){
        global $wpdb;
        $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);
        if(empty($accessGroups)){
           return;
        }

      add_meta_box( 'userbase-access-control', 'Access Control', 'uac_assign', 'page', 'side', 'high' );
    }
    function uac_checkAuth(){
        /*
         * TO DO:
         * 1: check if the user is logged in,
         *      - If not logged in, redirect to login page.
         *          >> need to add in a login panel so that i can login a user and redirect them to the page they where on.
         *      - if is logged in check authentication.
         *          - If is authorised, Allow Access
         *          - if not authorised, redirect to upgrade/access denied page
         */



        if(!is_admin()){
            global $post;
            global $current_user;
            global $wpdb;
            $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);
            if(empty($accessGroups)){
               return;
            }
            $isLogin = get_option('_groupAccess_isdefault');
            if($post->ID == $isLogin){
                return;
            }
            $isDenied = get_option('_groupAccess_isdenied');            
            if($post->ID == $isDenied){
                return;
            }
                get_currentuserinfo();
                $accessAllowed = 'login';
                $userperms = get_usermeta($current_user->id, '_accessControl');
                $perms = get_post_meta($post->ID, '_accessControl');


            if(!empty($perms)){
                foreach($perms as $access){
                    if(!empty($userperms)){
                        $accessAllowed = 'denied';
                        if(is_array($userperms)){
                            if(in_array($access, $userperms)){
                                $accessAllowed = 'yes';
                            }
                        }
                    }
                }
            }else{
                $accessAllowed = 'yes';
            }


            // redirect to relevant page
            // - if not logged in - login user
            // - if no access - send user to denied/upgrade page


            switch($accessAllowed){
                case 'yes':
                    return;
                    break;
                case 'login':
                    $url = get_permalink($isLogin);
                    if(empty($isLogin)){
                        $url = get_bloginfo('url').'/wp-admin';
                    }
                    header('Location:'.$url);
                    die;
                    break;
                case 'denied':
                    $url = get_permalink($isDenied);
                    if(empty($isDenied)){
                        $url = get_bloginfo('url').'/wp-admin';
                    }
                    header('Location:'.$url);
                    die;
                    break;
                default:
                    return;
            }

        }
    }
    function uac_savemeta($post_id){

      // verify this came from the our screen and with proper authorization,
      // because save_post can be triggered at other times
       //echo plugin_basename();
       if(empty($_POST['usergroup_dataCheck'])){
        return $post_id;
       }
      //  return $post_id;
      //}

       $isDefault = get_option('_groupAccess_isdefault');
       if($isDefault == $post_id){
           delete_option('_groupAccess_isdefault');
       }

       if(!empty($_POST['usergroup_setLogin'])){
            update_option('_groupAccess_isdefault', $post_id );
       }
       $isDenied = get_option('_groupAccess_isdenied');
       if($isDefault == $post_id){
           delete_option('_groupAccess_isdenied');
       }

       if(!empty($_POST['usergroup_setLogin'])){
            update_option('_groupAccess_isdefault', $post_id );
       }
       if(!empty($_POST['usergroup_setDenied'])){
            update_option('_groupAccess_isdenied', $post_id );
       }

      // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
      // to do anything
      if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
        return $post_id;
      }



      // Check permissions
      if ($_POST['post_type'] == 'page') {
        if ( !current_user_can( 'edit_page', $post_id ) )
          return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
          return $post_id;
      }

      //foreach()
      $oldMeta = get_post_meta($post_id, '_accessControl');
      if(!empty($oldMeta)){
          delete_post_meta($post_id, '_accessControl');
      }
      if(empty($_POST['usergroup_setLogin']) && empty($_POST['usergroup_setDenied'])){
          if(!empty($_POST['usergroup_data'])){
            foreach ($_POST['usergroup_data'] as $userGroup){
                add_post_meta($post_id, '_accessControl', $userGroup);
            }
          }
      }
      return;
    }

    function uac_saveuser($user_id){
	if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/user-edit.php') === false && strpos($_SERVER['REQUEST_URI'], '/wp-admin/profile.php') === false ||
			$_POST['action'] != 'update')
		return;

	$user_id = empty($_POST['user_id']) ? $_GET['user_id'] : $_POST['user_id'];

	if(!empty($_POST['accessGroup'])){
            update_usermeta( $user_id, '_accessControl', serialize($_POST['accessGroup']));
        }else{
            delete_usermeta($user_id,'_accessControl');
        }
       // die;
    }

    // menu
    add_action('admin_menu', 'uac_menu');
    // meta-box
    add_action('admin_menu', 'uac_metabox');

    add_action('wp', 'uac_checkAuth');
    // save data
    add_action('save_post', 'uac_savemeta');

    add_action('edit_user_profile', 'uac_saveuser');

    add_action('init','uac_saveuser');





    ///// user profile addon test

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) {
    global $current_user;
    get_currentuserinfo();
    $defaults = array();
    if($current_user->roles[0] == 'administrator'){

        $defaults = get_usermeta($user->id, '_accessControl');
        //print_r($defaults);

    global $wpdb;
    $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);

    if(!empty($accessGroups)){
    ?>
<h3>Membership Information</h3>
<table class="form-table">
    <tr>
        <th><label for="accessGroup">Access Groups</label></th>
        <?php
            echo '<td>';
            if(!empty($accessGroups)){
                foreach($accessGroups as $group){
                    $sel = '';
                    if(is_array($defaults)){
                        if(in_array($group['option_name'], $defaults)){
                            $sel = 'checked="checked"';
                        }
                    }
                    $groupData = get_option($group['option_name']);
                    $groupData = unserialize($groupData);

                        echo '<div style="padding:3px;"><input type="checkbox" name="accessGroup[]" id="groupname_'.$group['option_name'].'" value="'.$group['option_name'].'" class="checkbox" '.$sel.' /> <strong><label for="groupname_'.$group['option_name'].'">'.$groupData['name'].'</label></strong> :';
                        echo '<span class="description">'.$groupData['desc'].'</span></div>';

                }
            }
            echo '</td>';
        ?>
    </tr>
</table>
<?php
    }
    }
}

?>