jQuery(document).ready(function() {
    jQuery('#akvodata-settings').submit(function() {
        var postURL = jQuery(this).attr('action');
        var serializedSettings = jQuery(this).serialize();
        
        //Post the serialized form data into the action URL in the form
        jQuery.post(postURL+'?action=add', serializedSettings,function(data, textStatus, jqXHR){
            
            document.location.href='?page=akvodata';
        }).error(function(e) { 
            document.location.href='?page=akvodata';
    });
 
        //Update the form stated to 'Updated!'
        jQuery('.update-status').text('Updated!');
        
        //We do not want our form submitted so we return false
        return false;
    });
 
    //Remove the 'Updated!' status when an input is focused upon
    jQuery('#akvodata-settings input').focus(function() {
         jQuery('.update-status').text('');
    });
    
    jQuery('.cAdelAkvoData').click(function(e){
        var formdata={};
        formdata.id = jQuery(this).attr('rel');
        jQuery(this).parents('tr[rel="'+formdata.id+'"]').fadeOut('fast');
        jQuery.post(jQuery('#iInputUrl').val()+'?action=delete', formdata).error(function(){});
        return false;
    })
 
});