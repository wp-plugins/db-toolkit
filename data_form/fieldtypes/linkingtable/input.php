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
global $wpdb; // not sure if i need this as i dont use wordpresses database thing.


$Name = 'dataForm['.$Element['ID'].']['.$Field.'][]';
$linkQuery = "SELECT * FROM `".$Config['_Linkingtablefields'][$Field]['DestinationTable']."` ORDER BY `".$Config['_Linkingtablefields'][$Field]['DestValue']."` ASC;";
$defaultLinkQuery = "SELECT * FROM `".$Config['_Linkingtablefields'][$Field]['LinkingTable']."` WHERE `".$Config['_Linkingtablefields'][$Field]['LinkID']."` = '".$Defaults[$Config['_CloneField'][$Field]['Master']]."' ";
//vardump($Config['_Linkingtablefields'][$Field]);
//echo $linkQuery;
//die;
$linkRes = mysql_query($linkQuery);
$defaultRes = mysql_query($defaultLinkQuery);
$LinkDefaultsList = array();
if(mysql_num_rows($defaultRes) >= 1){
    while($linkDefaults = mysql_fetch_assoc($defaultRes)){
       //vardump($linkDefaults);
       //vardump($Config['_Linkingtablefields']);
        $LinkDefaultsList[$linkDefaults[$Config['_Linkingtablefields'][$Field]['LinkDestID']]] = true;
    }
  
}
//vardump($LinkDefaultsList);
while($linkData = mysql_fetch_array($linkRes)){
    $Sel = '';
    if(!empty($LinkDefaultsList[$linkData[$Config['_Linkingtablefields'][$Field]['DestID']]])){
        $Sel = 'checked="checked"';
    }
    echo '<div>';
    echo '<input type="checkbox" value="'.$linkData[$Config['_Linkingtablefields'][$Field]['DestID']].'" name="'.$Name.'" id="'.$Field.'_'.$Config['_Linkingtablefields'][$Field]['DestID'].'" '.$Sel.' /> <offlabel for="'.$Field.'_'.$Config['_Linkingtablefields'][$Field]['DestID'].'">'.$linkData[$Config['_Linkingtablefields'][$Field]['DestValue']].'</offlabel>';
    echo '</div>';
}
//echo $linkQuery;

?>