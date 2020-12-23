<?php
require_once ABSPATH . 'wp-content/plugins/nursing-schedule/vendor/autoload.php';
require_once ABSPATH . 'wp-content/plugins/nursing-schedule/inc/Model.php';
/*get value from object or array*/
function nurse_set($data,$value){
	$result = '';
	if(is_object($data)){
		$result = isset($data->$value)?$data->$value:'';
	}
	elseif(is_array ($data)){
		$result = isset($data[$value])?$data[$value]:'';
	}

	return $result;
}
function get_staff_roles(){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$roles = $db->table('staff_roles')->where('user_id',$user_id)->orderBy('role_name', 'ASC')->get();
	return $roles;
}
function get_time_invervals(){
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$intervals = $db->table('shifts_intervals')->orderBy('minuts', 'ASC')->get();
	return $intervals;
}
function get_user_groups(){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$groups = $db->table('staff_group')->orderBy('group_name', 'ASC')->get();
	return $groups;
}
function getslotgroup($slot_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$group = $db->table('slots_group')->where('slot_id',$slot_id)->first();
	return $group;
}
function getshiftIterval($shift_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$interval = $db->table('interval_shifts')->where('shift_id',$shift_id)->first();
	return $interval;
}
function getsigeRole($role_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$role = $db->table('staff_roles')->where('id',$role_id)->first();
	return $role;
}
function get_staff_shifts(){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$slots = $db->table('staff_shifts')->where('user_id',$user_id)->orderBy('shift_name', 'ASC')->get();
	return $slots;
}
function getsingleShift($shift_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$role = $db->table('staff_shifts')->where('id',$shift_id)->first();
	return $role;
}
function getsingleGroup($group_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$groups = $db->table('staff_group')->where('id',$group_id)->first();
	return $groups;
}
function get_staff_slots(){
	global $wpdb;
	$user_id       = get_current_user_id();
	$prefix = $wpdb->prefix;

	$staff_slots = $prefix.'staff_slots';
	$staff_shifts = $prefix.'staff_shifts';
	$staff_roles = $prefix.'staff_roles';

	$query = "
	SELECT slots.id as slot_id , slots.role_id, slots.shift_id, slots.slot_name, shifts.shift_name, roles.role_name
	FROM $staff_slots slots
	LEFT JOIN $staff_shifts shifts ON slots.shift_id = shifts.id
	LEFT JOIN $staff_roles roles ON slots.role_id = roles.id
	WHERE slots.user_id = '$user_id'";

	

	$slots= $wpdb->get_results($query);
	

	return $slots;
}
function get_group_staff($group_id =''){
	global $wpdb;
	$user_id       = get_current_user_id();
	$prefix = $wpdb->prefix;

	$slots_group = $prefix.'slots_group';
	$staff_slots = $prefix.'staff_slots';
	$users_roles = $prefix.'users_roles';
	$user_ids = array();
	$query = "
	SELECT users_roles.user_id as user_id
	FROM $slots_group slots_group
	LEFT JOIN $staff_slots staff_slots ON staff_slots.id = slots_group.slot_id
	LEFT JOIN $users_roles users_roles ON staff_slots.role_id = users_roles.role_id
	WHERE slots_group.group_id = '$group_id'";

	$groups= $wpdb->get_results($query);
	if(!empty($groups)){
		foreach($groups as $group){
			$user_ids[] = $group->user_id;
		}
	}
	return $user_ids;
}
function getsingleSlot($slot_id=''){
	$user_id = get_current_user_id();
	$db 	 = \WeDevs\ORM\Eloquent\Database::instance();
	$role 	 = $db->table('staff_slots')->where('id',$slot_id)->first();
	return $role;
}
function getStaffRoles($user_id){

	global $wpdb;
	$prefix = $wpdb->prefix;

	$staff_roles = $prefix.'staff_roles';
	$users_roles = $prefix.'users_roles';
	$query = "
	SELECT roles.id as role_id , roles.role_name
	FROM $users_roles user_roles
	LEFT JOIN $staff_roles roles ON user_roles.role_id = roles.id
	WHERE user_roles.user_id = '$user_id'";
	if(!empty($query)){
		$roles= $wpdb->get_results($query);

		return $roles;
	}
}


function get_slots_with_data(){

	global $wpdb;
	$user_id       	= get_current_user_id();
	$prefix 		= $wpdb->prefix;
	$staff_slots 	= $prefix.'staff_slots';
	$staff_shifts 	= $prefix.'staff_shifts';
	$staff_group 	= $prefix.'staff_group';
	$slots_group 	= $prefix.'slots_group';

	$query = "
	SELECT user_slots.id as slot_id , user_slots.role_id,user_slots.slot_name,shift.shift_name,shift.shift_color, staff_group.group_name
	FROM $staff_slots user_slots
	LEFT JOIN $staff_shifts shift ON  shift.id = user_slots.shift_id
	LEFT JOIN $slots_group slots_group ON slots_group.slot_id = user_slots.id
	LEFT JOIN $staff_group staff_group ON staff_group.id = slots_group.group_id
	WHERE user_slots.user_id = '$user_id' ORDER BY staff_group.group_name,user_slots.slot_name ASC" ;
	if(!empty($query)){
		$data= $wpdb->get_results($query);

		return $data;

	}
}

function get_roles_with_data(){

	global $wpdb;
	$user_id       = get_current_user_id();
	$prefix = $wpdb->prefix;
	$staff_slots = $prefix.'staff_slots';
	$staff_shifts = $prefix.'staff_shifts';
	$staff_roles = $prefix.'staff_roles';
	$wp_staff_roles = $prefix.'users_roles';

	$query = "
	SELECT user_roles.id as role_id , staff_slots.slot_name,staff_roles.user_id
	FROM $staff_roles user_roles
	LEFT JOIN $wp_staff_roles staff_roles ON  staff_roles.role_id = user_roles.id
	LEFT JOIN $staff_slots staff_slots ON  staff_slots.role_id = user_roles.id
	WHERE user_roles.user_id = '$user_id' ORDER BY staff_slots.slot_name ASC" ;

	if(!empty($query)){
		$data= $wpdb->get_results($query);
		return $data;

	}
}

function get_user_against_role($role_id=''){
	$user_id       = get_current_user_id();
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	/*$users_ids = $db->table('users_roles')->where('role_id',$role_id)->get();*/
	global $wpdb;
	$prefix = $wpdb->prefix;
	$users_roles = $prefix.'users_roles';
	$users = $prefix.'users';

	$query = "
	SELECT users_roles.user_id,users_roles.id,users_roles.role_id
	FROM $users_roles users_roles
	LEFT JOIN $users users ON  users.ID = users_roles.user_id
	WHERE users_roles.role_id = '$role_id' ORDER BY users.display_name ASC" ;
	if(!empty($query)){
		$users_ids= $wpdb->get_results($query);
		return $users_ids;

	}
	/*return $users_ids;*/
}
function printr($data){
	echo('pre>> ');
	print_r($data);
	exit;
}

function get_staff_hours($user_id,$date){

	global $wpdb;
	$prefix = $wpdb->prefix;
	$total_hours = 0;
	$calendar_date = $date;

	$calendar_date =  explode('/',  $calendar_date);

	$day    =  (int)nurse_set($calendar_date,1);
	$month =  (int)nurse_set($calendar_date,0);
	$year   = nurse_set($calendar_date,2);
	$inner_day = $day;
	$new_date = 1;
	$new_year_date = 1;
	$staff_slots = $prefix.'staff_slots';
	$shifts_intervals = $prefix.'shifts_intervals';
	$interval_shifts = $prefix.'interval_shifts';


	$month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

	$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');

	$weekly_days = array('Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7);

	$db     = \WeDevs\ORM\Eloquent\Database::instance();

	for ($week_date=1; $week_date<=7; $week_date++){


		if($inner_day > nurse_set($month_days,$month)){

			if($month == 12){

				$month = 1;
				$year = $year+1;
				$new_date = $new_year_date;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user_id)->get();

				if($schedule->first()){
					foreach($schedule as $date_schedule){
						$query = "
						SELECT shifts_intervals.minuts as minuts
						FROM $staff_slots staff_slots
						JOIN $interval_shifts interval_shifts ON staff_slots.shift_id = interval_shifts.shift_id
						JOIN $shifts_intervals shifts_intervals ON shifts_intervals.id = interval_shifts.interval_id
						WHERE staff_slots.id = '$date_schedule->slot_id'";
						if(!empty($query)){
							$minuts= $wpdb->get_row($query);

							$total_hours = $total_hours +  $minuts->minuts;
						}
					}

				}
				$current_date = $month.'/'. $new_date.'/'.$year;
				$month = 12;
				$new_year_date++;
				$year = $year-1;

			}
			else{
				$new_month = $month + 1;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('user_id',$user_id)->get();
				if($schedule->first()){
					foreach($schedule as $date_schedule){
						$query = "
						SELECT shifts_intervals.minuts as minuts
						FROM $staff_slots staff_slots
						JOIN $interval_shifts interval_shifts ON staff_slots.shift_id = interval_shifts.shift_id
						JOIN $shifts_intervals shifts_intervals ON shifts_intervals.id = interval_shifts.interval_id
						WHERE staff_slots.id = '$date_schedule->slot_id'";
						if(!empty($query)){
							$minuts= $wpdb->get_row($query);

							$total_hours = $total_hours +  $minuts->minuts;
						}
					}

				}
				$current_date = $new_month.'/'. $new_date.'/'.$year;

				$new_date++;
			}

		}
		else{
			$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('user_id',$user_id)->get();

			if($schedule->first()){
				foreach($schedule as $date_schedule){

					$query = "
					SELECT shifts_intervals.minuts as minuts
					FROM $staff_slots staff_slots
					JOIN $interval_shifts interval_shifts ON staff_slots.shift_id = interval_shifts.shift_id
					JOIN $shifts_intervals shifts_intervals ON shifts_intervals.id = interval_shifts.interval_id
					WHERE staff_slots.id = '$date_schedule->slot_id'";
					
					if(!empty($query)){
						$minuts= $wpdb->get_row($query);

						$total_hours = $total_hours +  $minuts->minuts;
					}
					
				}

			}

		}
		$inner_day++;
	}
	$total_hours = $total_hours/60;
	return $total_hours;
}
function get_span_count($user_id,$date){

	$total_hours = 0;

	$calendar_date = $date;
	$calendar_date =  explode('/',  $calendar_date);
	$day    =  (int)nurse_set($calendar_date,1);
	$month =  (int)nurse_set($calendar_date,0);
	$year   = nurse_set($calendar_date,2);
	$inner_day = $day;
	$new_date = 1;
	$new_year_date = 1;

	$month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

	$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');

	$weekly_days = array('Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7);

	$db     = \WeDevs\ORM\Eloquent\Database::instance();

	$span = 0;

	for ($week_date=1; $week_date<=7; $week_date++){
		$counter=1;
		if($inner_day > nurse_set($month_days,$month)){

			if($month == 12){

				$month = 1;
				$year = $year+1;
				$new_date = $new_year_date;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user_id)->where('work_hours',8)->get();
				if($schedule->first()){
					foreach($schedule as $date_schedule){
						if($counter > $span){

							$span++;
						}
						$counter++;
					}

				}
				$current_date = $month.'/'. $new_date.'/'.$year;
				$month = 12;
				$new_year_date++;
				$year = $year-1;

			}
			else{
				$new_month = $month + 1;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('user_id',$user_id)->where('work_hours',8)->get();
				if($schedule->first()){
					foreach($schedule as $date_schedule){
						if($counter > $span){

							$span++;
						}
						$counter++;
					}

				}
				$current_date = $new_month.'/'. $new_date.'/'.$year;

				$new_date++;
			}

		}
		else{
			$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('user_id',$user_id)->where('work_hours',8)->get();
			//printr($schedule);
			if($schedule->first()){
				foreach($schedule as $date_schedule){
					if($counter > $span){

						$span++;
					}
					$counter++;
				}

			}

		}
		$inner_day++;

	}

	return $span;
}

function getStaffSlots($user_id){

	global $wpdb;
	$prefix = $wpdb->prefix;
	$users_roles = $prefix.'users_roles';
	$staff_slots = $prefix.'staff_slots';
	$query = "
	SELECT slots.id as slot_id , slots.slot_name
	FROM $users_roles user_roles
	LEFT JOIN $staff_slots slots ON user_roles.role_id = slots.role_id
	WHERE user_roles.user_id = '$user_id'";
	if(!empty($query)){
		$slots= $wpdb->get_results($query);

		return $slots;
	}
}

function assigned_slot_staff($user_id,$slot_id,$date){

	$is_row = 0;

	$calendar_date = $date;
	$calendar_date =  explode('/',  $calendar_date);
	$day    =  (int)nurse_set($calendar_date,1);
	$month =  (int)nurse_set($calendar_date,0);
	$year   = nurse_set($calendar_date,2);
	$inner_day = $day;
	$new_date = 1;
	$new_year_date = 1;

	$month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

	$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');

	$weekly_days = array('Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7);

	$db     = \WeDevs\ORM\Eloquent\Database::instance();

	for ($week_date=1; $week_date<=7; $week_date++){
		$counter=1;
		if($inner_day > nurse_set($month_days,$month)){

			if($month == 12){

				$month = 1;
				$year = $year+1;
				$new_date = $new_year_date;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user_id)->where('work_hours',8)->where('slot_id',$slot_id)->first();
				if($schedule->first()){

					$is_row = 1;

					break;
					

				}
				$month = 12;
				$new_year_date++;
				$year = $year-1;

			}
			else{
				$new_month = $month + 1;
				$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('user_id',$user_id)->where('work_hours',8)->where('slot_id',$slot_id)->first();
				if($schedule->first()){

					$is_row = 1;

					break;
					
				}

				$new_date++;
			}

		}
		else{
			$schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('user_id',$user_id)->where('work_hours',8)->where('slot_id',$slot_id)->first();
			if($schedule){

				$is_row = 1;

				break;
			}

		}
		$inner_day++;

	}

	return $is_row;

}
function get_slot_data($user_id,$date){

	global $wpdb;
	$prefix = $wpdb->prefix;
	$staff_shifts = $prefix.'staff_shifts';
	$staff_slots = $prefix.'staff_slots';
	$staff_schedule = $prefix.'staff_schedule';

	$calendar_date = $date;
	$calendar_date =  explode('/',  $calendar_date);
	$day    =  (int)nurse_set($calendar_date,1);
	$month =  (int)nurse_set($calendar_date,0);
	$year   = nurse_set($calendar_date,2);

	$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');

	$query = "
	SELECT shifts.shift_name , shifts.shift_color,staff_schedule.work_hours,staff_schedule.id as schedule_id
	FROM $staff_schedule staff_schedule
	LEFT JOIN $staff_slots slots ON staff_schedule.slot_id = slots.id
	LEFT JOIN $staff_shifts shifts ON slots.shift_id = shifts.id
	WHERE staff_schedule.user_id = '$user_id' AND staff_schedule.date = '$day' AND staff_schedule.month = '$months[$month]' AND staff_schedule.year = '$year'";
	if(!empty($query)){
		$data= $wpdb->get_results($query);
		return $data;

		
	}
	
}

function get_user_text($user_id,$date){
	$db     = \WeDevs\ORM\Eloquent\Database::instance();
	$note = $db->table('nusre_schedule_notes')->where('user_id',$user_id)->where('date',$date)->first();

	return $note;
}

function get_user_against_slots($schedule_id){

	global $wpdb;
	$prefix = $wpdb->prefix;
	$users_roles = $prefix.'users_roles';
	$staff_slots = $prefix.'staff_slots';
	$staff_schedule = $prefix.'staff_schedule';
	$staff_shifts = $prefix.'staff_shifts';
	$query = "
	SELECT users_roles.user_id as user_id
	FROM $staff_schedule staff_schedule
	LEFT JOIN $staff_slots slots ON staff_schedule.slot_id = slots.id
	LEFT JOIN $users_roles users_roles ON slots.role_id = users_roles.role_id
	WHERE staff_schedule.id = '$schedule_id'";
	if(!empty($query)){
		$users= $wpdb->get_results($query);

		return $users;
	}
}
function get_shift_against_slot($schedule_id){

	global $wpdb;
	$prefix = $wpdb->prefix;
	$users_roles = $prefix.'users_roles';
	$staff_slots = $prefix.'staff_slots';
	$staff_schedule = $prefix.'staff_schedule';
	$staff_shifts = $prefix.'staff_shifts';
	$query = "
	SELECT shifts.shift_name,staff_schedule.date,staff_schedule.month,staff_schedule.year
	FROM $staff_schedule staff_schedule
	LEFT JOIN $staff_slots slots ON staff_schedule.slot_id = slots.id
	LEFT JOIN $staff_shifts shifts ON slots.shift_id = shifts.id
	WHERE staff_schedule.id = '$schedule_id'";
	if(!empty($query)){
		$users= $wpdb->get_row($query);

		return $users;
	}
}

function get_nursing_employement(){
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$employement = $db->table('employement_status')->get();
	return $employement;
}

/*-----------------
Old Functions
--------------------------*/
function get_nursing_group_by_id($key){

	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$users = $db->table('nursing_group')->where('group_key', $key)->first();

	return $users;
}
function get_schedule_by_month($month,$user_id=''){

	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$month= explode("-",$month);
	$mon = nurse_set($month,0);
	$year = nurse_set($month,1);
	if(!empty($user_id)){
		$users = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->where('owner_id',$user_id)->get();
		return $users;
	}
	$users = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->get();

	return $users;
}

function get_schedule_by_user($user,$month,$single=false,$range=0,$single_date=''){

	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$month= explode("-",$month);
	$mon = nurse_set($month,0);
	$year = nurse_set($month,1);
	if($single_date != ''){

		$user = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->where('user_id', $user)->where('date',$single_date)->get();
		return $user;
	}
	if($single){
		$user = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->where('user_id', $user)->first();
		return $user;
	}
	if($range !=0){
		$current = date("j");
		$week = intval($current)+1+ $range;
		$user = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->where('user_id', $user)->where('date','>',intval($current))->where('date','<=',intval($week))->get();
		return $user;
	}
	$user = $db->table('nusre_schedule')->where('month', $mon)->where('year', $year)->where('user_id', $user)->get();

	return $user;
}

function get_nursing_group(){
	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$groups = $db->table('nursing_group')->get();
	return $groups;
}



function get_shift_id($id){

	$shifts = array(
		1 => '1st (7a-3p)',
		2 => '2nd (3p-11p)',
		3 => '3rd (11p-7a)'
	);
	$shift = nurse_set($shifts,$id);
	return $shift;
}
function get_nursing_group_table_by_id($id){

	$db = \WeDevs\ORM\Eloquent\Database::instance();
	$group = $db->table('nursing_group')->where('id', $id)->first();

	return $group;
}
?>