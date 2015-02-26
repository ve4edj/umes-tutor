<?php
require_once("../models/config.php");

// Request method: GET
$ajax = checkRequestMode("get");

if (!securePage(__FILE__)){
    apiReturnError($ajax);
}

setReferralPage(getAbsoluteDocumentPath(__FILE__));

require_once("../models/class.tutor.faculty.php");
require_once("../models/class.tutor.course.php");

$faculties = Faculty::getAll();
$myCourses = Course::getAllByTutor($_SESSION["userCakeUser"]->user_id);

?>

<!DOCTYPE html>
<html lang="en">
  <?php
  	echo renderAccountPageHeader(array("#SITE_ROOT#" => SITE_ROOT, "#SITE_TITLE#" => SITE_TITLE, "#PAGE_TITLE#" => "Tutor Dashboard"));
  ?>

  <style>
    .list-group-item {
      display: inline-block;
      width: 100%;
      padding: 7px 7px 7px 14px;
    }
    .list-group-buttons {
      float: right;
    }
    .list-group-item button {
      width: 75px;
      padding: 4px 0px;
      float: left;
    }
    .list-group-item span {
      float: left;
      display: inline-block;
      padding: 4px 0px;
    }
    .list-group-item button:not(:last-child) {
      margin-right: 10px;
    }
  </style>

  <body>

    <div id="wrapper">

      <!-- Sidebar -->
        <?php
          echo renderMenu("dashboard-tutor");
        ?>  

      <div id="page-wrapper">
	  	<div class="row">
          <div id='display-alerts' class="col-lg-12">
          
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <h1 style="margin-top:0px;">Tutor Overview</h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
          </div>
        </div><!-- /.row -->

        <div class="row">
          <div class="col-lg-6">
            <div class="panel panel-primary">
              <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-book"></i> My Courses</h3>
              </div>
              <div class="panel-body">
                <div class="list-group">
                  <?php foreach ($myCourses as $id => $course) { ?>
                  <span class="list-group-item">
                    <span><i class="fa fa-university"></i> <?=$course->toString($faculties)?></span>
                    <div class="list-group-buttons">
                      <button type="button" class="btn btn-md btn-warning course_link_edit" data-course-id="<?=$id?>"><i class="fa fa-edit"></i> Edit</button>
                      <button type="button" class="btn btn-md btn-danger course_link_delete" data-course-id="<?=$id?>"><i class="fa fa-trash-o"></i> Delete</button>
                    </div>
                  </span>
                  <?php } ?>
                </div>
                <button type="button" class="btn btn-block btn-md btn-success course_link_add" value="Add"><i class="fa fa-plus"></i> Add</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="panel panel-primary">
              <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-clock-o"></i> My Availability</h3>
              </div>
              <div class="panel-body">
                Coming Soon!!!
                <!-- put the form thingy here-->
              </div>
            </div>
          </div>
        </div><!-- /.row -->

      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->

	<script>
    $(document).ready(function() {       
      alertWidget('display-alerts');

      $('.course_link_add').click(function() {
        alert("Let's link a new course!" + "\nUser ID is: " + <?=$_SESSION["userCakeUser"]->user_id?>);
        // call the function to make a new edit form with no course ID
      });

      $('.course_link_edit').click(function() {
        var btn = $(this);
        var course_id = btn.data('course-id');
        alert("Course ID to edit is: " + course_id + "\nUser ID is: " + <?=$_SESSION["userCakeUser"]->user_id?>);
        // call the function to make a new edit form with the selected course ID
      });

      $('.course_link_delete').click(function() {
        var btn = $(this);
        var course_id = btn.data('course-id');
        alert("Course ID to delete is: " + course_id + "\nUser ID is: " + <?=$_SESSION["userCakeUser"]->user_id?>);
        // call the function to make a new delete confirm form with no course ID
      });
		});
	</script>
  </body>
</html>


