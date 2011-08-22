<?php
/// This creates the actual input fields for capturing. this will handle the occurance of the setting

//vardump($Config['_SelectOptions'][$Field]);

switch ($Config['_SelectType'][$Field]){
    case 'dropdown':
        echo '<select name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'" class="'.$Req.'" >';
        foreach($Config['_SelectOptions'][$Field] as $optionValue){
            $sel = '';
            if(!empty($Val)){
                if($Val == $option){
                    $Sel = 'selected="selected"';
                }
            }
            echo '<option value="'.$optionValue.'" '.$sel.'>'.$optionValue.'</option>';
        }
        echo '</select>';
        break;
    case 'radio':
        foreach($Config['_SelectOptions'][$Field] as $optionValue){
            $sel = '';
            if(!empty($Val)){
                if($Val == $option){
                    $Sel = 'checked="checked"';
                }
            }
            $id= uniqid();
            echo '<div><input type="radio" value="'.$optionValue.'" name="dataForm['.$Element['ID'].']['.$Field.']" id="entry_'.$Element['ID'].'_'.$Field.'_'.$id.'" '.$sel.' class="'.$Req.'" /> '.$optionValue.'</div>';
        }
        break;
    case 'checkbox':
        if(!empty($Val)){
            $valData = unserialize($Val);
        }
        echo $Val;
        foreach($Config['_SelectOptions'][$Field] as $optionValue){
            $sel = '';
            if(!empty($valData)){
                
                if(in_array($optionValue, $valData)){
                    $sel = 'checked="checked"';
                }
            }
            $id= uniqid();
            echo '<div><input type="checkbox" value="'.$optionValue.'" name="dataForm['.$Element['ID'].']['.$Field.'][]" id="entry_'.$Element['ID'].'_'.$Field.'_'.$id.'" '.$sel.' class="'.$Req.'" /> '.$optionValue.'</div>';
        }
        break;
}



?>