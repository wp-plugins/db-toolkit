<div id="tabs-4" class="setupTab">
    <div id="layoutTemplateArea">
<div class="admin_config_toolbar">
<?php

    $Sel = '';
    if(!empty($Element['Content']['_useListTemplate'])) {
        $Sel = 'checked="checked"';
    }
    echo dais_customfield('checkbox', 'Enable List Templates', '_useListTemplate', '_useListTemplate', 'list_row1' , 1 , $Sel);


?>
</div>

        <div id="layoutHeaderTemplate">
            <h3>Header Template <span class="description">Placed before the interface is rendered.</span></h3>
            <?php
            $HeaderTemplate = '';
            if(!empty($Element['Content']['_layoutTemplate']['_Header'])){
                $HeaderTemplate = $Element['Content']['_layoutTemplate']['_Header'];
            }
            ?>
            <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Header]"><?php echo $HeaderTemplate; ?></textarea>
            <!-- Header Template Area -->
        </div>
        <div id="layoutFieldTemplate">
            <ul class="tools_widgets">
                <li class="root_item"><a class="parent hasSubs"><strong>Fields</strong></a>
                    <ul id="" style="visibility: hidden; display: block;">
                        <?php
                            //echo df_listProcessors();
                        if(!empty($Element['Content']['_FieldTitle'])){
                            foreach($Element['Content']['_FieldTitle'] as $FieldKey=>$Val){
                                echo '<li><a onclick="alert(\''.$Val.'\');"><img align="absmiddle" src="'.WP_PLUGIN_URL.'/db-toolkit/data_report/arrow_switch.png"> '.$Val.'</a></li>';
                            }
                        }

                        ?>
                    </ul>
                </li>
            </ul>
            <div style="clear:both;"></div>
            <!-- Fields Template Panel -->
            
            <div>
                <div class="admin_list_row3 table_sorter postbox" id="Field_Total">
                    <img align="absmiddle" style="float: right; padding: 5px;" onclick="alert('delete');" src="http://localhost/wordpress/wp-content/plugins/db-toolkit/images/cancel.png">
                    <img align="absmiddle" style="float: right; padding: 5px;" onclick="jQuery('.fieldTemplate').toggle();" src="http://localhost/wordpress/wp-content/plugins/db-toolkit/images/cog.png">
                    <h3>UserID</h3>
                    <div style="display: none;" class="admin_config_panel fieldTemplate"></div>
                    <div style="display: none;" class="admin_config_panel fieldTemplate"><strong>Before</strong></div>
                    <div style="display: none;" class="admin_config_panel fieldBeforeAfter fieldTemplate">
                        <textarea class="layoutTextArea"></textarea>
                    </div>
                    <div style="display: none;" class="admin_config_panel fieldTemplate"><strong>After</strong></div>
                    <div style="display: none;" class="admin_config_panel fieldBeforeAfter fieldTemplate">
                        <textarea class="layoutTextArea" id="qwer"></textarea>
                    </div>
                    <div style="clear:both"></div>                    
                </div>
            </div>

            
        </div>
        <div id="layoutContentTemplate">
            <img align="absmiddle" style="float: right; padding: 5px; cursor: pointer;" onclick="jQuery('.row_helpPanel').toggle();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/help.png">
            <ul class="tools_widgets">
                <li class="root_item"><a class="parent" onclick="addRowTemplate();"><strong>Add Row Template</strong></a></li>
            </ul>
            <div style="clear:both;"></div>

            <!-- Content Template Panel -->
                    <div style="display: none; background-color: #fff; border: 1px solid #666666;" class="admin_config_panel row_helpPanel">
                        <div><strong>I will reformat this into a proper help dialog when I finialise the codes.</strong></div>
<strong>Dynamic Template Codes</strong> <span class="description">Can be used in all template boxes.</span>
<pre>
{{_ViewEdit}}           : View and Edit Icons
{{_ViewLink}}           : View Item Link
{{_RowClass}}           : Row Class
{{_RowIndex}}           : Row Index
{{_UID}}                : Unique Row ID
{{_PageID}}             : Page ID
{{_PageName}}           : Page Name
{{_EID}}                : Element ID
{{<i><b>Fieldname</b></i>}}           : Field Data
{{_<i>Fieldname</i>_name}}     : Field Name
{{_return_<i><b>Fieldname</b></i>}}   : Return Field

{{<i>Fieldname</i>|<i>substr value</i> [, substring char count]}}
Formats substr(Value, 0, num)
if "," and second num is added:substr(Value, first num, second num)

{{<i>Fieldname</i>|<i>php formatting function</i>}}
Field Data | php formatting function eg: add_slashes, urlencode, htmlentities etc...

Field Keys:
<?php
    if(!empty($Element['Content']['_FieldTitle'])){
        foreach($Element['Content']['_FieldTitle'] as $FieldKey=>$Val){
            echo $Val.' = {{'.$FieldKey.'}}<br />';
        }
    }else{
        echo 'Save and edit to see available fields';
    }
?>
</pre>
<strong>Dynamic Footer Codes</strong> <span class="description">Can only be used in AFTER template boxes.</span>
<pre>
{{_footer_prev}}        : Previous Page/Entries
{{_footer_next}}        : Next Page/Entries
{{_footer_page_jump}}   :
Page Index Input Box (page __ of 20)

{{_footer_item_count}}  :
Number of items found and displayed (1 - 10 of 200 items)

</pre>
To enable selection and deleting:<br/>
id="row_{{_EID}}_{{_RowIndex}}"  ref="{{_return_<em><strong>Fieldname</strong></em>}} highlight" class="itemRow_{{_EID}}  report_entry"

                    </div>

            <div class="rowTemplateHolder" id="rowTemplateHolder">
            <?php

                if(empty($Element['Content']['_layoutTemplate']['_Content'])){
                    echo dr_addListRowTemplate();
                }else{
                    $TemplateTotal = count($Element['Content']['_layoutTemplate']['_Content']['_name'])-1;
                    for($T=0; $T <= $TemplateTotal; $T++){
                    $Defaults = array(
                        '_name'=> $Element['Content']['_layoutTemplate']['_Content']['_name'][$T],
                        '_before'=> $Element['Content']['_layoutTemplate']['_Content']['_before'][$T],
                        '_content'=> $Element['Content']['_layoutTemplate']['_Content']['_content'][$T],
                        '_after'=> $Element['Content']['_layoutTemplate']['_Content']['_after'][$T],
                        );

                     echo dr_addListRowTemplate($Defaults);

                    }

                }
                $rowTemplateID = uniqid('Template');
            ?>
            </div>


            

        </div>
        <div id="layoutFooterTemplate">            
            <h3>Footer Template <span class="description">Placed before the interface is rendered.</span></h3>

            <?php
            $FooterTemplate = '';
            if(!empty($Element['Content']['_layoutTemplate']['_Footer'])){
                $FooterTemplate = $Element['Content']['_layoutTemplate']['_Footer'];
            }
            ?>
            <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Footer]"><?php echo $FooterTemplate; ?></textarea>
            <!-- Footer Template Area -->
        </div>
    </div>
</div>

<?php
/*



<pre>
{{_ViewEdit}}	: View and Edit Icons
{{_ViewLink}}	: View Item Link
{{_RowClass}}	: Row Class
{{_RowIndex}}	: Row Index
{{_UID}}	: Unique Row ID
{{_PageID}}	: Page ID
{{_PageName}}	: Page Name
{{_EID}}	: Element ID
{{<i><b>Fieldname</b></i>}}	: Field Data
{{_<i>Fieldname</i>_name}}	: Field Name
{{_return_<i><b>Fieldname</b></i>}}	: Return Field
{{<i>Fieldname</i>|<i>substr value</i>}}	: Field Data | substring value

Field Keys:
<?php
    if(!empty($Element['Content']['_FieldTitle'])){
        foreach($Element['Content']['_FieldTitle'] as $FieldKey=>$Val){
            echo $Val.' = {{'.$FieldKey.'}}<br />';
        }
    }else{
        echo 'Save and edit to see available fields';
    }
?>

</pre>
to enable selection and deleting:
    id="row_{{_EID}}_{{_RowIndex}}"  ref="{{_return_<em><strong>Fieldname</strong></em>}} highlight" class="itemRow_{{_EID}}  report_entry"

{{_footer_prev}}        : Previous Page/Entries
{{_footer_next}}        : Next Page/Entries
{{_footer_page_jump}}   : Page Index Input Box (page __ of 20)
{{_footer_item_count}}  : Number of items found and displayed (1 - 10 of 200 items)
{{_footer_no_entries}}  : No results




*/

?>
<script>
jQuery(function() {
        jQuery(".rowTemplateHolder").sortable({

            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            connectWith: '.fieldsTemplateHolder',
            stop: function(p){
                //alert(columns);
            }

        });
});

</script>