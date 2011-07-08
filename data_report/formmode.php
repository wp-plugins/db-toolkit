<?php
echo '<div style="width: '.$Config['_popupWidth'].'px;">';
    foreach($Config['_Field'] as $Field=>$Value) {
        $typeSet = explode('_', $Value);
        if(function_exists($typeSet[0].'_preForm')) {
            $Func = $typeSet[0].'_preForm';
            $Func($Field, $typeSet[1], $Media, $Config);
        }
    }
    if(!empty($_GET[$Config['_ReturnFields'][0]])) {
        $Form = dr_BuildUpDateForm($Media['ID'], $_GET[$Config['_ReturnFields'][0]]);
    }else {
        $Form = df_buildQuickCaptureForm($Media['ID']);
    }
    foreach($Config['_Field'] as $Field=>$Value) {
        $typeSet = explode('_', $Value);
        if(function_exists($typeSet[0].'_postForm')) {
            $Func = $typeSet[0].'_postForm';
            $Func($Field, $typeSet[1], $Media, $Config);
        }
    }
    if(empty($Config['_HideFrame'])) {
        InfoBox($Form['title']);
    }
    echo $Form['html'];
    if(empty($Config['_HideFrame'])) {
        EndInfoBox();
    }
    echo '</div>';
    return;
?>