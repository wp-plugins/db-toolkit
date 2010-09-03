//<script>

function dr_addLinking(table){
    
    ajaxCall('df_tableReportSetup', table, 0, 0, 'Linking', function(l){

        jQuery('#drToolBox').append(l);

    })
}

function dr_loadLinkFields(table){
    ajaxCall('df_ListFields', table, 'false', '', function(p){
        
        alert(p);
        
    })
}

function dr_getParameterByName(name){
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}


function dr_pageInput(eid, pg){
	jQuery("input").keypress(function(e){
		switch(e.which){
			case 13:
				dr_goToPage(eid, pg);
				break;
		}
	});
}

function dr_switchLive(EID){
	jQuery('#liveView_'+EID).toggleClass('reloadliveon', 1000);
}

function dr_addSectionBreak(area){

 // number = jQuery('.sectionBreak').length;
	number=Math.floor(Math.random()*99999);
	jQuery('#fieldTray'+area).prepend('<div style="padding: 3px;" class="list_row4 table_sorter sectionBreak '+area+'portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="SectionBreak_SectionBreak'+number+'"><div class="'+area+'portlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="data_report/arrow_out.png" style="-moz-user-select: none;"/>&nbsp;<input type="text" class="sectionTitle" name="Data[Content][_SectionBreak][_SectionBreak'+number+'][Title]" /><span class="ui-icon ui-icon-close"></span></div><div style="padding:3px;">Caption: <input type="text" class="sectionTitle" name="Data[Content][_SectionBreak][_SectionBreak'+number+'][Caption]" /></div><input class="layOut'+area+' positioning" type="hidden" name="_SectionBreak'+number+'" id="section_'+number+'" value="1"/></div>');
	jQuery("."+area+"portlet-header .ui-icon").click(function() {
		jQuery(this).toggleClass("ui-icon-minusthick");
		jQuery(this).parents("."+area+"portlet:first").remove();
	});

	jQuery("."+area+"Grid"+area).sortable({
		connectWith: '.'+area+'Grid'+area,
		update: function(event, ui){
			jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
			formSetup_columSave();
			viewSetup_columSave();
		}
	});
	jQuery(".formGrid"+area).disableSelection();

	//dr_sorter();
}

function dr_addTab(area){

 // number = jQuery('.sectionBreak').length;
	number=Math.floor(Math.random()*99999);
	jQuery('#fieldTray'+area).prepend('<div style="padding: 3px;" class="list_row4 table_sorter tab '+area+'portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="tab_tab'+number+'"><div class="'+area+'portlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="data_report/arrow_out.png" style="-moz-user-select: none;"/>&nbsp;<input type="text" class="tabTitle" name="Data[Content][_Tab][_tab'+number+'][Title]" /><span class="ui-icon ui-icon-close"></span></div><input class="layOut'+area+' positioning" type="hidden" name="_tab'+number+'" id="tab_'+number+'" value="1"/></div>');
	jQuery("."+area+"portlet-header .ui-icon").click(function() {
		jQuery(this).toggleClass("ui-icon-minusthick");
		jQuery(this).parents("."+area+"portlet:first").remove();
	});

	jQuery("."+area+"Grid"+area).sortable({
		connectWith: '.'+area+'Grid'+area,
		update: function(event, ui){
			jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id')+'_'+jQuery(this).parent().css('width'));
			formSetup_columSave();
			viewSetup_columSave();
		}
	});
	jQuery(".formGrid"+area).disableSelection();

	//dr_sorter();
}

function dr_removeSectionBreak(el){
	jQuery('#'+el).remove();
	//dr_sorter();
	//alert('pi');
}
function dr_addPassbackField(){
	table = jQuery('#_main_table').val();
	ajaxCall('dr_loadPassbackFields',table, function(c){
		jQuery('#PassBack_FieldSelect').append(c);	
	});
}

function dr_sortReport(eid, field, dir){

	jQuery('#reportPanel_'+eid).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style="-moz-user-select: none; float:left;">close</span>Loading Data</div></div>')
	
	ajaxCall('dr_BuildReportGrid',eid, 0, field, dir, function(dta){
		jQuery('#reportPanel_'+eid).html(dta);
		df_loadOutScripts();
	});
}
function dr_goToPage(eid, page, global){

	if(page == false){
		if(global==undefined){
			jQuery('#reportPanel_'+eid).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style="-moz-user-select: none; float:left;">close</span>Loading Data</div></div>')
			ajaxCall('dr_BuildReportGrid',eid, function(dta){
				jQuery('#reportPanel_'+eid).html(dta);
				df_loadOutScripts();
			});
		}else{
			jQuery('.data_report_Table').each(function(g){
				report = this.id.split("_")[2];
				jQuery('#reportPanel_'+report).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+report+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style="-moz-user-select: none; float:left;">close</span>Loading Data</div></div>')			
				dr_reloadData(report);
			});
		}
	}else{
		jQuery('#reportPanel_'+eid).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style="-moz-user-select: none; float:left;">close</span>Loading Data</div></div>');
		ajaxCall('dr_BuildReportGrid',eid, page, function(dta){
			jQuery('#reportPanel_'+eid).html(dta);
			df_loadOutScripts();
		});
	}
	df_loadOutScripts();
}

function dr_reloadData(eid){
	ajaxCall('dr_BuildReportGrid',eid, function(x){
		jQuery('#reportPanel_'+eid).html(x);
		df_loadOutScripts();
	});
}

function dr_selectAll(id){
	jQuery('.itemRow_'+id).addClass('highlight');
}
function dr_deSelectAll(id){
	jQuery('.itemRow_'+id).removeClass('highlight');
}

function dr_fetchPrimSetup(table){
	jQuery('#fieldTray').html('');
	jQuery('.formColumn').html('');
	table = jQuery('#'+table).val();
	jQuery('#FieldList_left').html('');
	jQuery('#FieldList_right').html('');
	jQuery('#referenceSetup').html('Searching insert reference...');
	jQuery('#PassBack_FieldSelect').html('Loading Fields...');
	ajaxCall('df_searchReferenceForm',table, function(v){
		jQuery('#PassBack_FieldSelect').html('');
		if(v == false){
			jQuery('#referenceSetup').html('');
			df_fetchreportSetp(table, false);
			dr_addPassbackField(table);
		}else{
			jQuery('#referenceSetup').html(v);										 
		}
	});
}

function df_fetchreportSetp(table, eid){
	jQuery('#FieldList_Main').html('Loading Fields');									 
	jQuery('#Return_FieldSelect').html('Loading Fields');									 
	jQuery('#sortFieldSelect').html('Loading Fields');									 
	ajaxCall('df_tableReportSetup',table, eid, function(c){
		jQuery('#FieldList_Main').html(c);
		dr_sorter();
	});	
	ajaxCall('df_loadReturnFields',table, function(r){
		jQuery('#Return_FieldSelect').html(r);
	});
	ajaxCall('df_loadSortFields',table, function(s){
		jQuery('#sortFieldSelect').html(s);									  
	});
}

function dr_sorter(){
		jQuery(".columnSorter").sortable({
		
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			connectWith: '.columnSorter',
			stop: function(p){
				//alert(columns);
				dr_columSave();
			}
		
		});
		dr_columSave();
}
function dr_columSave(){
	var columns = new Array();
	var index = 0;
	jQuery('.columnSorter').each(function(l){
		list = this.id;
		if(list != ''){
			colSer = jQuery(this).sortable('serialize', {'key' : this.id+'[]'});
			columns[index] = colSer
			index++;
		}
	});
	output = columns.join('&');
	jQuery('#_FormLayout').val(output);
}
function dr_enableDisableField(field){
	if(field.checked == false){
		jQuery('#Fieldtype_'+field.id).attr('disabled', 'disabled');
	}else{
		jQuery('#Fieldtype_'+field.id).removeAttr('disabled');	
	}
	
}

function df_valuedFilter(id){
    jQuery('#ExtraSetting_'+id).html('Loading Filter Setup');
	
	ajaxCall('df_listTables',id, 'dr_loadvaluefilteredfields', function(tf){
		jQuery('#ExtraSetting_'+id).html(' => '+tf+'<span id="extrasettings_'+id+'"></span>');
	});
	
	
	//ajaxCall('df_vlauedFilterSetup',id, function(tf){
	//	jQuery('#ExtraSetting_'+id).html(' => '+tf+'<span id="extrasettings_'+id+'"></span>');
	//});
}

function df_loadEntry(id, eid, ismodal){

	if(jQuery("#ui-jsDialog-"+id+"-"+eid+"").length == 1){
		jQuery("#ui-jsDialog-"+id+"-"+eid+"").remove();
	}
	jQuery('body').prepend('<div id="ui-jsDialog-'+id+'-'+eid+'" title="Loading"><p>Loading Entry</p></div>');
	jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog({
			position: 'center',
			autoResize: true,
			minWidth: 200,
                        modal: ismodal,
			buttons: {
				'Close': function() {jQuery(this).dialog("close"); }
			},
											
			open: function(event, ui) {
				ajaxCall('di_showItem', eid, id, function(c){
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'title', c.title);	
					if(c.edit == true){
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'buttons', {
						'Close': function() {
						jQuery(this).dialog("close"); },
						'Edit': function() {
							dr_BuildUpDateForm(eid, id); jQuery(this).dialog('close');
							}
					});
					//}else{
					//	jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'buttons', { 'Close': function() {
					//		jQuery(this).dialog("close"); }
					//	});
					}
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").html(c.html);
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'width', parseFloat(c.width));
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'position', 'center');

					// add tile dialog tool
					if(jQuery('.ui-dialog').length == 2){
						jQuery('.ui-dialog-tile').show();
					}
					
					df_loadOutScripts();
				});
			},
			close: function(event, ui) {
				if(jQuery('.ui-dialog').length <= 1){
					jQuery('.ui-dialog-tile').hide();
				}
				jQuery("#ui-jsDialog-"+id+"-"+eid+"").remove();	
			}										
		});
}

function dr_BuildUpDateForm(eid, id){


	if(jQuery("#ui-jsDialog-"+id+"-"+eid+"").length == 1){
		jQuery("#ui-jsDialog-"+id+"-"+eid+"").remove();
	}
	jQuery('body').prepend('<div id="ui-jsDialog-'+id+'-'+eid+'" title="Loading"><p>Loading Entry</p></div>');
	jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog({
			autoResize: true,
			modal: true,
			buttons: {
				'Cancel': function() {jQuery(this).dialog("close"); }
			},
			dragStart: function(event, ui) {
			 	jQuery(".formError").remove();
			},
			open: function(event, ui) {
				ajaxCall('dr_BuildUpDateForm',eid, id, function(c){
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'title', c.title);	
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'buttons', {
						'Close': function() {
							jQuery(this).dialog("close");
						},
						'Save': function() {
							//dr_BuildUpDateForm(eid, id); jQuery(this).dialog('close');
							jQuery("#data_form_"+eid+"").submit();
						}
					});
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").html(c.html);	
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'width', parseFloat(c.width));
					jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'position', 'center');
					df_loadOutScripts();
				});
			},
			close: function(event, ui) {
				jQuery(".formError").remove();
				jQuery("#ui-jsDialog-"+id+"-"+eid+"").remove();	
			}										
		});
}

function dr_fetchPageElements(el){
	jQuery('#MatchingElements').html('Loading Elements');
	ajaxCall('dr_loadPageElements',el, function(x){
		jQuery('#MatchingElements').html(x);
	});
}

function dr_addTotalsField(){
	main = jQuery('#_main_table').val();
	if(main == ''){
		jQuery('#totalsListStatus').html('Select a table first');
		return;
	}
	ajaxCall('dr_addTotalsField',main, function(x){
		jQuery('#totalsListStatus').html('');
		jQuery('#totalsList').append(x);							 
	});
}

function dr_exportResults(q, r){
	ajaxCall('dr_db2csv',q, r, function(f){
		eval(f);
	});
}


function dr_deleteEntries(EID){
	var items = jQuery('.itemRow_'+EID+'.highlight');
	if(items.length > 0){
		if(confirm("Are you sure you want to delete the selected items?")){
			var itemlist=new Array();
			for(i=0;i<items.length;i++){
				itemlist[i] = jQuery(items[i]).attr('ref');
			}
			jQuery('#reportPanel_'+EID).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+EID+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;" id="reportpanel_block_message_'+EID+'"><span class="ui-icon ui-icon-notice" unselectable="on" style="-moz-user-select: none; float:left;">close</span>Deleting Items</div></div>')
			
			ajaxCall('df_deleteEntries',EID, itemlist.join('|||'), function(x){
				jQuery('#reportpanel_block_message_'+EID).html('<span class="ui-icon ui-icon-check" unselectable="on" style="-moz-user-select: none; float:left;">close</span>'+x+'</div></div>');																   
				setTimeout('dr_goToPage(\''+EID+'\', jQuery(\'#pageJump_'+EID+'\').val())', 2000);
				//df_dialog(x);
			});
		}
	}
}

function dr_deleteItem(EID, ID){
    if(confirm("Are you sure you want to delete this entry?")){
        ajaxCall('df_deleteEntries',EID, ID, function(x){
                jQuery('#reportpanel_block_message_'+EID).html('<span class="ui-icon ui-icon-check" unselectable="on" style="-moz-user-select: none; float:left;">close</span>'+x+'</div></div>');
                setTimeout('dr_goToPage(\''+EID+'\', jQuery(\'#pageJump_'+EID+'\').val())', 2000);
                //df_dialog(x);
            });  
    }
}

// Tile Dialogs

function dialog_tile(){
	top = 20;
	left = 20;
	maxWidth = jQuery(window).width()-20;
	jQuery('.ui-dialog').each(function(){
		if(left >= maxWidth){
			top = top+jQuery(this).height()+10;
			left = 20;
		}
		jQuery(this).css('left', left);
		jQuery(this).css('top', top);
		
		left = left+jQuery(this).width()+10;
	})
	
	
}



function toggle(foo) {
	jQuery("#"+foo).animate({"height": "toggle"}, {"duration": 200});				   
}











