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

  <body>

    <div id="wrapper">

      <!-- Sidebar -->
        <?php
          echo renderMenu("dashboard");
        ?>  

      <div id="page-wrapper">
	  	<div class="row">
          <div id='display-alerts' class="col-lg-12">
          
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <h1>Dashboard <small>Tutor Overview</small></h1>
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
                  <a href="#" class="list-group-item">
                    <!--<span class="badge">edit</span>-->
                    <i class="fa fa-university"></i> <?=$course->toString($faculties)?>
                  </a>
                  <?php } ?>
                </div>
                <button type="button" class="btn btn-block btn-md btn-success" value="Add"><i class="fa fa-plus"></i> Add</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="panel panel-primary">
              <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-info-circle"></i> Course Details</h3>
              </div>
              <div class="panel-body">
                <!-- put the form thingy here-->
                <button type="button" class="btn btn-block btn-md btn-warning" value="Save"><i class="fa fa-edit"></i> Save</button>
                <button type="button" class="btn btn-block btn-md btn-danger" value="Delete"><i class="fa fa-trash-o"></i> Delete</button>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-6">
            <div class="panel panel-primary">
              <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-clock-o"></i> My Availability</h3>
              </div>
              <div class="panel-body">
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
		});
	</script>
  </body>
</html>


