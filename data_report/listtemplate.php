<div id="tabs-4" class="setupTab">
    <div id="layoutTemplateArea">
<div class="admin_config_toolbar">
    Enable List Template
</div>

        <div id="layoutHeaderTemplate">
            <h3>Header Template</h3>
            <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Header]"></textarea>
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
            
            <div class="fieldsTemplateHolder">
                <div class="admin_list_row3 table_sorter postbox" id="Field_Total">
                    <img align="absmiddle" style="float: right; padding: 5px;" onclick="alert('deleting')" src="http://localhost/wordpress/wp-content/plugins/db-toolkit/images/cancel.png">
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
            <ul class="tools_widgets">
                <li class="root_item"><a class="parent"><strong>Row Template</strong></a></li>
            </ul>
            <div style="clear:both;"></div>

            <!-- Content Template Panel -->
        </div>
        <div id="layoutFooterTemplate">            
            <h3>Footer Template</h3>
            <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Footer]"></textarea>
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
        jQuery(".fieldsTemplateHolder").sortable({

            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            connectWith: '.fieldsTemplateHolder',
            stop: function(p){
                //alert(columns);
            }

        });
});

</script>