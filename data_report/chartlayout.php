<div id="tabs-2c">
<?php
                    //$Sel = '';
                    //if(!empty($Element['Content']['_chartMode'])) {
                    //    $Sel = 'checked="checked"';
                    //}

                    //echo dais_customfield('checkbox', 'Show Chart', '_chartMode', '_chartMode', 'list_row1' , 1 , $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_chartMode'])) {
                        $Sel = 'checked="checked"';
                    }

                    echo dais_customfield('checkbox', 'Enable Charts', '_chartMode', '_chartMode', 'list_row1' , 1 , $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_chartOnly'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Chart Only', '_chartOnly', '_chartOnly', 'list_row1' , 1 , $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_multiAxis'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Multiple Axis', '_multiAxis', '_multiAxis', 'list_row1' , 1 , $Sel);
                    echo dais_customfield('text', 'Chat Height (px)', '_chartHeight', '_chartHeight', 'list_row1' , @$Element['Content']['_chartHeight'], '');
                    echo dais_customfield('Text', 'Title', '_chartTitle', '_chartTitle', 'list_row1' , @$Element['Content']['_chartTitle'] , '');
                    echo dais_customfield('Text', 'Caption', '_chartCaption', '_chartCaption', 'list_row1' , @$Element['Content']['_chartCaption'] , '');

                    echo dais_customfield('Text', 'Top Padding', '_topPad', '_topPad', 'list_row1' , @$Element['Content']['_topPad'] , '');
                    echo dais_customfield('Text', 'Right Padding', '_rightPad', '_rightPad', 'list_row1' , @$Element['Content']['_rightPad'] , '');
                    echo dais_customfield('Text', 'Bottom Padding', '_bottomPad', '_bottomPad', 'list_row1' , @$Element['Content']['_bottomPad'] , '');
                    echo dais_customfield('Text', 'left Padding', '_leftPad', '_leftPad', 'list_row1' , @$Element['Content']['_leftPad'] , '');

                    echo dais_customfield('Text', 'X-Axis Angle', '_xAngle', '_xAngle', 'list_row1' , @$Element['Content']['_xAngle'] , '');

                    $Sel = '';
                    if(!empty($Element['Content']['_hideLegend'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Hide Legend', '_hideLegend', '_hideLegend', 'list_row1' , 1 , $Sel);

                    $Sel = '';
                    if(!empty($Element['Content']['_yShowDataLables'])) {
                        $Sel = 'checked="checked"';
                    }
                    echo dais_customfield('checkbox', 'Show Data Lables', '_yShowDataLables', '_yShowDataLables', 'list_row2' , 1 , $Sel);

                    $left = '';
                    $center = '';
                    $right = '';
                    if(!empty($Element['Content']['_xAxis_Align'])){
                        switch($Element['Content']['_xAxis_Align']){
                            case 'left':
                                $left = 'selected="selected"';
                                $center = '';
                                $right = '';
                                break;
                            case 'center':
                                $left = '';
                                $center = 'selected="selected"';
                                $right = '';
                                break;
                            case 'right':
                                $left = '';
                                $center = '';
                                $right = 'selected="selected"';
                                break;

                        }
                    }

                    echo '<div style="padding:3px;" class="list_row2"><strong>xAxis text Alignment: </strong>';
                    echo '<select name="Data[Content][_xAxis_Align]" >';
                    echo '<option value="left" '.$left.'>Left</option>';
                    echo '<option value="center" '.$center.'>Center</option>';
                    echo '<option value="right" '.$right.'>Right</option>';
                    echo '</select>';
                    echo '</div>';

                    $ToolTip = '<b>{{SeriesName}}</b><br/>{{YValue}}: {{XValue}}';
                    if(!empty($Element['Content']['_xAngle'])){
                        $ToolTip = $Element['Content']['_yToolTipTemplate'];
                    }
                    echo dais_customfield('textarea', 'Tool Tip Template', '_yToolTipTemplate', '_yToolTipTemplate', 'list_row1' , $ToolTip, '');


                    echo '<div style="width:450px;">';
                    InfoBox('Notice');
                    echo '<a href="http://www.highcharts.com" target="_blank" border="0"><img src="'.WP_PLUGIN_URL . '/db-toolkit/images/logohighcharts.png" /></a><br /><br />';
                    echo 'Charting is powered <a href="http://www.highcharts.com" target="_blank">Highcharts</a> Which is free for Personal/non-profit under the Creative Commons Attribution-NonCommercial 3.0 License.<br /><br />
                        For more on licencing for commercial use see the <a href="http://www.highcharts.com/license" target="_blank">licencing page on highcharts site</a>.';
                    EndInfoBox();
                    echo '</div>';


?>
</div>