<h2>Fields Setup</h2>
<?php
echo df_listTables('_main_table', 'dr_fetchPrimSetup', $Element['Content']['_main_table']);
//EndInfoBox();
?>

<div id="col-container" >
    <?php
    echo '<h2>Define Fieldtypes</h2>';
//dump($Element);
    if ($_GET['page'] != 'Add_New') {
    ?>
        <div style="width:565px;">

        <?php echo '<h2>Advanced Field Types</h2>'; ?>
        <div class="list_row3"><input type="button" class="button" value="Add Clone Field" onclick="dr_addLinking('<?php echo $Element['Content']['_main_table']; ?>')" /></div>
        <div class="columnSorter" id="drToolBox">
            <?php
//echo df_tableReportSetup($Element['Content']['_main_table'], $Element, false, 'C');
            ?>
        </div>


    </div>
    <?php
        }
    ?>
        <div style="">
            <div id="referenceSetup"></div>
            <div style="overflow:auto;">
                <table width="" border="0" cellspacing="2" cellpadding="2">
                    <tr>
                        <td valign="top" class="columnSorter" id="FieldList_Main"><?php
        echo df_tableReportSetup($Element['Content']['_main_table'], 'false', $Element);
    ?></td>
                </tr>
            </table>
        </div>
    </div>

</div>
<?php
        echo '<h2>Passback Field</h2>';
?>
        <div style="padding:3px;">
            <input type="button" name="button" id="button" class="button" value="Add Passback Field" onclick="dr_addPassbackField();"/></div>
        <div id="PassBack_FieldSelect">
    <?php
        echo dr_loadPassbackFields($Element['Content']['_main_table'], $Element['Content']['_ReturnFields'], $Element['Content']);
    ?></div>
<?php
        echo '<h2>Sort Field</h2>';
?>
        <div id="sortFieldSelect">
<?php
        if ($_GET['page'] != 'Add_New') {
            echo df_loadSortFields($Element['Content']['_main_table'], $Element['Content']['_SortField'], $Element['Content']['_SortDirection']);
        }
?>
</div>
