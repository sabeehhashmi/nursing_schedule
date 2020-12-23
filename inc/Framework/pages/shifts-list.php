 
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


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
  <div class="row calendar_section" id="nursing_calendar_listing">
    <div class="row">
      <div class="col-sm-12">
        <div class="response-message"></div>
      </div>
      <div class="col-sm-2 text-left">
        <a href="<?php echo home_url('/'); ?>add-shift" class="btn btn-primary stretched-link">Add New</a>
      </div>
    </div>
    <div class="col-sm-12 request-table">
     <div class="row accepted_color">
       <div class="col-sm-10">
        <p class="calendar-request">Manage Shifts</p>
      </div>
    </div>
    <div class="row">
      <?php
      $shifts = get_staff_shifts()

      ?>
      <div class="col-sm-12">
        <div class="collapse in nurse-calender-table">
          <div class="table-wrap">
            <table class="table" border="1"> 
              <tbody>
                <tr>
                  <th>Shift Number</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                <?php
                if(!empty($shifts)):
                  $counter = 1;
                  foreach($shifts as $shift):
                    ?>
                    <tr>
                      <td width="200px"><?php echo $counter; ?></td>
                      <td width="200px"><?php echo $shift->shift_name; ?></td>

                      <td><a href="<?php echo home_url('/').'add-shift/?shift-id='.$shift->id; ?>">Edit </a> / <a href="#" data-del_id="<?php echo $shift->id; ?>" Style="color:red;" class="delete-shift">Delete</a></td>
                    </tr>
                    <?php
                    $counter++;
                  endforeach;
                endif;
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>
</div>

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

    $( ".delete-shift" ).click(function(e) {
      e.preventDefault();
      $('.loading').show();
      shift_id = $(this).data("del_id");

      jQuery.ajax({
       type : "post",
       dataType : "json",
       url : myAjax.ajaxurl,
       data: {action:'delete_shift',shift_id:shift_id},
       success: function(response) {
        if(response.status == "success") {
          jQuery(".response-message").html(response.message);
          $('.loading').hide();
        }
        else {
          $('.loading').hide();
          jQuery(".response-message").html(response.message);
        }
        setTimeout(function () { location.reload(true); }, 5000);

      }

    });
    }); 
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