<script language="JavaScript">

jQuery(document).ready(function(){


    jQuery('#data_form_101').live('submit', function(){

        var goodToGo = true;
        item1 = jQuery('#entry_101_VSPItem').val();
        item2 = jQuery('#entry_101_NonVSPItem').val();
        item3 = jQuery('#entry_101_ForwardOrderItem').val();

        if(item1 == '' && item2 == ''&& item3 == ''){
            goodToGo = false;
        }

        if(goodToGo == false){
            alert("You cannot create a blank Order Item\nPlease select at least 1 item for the order");
            return false;
        }


    })
});

</script>