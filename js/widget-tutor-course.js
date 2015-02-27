function tutorCourseForm(box_id, user_id, course_id) {
	course_id = typeof course_id !== 'undefined' ? course_id : "";
	
	// Delete any existing instance of the form with the same name
	if($('#' + box_id).length ) {
		$('#' + box_id).remove();
	}
	
	var data = {
		box_id: box_id,
		render_mode: 'modal',
		ajaxMode: "true",
		fields: {
			'course' : {
				'display' : 'show'
			},
			'rate' : {
				'display' : 'show'
			}
		},
		buttons: { }
	};

	data['user_id'] = user_id;
	if (course_id != "") {
		console.log("Update mode");
		data['course_id'] = course_id;
	}
	
	// Generate the form
	$.ajax({  
	  type: "GET",  
	  url: FORMSPATH + "form_tutor_course.php",  
	  data: data,
	  dataType: 'json',
	  cache: false
	})
	.fail(function(result) {
		addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs." + result.responseText);
		alertWidget('display-alerts');
	})
	.done(function(result) {
		// Append the form as a modal dialog to the body
		$( "body" ).append(result['data']);
		$('#' + box_id).modal('show');
		
		// Link submission buttons
		$('#' + box_id + ' form').submit(function(e){ 
			var errorMessages = validateFormFields(box_id);
			if (errorMessages.length > 0) {
				$('#' + box_id + ' .dialog-alert').html("");
				$.each(errorMessages, function (idx, msg) {
					$('#' + box_id + ' .dialog-alert').append("<div class='alert alert-danger'>" + msg + "</div>");
				});	
			} else {
				if (course_id != "")
					updateTutorCourse(box_id, user_id, course_id);
				else
					createTutorCourse(box_id, user_id);
			}
			e.preventDefault();
		});    	
	});
}