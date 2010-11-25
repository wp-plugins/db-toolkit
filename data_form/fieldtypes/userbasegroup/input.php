<?php
/// This creates the actual input fields for capturing.
// output is echoed not returned.

/* 

 Available Variables:
 
 $Config - Element Config
 $FieldSet - array(0=>fieldfolder, 1=>fieldtype);
 $Defaults - array with all fields and all data for each field
 $Field - the current field name
 $Element - Element data
 $Req - the form validation class, sets the field to required if configured to do so
 
 field names are : name="dataForm['.$Element['ID'].']['.$Field.']"
 fieled ID's are : id="entry_'.$Element['ID'].'_'.$Field.'"
 
*/

    global $wpdb;
    $accessGroups = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'group_%' ", ARRAY_A);

    $Out = '<select name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'">';
    $Out .= '<option value="">Select a Group</option>';
    $Out .= '<option value="_public">Public</option>';
        foreach($accessGroups as $Group){
            $Det = get_option($Group['option_name']);
            //vardump($Det);
            $Sel = '';
		$Sel = '';
		if($Group['option_name'] == $Val){
			$Sel = 'selected="selected"';
		}
           $Out .= '<option value="'.$Group['option_name'].'" '.$Sel.'>'.$Det['name'].'</option>';
        }

    $Out .= '</select>';

    echo $Out;
?>