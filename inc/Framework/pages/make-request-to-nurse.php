<?php
if(session_id() == '')
  session_start(); 
?>
<div>
 
  <?php
  if(isset($_SESSION['poststatus']) && $_SESSION['poststatus']=="Success")
  {
        //echo "<p>Request has been accepted</p>";
    echo '<div class="alert alert-success">
    <strong>Success!</strong> Request Successfully saved.
    </div>';
    unset($_SESSION['poststatus']);
  }
  $user_id = get_current_user_id();
  $address = get_user_meta($user_id,'user_address',true);
  ?>
  <div id="client_content_page">
    <div class="container-fluid">
      <form action="" method="post" id="make_request_form" onsubmit="return validateRequest();">
       <!--<div class="row">
           <div class="col-sm-12 col-xs-12 location_coloumn">
               <input type="text" name="fname" placeholder="Facility" class="location">
           </div>
         </div>-->
        <!--  <div class="row dropdown-row">
           <div class="col-md-6 col-sm-6 col-xs-12 dropdown-list">
            <select class="form-control button_width" id="requestType" name="requestType">
              <option value="">Type</option>
              <option value="CNA">CNA</option>
              <option value="LPN">LPN</option>
              <option value="RN">RN</option>
            </select>
            <br>
          </div> -->
          
           <!--<div class="col-md-6 col-sm-6 col-xs-12 dropdown-list requestQuantityRadio">
           
            <legend class="col-form-label col-sm-12 pt-0 Type01">Quantity</legend>
      
            <div class="form-check">
               <input type="radio" name="requestQuantity" value="1" class="requestQuantity">
               <label class="form-check-label" for="gridRadios1">
               1
               </label>
            </div>
            <div class="form-check">
               <input type="radio" name="requestQuantity" value="2" class="requestQuantity">
               <label class="form-check-label" for="gridRadios2">
               2
               </label>
            </div>
            <div class="form-check">
               <input type="radio" name="requestQuantity" value="3" class="requestQuantity">
               <label class="form-check-label" for="gridRadios2">
               3
               </label>
            </div>
            <div class="form-check">
               <input type="radio" name="requestQuantity" value="4" class="requestQuantity"> 
               <label class="form-check-label" for="gridRadios3">
               4
               </label>
            </div>
            <div class="form-check">
               <input type="radio" name="requestQuantity" value="5" class="requestQuantity">
               <label class="form-check-label" for="gridRadios3">
               5
               </label>
            </div>
         
          </div>-->
          
          <!-- <div class="col-md-6 col-sm-6 col-xs-12 dropdown-list"> -->
           <!--<i class="fa fa-calendar calendar01 request-date" aria-hidde="true"></i>-->
           <!-- <img src="../wp-content/uploads/2018/12/cld1.png" class="calendar01">
           <input type="text" name="requestDate" id="requestDate" class="request-date  request-date01" Placeholder="Date" value="<?php echo date('m/d/Y');?>" autocomplete=off> -->
       <!--   </div> -->
       </div>
       <div class="row">
         <!-- <div class="col-md-6 col-sm-6 col-xs-12 dropdown-list">
          <select class="form-control button_width request_shift" id="requestShift" name="requestShift">
            <option value="">Request Shift</option>
            <option value="1" selected="selected" data-i="0">1st (7a-3p)</option>
            <option value="2">2nd (3p-11p)</option>
            <option value="3">3rd (11p-7a)</option>
          </select> 
        </div> -->
       <!--  
        <div class="col-md-6 col-sm-6 col-xs-12 dropdown-list">
         <input type="text" name="location" id="location" placeholder="Location" value="<?php echo $address!=''?$address:'';?>" class="location">
       </div> -->
       <div class="col-md-12 col-sm-12 col-xs-12 dropdown-list">
         <textarea rows="4" cols="50" name="client_note" id="client_note" placeholder="Client Note"></textarea>
       </div>
       
     </div>
           <!--<div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12 location_coloumn">
                   <input type="text" name="location" id="location" placeholder="Location" value="<?php echo $address!=''?$address:'';?>" class="location">
              </div> 
            </div>-->
            <!--  <div class="row">-->
             <!--   <div class="col-sm-12 col-xs-12 location_coloumn">-->
               <!--      <?php //get_address_form($user_id);?>-->
               <!--   </div>-->
               <!--</div>-->
            <!--<div class="row">
              <div class="col-sm-12 col-xs-12 location_coloumn">
                  <input type="text" name="city" placeholder="City" class="location">
                   
              </div> 
            </div>-->
            <div class="row">
             <div class="col-md-12 col-sm-12 col-xs-12">
               <div class="pull-right">
                
                <input type="hidden" name="makeRequestNonce" value="<?php echo wp_create_nonce('make-Request-To_Nurse-Nonce');?>" >
                <!--<input type="submit" name="save" value="Proceed">-->
                <button type="reset" name="cancel" onclick="resetMakeRequestForm();"> <img src="<?php echo home_url(); ?>/wp-content/uploads/2018/12/cancle_new.png" style="width:134px;height:45px;" class="submit-btn-section"></button>
                <button type="submit" name="save"> <img src="<?php echo home_url(); ?>/wp-content/uploads/2018/12/continue_new.png" style="width:134px;height:44px;" class="submit-btn-section"></button>
              </div>
            </div>
          </div>
        </div>
      </form>

      
    </div>
  </div>
  
  <?php google_suggest('location');?>