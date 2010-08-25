//<script>

    function vodashop_sync_Fetch(el, field, process, check){
	num = jQuery('#'+el+'_'+field).val();

	jQuery('body').prepend('<div id="ui-jsDialog-'+el+'_'+field+'" title="Loading"><p>Loading Entry</p></div>');

	jQuery("#ui-jsDialog-"+el+"_"+field+"").dialog({
            position: 'center',
            autoResize: true,
            minWidth: 200,
            buttons: {
                'Close': function() {jQuery(this).dialog("close"); }
            },

            open: function(event, ui) {
                ajaxCall('vodashop_syncMSISDN',num, process, function(n){
                    if(n.status == 0){
                        alert(n.result);
                        jQuery("#ui-jsDialog-"+el+"_"+field+"").remove();
                        return;
                    }
                    //alert(n.result);
                    //Dialog.setContent('<div>'+n.phoneuser+'</div>');
                    //Dialog.setTitle(c.title);
                    //Dialog.center('x');
                    //Dialog.center('y');
                    jQuery("#ui-jsDialog-"+el+"_"+field+"").remove();
                    if(check == true){
                        alert(n.error);
                    }

                    if(n.status == '1'){
                        jQuery('#'+el+'_Title').val(n.title);
                        jQuery('#'+el+'_FirstName').val(n.name);
                        jQuery('#'+el+'_Surname').val(n.surname);
                        jQuery('#'+el+'_UpgradeDueDate').val(n.upgradedate);
                        jQuery('#'+el+'_ReferalNumber').val(n.referalnumber);
                        jQuery('#'+el+'_PhoneUser').val(n.phoneuser);
                        jQuery('#'+el+'_IdNumber').val(n.idnumber);
                        jQuery('#'+el+'_Birthday').val(n.birthday);
                        jQuery('#'+el+'_DealerID').val(n.dealerid);
                        jQuery('#'+el+'_AverageSpenditure').val(n.spend);
                        jQuery('#'+el+'_Package').val(n.package);
                        jQuery('#'+el+'_LastSync_date').val(n.lastsyncdate);
                        jQuery('#'+el+'_LastSync_time').val(n.lastsynctime);
                        jQuery('#'+el+'_AccountType').val(n.type);

                        //lastsync
                    }else{
                        alert(n.error);
                    }

                    df_loadOutScripts();
                    });
                },
                close: function(event,ui){
                    if(jQuery('.ui-dialog').length <= 1){
                        jQuery('.ui-dialog-tile').hide();
                    }
                    jQuery("#ui-jsDialog-"+el+"_"+field+"").remove();
                }
            });




            /*	var Dialog = new Boxy('<p><img src="system/dais/plugins/data_form/loading.gif" width="16" height="16" alt="loading" align="absmiddle" /> Loading Data for '+num+' from Vodacom...</p>', {
						  	title: 'Vodacom Sync',
							modal: true,
							unloadOnHide: true,
							//closeable: false,
							afterShow: function(){
								x_vodashop_syncMSISDN(num, process, function(n){
								if(n.status == 0){
									//alert(n.result);
									Dialog.hide();
									return;
								}
								//alert(n.result);
								//Dialog.setContent('<div>'+n.phoneuser+'</div>');
								//Dialog.setTitle(c.title);
								//Dialog.center('x');
								//Dialog.center('y');
								Dialog.hide()

								$('#'+el+'_Title').val(n.title);
								$('#'+el+'_FirstName').val(n.name);
								$('#'+el+'_Surname').val(n.surname);
								$('#'+el+'_UpgradeDueDate').val(n.upgradedate);
								$('#'+el+'_ReferalNumber').val(n.referalnumber);
								$('#'+el+'_PhoneUser').val(n.phoneuser);
								$('#'+el+'_IdNumber').val(n.idnumber);
								$('#'+el+'_Birthday').val(n.birthday);
								$('#'+el+'_DealerID').val(n.dealerid);


								df_loadOutScripts();
							});
						}});

             */

        }

        jQuery(document).ready(function(){
            jQuery('#entry_113_CustomerClassification').bind('change', function(){
		if(jQuery(this).val() == 3){
                    x_vodacom_smsupgradedate($('#entry_113_UpgradeDueDate').val(), function(f){
                        jQuery('#entry_113_UpgradeSMSDate').val(f);
                    });
		}else{
                    jQuery('#entry_113_UpgradeSMSDate').val(jQuery('#entry_113_UpgradeDueDate').val());
		}
            });



            jQuery('#entry_82_CustomerClassification').bind('change', function(){
		if(jQuery(this).val() == 3){
                    ajaxCall('vodacom_smsupgradedate', jQuery('#entry_82_UpgradeDueDate').val(), function(f){
                        jQuery('#entry_82_UpgradeSMSDate').val(f);
                    });
		}else{
                    jQuery('#entry_82_UpgradeSMSDate').val(jQuery('#entry_82_UpgradeDueDate').val());
		}
            });


            jQuery('#entry_75_CustomerClassification').bind('change', function(){
		if(jQuery(this).val() == 3){
                    ajaxCall('vodacom_smsupgradedate', jQuery('#entry_75_UpgradeDueDate').val(), function(f){
                        jQuery('#entry_75_UpgradeSMSDate').val(f);
                    });
		}else{
                    jQuery('#entry_75_UpgradeSMSDate').val(jQuery('#entry_75_UpgradeDueDate').val());
		}
            });


            jQuery('.vodashopScanner').bind('keypress', function(a){
		if(a.which == 13){
                    //alert($(this).val());
                    idcount = $(this).attr('ref');
                    code = $(this).val();
                    fid = $(this).attr('rel');
                    idcount++;
                    element = $(this);
                    element.val('saving....');
                    ajaxCall('vodashop_scanned',code, fid, function(p){
                        if(p.status == 0){
                            element.val(p.message);
                            $('#scan_'+idcount).focus();
                            element.attr('disabled','disabled');
                        }else{
                            element.val('');
                            alert(p.message);
                        }
                    });
		}
            });



        });