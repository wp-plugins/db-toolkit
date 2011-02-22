<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if(!empty($_GET['edit'])){
    echo '<div class="wrap">';
    echo '<div class="icon32" id="icon-users"><br></div>';
    echo '<h2>Edit Group</h2>';
    ?>
<form name="editInterfaceForm" method="post" action="<?php echo str_replace('&edit='.$_GET['edit'], '', $_SERVER['REQUEST_URI']); ?>">
    <?php
    $data = unserialize(get_option($_GET['edit']));

        echo '<div class="tablenav">New Access Group: <input type="text" name="editGroup[name]" value="'.$data['name'].'" id="new-access-group" /> Description: <input type="text" name="editGroup[desc]" value="'.$data['desc'].'" id="edit-access-group" /><input type="hidden" name="editGroup[id]" value="'.$_GET['edit'].'" /> <input type="submit" value="Save" class="button" /> |  <input type="submit" name="editGroup[delete]" value="Delete" class="button" onclick="return confirm(\'Are you sure you want to delete this group?\');" /></div>';
        if(!empty($_POST['group'])){
            echo add_option(uniqid('group_'), serialize($_POST['group']));
        }
    ?>
    </form>

    </div>
    <?php
    return;
}
?>


<?php
if($_GET['page']=='access_control'){
    echo '<div class="wrap">';
    echo '<div class="icon32" id="icon-users"><br></div>';
    echo '<h2>Userbase Access Control</h2>';
?>
<form name="newInterfaceForm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<?php
    echo '<div class="tablenav">New Access Group: <input type="text" name="group[name]" value="" id="new-access-group" /> Description: <input type="text" name="group[desc]" value="" id="new-access-group" /> <input type="submit" value="Create" class="button" /></div>';
    if(!empty($_POST['group'])){
        add_option(uniqid('group_'), serialize($_POST['group']));
    }
   
    if(!empty($_POST['editGroup'])){
        if(empty($_POST['editGroup']['delete'])){
            update_option($_POST['editGroup']['id'], serialize($_POST['editGroup']));
        }else{
            delete_option($_POST['editGroup']['id']);
        }
    }
?>
</form>

<table cellspacing="0" class="widefat fixed">
    <thead>
	<tr>
            <th scope="col" id="group-name" class="manage-column column-group-name" style="" width="30%">Group Name</th>
            <th scope="col" id="group-desc" class="manage-column column-group-desc" style="">Description</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th scope="col" id="group-name" class="manage-column column-group-name" style="">Group Name</th>
            <th scope="col" id="group-desc" class="manage-column column-group-desc" style="">Description</th>
        </tr>
    </tfoot>
    <tbody class="list:group" id="group-list">
        <tr class="iedit alternate" id="cat-1">
            <td class="name column-name"><strong>Public</strong></td>
            <td class="name column-desc">Default Public Group. No login or membership required.</td>
        </tr>
<?php
    global $wpdb;

    $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);
    
    if(!empty($accessGroups)){
        foreach($accessGroups as $group){
            $groupData = get_option($group['option_name']);
            if(!is_array($groupData)){
                $groupData = unserialize($groupData);
            }
            echo '<tr>';
                echo '<td class="name column-group-name"><strong>'.$groupData['name'].'</strong><div class="row-actions"><span class="edit"><a href="'.$_SERVER['REQUEST_URI'].'&edit='.$group['option_name'].'">Edit</a></div></td>';
                echo '<td class="name column-group-desc">'.$groupData['desc'].'</td>';
            echo '</tr>';

        }
    }
?>
    </tbody>
</table>
<?php
    echo '</div>';
}
?>