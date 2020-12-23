<?php
// Get the PHP helper library from https://twilio.com/docs/libraries/php

/*require('/home2/wantechsolutions/nurse.wantechsolutions.com/wp-load.php');

require_once  ABSPATH . 'wp-content/plugins/nursing-schedule/vendor/autoload.php';*/

require('../../../../../wp-load.php');

require_once  ABSPATH . 'wp-content/plugins/nursing-schedule/vendor/autoload.php';

use Twilio\TwiML\MessagingResponse;

$response = new MessagingResponse();

$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
$months=array_flip($months);

/*$from = str_replace('+1', '', $_REQUEST['From']) ;
$args = array(
	'role'         => 'nurse',
	'meta_key'     => 'user_phone_number',
	'meta_value'   => $from,
	'meta_compare' => '=',
); 
$users = get_users( $args );*/

$user_date = explode('xx',  $_REQUEST['Body']);

require_once ABSPATH . 'wp-content/plugins/nursing-schedule/inc/Model.php';

$db     = \WeDevs\ORM\Eloquent\Database::instance();
/*if(!empty($users)){
	foreach ($users as $user) {
		$user_id     = $user->ID;
		break;
	}
	
}*/

$user_id = $user_date[1];

$from = str_replace('+1', '', $_REQUEST['From']) ;
$phone = get_the_author_meta( 'user_phone_number', $user_id );

global $wpdb;

$data_id     = $user_date[0];



if(empty($user_id)){
	printr('ssss');
	return 'ssss';
}


$schedule    = $db->table('staff_schedule')->where('id',$data_id)->first();

$invites    = $db->table('intive_sent')->where('schedule_id',$data_id)->first();

$shift = get_shift_against_slot($data_id);

if(empty($schedule)){

	return 'ssss';
}
if($from != $phone){
	$body_message = 'No schedule found';
	$response->message($body_message);
	print $response;
	return 'ssss';
}
if($schedule && $schedule->user_id != 0){

	$body_message = 'Shift has already been filled. - '.$shift->shift_name.' on '.$months[$schedule->month].'/'.$schedule->date.'/'.$schedule->year ;
	$response->message($body_message);
	print $response;
	return 'ssss';
}

if(empty($invites)){
	$body_message = 'No schedule found' ;
	$response->message($body_message);
	print $response;
	return 'ssss';
}

$table       = $wpdb->prefix.'staff_schedule';
$wpdb->update( 
	$table, 
	array( 
		'user_id'   => $user_id,
		'notification_sent' => 0,
		'work_hours' => 8
	), 
	array( 'id' => $data_id ), 
	array( 
		'%s',
		'%d'
	), 
	array( '%d' ) 
);

$wpdb->query(
	'DELETE  FROM '.$wpdb->prefix.'intive_sent
	WHERE schedule_id = "'.$data_id.'"');

$shift = get_shift_against_slot($data_id);


$body_message = ' Shift Confirmed! - '.$shift->shift_name.' on '.$months[$shift->month].'/'.$shift->date.'/'.$shift->year ;
$response->message($body_message);
print $response;
return 'ssss';
