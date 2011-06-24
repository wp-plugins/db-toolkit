<h2>methods</h2>
                    <?php
                    $API = str_replace('dt_intfc', '', $Media['ID']).'_'.md5(str_replace('dt_intfc', '', $Media['ID']).$Config['_APISeed']);
                    if(empty($Config['_UseListViewTemplate'])){
                    echo 'API Key: '.$API.'<br />';

                    echo '<div class="row-actions-hide">';
                    echo '<strong>List Records</strong><br />';
                    ?>
                    <div class="row-actions-show">
                        <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=xml" target="_blank">XML</a> |
                        <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=json" target="_blank">JSON</a>
                    </div>
                    <strong>Insert Records</strong><br />
                    POST URL: <input type="text" style="width: 80%;" value="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&action=insert" />

                    <?php
                    $Fields = array();
                        foreach($Config['_Field'] as $Field=>$Types){
                            if(!empty($Types)){
                                $Type = explode('_', $Types);
                                if($Type[0] != 'auto'){
                                    $Fields[] = $Field;
                                }
                            }
                        }
                        echo '<div>Submitted Data: '.implode(', ', $Fields).'</div>';
                        $Fields = array();
                        if(!empty($Config['_ReturnFields'])){
                        foreach($Config['_ReturnFields'] as $Field){
                            $Fields[] = $Field;
                        }
                            echo '<div>Returned Fields: '.implode(', ', $Fields).'</div>';
                        }
                        echo '</div>';
                    }else{


                        echo "<div class=\"row-actions\">API Only Supported in non-templated list mode</div>";
                    }
                    ?>

<?php


//vardump($Config);


exit;
?>

<table width="100%" border="0" cellspacing="2" cellpadding="2" class="widefat">
    <thead>
        <tr>
            <th scope="col" class="manage-column" id="interface-spacer-top"></th>
            <th scope="col" class="manage-column" id="interface-name-top">Interface Name</th>
            <th scope="col" class="manage-column" id="interface-table-top">Table Interfaced</th>
            <th scope="col" class="manage-column" id="interface-date-top">Short Code</th>
            <th scope="col" class="manage-column" id="interface-api-top">API Access (experimental)</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th scope="col" class="manage-column" id="interface-spacer-bottom"></th>
            <th scope="col" class="manage-column" id="interface-name-bottom">Interface Name</th>
            <th scope="col" class="manage-column" id="interface-table-bottom">Table Interfaced</th>
            <th scope="col" class="manage-column" id="interface-date-bottom">Short Code</th>
            <th scope="col" class="manage-column" id="interface-api-bottom">API Access (experimental)</th>
        </tr>
    </tfoot>
