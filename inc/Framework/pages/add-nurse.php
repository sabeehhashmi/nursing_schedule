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

<link href="<?php echo home_url(); ?>/wp-content/plugins/nursing-schedule/assets/select2.min.css" rel="stylesheet" />
<script src="<?php echo home_url(); ?>/wp-content/plugins/nursing-schedule/assets/select2.min.js"></script>

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
  $nurse_id = isset($_GET['nurse_id'])?$_GET['nurse_id']:'';
  $phone = get_the_author_meta( 'user_phone_number', $nurse_id );
  $emplyment_status = get_the_author_meta( 'employement_status', $nurse_id );
  $nursing_group = get_the_author_meta( 'nursing_group', $nurse_id );
  $nurse = get_user_by( 'id', $nurse_id );
  $display_name = ($nurse_id)?$nurse->display_name:'';
  if($nurse_id):
    ?>
    <h2>Edit Staff</h2>
    <?php else: ?>
      <h2>Add New Staff</h2>
    <?php endif; ?>
    <form class="register-nurse">
      <div class="response-message">
      </div>
      <div class="col-sm-6">
        <input type="hidden" name="xyz">
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name" name="name" value="<?php echo $display_name; ?>">
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <?php
          $roles = get_staff_roles();
          $current_roles = getStaffRoles($nurse_id);
          $roles_conatainer = array();
          if(!empty($current_roles)){
            foreach ($current_roles as $current_role) {
             $roles_conatainer[] = $current_role->role_id;
           }
         }
         ?>
         <label for="exampleInputPassword1">Staff Roles</label>
         <select class="get-schedule-by staff-roles form-control" name="roles[]" multiple="multiple">      
           <?php
           if($roles->first()):
            foreach ($roles as $role):
              ?>
              <option value="<?php echo $role->id; ?>" <?php echo ( in_array($role->id, $roles_conatainer))?'selected':''; ?>>
                <?php echo $role->role_name; ?>
              </option>

              <?
            endforeach;
          endif;
          ?>
        </select>
      </div>
    </div> 
    <div class="col-sm-6">
      <div class="form-group">
        <?php
        $employement_status = get_nursing_employement();

        ?>
        <label for="exampleInputPassword1">Employment Status</label>
        <select class="get-schedule-by form-control" name="employement_status">      
         <?php
         if($employement_status->first()):
          foreach ($employement_status as $employement):
            ?>
            <option value="<?php echo $employement->employement_key; ?>" <?php echo ($emplyment_status==$employement->employement_key)?'selected':''; ?>>
              <?php echo $employement->name; ?>

            </option>

            <?
          endforeach;
        endif;
        ?>
      </select>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="form-group">
      <label for="name">Phone</label>
      <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter phone" name="phone" value="<?php echo $phone; ?>">
      <input type="hidden" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"  name="current_nurse_id" value="<?php echo $nurse_id; ?>">
    </div>
  </div>
  <div class="col-sm-6">
    <div class="form-group fprm-submit-button">
      <button  class="btn btn-success add-nurse">Submit</button>
      <?php if($nurse_id):?>
        <a href="<?php echo home_url(); ?>/register-a-nurse"  class="btn btn-success ">Add Another</a>
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
   $('.staff-roles').select2({
    multiple:true,
    tags: true});
   $('.loading').hide();
   $( ".add-nurse" ).click(function(e) {
    e.preventDefault();
    $('.loading').show();
    form_data = $( "form.register-nurse" ).serialize();
    var data = 'form_data=' + form_data + '&action=registerNewNurse';

    jQuery.ajax({
     type : "post",
     dataType : "json",
     url : myAjax.ajaxurl,
     data: data,
     success: function(response) {
      if(response.status == "success") {
        jQuery(".response-message").html(response.message);
        $('.loading').hide();
        setTimeout(function () { 
          window.location.href = home_url + "/nurse-list";
          jQuery(".response-message").html(''); }, 5000);
      }
      else {
        $('.loading').hide();
        jQuery(".response-message").html(response.message);
      }
      

    }

  });
  });
 });

  /*end */
</script>