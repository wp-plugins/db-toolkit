<div id="dbt_container" class="wrap poststuff">

        <input type="hidden" name="Data[Content][_FormLayout]" cols="50" rows="10" id="_FormLayout" />
        <div id="header">
            <div class="logo">
                <h2>Application Manager</h2>
            </div>

            <div class="clear"></div>
        </div>
        <div id="main">
            <?php
            // Tabs
            ?>
            <div id="dbt-nav">
                <ul>
                <?php

                //app_fetchCategories($user, $pass);
            //$interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
                $apps = get_option('dt_int_Apps');                
                
                $tabIndex = 1;
                foreach($apps as $Title=>$State){
                    if(is_array($State)){
                        $Class = '';
                        if(!empty($_GET['ctb'])){
                        if($_GET['ctb'] == $tabIndex){
                            $Class = 'current';
                        }
                        }else{
                            if($tabIndex == 1){
                                $Class= 'current';
                            }
                        }
                        echo '<li class="'.$Class.'">';
                        echo '<a href="#dbt-option-'.$tabIndex++.'" title="'.$Title.'">'.$State['name'].'</a>';
                        echo '</li>';
                    }

                }

                ?>
                </ul>

            </div>

            <div id="content">

                <?php
                // Option Tab
                $interfaces = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE 'dt_intfc%' ", ARRAY_A);
                
                $tabIndex = 1;
                foreach($apps as $Title=>$State){
                    if(is_array($State)){
                        $appConfig = get_option('_'.$Title.'_app');
                        $view = 'none';
                        if(!empty($_GET['ctb'])){
                            if($_GET['ctb'] == $tabIndex){
                                $view = 'block';
                            }
                        }else{
                            if($tabIndex == 1){
                                $view = 'block';
                            }
                        }

                        echo '<div id="dbt-option-'.$tabIndex++.'" class="group" style="display: '.$view.';">';
                        //echo '<h2>'.$State['name'].'</h2>';
                        if(!empty($appConfig['imageURL'])){
                            echo '<img src="'.$appConfig['imageURL'].'" name="DB-Toolkit" title="DB-Toolkit" align="absmiddle" style="float:right;" />';
                        }?>

<div id="<?php echo 'app-'.$tabIndex++; ?>" class="appModule" style="height: 160px;">
<h2>Digilab Media Portfolio & Customer Database</h2>
<div class="appDescription">
<div class="appLogo">
<img width="71" height="45" align="right" src="http://localhost/wordpress/wp-content/plugins/jetpack/_inc/images/icons/twitter-widget.png">
<p>$45.00</p>
</div>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla at sem ipsum, quis iaculis est. Maecenas iaculis congue augue, quis volutpat risus fringilla eu. Aenean porttitor nulla in nulla mollis sodales. Nulla ac lacinia diam. In mollis aliquet felis, cursus mattis nunc aliquam eget. Sed in porta sem.</p>
</div>

<div class="appModuleButton">
<a href="http://en.blog.wordpress.com/2009/03/26/twitter-widget/" class="button more-info-link">Learn More</a>								</div>
</div>
                            
                <?php


                        echo '</div>';

                    }
                }


            ?>


            </div>
            <div class="clear"></div>

        </div>
        <div class="save_bar_top">

                <span class="submit-footer-reset">
                </span>
        </div>

    <div style="clear:both;"></div>
</div>











<script type="text/javascript">
    jQuery(document).ready(function(){

        jQuery('#dbt-nav li a').click(function(){
            jQuery('#dbt-nav li').removeClass('current');
            jQuery('.group').hide();
            jQuery(''+jQuery(this).attr('href')+'').show();
            jQuery(this).parent().addClass('current');
            //alert(jQuery(this).attr('href'));
            return false;
        });

        jQuery('#dbt_container .help').click(function(){
            jQuery(''+jQuery(this).attr('href')+'').toggle();
            return false;
        })
    });
</script>