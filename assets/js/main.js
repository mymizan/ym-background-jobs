var ymcode_start_job = function(){
	jQuery('#ymbg-start-stop-progress').show();

	jQuery.post(ajaxurl, {
		action: "ymbgs_start_jobs",
		steps: jQuery('input[name=ymbgs_count]').val()
	}, function(data){

		data = JSON.parse(data);

		if (data.status == 'failed') {
			alert('Job failed to start');
		} else {
			window.YMBJS = data;
			jQuery('#ymbg-start-stop-progress').append("Started. <br />");
			jQuery('#ymbg-start-stop-progress').append("Job ID: " + data.job_id + " <br />");
			jQuery('.ymbg-start-stop-btn').attr('value', "Stop");
			ymcode_run_job(data);
		}
	});
};

var ymcode_run_job = function(data) {
	window.YMBJS = data;

	jQuery.post(ajaxurl, {
		action: "ymbgs_process_jobs",
		job_id: window.YMBJS.job_id
	}, function(data){

		data = JSON.parse(data);

		if (data.status == 'failed') {
			alert(data.message);
		} else {

			if ( data.current_step >= parseInt( jQuery('input[name=ymbgs_count]').val()) || data.status == 'completed' ) {
				//stop ajax request
				jQuery('#ymbg-start-stop-progress').append("<span style='color:green;'> Job Completed.  </span> <br />");
				jQuery('.ymbg-start-stop-btn').attr('value', "Start");
				jQuery('.ymbg-start-stop-btn').removeAttr('disabled');
				window.YMBJS = null;
				return true;
			} else {
				jQuery('#ymbg-start-stop-progress').append("Step Running Now: " + data.current_step + "  <br />");
				ymcode_run_job(data);
			}
		}
	});
};

var ymcode_stop_job = function () {

	if ( confirm("Do you really want to stop the job?") )
	{
		jQuery.post(ajaxurl, {
			action: "ymbgs_stop_jobs",
			job_id: window.YMBJS.job_id
		}, function(data){

			data = JSON.parse(data);

			if (data.status == 'failed') {
				alert('Job failed to stop. It may have already stopped.');
			} else {
				window.YMBJS = null;
				jQuery('#ymbg-start-stop-progress').append("<span style='color:red;'> Stop Requested. </span> <br />");
				jQuery('.ymbg-start-stop-btn').attr('disabled', "disabled");
			}

		});

	}

};


jQuery( document ).ready(function($) {
    $('.ymbg-start-stop-btn').on('click', function(){

		if ( $(this).attr('value') == "Start" ) {
			ymcode_start_job();
		} else {
			ymcode_stop_job();
		}
	});
});