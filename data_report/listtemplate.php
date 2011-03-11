<div id="tabs-4" class="setupTab">
    <div id="layoutTemplateArea">
        <div class="admin_config_toolbar">
            <?php
            $Sel = '';
            if (!empty($Element['Content']['_useListTemplate'])) {
                $Sel = 'checked="checked"';
            }
            echo dais_customfield('checkbox', 'Enable List Templates', '_useListTemplate', '_useListTemplate', 'list_row1', 1, $Sel);
            ?>
        </div>

        <div id="templateTabs" class="dbtools_tabs">
            <ul class="content-box-tabs">
                <li><a href="#listTemplate">Row/Entry Template</a></li>
                <li><a href="#fieldTemplate">Field Template</a></li>
            </ul>
            <div id="listTemplate" class="setupTab">


                


                <div id="layoutContentTemplate">

                    <div id="layoutHeaderTemplate">
                        <h3>Header Template <span class="description">Placed before the interface is rendered.</span></h3>
<?php
            $HeaderTemplate = '';
            if (!empty($Element['Content']['_layoutTemplate']['_Header'])) {
                $HeaderTemplate = $Element['Content']['_layoutTemplate']['_Header'];
            }
?>
                        <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Header]"><?php echo $HeaderTemplate; ?></textarea>
                        <!-- Header Template Area -->
                    </div>


                    <img align="absmiddle" style="float: right; padding: 5px; cursor: pointer;" onclick="jQuery('.row_helpPanel').toggle();" src="<?php echo WP_PLUGIN_URL; ?>/db-toolkit/images/help.png">
                    <ul class="tools_widgets">
                        <li class="root_item tabBox"><a class="parent" onclick="addRowTemplate();"><strong>Add Row Template</strong></a></li>
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
                            if (!empty($Element['Content']['_FieldTitle'])) {
                                foreach ($Element['Content']['_FieldTitle'] as $FieldKey => $Val) {
                                    echo $Val . ' = {{' . $FieldKey . '}}<br />';
                                }
                            } else {
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
                            if (empty($Element['Content']['_layoutTemplate']['_Content'])) {
                                echo dr_addListRowTemplate();
                            } else {
                                $TemplateTotal = count($Element['Content']['_layoutTemplate']['_Content']['_name']) - 1;
                                for ($T = 0; $T <= $TemplateTotal; $T++) {
                                    $Defaults = array(
                                        '_name' => $Element['Content']['_layoutTemplate']['_Content']['_name'][$T],
                                        '_before' => $Element['Content']['_layoutTemplate']['_Content']['_before'][$T],
                                        '_content' => $Element['Content']['_layoutTemplate']['_Content']['_content'][$T],
                                        '_after' => $Element['Content']['_layoutTemplate']['_Content']['_after'][$T],
                                    );

                                    echo dr_addListRowTemplate($Defaults);
                                }
                            }
                            $rowTemplateID = uniqid('Template');
?>
                        </div>


                        <div id="layoutFooterTemplate">
                            <h3>Footer Template <span class="description">Placed before the interface is rendered.</span></h3>

<?php
                            $FooterTemplate = '';
                            if (!empty($Element['Content']['_layoutTemplate']['_Footer'])) {
                                $FooterTemplate = $Element['Content']['_layoutTemplate']['_Footer'];
                            }
?>
                            <textarea class="headerFooterTemplate" name="Data[Content][_layoutTemplate][_Footer]"><?php echo $FooterTemplate; ?></textarea>
                            <!-- Footer Template Area -->
                        </div>



                    </div>




                </div>
                <div id="fieldTemplate" class="setupTab">
                <span class="description">The Field template wraps each field value is your custom code.</span>
                <br />
                <br />
                    <div id="layoutFieldTemplate">
                        <ul class="tools_widgets">
                            <li class="root_item tabBox"><a class="parent hasSubs"><strong>Fields</strong></a>
                                <ul id="" style="visibility: hidden; display: block;">
<?php
                            //echo df_listProcessors();                            
                            if (!empty($Element['Content']['_FieldTitle'])) {
                                foreach ($Element['Content']['_FieldTitle'] as $FieldKey => $Val) {
                                    echo '<li><a onclick="dr_addListFieldTemplate(\'' . $FieldKey . '\');"><img align="absmiddle" src="' . WP_PLUGIN_URL . '/db-toolkit/data_report/arrow_switch.png"> ' . $Val . '</a></li>';
                                }
                            }
?>
                            </ul>
                        </li>
                    </ul>
                    <div style="clear:both;"></div>
                    <!-- Fields Template Panel -->

                    <div id="fieldTemplateHolder">
                        <?php                            
                            // Echo out Field Templates
                            if (!empty($Element['Content']['_layoutTemplate']['_Fields'])){
                                
                                foreach($Element['Content']['_layoutTemplate']['_Fields'] as $Field=>$Defaults){                                    
                                    echo dr_addListFieldTemplate($Field, $Defaults);
                                }
                                
                            }


                        ?>
                    </div>


                </div>

            </div>
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
        jQuery("#templateTabs").tabs();
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