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

function pre_process_googlemap($Data, $Setup, $Config, $EID){
    global $ReportReturn;

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
?>

<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>

<div id="<?php echo $instance; ?>" style="<?php echo $Width.$Height;?>">Loading Map</div>


<script>

jQuery(document).ready(function() {

        var geocoder;
        geocoder = new google.maps.Geocoder();
        geocoder.geocode( { 'address': '<?php echo $Data[0][$Setup['_Address']]; ?>'}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {            
            var options = {
                    zoom: <?php echo $Zoom; ?>,
                    center: results[0].geometry.location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('<?php echo $instance; ?>'), options);



<?php
foreach($Data as $Row){

    $RowID= uniqid();

?>
                
                geocoder.geocode( { 'address': '<?php echo $Row[$Setup['_Address']]; ?>'}, function(results, status) {
                    
                  if (status == google.maps.GeocoderStatus.OK) {

                    var marker<?php echo $RowID; ?> = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map
                    });

                    google.maps.event.addListener(marker<?php echo $RowID; ?>, 'click', function() {
                        df_loadEntry('<?php echo $Row['_return_'.$Config['_ReturnFields'][0]]; ?>', '<?php echo $EID; ?>', false);
                    });


                  }

                });



                var infowindow<?php echo $RowID; ?> = new google.maps.InfoWindow({
                    content:  createInfo('<?php echo $RowID; ?>', '<div style="padding:10px 3px 3px 0; float:left; width: 50px;">{{AgentPhoto}}</div><div style="padding: 10px 0 3px 3px; float: left; min-width:200px;"><div style="padding: 2px;"><strong>Outlet Name:</strong> {{OutletName}}</div><div style="padding: 2px;"><strong>ID No:</strong> {{AgentIDNo}}</div><div style="padding: 2px;"><strong>Outlet Code:</strong> {{OutletCode}}</div></div><div style="clear:both;"></div>')
                });


<?php
    }
?>



          }
        });

        function createInfo(title, content) {

            return '<div class="content-box-header"><h3>'+title+'</h3></div><div class="content-box-content" style="font-size: 12px;">'+content+'</div>';


            return '<div class="infowindow"><h2>'+ title +'</h2>'+content+'</div>';
        }

        });

    </script>



<?php
return $Data;
}

function post_process_googlemap($Data, $Setup, $Config){

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
    $Zoom = 10;
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_Zoom'])){
        $Zoom = $Config['_ViewProcessors'][$ProcessID]['_Zoom'];
    }
    $Width = 'auto';
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_Width'])){
        $Width = $Config['_ViewProcessors'][$ProcessID]['_Width'];
    }
    $Height = '400';
    if(!empty($Config['_ViewProcessors'][$ProcessID]['_Height'])){
        $Height = $Config['_ViewProcessors'][$ProcessID]['_Height'];
    }

    $Return .= '<p>Zoom Level: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Zoom]" value="'.$Zoom.'" style="width:20px;" /> 1-18</p>';
    $Return .= '<p>Width: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Width]" value="'.$Width.'" style="width:20px;" /> in px (leave blank for auto)</p>';
    $Return .= '<p>Height: <input type="text" name="Data[Content][_ViewProcessors]['.$ProcessID.'][_Height]" value="'.$Height.'" style="width:20px;" /> in px (leave blank for auto)</p>';




    return $Return;
}

?>
