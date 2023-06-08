jQuery(document).ready(function () {
    
    
    jQuery(document).on('click', '.jobhunt-offer-job-btn', function () {
        
        var thisObj = jQuery(this);
        var label = jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html();
        jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html('<i class="icon-spinner8 icon-spin"></i>');
        var user_id = thisObj.data('user_id');
        var user_job_id = thisObj.data('user_job_id');
       
        jQuery.ajax({
            type: "POST",
            url: jobhunt_globals.ajax_url,
            data: 'action=job_offer_form&user_id=' + user_id+'&user_job_id=' + user_job_id,
            success: function (response) {
                jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html(label);
                jQuery(".jobhunt-job-offer-form").html(response);
                jQuery("#job-offer-form").modal('show');
            }
        });
        
    });
    
    

    jQuery(document).on('submit', '#job-offer-form-submission', function () {
        console.log('test');
        
        var form_data = new FormData(jQuery("#job-offer-form-submission")[0]);
        form_data.append('action', 'jobhunt_job_offer_submission');
        
        console.log(form_data);
        
        jQuery.ajax({
            url: jobhunt_globals.ajax_url,
            data: form_data,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: "json",
            success: function ( data ) {
                jQuery("#job-offer-form").modal('hide');
                if( response.type === 'success' ){
                    show_alert_msg(response.msg);
                }else{
                    show_error_alert_msg(response.msg);
                }
            }
        });
        
        return false;
        
    });
    
    jQuery(document).on('click', '.jobhunt-view-offer-job-btn', function () {
        
        var thisObj = jQuery(this);
        var label = jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html();
        jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html('<i class="icon-spinner8 icon-spin"></i>');
        var user_id = thisObj.data('user_id');
        var user_job_id = thisObj.data('user_job_id');
       
        jQuery.ajax({
            type: "POST",
            url: jobhunt_globals.ajax_url,
            data: 'action=job_offer_details&user_id=' + user_id+'&user_job_id=' + user_job_id,
            success: function (response) {
                jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html(label);
                jQuery(".jobhunt-job-offer-form").html(response);
                jQuery("#job-offer-form").modal('show');
            }
        });
        
    });
    
    
    jQuery(document).on('click', '.jobhunt-accept-offer-job-btn', function () {
        
        var thisObj = jQuery(this);
        var label = jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html();
        jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html('<i class="icon-spinner8 icon-spin"></i>');
        var user_id = thisObj.data('user_id');
        var user_job_id = thisObj.data('user_job_id');
       
        jQuery.ajax({
            type: "POST",
            url: jobhunt_globals.ajax_url,
            dataType: "json",
            data: 'action=job_offer_accept&user_id=' + user_id+'&user_job_id=' + user_job_id,
            success: function (response) {
                jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html(label);
                if( response.type === 'success' ){
                    show_alert_msg(response.msg);
                }else{
                    show_error_alert_msg(response.msg);
                }
            }
        });
        
    });
    
    jQuery(document).on('click', '.jobhunt-decline-offer-job-btn', function () {
        
        var thisObj = jQuery(this);
        var label = jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html();
        jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html('<i class="icon-spinner8 icon-spin"></i>');
        var user_id = thisObj.data('user_id');
        var user_job_id = thisObj.data('user_job_id');
       
        jQuery.ajax({
            type: "POST",
            url: jobhunt_globals.ajax_url,
            dataType: "json",
            data: 'action=job_offer_decline&user_id=' + user_id+'&user_job_id=' + user_job_id,
            success: function (response) {
                jQuery(thisObj).closest('.cs-downlod-sec').find('a.label').html(label);
                if( response.type === 'success' ){
                    show_alert_msg(response.msg);
                }else{
                    show_error_alert_msg(response.msg);
                }
            }
        });
        
    });
    
    
    

});
