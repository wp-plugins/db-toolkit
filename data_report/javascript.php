//<script>

    function app_dockApp(app){

        ajaxCall('app_dockApp', app, function(a){
            if(a.docked == true){
                jQuery('#app_'+app).removeClass('button');
                jQuery('#app_'+app).addClass('button-primary');
            }
            if(a.docked == false){
                jQuery('#app_'+app).addClass('button');
                jQuery('#app_'+app).removeClass('button-primary');
            }
            // alter Meny
            jQuery.ajax({
              url: "admin.php?page=Database_Toolkit_Welcome",
              context: document.body,
              success: function(data){
                jQuery('#adminmenu').html(jQuery('#adminmenu', data).html());
              }
            });
            //jQuery('#adminmenu').html(a.html);
        })
    }

    function app_saveDesc(){

        
        
        text = jQuery('.appConfigPanel').serialize();
        


        jQuery('.appConfigPanel').attr('disabled', true);
        ajaxCall('app_SaveDesc', text, function(a){
            alert(a);
            jQuery('.appConfigPanel').removeAttr('disabled');
        })
        
    }

    function dt_saveInterface(str){

        jQuery('.save_bar_top').css('position', 'relative').prepend('<div class="ui-overlay" id="newInterfaceForm_overlay"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;">close</span>Saving Data</div></div>')
        ajaxCall('dt_saveInterface', str, function(o){
            jQuery('#interfaceID').val(o);
            jQuery('#newInterfaceForm_overlay').hide('slow', function(){
                jQuery(this).remove();
            });
        });
    }

    function dbt_sendError(interface, errordata){
        //alert(errordata);
        ajaxCall('dbt_sendError', interface, errordata, function(i){

            if(i){
                alert('Error Report Sent!')
                jQuery('#interfaceError').fadeOut('5000');
            }else{

                alert('Error sending report. Your server might not be configured to use PHP\'s mail() function. Sorry:)')
                jQuery('#interfaceError').fadeOut('5000');
            }

        })
    }

    function addRowTemplate(){

        // add notification
        ajaxCall('dr_addListRowTemplate', function(t){
            jQuery('#rowTemplateHolder').append(t);
        })
        
    }

    function dr_addListFieldTemplate(field){
        
        ajaxCall('dr_addListFieldTemplate', field, function(f){
             jQuery('#fieldTemplateHolder').append(f);
        })
             
        
    }


    function dr_exportReport(url, eid, isGlobal){

        jQuery('.export').addClass('active');
        jQuery('.export').removeClass('export');
        if(isGlobal){
            chartData = jQuery('.chartData').serializeArray();
        }else{
            chartData = jQuery('#chartData_'+eid).serializeArray();
        }

        if(chartData.length > 0){
            ajaxCall('dr_exportChartImage',chartData, function(d){
                jQuery('.active').addClass('export');
                jQuery('.active').removeClass('active');
                window.location.replace(url);
            })
        }else{
                jQuery('.active').addClass('export');
                jQuery('.active').removeClass('active');
                window.location.replace(url);
        }
        return;
    }

    function df_addPRocess(process){
        table = jQuery('#_main_table').val();
        jQuery('#.root_item a.parent').html('<img src="../wp-content/plugins/db-toolkit/images/indicator.gif" align="absmiddle" /> Loading Processor...')
        jQuery('.root_item ul').hide();        
        ajaxCall('df_addProcess', process, table, function(p){
            jQuery('#formProcessList').append(p);
            jQuery('#.root_item a.parent').html('<strong>Processors</strong>');
            df_loadOutScripts();
        });
    }
    function df_addViewProcess(process){
        table = jQuery('#_main_table').val();
        jQuery('#.root_item a.parent').html('<img src="../wp-content/plugins/db-toolkit/images/indicator.gif" align="absmiddle" /> Loading Processor...')
        jQuery('.root_item ul').hide();
        ajaxCall('df_addViewProcess', process, table, function(p){
            jQuery('#viewProcessList').append(p);
            jQuery('#.root_item a.parent').html('<strong>Processors</strong>');
            df_loadOutScripts();
        });
    }

    function dt_addLibrary(){
        ajaxCall('dais_addJSLibrary', function(i){
            jQuery('#addonLibrary').append(i);
        })

    }
    function dt_addCSSLibrary(){
        ajaxCall('dais_addCSSLibrary', function(i){
            jQuery('#addonCSSLibrary').append(i);
        })

    }

    function dt_saveFilterSet(EID){
        //alert(EID);
	jQuery('body').prepend('<div id="ui-jsDialog-filterlock" title="Save Filter Set"><p>Loading Panel</p></div>');
	jQuery("#ui-jsDialog-filterlock").dialog({
            position: 'center',
            autoResize: true,
            width: 330,
            modal: true,
            buttons: {
                'Close': function() {jQuery(this).dialog("close"); },
                'Save': function() {

                    ajaxCall('dt_saveFilterLock', EID, jQuery('#ui-jsDialog-filterlock form').serializeArray(), function(c){
                        if(c == true){

                            jQuery('#setFilters_'+EID).submit();
                        }else{
                            jQuery("#ui-jsDialog-filterlock").html('<form>'+c+'</form>');
                            jQuery("#ui-jsDialog-filterlock").dialog('option', 'position', 'center');
                            df_loadOutScripts();
                        }
                    });

                }
            },

            open: function(event, ui) {
                ajaxCall('dt_saveFilterLock', EID, function(c){
                    jQuery("#ui-jsDialog-filterlock").html('<form>'+c+'</form>');
                    jQuery("#ui-jsDialog-filterlock").dialog('option', 'position', 'center');
                    df_loadOutScripts();
                });
            },
            close: function(event, ui) {
                jQuery("#ui-jsDialog-filterlock").remove();
            }
        });


    }

    function dt_addNewApp(){
        sw = jQuery('#_Application_New').css('display');
        if(sw == 'none'){
            jQuery('#_Application_New').show().removeAttr('disabled').focus();
            jQuery('#appsSelector').hide().attr('disabled', 'disabled');
            jQuery('#addAppB').html('Cancel');
        }else{
            jQuery('#_Application_New').hide().attr('disabled', 'disabled');
            jQuery('#appsSelector').show().removeAttr('disabled').focus();
            jQuery('#addAppB').html('Add New');
        }
        return false;
    }

    function dt_iconChooser(icon){
        src = jQuery('#interfaceIconPreview').attr('src');
        parts = src.split('/');

	jQuery('body').prepend('<div id="ui-jsDialog-iconchooser" title="Loading"><p>Loading Icons</p></div>');
	jQuery("#ui-jsDialog-iconchooser").dialog({
            position: 'center',
            autoResize: true,
            width: 390,
            modal: true,
            buttons: {
                'Close': function() {jQuery(this).dialog("close"); },
                'Select': function() {
                    newIcon = jQuery('input[name=selectedIcon]:checked').val();
                    //alert(newIcon);
                    jQuery('#interfaceIconPreview').attr("src", newIcon);
                    iconparts = newIcon.split('/');
                    jQuery('#_Application_Icon').val(iconparts[iconparts.length-1]);
                    jQuery(this).dialog("close");
                }
            },

            open: function(event, ui) {
                ajaxCall('dt_iconSelector',parts[parts.length-1], function(c){
                    jQuery("#ui-jsDialog-iconchooser").html(c);
                    jQuery("#ui-jsDialog-iconchooser").dialog('option', 'position', 'center');
                    df_loadOutScripts();
                });
            },
            close: function(event, ui) {
                jQuery("#ui-jsDialog-iconchooser").remove();
            }
        });
    }

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
	jQuery('#fieldTray'+area).prepend('<div style="padding: 3px;" class="list_row4 table_sorter sectionBreak '+area+'portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="SectionBreak_SectionBreak'+number+'"><div class="'+area+'portlet-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="data_report/arrow_out.png" style=""/><strong>Title:</strong><input type="textfield" class="sectionTitle" name="Data[Content][_SectionBreak][_SectionBreak'+number+'][Title]" /><span class="ui-icon ui-icon-close"></span></div><div style="padding:3px;"><strong>Caption:</strong> <input type="textfield" class="sectionTitle" name="Data[Content][_SectionBreak][_SectionBreak'+number+'][Caption]" /></div><input class="layOut'+area+' positioning" type="hidden" name="_SectionBreak'+number+'" id="section_'+number+'" value="1"/></div>');
	jQuery("."+area+"portlet-header .ui-icon").click(function() {
            jQuery(this).toggleClass("ui-icon-minusthick");
            jQuery(this).parents("."+area+"portlet:first").remove();
	});
        return;

    }

    function dr_addTab(area){

        // number = jQuery('.sectionBreak').length;
	number=Math.floor(Math.random()*99999);
	jQuery('#fieldTray'+area).prepend('<div style="padding: 3px;" class="list_row4 table_sorter tab '+area+'portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="tab_tab'+number+'"><div class="'+area+'portlet-header ui-widget-header ui-corner-all"><img align="absmiddle" class="OrderSorter" src="data_report/arrow_out.png" style=""/>&nbsp;<input type="text" class="tabTitle" name="Data[Content][_Tab][_tab'+number+'][Title]" /><span class="ui-icon ui-icon-close"></span></div><input class="layOut'+area+' positioning" type="hidden" name="_tab'+number+'" id="tab_'+number+'" value="1"/></div>');
	jQuery("."+area+"portlet-header .ui-icon").click(function() {
            jQuery(this).toggleClass("ui-icon-minusthick");
            jQuery(this).parents("."+area+"portlet:first").remove();
	});

	jQuery("."+area+"Grid"+area).sortable({
            connectWith: '.'+area+'Grid'+area,
            update: function(event, ui){
                jQuery(this).find(".positioning").val(jQuery(this).parent().attr('id'));
                formSetup_columSave();
                viewSetup_columSave();
            }
	});
	//jQuery(".formGrid"+area).disableSelection();

	//dr_sorter();
    }

    function dr_removeSectionBreak(el){
	jQuery('#'+el).remove();
	//dr_sorter();
	//alert('pi');
    }
    function dr_addPassbackField(){
	table = jQuery('#_main_table').val();
        remove = 0;
        if(jQuery('.passBackField').length >= 1){
            remove = 1;
        }
        
	ajaxCall('dr_loadPassbackFields', table, 'none', 0, remove, function(c){
            jQuery('#PassBack_FieldSelect').append(c);
	});
    }

    function dr_sortReport(eid, field, dir){

	jQuery('#reportPanel_'+eid).css('position', 'relative').prepend('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;">close</span>Loading Data</div></div>')

	ajaxCall('dr_BuildReportGrid',eid, 0, field, dir, function(dta){
            jQuery('#reportPanel_'+eid).html(dta);
            df_loadOutScripts();
	});
    }
    function dr_goToPage(eid, page, global){
	if(page == false){
            if(global==undefined){
                jQuery('#reportPanel_'+eid).css('position', 'relative').prepend('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;">close</span>Loading Data</div></div>')
                
                ajaxCall('dr_BuildReportGrid',eid, function(dta){
                    jQuery('#reportPanel_'+eid).html(dta);
                    //df_loadOutScripts();
                });
            }else{
                jQuery('.data_report_Table').each(function(g){
                    report = this.id.split("_")[2];
                    jQuery('#reportPanel_'+report).css('position', 'relative').prepend('<div class="ui-overlay" id="reportpanel_block_'+report+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;">close</span>Loading Data</div></div>')
                    dr_reloadData(report);
                });
            }
	}else{
            jQuery('#reportPanel_'+eid).css('position', 'relative').prepend('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;">close</span>Loading Data</div></div>');
            ajaxCall('dr_BuildReportGrid',eid, page, function(dta){
                jQuery('#reportPanel_'+eid).html(dta);
                //df_loadOutScripts();
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
        jQuery('#addFieldButton').show();
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
                dr_addPassbackField();
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
            handle: 'h3',
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

    function df_loadEntry(rid, eid, ismodal){

        id = Math.floor(Math.random()*9999999);

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
                ajaxCall('di_showItem', eid, rid, function(c){
                    jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'title', c.title);
                    if(c.edit == true){
                        jQuery("#ui-jsDialog-"+id+"-"+eid+"").dialog('option', 'buttons', {
                            'Close': function() {
                                jQuery(this).dialog("close"); },
                            'Edit': function() {
                                jQuery(this).dialog('close');
                                dr_BuildUpDateForm(eid, rid);
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

    function dr_pushResult(eid, str){

        jQuery('#reportPanel_'+eid).css('position', 'relative').prepend('<div class="ui-overlay" id="reportpanel_block_'+eid+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0; z-index:9999999;"><span class="ui-icon ui-icon-arrowrefresh-1-w" unselectable="on" style=" float:left;"></span>Loading Data</div></div>');
        //jQuery('#'+eid+'_wrapper').html('<div class="loading">loading</div>');
        ajaxCall('dr_callInterface',eid, str, function(h){
            jQuery('#report_tools_'+eid).remove();
            jQuery('#reportPanel_'+eid).replaceWith(h);
            df_loadOutScripts();

        });
    }

    function dr_BuildUpDateForm(eid, rid, ismodal){
    
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
                
        id = rid;//Math.floor(Math.random()*9999999);

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
                ajaxCall('dr_BuildUpDateForm',eid, rid, function(c){
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
                    //jQuery("#ui-jsDialog-"+id+"-"+eid+" select, #ui-jsDialog-"+id+"-"+eid+" input:checkbox, #ui-jsDialog-"+id+"-"+eid+" input:radio, #ui-jsDialog-"+id+"-"+eid+" input:file").uniform();
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
                jQuery('#reportPanel_'+EID).css('position', 'relative').append('<div class="ui-overlay" id="reportpanel_block_'+EID+'"><div class="ui-widget-overlay ui-corner-all"></div><div style="position:absolute; padding:6px; left:0; top:0;" id="reportpanel_block_message_'+EID+'"><span class="ui-icon ui-icon-notice" unselectable="on" style=" float:left;">close</span>Deleting Items</div></div>')

                ajaxCall('df_deleteEntries',EID, itemlist.join('|||'), function(x){
                    jQuery('#reportpanel_block_message_'+EID).html('<span class="ui-icon ui-icon-check" unselectable="on" style=" float:left;">close</span>'+x+'</div></div>');
                    setTimeout('dr_goToPage(\''+EID+'\', jQuery(\'#pageJump_'+EID+'\').val())', 2000);
                    //df_dialog(x);
                });
            }
	}
    }

    function dr_deleteItem(EID, ID){
        if(confirm("Are you sure you want to delete this entry?")){
            ajaxCall('df_deleteEntries',EID, ID, function(x){
                jQuery('#reportpanel_block_message_'+EID).html('<span class="ui-icon ui-icon-check" unselectable="on" style=" float:left;">close</span>'+x+'</div></div>');
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

    function dr_loadDataSource(url){        

        jQuery('#_dataSourceView').html('<img src="../wp-content/plugins/db-toolkit/images/indicator.gif" align="absmiddle" /> Loading Data...');
        ajaxCall('dr_dataSourceMapping', url, function(f){
            jQuery('#_dataSourceView').html(f);
            df_loadOutScripts();
        });

    }

    function dr_loadFieldMapping(url, root, table){

        jQuery('#_dataFieldMapView').html('<img src="../wp-content/plugins/db-toolkit/images/indicator.gif" align="absmiddle" /> Loading Data...');
        ajaxCall('dr_loadFieldMapping', url, root, table, function(i){
           jQuery('#_dataFieldMapView').html(i);
        });
    }




    function dt_addNewTable(){

        id = Math.floor(Math.random()*9999999);

	if(jQuery("#ui-jsDialog-"+id).length == 1){
            jQuery("#ui-jsDialog-"+id).remove();
	}
	jQuery('body').prepend('<div id="ui-jsDialog-'+id+'" title="Create Table"><p><label>Name</lable><input type="text" id="'+id+'_newTable" value="" style="width:98%;" /></p></div>');
	jQuery("#ui-jsDialog-"+id).dialog({
            position: 'center',
            title: 'New Table',
            autoResize: true,
            minWidth: 200,
            modal: true,
            buttons: {
                'Cancel': function() {jQuery(this).dialog("close"); },
                'Create': function() {
                    if(jQuery('#'+id+'_newTable').val().length <= 0){
                        alert('Please give your table a name.');
                    }else{
                        jQuery('#'+id+'_newTable').attr('disabled', 'disabled');
                        ajaxCall('dt_buildNewTable', jQuery('#'+id+'_newTable').val(), function(v){
                            if(v.error){
                                alert(v.error);
                                jQuery('#'+id+'_newTable').removeAttr('disabled');
                            }else{
                                jQuery('#mainTableSelector').html(v.html);
                                dr_fetchPrimSetup('_main_table');

                                jQuery('#ui-jsDialog-'+id).dialog("close");
                            }

                        })
                    }
                }
            },
            close: function(event, ui) {
                jQuery("#ui-jsDialog-"+id).remove();
            }
        });

    }


    function dr_addField(){

        id = Math.floor(Math.random()*9999999);

	if(jQuery("#ui-jsDialog-"+id).length == 1){
            jQuery("#ui-jsDialog-"+id).remove();
	}
	jQuery('body').prepend('<div id="ui-jsDialog-'+id+'" title="Add Field"><p><label>Name</lable><input type="text" id="'+id+'_newField" value="" style="width:98%;" /></p></div>');
	jQuery("#ui-jsDialog-"+id).dialog({
            position: 'center',
            title: 'Add Field',
            autoResize: true,
            minWidth: 200,
            modal: true,
            buttons: {
                'Cancel': function() {jQuery(this).dialog("close"); },
                'Add Field': function() {
                    if(jQuery('#'+id+'_newField').val().length <= 0){
                        alert('Please give your field a name.');
                    }else{
                        jQuery('#'+id+'_newField').attr('disabled', 'disabled');
                        ajaxCall('dt_buildNewField', jQuery('#'+id+'_newField').val(), function(f){
                            if(f.error){
                                alert(f.error);
                                jQuery('#'+id+'_newField').removeAttr('disabled');
                            }else{
                                jQuery('#FieldList_Main').append(f);
                                jQuery('#ui-jsDialog-'+id).dialog("close");
                            }

                        })
                    }
                }
            },
            close: function(event, ui) {
                jQuery("#ui-jsDialog-"+id).remove();
            }
        });


    }

    function dr_rebuildApps(){


        jQuery('#content').append('<div id="rebuildStatus" style="padding:10px;">Rebuilding Application Indexes....</div>');
        jQuery('#dbt-apps').hide();

        ajaxCall('dr_rebuildApps', function(f){
            jQuery('#rebuildStatus').remove();
            // alter Meny
            jQuery.ajax({
              url: "admin.php?page=dbt_builder",
              context: document.body,
              success: function(data){
                jQuery('#dbt-apps').html(jQuery('#dbt-apps', data).html());
                jQuery('#dbt-apps').fadeIn(500);
              }
            });
        });

    }

    function dr_addApplication(){

        id = Math.floor(Math.random()*9999999);

	if(jQuery("#ui-jsDialog-"+id).length == 1){
            jQuery("#ui-jsDialog-"+id).remove();
	}
	jQuery('body').prepend('<div id="ui-jsDialog-'+id+'" title="New Application"><p><label>Name</lable><input type="text" id="'+id+'_newTitle" value="" style="width:98%;" /></p><p><label>Description</lable><textarea type="text" id="'+id+'_newDesc" style="width:98%; height:100px;" ></textarea></p></div>');
	jQuery("#ui-jsDialog-"+id).dialog({
            position: 'center',
            title: 'New Application',
            autoResize: true,
            minWidth: 200,
            modal: true,
            buttons: {
                'Cancel': function() {jQuery(this).dialog("close"); },
                'Create Application': function() {
                    if(jQuery('#'+id+'_newTitle').val().length <= 0){
                        alert('You need to give your application a name.');
                    }else{
                        jQuery('#'+id+'_newTitle').attr('disabled', 'disabled');
                        jQuery('#'+id+'_newDesc').attr('disabled', 'disabled');
                        ajaxCall('app_createApplication', jQuery('#'+id+'_newTitle').val(),jQuery('#'+id+'_newDesc').val(), function(f){
                            if(f.error){
                                alert(f.error);
                                jQuery('#'+id+'_newTitle').removeAttr('disabled');
                                jQuery('#'+id+'_newDesc').removeAttr('disabled');
                            }else{
                                jQuery('#FieldList_Main').append(f);
                                window.location = "admin.php?page=dbt_builder";
                                //location.reload();
                            }

                        })
                    }
                }
            },
            close: function(event, ui) {
                jQuery("#ui-jsDialog-"+id).remove();
            }
        });


    }



        function dbtMarketLogin(){

            user=jQuery('#dbt_user').val();
            pass=jQuery('#dbt_pass').val();
            jQuery('#loginStatus').html('');
            ajaxCall('app_marketLogin', user, pass, function(x){
                if(x.error){                    
                     if(x.error.errors.incorrect_password){
                        jQuery('#loginStatus').html(x.error.errors.incorrect_password[0]);
                     }
                     if(x.error.errors.invalid_username){                        
                        jQuery('#loginStatus').html(x.error.errors.invalid_username[0]);
                     }
                     return;
                }
                location.reload();
            })

        }

        function app_setLanding(app, intf){

            if(jQuery('#rdo_'+intf).attr('checked')){
                ajaxCall('app_setLanding', app, intf, function(){});
            }

        }
    