 <?php

 global $wpdb;

 $user_id = get_current_user_id();
 

 ?>
 <!-- Mobiscroll JS and CSS Includes -->
<!--<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/bb-theme-child/css/mobiscroll.jquery.min.css">
<script src="<?php echo home_url();?>/wp-content/themes/bb-theme-child/js/mobiscroll.jquery.min.js"></script>
-->
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->

<style type="text/css">
  body {
    margin: 0;
    padding: 0;
  }

  .md-multi-month .md-desc p {
    margin: 0;
  }
  .ui-datepicker{
   width:100% !important;
 }
 .css-class-to-highlight{
  background-color: orange;
}
.accepted_color {
  background-image: linear-gradient(to right, #54d677 , #338651f2) !important;
  padding-bottom: 43px !important;
  color: #fff;
}
.request_shift{
  width: 250px;
}
.table > tbody > tr > td{
  min-width: 200px;
}
.nurse-calender-table {
  overflow-x: auto;
}
</style>
<div class="row client_calendar_padding">

  <?php
  $shift_id = isset($_GET['shift-id'])?$_GET['shift-id']:'';
  $interval = ($shift_id)?getshiftIterval($shift_id):'';
  $interval_id = ($interval)?$interval->interval_id:'';
  $shift = getsingleShift($shift_id);
  $intervals = get_time_invervals();
  $shift_name = ($shift)?$shift->shift_name:'';
  $shift_color = ($shift)?$shift->shift_color:'';
  if($shift_id):
    ?>
    <h2>Edit Shift</h2>
    <?php else: ?>
     <h2>Add New Shift</h2>
   <?php endif; ?>

   <form class="register-nurse">
    <div class="response-message">
    </div>
    <div class="col-sm-6">
      <input type="hidden" name="xyz">
      <div class="form-group">
        <label for="exampleInputEmail1">Shift Name</label>
        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Shift Name" name="shift_name" value="<?php echo $shift_name; ?>">
        <input type="hidden" name="shift_id" value="<?php echo $shift_id; ?>">
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <label for="exampleInputEmail1">Shift Color</label>
        <input type="color" class="form-control" placeholder="Select Color" name="shift_color" value="<?php echo $shift_color; ?>">
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <label for="exampleFormControlSelect1">Select Interval</label>
        <select class="form-control" id="shift-select" name="interval_id">
          <option value="" >Select Interval</option>
          <?php

          if(!empty($intervals)) :
            foreach ($intervals as $interval) {
              ?>
              <option value="<?php echo $interval->id; ?>" <?php echo ($interval_id == $interval->id)?'selected':''; ?>><?php echo $interval->hours; ?></option>
              <?php
            }
          endif;
          ?>
        </select>
      </div>

    </div>
    <div class="col-sm-6">
      <div class="form-group fprm-submit-button">
        <button  class="btn btn-success add-nurse">Submit</button>
        <?php if($shift_id): ?>
          <a href="<?php echo home_url(); ?>/add-shift/"  class="btn btn-success">Add Another</a>
        <?php endif; ?>
      </div>
    </div>
  </form>

  <div class="loading">Loading&#8230;</div>
  <style type="text/css">
    .loading{
      display: none;
    }
    .client_calendar_padding .form-group {
      text-align: left;
    }
    .form-group.fprm-submit-button {
      padding-top: 25px;
    }
  </style>
  <script>

    /*collapse script */

    jQuery(document).ready(function($){
      $('.loading').hide();
      $( ".add-nurse" ).click(function(e) {
        e.preventDefault();
        $('.loading').show();
        form_data = $( "form.register-nurse" ).serialize();
        var data = 'form_data=' + form_data + '&action=add_staff_shift';

        jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data: data,
         success: function(response) {
          if(response.status == "success") {
            jQuery(".response-message").html(response.message);
            $('.loading').hide();
            window.location.href = home_url + "/shifts-list"; 
          }
          else {
            $('.loading').hide();
            jQuery(".response-message").html(response.message);
          }
          setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

        }

      });
      });
    });

    /*end */
  </script>