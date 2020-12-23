
<?php


/** Step 2 (from text above). */
/*add_action( 'admin_menu', 'send_request_nursing_menu' );*/

/** Step 1. */
function send_request_nursing_menu() {

  add_submenu_page('nursing-panel', 'Send Request', 'Send Request Panel', 'manage_options', 'nursing-request-panel',  'send_request_nursing' );
}
/** Step 3. */
function send_request_nursing() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <div class="wrap">
    <div class="headin-nurse-schedule">
      <h2>
        Send Request To Nurse 
      </h2>
    </div>

    <div class="duplicate-month-area">
      <div class="row">
        <div class="col-md-6 padding-area">
          <div class="duplicate-generater">
            <select class="form-control button_width selected-grp" id="requestShift" name="selected-grp">
              <option value="">Select Group</option>
              <?php
              $groups = get_nursing_group();
              if(!empty($groups)):
                foreach ($groups as $group):
                  ?>
                  <option value="<?php echo $group->group_key; ?>">

                    <?php echo $group->group_value; ?>

                  </option>
                  <?php
                endforeach;
              endif;
              ?>

            </select>
          </div>
        </div>

        <div class="col-md-6  padding-area obtained-slots">

        </div>
        <div class="col-md-12 dup-month-area padding-area">
         <div class="form-group">
          <label for="comment">Message:</label>
          <textarea class="form-control message-for-nurse" rows="5" id="comment"></textarea>
        </div> 
      </div>
      <div class="col-md-12 dup-month-area padding-area">
        <button type="submit" class="btn btn-info send-request-to-nurse-group">Submit</button>
      </div>
      <div class="col-md-12">
       <div class="response-message">

       </div>
     </div>
   </div>
 </div>
</div>

<style>


 .padding-area {
  padding: 12px;
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

}

</style>

<script type="text/javascript">

  jQuery(document).ready( function($) {

    jQuery.ajax({
     type : "post",
     dataType : "json",
     url : myAjax.ajaxurl,
     data : {action: "search_open_slots"},
     success: function(response) {
      if(response.status == "success") {
        jQuery(".obtained-slots").html(response.data);

      }
      else {
       jQuery(".response-message").html(response.message);
     }
     setTimeout(function () { jQuery(".response-message").html(''); }, 10000);

   }
 });
    
    jQuery(".send-request-to-nurse-group").click( function(e) {
      group = $('.selected-grp').val();
      slot  = $('.select-slot').val();
      msg  = $('.message-for-nurse').val();
      if (confirm("Click OK to continue?")){
        jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "send_request_message",group:group,slot:slot,msg:msg},
         success: function(response) {
          if(response.status == "success") {
            jQuery(".obtained-slots").html(response.data);

          }
          else {
           jQuery(".response-message").html(response.message);
         }
         setTimeout(function () { jQuery(".response-message").html(''); }, 10000);

       }
     });
      }
    }); 
    jQuery(".duplicate-generater").click( function(e) {
      $('.dup-month-area').show();
    });    

  });

</script>
<?php
}