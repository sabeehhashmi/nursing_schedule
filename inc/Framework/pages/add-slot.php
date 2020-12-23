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
  $slot_id = isset($_GET['slot-id'])?$_GET['slot-id']:'';
  $slot = getsingleSlot($slot_id);
  $slot_name = ($slot)?$slot->slot_name:'';
  $role_id = ($slot)?$slot->role_id:'';
  $shift_id = ($slot)?$slot->shift_id:'';
  $sgroup = ($slot_id)?getslotgroup($slot_id):'';
  $group_id = ($sgroup)?$sgroup->group_id:'';
  $shifts = get_staff_shifts();
  $roles = get_staff_roles();
  $groups = get_user_groups();
  if($slot_id):
    ?>
    <h2>Edit Need</h2>
    <?php else: ?>
      <h2>Add New Need</h2>
    <?php endif; ?>
    <form class="register-nurse">
      <div class="response-message">
      </div>
      <div class="col-sm-6">
        <input type="hidden" name="xyz">
        <div class="form-group">
          <label for="exampleInputEmail1">Need Name</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Need Name" name="slot_name" value="<?php echo $slot_name; ?>">
          <input type="hidden" name="slot_id" value="<?php echo $slot_id; ?>">
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Select Shift</label>
          <select class="form-control" id="shift-select" name="shift_id">
            <option value="" >Select Shift</option>
            <?php

            if(!empty($shifts)) :
              foreach ($shifts as $shift) {
                ?>
                <option value="<?php echo $shift->id; ?>" <?php echo ($shift_id == $shift->id)?'selected':''; ?>><?php echo $shift->shift_name; ?></option>
                <?php
              }
            endif;
            ?>
          </select>
        </div>

      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Select Group</label>
          <select class="form-control" id="shift-select" name="group_id">
            <option value="" >Select Group</option>
            <?php

            if(!empty($groups)) :
              foreach ($groups as $group) {
                ?>
                <option value="<?php echo $group->id; ?>" <?php echo ($group_id == $group->id)?'selected':''; ?>><?php echo $group->group_name; ?></option>
                <?php
              }
            endif;
            ?>
          </select>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="exampleFormControlSelect1">Select Role</label>
          <select class="form-control" id="shift-select" name="role_id">
            <option value="" >Select Role</option>
            <?php

            if(!empty($roles)) :
              foreach ($roles as $role) {
                ?>
                <option value="<?php echo $role->id; ?>" <?php echo ($role_id == $role->id)?'selected':''; ?>><?php echo $role->role_name; ?></option>
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
          <?php if($slot_id):?>
            <a href="<?php echo home_url(); ?>/add-slot" class="btn btn-success">Add Another</a>
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
          var data = 'form_data=' + form_data + '&action=add_staff_slot';

          jQuery.ajax({
           type : "post",
           dataType : "json",
           url : myAjax.ajaxurl,
           data: data,
           success: function(response) {
            if(response.status == "success") {
              jQuery(".response-message").html(response.message);
              $('.loading').hide();
            }
            else {
              $('.loading').hide();
              jQuery(".response-message").html(response.message);
            }
            setTimeout(function () { 
              window.location.href = home_url + "/slots-list"; 
              jQuery(".response-message").html(''); }, 5000);

          }

        });
        });
      });

      /*end */
    </script>