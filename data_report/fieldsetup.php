<h2>Fields Setup</h2>
<div id="mainTableSelector">
<?php
    if(empty($Element['Content']['_main_table']))
        $Element['Content']['_main_table'] = '';
    echo df_listTables('_main_table', 'dr_fetchPrimSetup', $Element['Content']['_main_table']);
//EndInfoBox();
?>
</div>
<div id="col-container" >
    <?php
    echo '<h2>Define Fieldtypes</h2>';
//dump($Element);
    $addFieldButton = 'none';
    if(!empty($Element['Content']['_main_table'])){
        $addFieldButton = '';
    }
    ?>
        <div style="width:565px;">        
            <div class="list_row3">
                <?php if ($_GET['page'] != 'Add_New') { ?><input type="button" class="button" value="Add Virtual Field" onclick="dr_addLinking('<?php echo $Element['Content']['_main_table']; ?>')" /> <?php } ?>
                <input id="addFieldButton" type="button" style="display:<?php echo $addFieldButton; ?>;" class="button" value="Add Field" onclick="dr_addField()" />
            </div>
        <div class="columnSorter" id="drToolBox">
            <?php
                //echo df_tableReportSetup($Element['Content']['_main_table'], $Element, false, 'C');
            ?>
        </div>


    </div>
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
    if(empty($Element['Content']['_ReturnFields']))
        $Element['Content']['_ReturnFields'] = '';
        
        echo dr_loadPassbackFields($Element['Content']['_main_table'], $Element['Content']['_ReturnFields'], $Element['Content']);
    ?></div>
<?php
        echo '<h2>Sort Field</h2>';
?>
        <div id="sortFieldSelect">
<?php
        if ($_GET['page'] != 'Add_New') {
            if(empty($Element['Content']['_SortField']))
                $Element['Content']['_SortField'] = '';

            if(empty($Element['Content']['_SortDirection']))
                $Element['Content']['_SortDirection'] = '';

            echo df_loadSortFields($Element['Content']['_main_table'], $Element['Content']['_SortField'], $Element['Content']['_SortDirection']);
        }
?>
</div>

<a tabindex="0" href="#news-items-2" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="hierarchybreadcrumb"><span class="ui-icon ui-icon-triangle-1-s"></span>ipod-style menu w/ breadcrumb</a>
<div id="news-items-2" class="hidden">
<ul>
	<li><a href="#">Breaking News</a>
		<ul>

			<li><a href="#">Entertainment</a></li>
			<li><a href="#">Politics</a></li>
			<li><a href="#">A&amp;E</a></li>
			<li><a href="#">Sports</a>
				<ul>
					<li><a href="#">Baseball</a></li>

					<li><a href="#">Basketball</a></li>
					<li><a href="#">A really long label would wrap nicely as you can see</a></li>
					<li><a href="#">Swimming</a>
						<ul>
							<li><a href="#">High School</a></li>
							<li><a href="#">College</a></li>

							<li><a href="#">Professional</a>
								<ul>
									<li><a href="#">Mens Swimming</a>
										<ul>
											<li><a href="#">News</a></li>
											<li><a href="#">Events</a></li>
											<li><a href="#">Awards</a></li>

											<li><a href="#">Schedule</a></li>
											<li><a href="#">Team Members</a></li>
											<li><a href="#">Fan Site</a></li>
										</ul>
									</li>
									<li><a href="#">Womens Swimming</a>
										<ul>

											<li><a href="#">News</a></li>
											<li><a href="#">Events</a></li>
											<li><a href="#">Awards</a></li>
											<li><a href="#">Schedule</a></li>
											<li><a href="#">Team Members</a></li>
											<li><a href="#">Fan Site</a></li>

										</ul>
									</li>
								</ul>
							</li>
							<li><a href="#">Adult Recreational</a></li>
							<li><a href="#">Youth Recreational</a></li>
							<li><a href="#">Senior Recreational</a></li>

						</ul>
					</li>
					<li><a href="#">Tennis</a></li>
					<li><a href="#">Ice Skating</a></li>
					<li><a href="#">Javascript Programming</a></li>
					<li><a href="#">Running</a></li>
					<li><a href="#">Walking</a></li>

				</ul>
			</li>
			<li><a href="#">Local</a></li>
			<li><a href="#">Health</a></li>
		</ul>
	</li>
	<li><a href="#">Entertainment</a>

	<ul>
		<li><a href="#">Celebrity news</a></li>
		<li><a href="#">Gossip</a></li>
		<li><a href="#">Movies</a></li>
		<li><a href="#">Music</a>
		<ul>
			<li><a href="#">Alternative</a></li>

			<li><a href="#">Country</a></li>
			<li><a href="#">Dance</a></li>
			<li><a href="#">Electronica</a></li>
			<li><a href="#">Metal</a></li>
			<li><a href="#">Pop</a></li>
			<li><a href="#">Rock</a>

				<ul>
					<li><a href="#">Bands</a>
						<ul>
							<li><a href="#">Dokken</a></li>
						</ul>
					</li>
					<li><a href="#">Fan Clubs</a></li>

					<li><a href="#">Songs</a></li>
				</ul>
			</li>
		</ul>
		</li>
		<li><a href="#">Slide shows</a></li>
		<li><a href="#">Red carpet</a></li>

	</ul>
	</li>
	<li><a href="#">Finance</a>
	<ul>
		<li><a href="#">Personal</a>
		<ul>
			<li><a href="#">Loans</a></li>

			<li><a href="#">Savings</a></li>
			<li><a href="#">Mortgage</a></li>
			<li><a href="#">Debt</a></li>
		</ul>
		</li>
		<li><a href="#">Business</a></li>
	</ul>

	</li>
	<li><a href="#">Food &#38; Cooking</a>
	<ul>
		<li><a href="#">Breakfast</a></li>
		<li><a href="#">Lunch</a></li>
		<li><a href="#">Dinner</a></li>

		<li><a href="#">Dessert</a>
			<ul>
				<li><a href="#">Dump Cake</a></li>
				<li><a href="#">Doritos</a></li>
				<li><a href="#">Both please.</a></li>
			</ul>
		</li>

	</ul>
	</li>
	<li><a href="#">Lifestyle</a></li>
	<li><a href="#">News</a></li>
	<li><a href="#">Politics</a></li>
	<li><a href="#">Sports</a>
		<ul>

			<li><a href="#">Baseball</a></li>
			<li><a href="#">Basketball</a></li>
			<li><a href="#">Swimming</a>
			<ul>
				<li><a href="#">High School</a></li>
				<li><a href="#">College</a></li>

				<li><a href="#">Professional</a>
				<ul>
					<li><a href="#">Mens Swimming</a>
					<ul>
							<li><a href="#">News</a></li>
							<li><a href="#">Events</a></li>
							<li><a href="#">Awards</a></li>

							<li><a href="#">Schedule</a></li>
							<li><a href="#">Team Members</a></li>
							<li><a href="#">Fan Site</a></li>
						</ul>
					</li>
					<li><a href="#">Womens Swimming</a>
					<ul>

						<li><a href="#">News</a></li>
						<li><a href="#">Events</a></li>
						<li><a href="#">Awards</a></li>
						<li><a href="#">Schedule</a></li>
						<li><a href="#">Team Members</a></li>
						<li><a href="#">Fan Site</a></li>

					</ul>
					</li>
				</ul>
				</li>
				<li><a href="#">Adult Recreational</a></li>
				<li><a href="#">Youth Recreational</a></li>
				<li><a href="#">Senior Recreational</a></li>

			</ul>
			</li>
			<li><a href="#">Tennis</a></li>
			<li><a href="#">Ice Skating</a></li>
			<li><a href="#">Javascript Programming</a></li>
			<li><a href="#">Running</a></li>
			<li><a href="#">Walking</a></li>

		</ul>
		</li>
	</ul>
</div>

    <script type="text/javascript">
    jQuery(function(){
    	// BUTTONS
    	jQuery('.fg-button').hover(
    		function(){ jQuery(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
    		function(){ jQuery(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
    	);

    	// MENUS
		jQuery('#hierarchy').menu({
			content: jQuery('#hierarchy').next().html(),
			crumbDefaultText: ' '
		});

		jQuery('#hierarchybreadcrumb').menu({
			content: jQuery('#hierarchybreadcrumb').next().html(),
			backLink: false
		});
    });
    </script>
