 
<?php

global $wpdb;

$user_id = get_current_user_id();

?>
<!-- Mobiscroll JS and CSS Includes -->
<!--<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/bb-theme-child/css/mobiscroll.jquery.min.css">
<script src="<?php echo home_url();?>/wp-content/themes/bb-theme-child/js/mobiscroll.jquery.min.js"></script>
-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="<?php echo home_url(); ?>/wp-content/plugins/nursing-schedule/assets/select2.min.css" rel="stylesheet" />
<script src="<?php echo home_url(); ?>/wp-content/plugins/nursing-schedule/assets/select2.min.js"></script>
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
	button.btn.btn-primary {
		background: #337ab7 !important;
		color: #fff !important;
		border-radius: 5px !important;
		float: right;
	}
	#ui-datepicker-div {
		width: 17em !important;
	}
</style>
<div class="row client_calendar_padding">
	<div class="duplicate-month-area">
		<div class="row">
			<?php
			$schedule_id  = (isset($_GET['schedule_id']))?$_GET['schedule_id']:'';
			$current_date  = (isset($_GET['curret_date']))?$_GET['curret_date']:'';
			$current_slot = (isset($_GET['current_slot']))?$_GET['current_slot']:'';
			$start_week_date = (isset($_GET['start_week_date']))?$_GET['start_week_date']:'';
			$employement_status = get_nursing_employement();
			?>
			<div class="col-md-12">
				<div class="response-message">

				</div>
			</div>
			<form class="search-slot-form">
				<input type="hidden" name="notings">
				<?php if(!$schedule_id) : ?>
					<input type="hidden" class="message-type" name="message-type" value="custom">
					<div class="invite-options">
						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleFormControlSelect1">Select Role:</label>
								<?php $roles = get_staff_roles(); ?>
								<select class="form-control rols-container" name="message-type">
									<option value="all">All</option>
									<?php foreach($roles as $role): ?>
										<option value="<?php echo $role->id; ?>"><?php echo $role->role_name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

					</div>
					<?php else:{

						?>
						<div class="status-options">
							<div class="col-md-4">
								<div class="form-group">
									<label for="exampleFormControlSelect1">Exclude Staff:</label>
									<select class="form-control status-container" name="status[]" multiple="multiple">
										<option value="">Select Option</option>
										<option value="all">40 hours or Over</option>
										<?php
										if($employement_status->first()):
											foreach ($employement_status as $employement):
												?>
												<option value="<?php echo $employement->employement_key; ?>">
													<?php echo $employement->name; ?>
												</option>

												<?
											endforeach;
										endif;
										?>
									</select>
								</div>
							</div>

						</div>
						<?php

					} endif; ?>
					<input type="hidden" class="schedule_id" name="schedule_id" value="<?php echo $schedule_id; ?>">

					
					<div class="col-md-12 dup-month-area padding-area">
						<div class="form-group">
							<label for="comment">Message:</label>
							<textarea class="form-control message-for-nurse" rows="5" id="comment"></textarea>
						</div> 
					</div>
					


					<div class="col-md-12 dup-month-area padding-area">
						<button type="submit" class="btn btn-primary send-request-to-nurse-group">Send</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="loading">Loading&#8230;</div>
	<style type="text/css">
		.loading{
			display: none;
		}
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
		.form-group label{
			float: left;
		}
	</style>


	<script type="text/javascript">

		jQuery(document).ready( function($) {

			$('.status-container').select2({
				multiple:true,
				tags: true});

			$( function() {
				$( "#single_datepicker" ).datepicker({

				});
			} );

		/*document.getElementById("myInput").oninput = function() {myFunction()};

		function myFunction() {
			alert("The value of the input field was changed.");
		}*/
		$(document).on('change', '.search-single-date', function() {
			$('.loading').show();
			slot_date = $(this).val();
			slot_id = $('.slots-container').val();

			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "get_schedule_id",slot_date:slot_date,slot_id:slot_id},
				success: function(response) {
					$('.loading').hide();
					if(response.status == "success") {
						jQuery(".response-message").html(response.data);
						$('.schedule_id').val(response.schedule_id);
						
					}
					else {

						jQuery(".response-message").html(response.message);
						
					}
					setTimeout(function () { jQuery(".response-message").html(''); }, 10000);

				}
			});

		});


		jQuery(".send-request-to-nurse-group").click( function(e) {
			e.preventDefault();
			schedule_id = $('.schedule_id').val();
			msg  = $('.message-for-nurse').val();
			msg_type  = $('.message-type').val();
			rols_container  = $('.rols-container').val();
			status  = $('.status-container').val();
			exclude  = $('.exclude-nurse').is(":checked") ? $('.exclude-nurse').val() : '';
			current_date = '<?php echo $current_date; ?>'
			current_slot = '<?php echo $current_slot; ?>'
			start_week_date = '<?php echo $start_week_date; ?>'
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "send_request_message",schedule_id:schedule_id,msg:msg,msg_type:msg_type,exclude:exclude,rols_container:rols_container,current_date:current_date,current_slot:current_slot,status:status,start_week_date:start_week_date},
				success: function(response) {
					if(response.status == "success") {
						jQuery(".obtained-slots").html(response.data);
						$(".send-request-to-nurse-group").html("Resend");


					}
					else {
						if(response.refresh == "yes"){
							window.location.href = home_url + "/make-nurses-schedule";
						}
						else{
							location.reload(); 
						}
						jQuery(".response-message").html(response.message);
					}
					setTimeout(function () { jQuery(".response-message").html(''); }, 10000);

				}
			});
			
		}); 
		jQuery(".duplicate-generater").click( function(e) {
			$('.dup-month-area').show();
		});    

	});

</script>