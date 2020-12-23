
<?php


/** Step 2 (from text above). */
/*add_action( 'admin_menu', 'my_plugin_menu' );*/

/** Step 1. */
function my_plugin_menu() {
  add_menu_page( 'Nursing Schedule Panel', 'Nursing Schedule Panel ', 'manage_options', 'nursing-panel', 'my_plugin_options' ,'dashicons-calendar-alt',3);
}
/** Step 3. */
function my_plugin_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

  
  <?php
  
  echo '<div class="wrap">';
  echo '<div class="headin-nurse-schedule"><h2>
  Make Nurses Schedule</h2></div>';
  echo '</div>';
  $year = date("Y");
  $months = array(1=> 'January-'.$year, 2=> 'Febraury-'.$year, 3=> 'March-'.$year,4=> 'April-'.$year, 5=> 'May-'.$year, 6=> 'June-'.$year,7=> 'July-'.$year, 8=> 'August-'.$year, 9=> 'September-'.$year,10=> 'October-'.$year, 11=> 'November-'.$year,12=> 'December-'.$year);
  ?>
  <div class="month-wrap">
    <select class="form-control button_width selected-month" id="requestShift" name="select-month">
      <option value="">Select Month</option>
      <?php
      for ($month=1; $month<=12; $month++){

        echo '<option value='.$month.' >'.$months[$month].'</option>';

      }
      ?>
    </select>
  </div>
  <div class="duplicate-month-area">
    <div class="row">
      <div class="col-md-2">
        <div class="duplicate-generater">
          <button type="button" class="btn btn-primary">Duplicate this scehdule</button>
        </div>
      </div>
      <div class="col-md-4 dup-month-area">
        <select class="form-control button_width selected-dup-month" id="requestShift" name="select-month">
          <option value="">Select Month For Duplication</option>
          <?php
          for ($month=1; $month<=12; $month++){

            echo '<option value='.$month.' >'.$months[$month].'</option>';

          }
          ?>
        </select>
      </div>
      <div class="col-md-2 dup-month-area">
        <div class="duplicate-saver">
          <button type="button" class="btn btn-primary save-and-duplicate">Save and Duplicate</button>
        </div>
      </div>
    </div>
  </div>
  <div class="response-message">

  </div>
  <div class="nurses-data-container">
  </div>
  <div class="nursing-schedule">
    <button type="button" class="btn btn-primary save-schedule">Save Schedule</button>
  </div>

  <style>


   .table-wrap{
    overflow-x:auto;
  }
  .table-wrap select {

    width: 126px;
    margin: 10px;

  }
  .table-wrap th, .table-wrap td {

    text-align: center;
    min-width: 100px;

  }
  .nursing-schedule .btn-primary {
    float: right;
    margin: 20px;
  }
  .month-wrap {

    width: 153px;
    padding: 6px 10px;

  }
  .response-message{
    padding: 25px 20px 0px 20px;
  }
  .duplicate-month-area {

    padding: 20px;
    background: #e1e0e0;
    margin-right: 26px;
    border-radius: 10px;
    margin-top: 14px;
    display: none;

  }
  .dup-month-area {
    display: none;
  }
</style>

<script type="text/javascript">

  jQuery(document).ready( function($) {

    $( ".selected-month" ).change(function(e) {
      $('.duplicate-month-area').hide();
      month = jQuery(this).val();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_calender_preview",month:month},
       success: function(response) {
        if(response.status == "success") {
         jQuery(".nurses-data-container").html(response.data);
         console.log(response.enable_duplicate)
         if(response.enable_duplicate == 1){
          $('.duplicate-month-area').show();
        }
      }
      else {
       alert("May be some thing went wrong please try again")
     }
   }
 })   
    });
    jQuery(".duplicate-generater").click( function(e) {
      $('.dup-month-area').show();
    });
    /*Save Schedule*/
    jQuery(".save-schedule").click( function(e) {

      var shifts        =  $(".selected_shift").map(function(){return $(this).val();}).get();
      var cheked_values =  $(".mark-checked")
      .map(function(){return $(this).val();}).get(); 
      var user_with_group =  $(".user_with_group").map(function(){return $(this).val();}).get();
      var updated_id      =  $(".updated_id")
      .map(function(){return $(this).val();}).get();
      var month = $('.selected-month').val();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_month_data",shifts:shifts,cheked_values:cheked_values,user_with_group:user_with_group,month:month,updated_id:updated_id},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".response-message").html(response.message);
          $('.duplicate-month-area').show();

        }
        else {
         jQuery(".response-message").html(response.message);
       }
       setTimeout(function () { jQuery(".response-message").html(''); }, 5000);
     }
   })   
    });
    /*Duplicate Schedule*/

    jQuery(".save-and-duplicate").click( function(e) {

      var shifts        =  $(".selected_shift").map(function(){return $(this).val();}).get();
      var cheked_values =  $(".mark-checked")
      .map(function(){return $(this).val();}).get(); 
      var user_with_group =  $(".user_with_group").map(function(){return $(this).val();}).get();
      var month = $('.selected-dup-month').val();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "duplicate_month_data",shifts:shifts,cheked_values:cheked_values,user_with_group:user_with_group,month:month},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".response-message").html(response.message);

        }
        else {
         jQuery(".response-message").html(response.message);
       }
       setTimeout(function () { jQuery(".response-message").html(''); }, 5000);
     }
   })   
    });

  });

</script>
<?php
}