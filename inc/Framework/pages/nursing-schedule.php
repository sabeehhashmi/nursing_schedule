 <!-- Mobiscroll JS and CSS Includes -->
<!--<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/bb-theme-child/css/mobiscroll.jquery.min.css">
<script src="<?php echo home_url();?>/wp-content/themes/bb-theme-child/js/mobiscroll.jquery.min.js"></script>
-->
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
	div#ui-datepicker-div {
		width: 17em !important;
	}
	.btn.btn-outline-info {
		color: #fff;
		border: 1px solid #00c2e5;
		border-radius: 22px !important;
		background: #00c2e5;
	}
	.btn.btn-outline-info.refresh-btn {
		float: left;
	}
</style>
<div class="row client_calendar_padding">
	<?php

	echo '<div class="wrap">';
	echo '<div class="headin-nurse-schedule"><h2>
	Make Schedule</h2></div>';
	echo '</div>';
	
	
	?>

	<div class="month-wrap">
		<div class="form-group">
			<label for="exampleInputEmail1">Start Date</label>
			<input type="text" class="form-control" id="datepicker">
			
		</div>
		
	</div>
	
	<div class="link-area">
		<div class="row duplicate-month-area">
			<div class="col-md-2">
				<div class="duplicate-generater">
					<button type="button" class="btn btn-primary">Duplicate this schedule</button>
				</div>
			</div>
			<div class="col-md-4 dup-month-area">
				<input type="text" class="form-control" id="datepicke2" placeholder="Select Date">
			</div>
			<div class="col-md-2 dup-month-area">
				<div class="duplicate-saver">
					<button type="button" class="btn btn-primary save-and-duplicate">Save and Duplicate</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="duplicate-generater-a">
					<a href="#" role="button" aria-pressed="true" class="btn btn-outline-info refresh-btn">Refresh</a>
					<a href="<?php echo home_url(); ?>/nurse-list" role="button" aria-pressed="true" class="btn btn-outline-info" >Manage Staff</a>
					<a href="<?php echo home_url(); ?>/roles-list" role="button" aria-pressed="true" class="btn btn-outline-info">Manage Roles</a>
					<a href="<?php echo home_url(); ?>/shifts-list" role="button" aria-pressed="true" class="btn btn-outline-info">Manage Shifts</a>
					<a href="<?php echo home_url(); ?>/slots-list" role="button" aria-pressed="true" class="btn btn-outline-info">Manage Needs</a>

				</div>
			</div>
		</div>
	</div>
	
</div>
</div>
<div class="response-message">

</div>
<!-- <div class="nursing-schedule uper-button">
	<button type="button" class="btn btn-success save-schedule">Save Schedule</button>
</div> -->
<div class="nurses-data-container">
</div>
<!-- <div class="nursing-schedule">
	<button type="button" class="btn btn-success save-schedule">Save Schedule</button>
</div> -->

<div class="loading">Loading&#8230;</div>
<style type="text/css">
	.loading{
		display: none;
	}

	.table-wrap{
		overflow-x:auto;
	}
	.table-wrap select {

		width: auto;
		margin: 10px;
		min-width:146px;

	}
	.table-wrap th, .table-wrap td {

		text-align: center;
		min-width: 110px;

	}
	.nursing-schedule {
		text-align: right;
		margin: 0px 0px 21px;
		display: none;
	}
	.month-wrap {

		width: 153px;
		padding: 6px 10px;

	}
	.response-message{
		padding: 25px 20px 0px 20px;
	}
	.link-area {

		padding: 20px;
		background: #e1e0e0;
		margin-right: 26px;
		border-radius: 10px;
		margin-top: 14px;
		

	}
	.dup-month-area,.duplicate-month-area {
		display: none;
		display: none;
	}
	button.btn.btn-success{
		border-radius: 5px !important;
	}
	button.btn.btn-primary {
		background: #eef6fa !important;
		color: #000 !important;
		border-radius: 5px !important;
	}
</style>

<?php
$week_starts_from = get_option( get_current_user_id().'week_starts_from', false );
?>

<script type="text/javascript">

	jQuery(document).ready( function($) {
		$(document).on('change', '.total_hourse', function() {
			/*$(this).closest("input:hidden").val("Glenn Quagmire");*/
			$(this).parent().find('.custom-hours').val('1');

		});
		
		var dateToday = new Date();
		var week_start_day = <?php echo (($week_starts_from || $week_starts_from == '0') && $week_starts_from != '')?$week_starts_from:1; ?>;
		var default_date = '<?php echo (get_option( get_current_user_id().'schdule_date', false ))?get_option( get_current_user_id().'schdule_date', false ):''; ?>';
		$( function() {
			$( "#datepicker,#datepicke2" ).datepicker({
				/*minDate: dateToday,*/
				beforeShowDay: function(date){ 
					var day = date.getDay(); 
					return [day == week_start_day,""];
				},
				
			});
			$("#datepicker").datepicker('setDate', default_date);
		} );
		$(document).on('change', '.selected-staff', function() {
			color = $(this).data('slot_color');
			staff = jQuery(this).val();
			user_with_date = jQuery(this).data('curret_date');
			updated_id = jQuery(this).data('updated_id');
			slot_data = jQuery(this).data('current_slot');
			
			if(staff == '0'){

				$(this).parent().find('.send-reuqest-link').show();
				$(this).parents('td').css("background-color", '#fff');
			}
			else{
				
				$(this).parent().find('.send-reuqest-link').hide();
				$(this).parents('td').css("background-color", color);
			}
			$('.loading').show();
			thisis = jQuery(this);
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "auto_save_for_staff",selected_staff:staff,user_with_date:user_with_date,updated_id:updated_id,slot_data:slot_data},
				success: function(response) {
					if(response.status == "success") {
						$('.loading').hide();
						$('.duplicate-month-area').show();

						if(response.updated_id){
							thisis.data('updated_id',response.updated_id);
						}

					}
					else {
						jQuery(".response-message").html(response.message);
					}
					
					setTimeout(function () { jQuery(".response-message").html(''); }, 5000);
				}
			})
			
		});
		if(default_date){

			date = default_date;

			$('.loading').show();
			$('.duplicate-month-area').hide();
			month = jQuery(this).val();
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "add_week_staff_preview",date:date},
				success: function(response) {
					if(response.status == "success") {
						jQuery(".nurses-data-container").html(response.data);
						$('.nursing-schedule').show();
						if(response.enable_duplicate == 1){
							$('.duplicate-month-area').show();
						}
					}
					else {
						alert("May be some thing went wrong please try again")
					}
					$('.loading').hide();
				}
			})

		}
		$("#datepicker").change(function(e) {
			date = jQuery(this).val();

			$('.loading').show();
			$('.duplicate-month-area').hide();
			month = jQuery(this).val();
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "add_week_staff_preview",date:date},
				success: function(response) {
					if(response.status == "success") {
						jQuery(".nurses-data-container").html(response.data);
						$('.nursing-schedule').show();
						if(response.enable_duplicate == 1){
							$('.duplicate-month-area').show();
						}
					}
					else {
						alert("May be some thing went wrong please try again")
					}
					$('.loading').hide();
				}
			})   
		});
		jQuery(".duplicate-generater").click( function(e) {
			$('.dup-month-area').show();
		});
		jQuery(".refresh-btn").click( function(e) {
			location.reload();
		});
		/*Save Schedule*/
		jQuery(".save-schedule").click( function(e) {

			var selected_staff        =  $(".selected-staff").map(function(){return $(this).val();}).get();
			
			var updated_id      =  $(".updated_id")
			.map(function(){return $(this).val();}).get();
			var user_with_date = $('.user_with_date').map(function(){return $(this).val();}).get();

			var total_hourse = $('.total_hourse').map(function(){return $(this).val();}).get();
			var slot_data = $('.slot_data').map(function(){return $(this).val();}).get();
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "add_week_data_for_staff",selected_staff:selected_staff,user_with_date:user_with_date,updated_id:updated_id,slot_data:slot_data},
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

			var selected_staff        =  $(".selected-staff").map(function(){return $(this).val();}).get();
			
			var updated_id      =  $(".updated_id")
			.map(function(){return $(this).val();}).get();

			var user_with_date = $('.user_with_date').map(function(){return $(this).val();}).get();

			var slot_data = $('.slot_data').map(function(){return $(this).val();}).get();

			var dublicate_date = $('#datepicke2').val();

			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl,
				data : {action: "dublicate_staff_week_data",selected_staff:selected_staff,updated_id:updated_id,user_with_date:user_with_date,dublicate_date:dublicate_date,slot_data:slot_data},
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