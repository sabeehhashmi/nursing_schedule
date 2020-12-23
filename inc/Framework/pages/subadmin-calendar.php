
<!-- Mobiscroll JS and CSS Includes -->
<!--<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/bb-theme-child/css/mobiscroll.jquery.min.css">
<script src="<?php echo home_url();?>/wp-content/themes/bb-theme-child/js/mobiscroll.jquery.min.js"></script>
-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
  background-image: linear-gradient(to right, #52d1da , #52d1da) !important;
  padding-bottom: 43px !important;
  color: #fff;
}
.request_shift{
  width: 250px;
}
.table > tbody > tr > td{
  min-width: 30px;
  color: #0b0b0b;
}
.nurse-calender-table {
  overflow-x: auto;
}
.nurse-group,.nurse-name {
  color: #000;
}
.table > tbody > tr > th,
.table > tbody > tr > td
{
  border:1px solid #ddd;
}
.day-slot,
.eve-slot,
.night-slot {
  border-radius: 13px;
  margin: 5px;
  color: #0b0b0b;
  min-height: 70px;
}
.night-slot{
  background-color: #f5aec2;
}
.eve-slot{
  background-color:#c6e8f5;
}
.day-slot{
  background-color: #c8efc8;
}
.header-bg a{
  color: #7c7292;
}
div#ui-datepicker-div {
  width: 17em !important;
}
@media print {
  th.hidden-cols, td.hidden-cols{
    display: none;
  }
  .row.header-bg.top-header {
    display: none !important;
  }
  .col-sm-2.col-xs-2.header-bg {
    display: none !important;
  }
  .col-md-10.col-sm-12.col-xs-12.table-padding {
    width: 100% !important;
  }
  .col-sm-12.search_sec {
    display: none;
  }
  .nurse-calender-table {
    overflow-x: visible !important;
  }
  .request-table .col-sm-12 {
    border: 0px solid #ffc8009e !important;
  }
  .print-options-container{
    display: none !important;
  }
}
.btn-primary {
  background-color:#eef6fa !important;
  border-color: #eef6fa !important;
  color: #000 !important; 
}
.col-sm-12.print-options-container {
  background: #b3b1b1;
  border-radius: 5px;
  margin-top: 10px;
}
.print-options {
  padding-bottom: 12px;
  padding-top: 5px;
  color: #211f60;
  background: #fff;
  margin-bottom: 12px;
  font-size: 18px;
}
.single-staff-selector{
  display: none;
}
.proceed-pint{
  background: #28a745 !important;
  border-color: #28a745 !important;
  color:#fff;
}
.proceed-pint {
  float: right;
}
.proceed-pint-container {
  padding-top: 14px;
  padding-right: 30px;
}
.print-panel-open,.print-options-container,.single_date_picker_container{
  display: none;
}
.single_date_picker_container{
  padding-top: 10px;
}
.close-panel {
  float: right;
  padding-right: 13px;
  cursor: pointer;
}
</style>
<?php //printr(get_option( get_current_user_id().'schdule_date', false )); ?>
<div class="row client_calendar_padding">
  <h2>View Schedule</h2>
  <div class="col-sm-12 search_sec">

    <div class="col-sm-4">
      <input type="text" class="form-control" id="datepicker" class="single-date" placeholder="Select Date">
    </div>
    <div class="col-sm-4">

      <select class="sort-by form-control" name="sort_by">      
        <option value="">Sort By</option>
        <option value="employement_status">Employment Status</option>
        <option value="name">Staff Member Name</option>

      </select>
    </div>

    <div class="col-sm-4 print-panel-open">
      <button type="button" class="btn btn-primary">Print Schedule</button>
    </div>

    <div class="col-sm-12 print-options-container">
      <div class="row">
        <di class="col-sm-12">
          <div class="print-options">
            Select Print Option
            <span class="close-panel">X</span>
          </div>
        </di>
        <div class="col-sm-4">
          <button type="button" class="btn btn-primary continue-with-print">Week Schedule</button>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn btn-primary class-show-single-nurse">Single Employee Schedule</button>
        </div>
        <div class="col-sm-4 single-staff-selector">
          <?php
          $current_user_id = get_current_user_id();
          $args = array(
            'role'         => 'nurse',
            'orderby'      => 'display_name',
            'order'        => 'ASC',
            'meta_query' => array(
              'relation' => 'AND',
              array(
                'key'     => 'nurse_subadmin',
                'value'   => $current_user_id,
                'compare' => '='
              )
            )
          );
          $users = get_users( $args );
          ?>
          <div class="form-group">
            <select class="form-control staff-selector-field" id="exampleFormControlSelect1">
              <option>Select Staff</option>
              <?php foreach ($users as $user) {
                ?>
                <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn btn-primary class-print-data-date">Single Date Schedule</button>
        </div>
        <div class="col-sm-4 single_date_picker_container">
          <input type="text" class="form-control" id="single_datepicker" class="search-single-date" placeholder="Select Date">
        </div>
        <div class="col-sm-12 proceed-pint-container">
          <button type="button" class="btn btn-success class-print-data proceed-pint">Proceed To Print</button>
        </div>
      </div>
    </div>

  </div>


  <div class="loading">Loading&#8230;</div>


  <!--#####################       not in use #####################-->
  <?php if(1==1){ ?>
    <div class="row calendar_section" id="nursing_calendar_listing">
     <div class="col-sm-12 request-table">
       <div class="row accepted_color">
         <div class="col-sm-10">
          <p class="calendar-request"><!-- Schedule For You --></p>
        </div>
        <div class="col-sm-2 text-right">

        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div  class="collapse in nurse-calender-table">
            <div class="default-text"><h3>Please Select the date</h3></div>
          </div>

        </div>
      </div>
    </div>

  </div>
  <div class="response-message">
  </div>

<?php } ?>
<!--#####################       not in user #####################-->
<?php //printr(get_option( get_current_user_id().'schdule_date', false )); ?>
<script>

  /*collapse script */
  
  jQuery(document).ready(function($){

    $('.loading').hide();
    $( ".class-print-data" ).click(function() {
      window.print();
    });
    $( ".class-show-single-nurse" ).click(function() {
      $('.single-staff-selector').show();
      $('.single_date_picker_container').hide();
    });
    $( ".class-print-data-date" ).click(function() {
      $('.single_date_picker_container').show();
      $('.single-staff-selector').hide();
    });
    $( ".continue-with-print" ).click(function() {
      $('.single_date_picker_container').hide();
      $('.single-staff-selector').hide();

      single_date = jQuery('#datepicker').val();
      nurse_order = jQuery('.sort-by').val();
      $('.loading').show();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_staff_calender_for_subadmin_preview",order_by:nurse_order,date:single_date},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".nurse-calender-table").html(response.data);
          $('.loading').hide();
          $('.print-panel-open').show();
        }
        else {
          $('.loading').hide();
          jQuery(".response-message").html(response.message);
        }
        setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

      }

    });
    });
    $('.print-panel-open').click(function() {
      $('.print-options-container').show();
    });
    $('.close-panel').click(function() {
      $('.print-options-container').hide();
    });
    var dateToday = new Date();
    var default_date = '<?php echo (get_option( get_current_user_id().'schdule_date', false ))?get_option( get_current_user_id().'schdule_date', false ):''; ?>';

    $( function() {
      $( "#datepicker" ).datepicker({
        /*minDate: dateToday,*/
        beforeShowDay: function(date){ 
          var day = date.getDay();

          return [day == week_start_day,""];
        }
      });
      $("#datepicker").datepicker('setDate', default_date);
    } );
    $( function() {
      $( "#single_datepicker" ).datepicker({
        /*minDate: dateToday,*/
        /*beforeShowDay: function(date){ 
          var day = date.getDay(); 
        }*/
      });
    } );

    single_date = default_date;
    nurse_order = jQuery('.sort-by').val();
    $('.loading').show();
    jQuery.ajax({
     type : "post",
     dataType : "json",
     url : myAjax.ajaxurl,
     data : {action: "add_staff_calender_for_subadmin_preview",order_by:nurse_order,date:single_date},
     success: function(response) {
      if(response.status == "success") {
        jQuery(".nurse-calender-table").html(response.data);
        $('.loading').hide();
        $('.print-panel-open').show();
      }
      else {
        $('.loading').hide();
        jQuery(".response-message").html(response.message);
      }
      setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

    }

  });

    $("#datepicker, .sort-by").change(function(e) {
     single_date = jQuery('#datepicker').val();
     nurse_order = jQuery('.sort-by').val();
     $('.loading').show();
     jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_staff_calender_for_subadmin_preview",order_by:nurse_order,date:single_date},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".nurse-calender-table").html(response.data);
          $('.loading').hide();
          $('.print-panel-open').show();
        }
        else {
          $('.loading').hide();
          jQuery(".response-message").html(response.message);
        }
        setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

      }

    });
   });
    $("#single_datepicker").change(function(e) {
     single_date = jQuery('#single_datepicker').val();
     nurse_order = jQuery('.sort-by').val();
     allow_single_date = 1;
     $('.loading').show();
     jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_staff_calender_for_subadmin_preview",order_by:nurse_order,date:single_date,allow_single_date:allow_single_date},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".nurse-calender-table").html(response.data);
          $('.loading').hide();
        }
        else {
          $('.loading').hide();
          jQuery(".response-message").html(response.message);
        }
        setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

      }

    });
   });
    $(".staff-selector-field").change(function(e) {
      staff_id = $(this).val();
      single_date = jQuery('#datepicker').val();
      nurse_order = jQuery('.sort-by').val();
      $('.loading').show();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_staff_calender_for_subadmin_preview",order_by:nurse_order,date:single_date,staff_id:staff_id},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".nurse-calender-table").html(response.data);
          $('.loading').hide();
        }
        else {
          $('.loading').hide();
          jQuery(".response-message").html(response.message);
        }
        setTimeout(function () { jQuery(".response-message").html(''); }, 5000);

      }

    });
    });
    
    $(document).on('change', '.selected-note', function() {
      $('.loading').show();
      note = jQuery(this).val();
      user_id = jQuery(this).data("user_id")
      date = jQuery(this).data("date");

      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "save_note_for_user",note:note,user_id:user_id,date:date},
       success: function(response) {
        if(response.status == "success") {
          $('.loading').hide();
          
        }
        else {
          $('.loading').hide();
          
        }

      }

    });
    });
    $(document).on('change', '.selected_shift', function() {

      data = jQuery(this).val();
      thiss = this;
      $('.loading').show();
      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "fill_slot_by_nurse",data:data},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".response-message").html(response.message);
          $('.loading').hide();
          $(thiss).parent().find('.send-request-link').hide();
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