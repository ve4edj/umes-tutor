<?php
require_once("../models/config.php");
require_once("../models/class.tutor.course.php");
require_once("../models/class.tutor.faculty.php");

// Request method: GET
$ajax = checkRequestMode("get");

if (!securePage(__FILE__)){
  apiReturnError($ajax);
}

// Sanitize input data
$get = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);

// Parameters: box_id, render_mode, user_id, [course_id]
// box_id: the desired name of the div that will contain the form.
// render_mode: modal or panel
// user_id: the id of the user
// course_id (optional): if specified, will load the relevant data for the course into the form.  Form will then be in "update" mode.

// Set up Valitron validator
$v = new Valitron\DefaultValidator($get);

$v->rule('required', 'box_id');
$v->rule('required', 'render_mode');
$v->rule('required', 'user_id');
$v->rule('in', 'render_mode', array('modal', 'panel'));
$v->rule('integer', 'user_id');
$v->rule('integer', 'course_id');

$v->setDefault('course_id', null);
$v->setDefault('fields', array());
$v->setDefault('buttons', array());

// Validate!
$v->validate();

// Process errors
if (count($v->errors()) > 0) {	
  foreach ($v->errors() as $idx => $error){
    addAlert("danger", $error);
  }
  apiReturnError($ajax, ACCOUNT_ROOT);    
} else {
    $get = $v->data();
}

if (!is_numeric($get['user_id']) || !userIdExists($get['user_id'])){
    addAlert("danger", lang("ACCOUNT_INVALID_USER_ID"));
    apiReturnError($ajax, getReferralPage());
}

// Create appropriate labels
if ($get['course_id']){
    if (!is_numeric($get['course_id'])){
        addAlert("danger", "Course ID was not numeric!");
        apiReturnError($ajax, getReferralPage());
    }
    $populate_fields = true;
    $button_submit_text = "Update";
    $target = "update_tutor_course.php";                                                // NEED TO CREATE THIS FILE
    $box_title = "Update Course Details";
} else {
    $populate_fields = false;
    $button_submit_text = "Add";
    $target = "create_tutor_course.php";                                                // NEED TO CREATE THIS FILE
    $box_title = "Add New Course";
}

// initialize a typeahead with courses
// if we're in update mode, set the typeahead to the current course

$data = array();
if ($populate_fields){
    $course = Course::getCourseByTutor($get['course_id'],$get['user_id']);
    $data["rate"] = $course->rate;
    $data["course"] = $course->toString(Faculty::getAll());
    if ($get['render_mode'] == "panel"){
        $box_title = $data["course"];
    }   
}

$fields_default = [
    'course' => [
        'type' => 'text',
        'label' => 'Course',
        'icon' => 'fa fa-graduation-cap',
        'validator' => [
            'minLength' => 1,
            'maxLength' => 25,
            'label' => 'Course'
        ],
        'placeholder' => 'Type to select a course'
    ],
    'rate' => [
        'type' => 'text',
        'label' => 'Rate',
        'icon' => 'fa fa-usd',
        'validator' => [
            'minLength' => 1,
            'maxLength' => 10,
            'label' => 'Rate'
        ],
        'placeholder' => 'Enter a rate'
    ]
];

$fields = array_merge_recursive_distinct($fields_default, $get['fields']);

// Buttons (optional)
// submit: display the submission button for this form.
// edit: display the edit button for panel mode.
// disable: display the enable/disable button.
// delete: display the deletion button.
// activate: display the activate button for inactive users.

$buttons_default = [
  "btn_submit" => [
    "type" => "submit",
    "label" => $button_submit_text,
    "display" => "show",
    "style" => "primary"
  ],
  "btn_cancel" => [
    "type" => "cancel",
    "label" => "Cancel",
    "display" => ($get['render_mode'] == 'modal') ? "show" : "hidden",
    "style" => "warning"
  ]
];

$buttons = array_merge_recursive_distinct($buttons_default, $get['buttons']);

$template = "";

if ($get['render_mode'] == "modal"){
    $template .=
    "<div id='{$get['box_id']}' class='modal fade'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$box_title</h4>
                </div>
                <div class='modal-body'>
                    <form method='post' action='$target'>";        
} else if ($get['render_mode'] == "panel"){
    $template .=
    "<div class='panel panel-primary'>
        <div class='panel-heading'>
            <h2 class='panel-title pull-left'>$box_title</h2>
            <div class='clearfix'></div>
            </div>
            <div class='panel-body'>
                <form method='post' action='$target'>";
} else {
    echo "Invalid render mode.";
    exit();
}

// Load CSRF token
$csrf_token = $loggedInUser->csrf_token;
$template .= "<input type='hidden' name='csrf_token' value='$csrf_token'/>";

$template .= "
<div class='dialog-alert'>
</div>
<div class='row'>
    <div class='col-sm-6'>
        {{course}}
    </div>
    <div class='col-sm-6'>
        {{rate}}
    </div>    
</div>";

// Buttons
$template .= "<br>
<div class='row'>
    <div class='col-xs-4 col-sm-3 pull-right'>
      {{btn_cancel}}
    </div>
    <div class='col-xs-8 col-sm-4 pull-right'>
        {{btn_submit}}
    </div>
</div>";

// Add closing tags as appropriate
if ($get['render_mode'] == "modal")
    $template .= "</form></div></div></div></div>";
else
    $template .= "</form></div></div>";

// Render form
$fb = new FormBuilder($template, $fields, $buttons, $data);
$response = $fb->render();
     
if ($ajax)
    echo json_encode(array("data" => $response), JSON_FORCE_OBJECT);
else
    echo $response;

?>