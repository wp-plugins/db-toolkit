<?php
/* 
 * googlemap functions
 * function naming:
 * 
 *      post_process_{{folder}}($Data)
 *      pre_process_{{folder}}($Data)
 *      config_{{folder}}($Config = false)
 *
 *
 * global var $ReportReturn;
 *
 */

function googlemap_doCall($query){

    $url = "http://maps.google.com/maps/geo?q=".urlencode($query)."&output=json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $url);

    $output = curl_exec($ch);
    return json_decode($output);
}

function pre_process_googlemap($Data, $Setup, $Config, $EID){
    global $ReportReturn;
    if(empty($Data))
        return $Data;
        
    $Zoom = 6;
    if(!empty($Setup['_Zoom'])){
        $Zoom = $Setup['_Zoom'];
    }
    
    //$ReportReturn = false;   
    // return false;
    $instance = uniqid($EID);

    $Width = '';
    if(!empty($Setup['_Width'])){
        $Width = $Setup['_Width'];
    }
    if(strtolower($Width) != 'auto'){
        $Width = 'width:'.$Width.'px;';
    }else{
        $Width = '';
    }
    $Height = '400';
    if(!empty($Setup['_Height'])){
        $Height = $Setup['_Height'];
    }
    if(strtolower($Height) != 'auto'){
        $Height = 'height:'.$Height.'px;';
    }
    

    foreach ($Data as $Row){
        $Out = googlemap_doCall($Row[$Setup['_Address']]);
        if($Out->Status->code == 200){
            $run = true;
            break;
        }
    }    

    if(empty($run)){
        return $Data;
    }
    
?>

<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>

<div id="<?php echo $instance; ?>" style="<?php echo $Width.$Height;?>; background: url('http://s1.wp.com/i/loading/fresh-64.gif') center center no-repeat;"></div>


<script>

jQuery(document).ready(function() {
            
            var bounds = new google.maps.LatLngBounds();
            var latlng = new google.maps.LatLng(<?php echo $Out->Placemark[0]->Point->coordinates[1].','.$Out->Placemark[0]->Point->coordinates[0]; ?>);
            var options = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('<?php echo $instance; ?>'), options);



<?php

foreach($Data as $Row){

    $RowID= uniqid();
    
    $Out = googlemap_doCall($Row[$Setup['_Address']]);
    

    if($Out->Status->code == 200){

?>

                    var latlng = new google.maps.LatLng(<?php echo $Out->Placemark[0]->Point->coordinates[1].','.$Out->Placemark[0]->Point->coordinates[0]; ?>);
                    var marker<?php echo $RowID; ?> = new google.maps.Marker({
                        position: latlng,
                        map: map
                    });
                    bounds.extend(marker<?php echo $RowID; ?>.position);

                    google.maps.event.addListener(marker<?php echo $RowID; ?>, 'click', function() {
                        df_loadEntry('<?php echo $Row['_return_'.$Config['_ReturnFields'][0]]; ?>', '<?php echo $EID; ?>', false);
                    });




<?php
    }
}
?>




        map.fitBounds(bounds);

        });

    </script>



<?php

if(!empty($Setup['_EndPoint'])){
    return false;
}
return $Data;
}


function config_googlemap($ProcessID, $Table, $Config = false){
global $wpdb;
    

    $Fields = $wpdb->get_results( "SHOW COLUMNS FROM ".$Table, ARRAY_N);

    foreach($Fields as $FieldData){

        $Sel = '';
        if($Config['_ViewProcessors'][$ProcessID]['_Address'] == $FieldData[0]){
            $Sel = 'selected="selected"';
        }
        $Sender .= '<option value="'.$FieldData[0].'" '.$Sel.'>'.$FieldData[0].'</option>';

    }

    $Return = '<p>Address/Coordinates Field: <select name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Address]">'.$Sender.'</select></p>';
    //$Zoom = 10;
    //if(!empty($Config['_ViewProcessors'][$ProcessID]['_Zoom'])){
    //    $Zoom = $Config['_ViewProcessors'][$ProcessID]['_Zoom'];
    //}
    $Width = 'auto';
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_Width'])){
        $Width = $Config['_ViewProcessors'][$ProcessID]['_Width'];
    }
    $Height = '400';
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_Height'])){
        $Height = $Config['_ViewProcessors'][$ProcessID]['_Height'];
    }
    $EnPoint = '';
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_EndPoint'])){
        $EnPoint = 'checked="checked"';
    }

    //$Return .= '<p>Zoom Level: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Zoom]" value="'.$Zoom.'" style="width:20px;" /> 1-18</p>';
    $Return .= '<p>Width: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Width]" value="'.$Width.'" style="width:20px;" /> in px (leave blank for auto)</p>';
    $Return .= '<p>Height: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Height]" value="'.$Height.'" style="width:20px;" /> in px (leave blank for auto)</p>';
    $Return .= '<p>Map as Endpoint: <input type="checkbox" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_EndPoint]" value="1" '.$EnPoint.' /> <span class="description">Map only mode, No other processors after map renders.</span></p>';




    return $Return;
}

?>
