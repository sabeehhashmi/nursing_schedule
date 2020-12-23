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
  $group_id = isset($_GET['group-id'])?$_GET['group-id']:'';
  $group = getsingleGroup($group_id);
  $group_name = ($group)?$group->group_name:'';
  if($group_id):
    ?>
    <h2>Edit Group</h2>
    <?php else: ?>
     <h2>Add New Group</h2>
   <?php endif; ?>

   <form class="register-nurse">
    <div class="response-message">
    </div>
    <div class="col-sm-6">
      <input type="hidden" name="xyz">
      <div class="form-group">
        <label for="exampleInputEmail1">Group Name</label>
        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Group Name" name="group_name" value="<?php echo $group_name; ?>">
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
      </div>
    </div>
   
    
    <div class="col-sm-6">
      <div class="form-group fprm-submit-button">
        <button  class="btn btn-success add-nurse">Submit</button>
        <?php if($group_id): ?>
        <a href="<?php echo home_url(); ?>/add-group/"  class="btn btn-success">Add Another</a>
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
        var data = 'form_data=' + form_data + '&action=add_staff_groups';

        jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data: data,
         success: function(response) {
          if(response.status == "success") {
            jQuery(".response-message").html(response.message);
            $('.loading').hide();
           window.location.href = home_url + "/groups-lis"; 
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