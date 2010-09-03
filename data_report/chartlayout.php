<div id="tabs-2c">
<?php
                    $Sel = '';
                    if(!empty($Element['Content']['_chartMode'])) {
                        $Sel = 'checked="checked"';
                    }

                    echo dais_customfield('checkbox', 'Show Chart', '_chartMode', '_chartMode', 'list_row1' , 1 , $Sel);
                    
                    $Sel = '';
                    if(!empty($Element['Content']['_chartOnly'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Chart Only', '_chartOnly', '_chartOnly', 'list_row1' , 1 , $Sel);

                    echo dais_customfield('text', 'Chat Height (px)', '_chartHeight', '_chartHeight', 'list_row1' , @$Element['Content']['_chartHeight'], '');

                    echo dais_customfield('Text', 'Title', '_chartTitle', '_chartTitle', 'list_row1' , @$Element['Content']['_chartTitle'] , '');
                    echo dais_customfield('Text', 'Caption', '_chartCaption', '_chartCaption', 'list_row1' , @$Element['Content']['_chartCaption'] , '');

                    echo '<div style="width:450px;">';
                    InfoBox('Notice');
                    echo '<a href="http://www.highcharts.com" target="_blank" border="0"><img src="'.WP_PLUGIN_URL . '/db-toolkit/images/logohighcharts.png" /></a><br /><br />';
                    echo 'Charting is powered <a href="http://www.highcharts.com" target="_blank">Highcharts</a> Which is free for Personal/non-profit under the Creative Commons Attribution-NonCommercial 3.0 License.<br /><br />
                        For more on licencing for commercial use see the <a href="http://www.highcharts.com/license" target="_blank">licencing page on highcharts site</a>.';
                    EndInfoBox();
                    echo '</div>';


?>
</div>