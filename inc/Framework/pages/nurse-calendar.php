 <?php

 global $wpdb;

 $user_id = get_current_user_id();
 $facility = get_user_meta($user_id,'user_faculty',true);
 $date = date("Y-m-d");
 
 $reuest_res = $wpdb->get_results("select request_id from wp_request_status where request_status IN (2,4) AND client_id =". get_current_user_id());
 $postIds = array();
 if(!empty($reuest_res)){
  foreach($reuest_res as $reqdata)
  {
    $postIds[] = $reqdata->request_id;
  }
}
else
{
  $postIds[] = 0;
}

 //print_r($postIds);
$req_args = array(
  'author' => get_current_user_id(),
  'posts_per_page' =>-1,
  'post__not_in' => $postIds,
  'post_type' => 'request',
  'order' => 'DESC',
  'meta_query' => array(
    array(
      'key' => 'request_date',
      'value' =>$date,
      'compare' => '='

    )
  )
);

$req_args = new WP_Query($req_args);
   // echo "<pre>";
    //$resquest = json_decode(json_encode($req_args->posts), true);
    //print_r($resquest);

/* - - - - -- - - Working - - - - - - */
$postIds = array();
    //echo "select request_id from wp_request_status where request_status = 1 AND client_id = ".get_current_user_id();
$working_res = $wpdb->get_results("select request_id from wp_request_status where request_status = 1 AND client_id =". get_current_user_id());

if(!empty($working_res)){
  foreach($working_res as $wdata)
  {
    $postIds[] = $wdata->request_id;
  }
}
else
{
  $postIds[] = 0;
}
    //print_r($postIds);

$working_args = array(
  'post__in' => $postIds,
  'posts_per_page' =>-1,
  'post_type' => 'request',
  'order' => 'DESC',
  'meta_query' => array(
    array(
      'key' => 'request_date',
      'value' =>$date,
      'compare' => '='

    )
  )
);

$workingposts = new WP_Query($working_args);
$working_posts = $workingposts->posts;
    //print_r($working_posts);

/* - -  - - - - - - Accept  - - - - - - --*/

$postIds = array();
$accepted_res = $wpdb->get_results("select request_id from wp_request_status where request_status = 2 AND client_id =".get_current_user_id());

if(!empty($accepted_res))
{
  foreach($accepted_res as $aData)
  {
    $postIds[] = $aData->request_id;
  }
}
else
{
  $postIds[] = 0;
}
$accepted_args = array(
  'post__in' => $postIds,
  'posts_per_page' =>-1,
  'post_type' => 'request',
  'order' => 'DESC',
  'meta_query' => array(
    array(
      'key' => 'request_date',
      'value' =>$date,
      'compare' => '='

    )
  )
);

    //$accepted_posts   = get_posts($accepted_args);
$acceptedposts = new WP_Query($accepted_args);
$accepted_posts = $acceptedposts->posts;
    //echo "<pre>";
    //print_r($accepted_posts);
/* -  - -  -  - - - - Denied - - - - - - -- - -- */

$postIds = array();
$denied_res = $wpdb->get_results("select request_id from wp_request_status where request_status = 3 AND client_id =".get_current_user_id());

if(!empty($denied_res))
{
  foreach($denied_res as $denData)
  {
    $postIds[] = $denData->request_id;
  }
}
else
{
  $postIds[] = 0;
}
$denied_args = array(
  'post__in' => $postIds,
  'posts_per_page' =>-1,
  'post_type' => 'request',
  'order' => 'DESC',
  'meta_query' => array(
    array(
      'key' => 'request_date',
      'value' =>$date,
      'compare' => '='

    )
  )
);

$deniedposts = new WP_Query($denied_args);
$denied_posts = $deniedposts->posts;

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
  <h2>Schedule For Current Month</h2>
  <div class="col-sm-12 search_sec">
    <div class="col-sm-2">
     Search By :
   </div>
   <div class="col-sm-4">
    <select class="get-schedule-by form-control">      
      <option value="">Current Month Calender</option>
      <option value="15">Next 15 Days Calender</option>
      <option value="7">Next 7 Days Calender</option>
      <option value="1">Search By date</option>
    </select>
  </div>
  <div class="col-sm-4">
    <select class="single-date form-control">      
      <option value="">Select Date</option>
      <?php for($i=1; $i<=31; $i++){
        ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php
      }?>
      
    </select>
  </div>

</div>


<div class="loading">Loading&#8230;</div>


<!--#####################       not in use #####################-->
<?php if(1==1){ ?>
  <div class="row calendar_section" id="nursing_calendar_listing">
   <div class="col-sm-12 request-table">
     <div class="row accepted_color">
       <div class="col-sm-10">
        <p class="calendar-request">Schedule For You</p>
      </div>
      <div class="col-sm-2 text-right">

      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div  class="collapse in nurse-calender-table">

        </div>

      </div>
    </div>
  </div>

</div>
<div class="response-message">
</div>

<?php } ?>
<!--#####################       not in user #####################-->

<script>

  /*collapse script */

  jQuery(document).ready(function($){
    $('.single-date').hide();
    jQuery.ajax({
     type : "post",
     dataType : "json",
     url : myAjax.ajaxurl,
     data : {action: "add_nurse_calender_preview"},
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
    $(".single-date").change(function(e) {
     single_date = jQuery(this).val();
     $('.loading').show();
     jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data : {action: "add_nurse_calender_preview",single_date:single_date},
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
    $( ".get-schedule-by" ).change(function(e) {

      days = jQuery(this).val();
      if(days == 1){
       $('.single-date').show();
     }
     else{
       $('.single-date').hide();
       $('.loading').show();
       jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "add_nurse_calender_preview",days:days},
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
     }
   });
    $( ".mark-checked" ).live('change',function(){
     slot = jQuery(this).val();
     if(slot != ''){
       if (confirm("Click OK to continue?")){
         jQuery.ajax({
           type : "post",
           dataType : "json",
           url : myAjax.ajaxurl,
           data : {action: "fil_slot",data_id:slot},
           success: function(response) {
            if(response.status == "success") {
              jQuery(".response-message").html(response.message);

            }
            else {
             jQuery(".response-message").html('<div class="alert alert-danger">Something went wrong</div>');
           }
           setTimeout(function () { jQuery(".response-message").html(''); }, 10000);

         }
       })   
       }
     }

   });
    
  });

  /*end */
</script>