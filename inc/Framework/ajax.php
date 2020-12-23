<?php
// Required if your environment does not handle autoloading
require_once ABSPATH . 'wp-content/plugins/nursing-schedule/vendor/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

$funtions = array(
  'delete_user',
  'add_staff_role',
  'add_staff_shift',
  'add_staff_slot',
  'add_staff_groups',
  'registerNewNurse',
  'add_week_staff_preview',
  'save_start_week_date',
  'add_week_data_for_staff',
  'dublicate_staff_week_data',
  'add_staff_calender_for_subadmin_preview',
  'save_note_for_user',
  'send_request_message',
  'delete_role',
  'delete_shift',
  'delete_slot',
  'delete_group',
  'get_schedule_id',
  'auto_save_for_staff',
  /*---------------------------
Old Functions Of the System Here
-----------------------------------*/
'add_month_data',
'add_week_data',
'add_calender_preview',
'duplicate_month_data',
'dublicate_week_data',
'search_open_slots',
'add_nurse_calender_preview',
'fil_slot',
'add_subadmin_calender_preview',
'add_calender_for_subadmin_preview',
'add_week_preview',
'fill_slot_by_nurse',
);

foreach ($funtions as $funtion) {
 add_action('wp_ajax_'.$funtion, $funtion);
 add_action('wp_ajax_nopriv_'.$funtion, $funtion);
}
/*---Get Schedule ID*/
function get_schedule_id(){
  $calendar_date = $_POST['slot_date'];
  $slot_id = $_POST['slot_id'];
  $output = '';
  if(empty($slot_id)){
    $output = '<div class="alert alert-danger">Please select Need</div>';
    
  }
  if(empty($calendar_date)){
    $output = '<div class="alert alert-danger">Please select date</div>';
    
  }
  if(!empty($output)){
    wp_send_json( array( 'status' => 'error', 'message' => $output) );
  }
  else{
    $calendar_date =  explode('/',  $calendar_date);
    $day    =  (int)nurse_set($calendar_date,1);
    $month =  (int)nurse_set($calendar_date,0);
    $year   = nurse_set($calendar_date,2);
    $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
    
    $db = \WeDevs\ORM\Eloquent\Database::instance();
    $schedule = $db->table('staff_schedule')->where('slot_id',$slot_id)->where('date',$day)->where('year',$year)->where('month',$months[$month])->first();
   // printr($schedule);
    if($schedule){
      $output = '<div class="alert alert-success">Open space found for this date</div>';
      wp_send_json( array( 'status' => 'success', 'data' => $output, 'schedule_id'=>$schedule->id) );
    }
    else{
     $output = '<div class="alert alert-danger">No open space found for this date</div>';
     wp_send_json( array( 'status' => 'error', 'message' => $output) );
   }
 }
}
/*--------Delete User-----------*/
function delete_user(){
  global $wpdb;
  $success = 'no';
  $id = $_POST['user_id'];
  wp_delete_user( $id);
  $wpdb->query(
    'DELETE  FROM '.$wpdb->prefix.'users_roles
    WHERE user_id = "'.$id.'"');
  $wpdb->query(
    'DELETE  FROM '.$wpdb->prefix.'staff_schedule
    WHERE user_id = "'.$id.'"');
  $success = 'yes';
  $output = '<div class="alert alert-success">User deleted successfully</div>';
  wp_send_json( array( 'success' => $success, 'message' => $output) );
}
/*----Delete Role---------*/
function delete_role(){
 global $wpdb;
 $id = $_POST['user_id'];
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'staff_roles
  WHERE id = "'.$id.'"');
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'users_roles
  WHERE role_id = "'.$id.'"');
 $success = 'yes';
 $output = '<div class="alert alert-success">Role Deleted successfully</div>';
 wp_send_json( array( 'success' => $success, 'message' => $output) );
}
/*----Delete Role---------*/
function delete_group(){
 global $wpdb;
 $id = $_POST['group_id'];
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'staff_group
  WHERE id = "'.$id.'"');
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'slots_group
  WHERE group_id = "'.$id.'"');
 $success = 'yes';
 $output = '<div class="alert alert-success">Group Deleted successfully</div>';
 wp_send_json( array( 'success' => $success, 'message' => $output) );
}
/*----Delete Shift---------*/
function delete_shift(){
 global $wpdb;
 $id = $_POST['shift_id'];
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'staff_shifts
  WHERE id = "'.$id.'"');
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'interval_shifts
  WHERE shift_id = "'.$id.'"'
);
 $success = 'yes';
 $output = '<div class="alert alert-success">Shift Deleted successfully</div>';
 wp_send_json( array( 'success' => $success, 'message' => $output) );
}
/*----Delete Slot---------*/
function delete_slot(){
 global $wpdb;
 $id = $_POST['slot_id'];
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'staff_slots
  WHERE id = "'.$id.'"');
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'staff_schedule
  WHERE slot_id = "'.$id.'"');
 $wpdb->query(
  'DELETE  FROM '.$wpdb->prefix.'slots_group
  WHERE slot_id = "'.$slot_id.'"');
 $success = 'yes';
 $success = 'yes';
 $output = '<div class="alert alert-success">Need Deleted successfully</div>';
 wp_send_json( array( 'success' => $success, 'message' => $output) );
}

/*---------Send Request----------*/
function send_request_message(){
  global $wpdb;

  $schedule_id   = $_REQUEST['schedule_id'];
  $start_week_date   = $_REQUEST['start_week_date'];
  $status = explode(',', $_REQUEST['status']) ;
  $msg     = $_REQUEST['msg'];
  $exclude = $_REQUEST['exclude'];
  $message_type = $_REQUEST['msg_type'];
  $rols_container = $_REQUEST['rols_container'];
  $current_user_id = get_current_user_id();
  $get_current_user = get_userdata(get_current_user_id());
  if($message_type == 'custom'){

    if($rols_container == 'all'){
      $args = array(
        'role'         => 'nurse',
        'orderby'      => 'display_name',
        'order'        => 'ASC',
        'include'      => $user_ids,
        'meta_query'   => array(
          'relation'   => 'AND',
          array(
            'key'     => 'nurse_subadmin',
            'value'   => $current_user_id,
            'compare' => '='
          )
        )
      );
      $users = get_users( $args );
    }
    else{

      $users = get_user_against_role($rols_container);
    }
    
    
  }
  else{
    if($schedule_id == 001){
      $calandar_date = $_POST['current_date'];
      $current_slot = $_POST['current_slot'];

      $table          = $wpdb->prefix.'staff_schedule';
      $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');        
      $calendar_date2   = explode('/', $calandar_date);
      $selected_date   = str_replace(' ', '',nurse_set($calendar_date2,1));
      $selected_year   = str_replace(' ', '',nurse_set($calendar_date2,2));
      $selected_month  = nurse_set($months,str_replace(' ', '', nurse_set($calendar_date2,0)));
      $db = \WeDevs\ORM\Eloquent\Database::instance();
      $shcdule_exists = $db->table('staff_schedule')->where('slot_id',$current_slot)->where('date',$selected_date)->where('year', $selected_year)->where('month',$selected_month)->first();
      if($shcdule_exists){

        $schedule_id = $shcdule_exists->id;

      }
      else
      {
        $wpdb->insert( 
         $table, 
         array( 
          'user_id'      => 0, 
          'month'        => $selected_month,
          'year'         => $selected_year,
          'date'         => $selected_date,
          'owner_id'     => $current_user_id,
          'work_hours'   => 0,
          'slot_id'      => $current_slot,
        ), 
         array( 
          '%s', 
          '%s',
          '%s',
          '%s',
          '%s',
          '%s'

        ) 
       );
        $schedule_id = $wpdb->insert_id;
      }
    }
    $users = get_user_against_slots($schedule_id);
    $shift = get_shift_against_slot($schedule_id);

  }
  $output ='';
  if(empty($msg) && $message_type == 'custom'){
    $output .= '<div class="alert alert-danger">Please Enter A Message</div>';
  }
  if($message_type == 'custom' && $rols_container == ''){
   $output .= '<div class="alert alert-danger">Sorry Cant send message</div>';

 }
 if(!empty($output)){
  wp_send_json( array( 'status' => 'error', 'message' => $output) );
}
// Your Account SID and Auth Token from twilio.com/console
$sid = 'AC143b19f59aeb7014ad55bafcd7418d0f';
$token = '43ab2bbf73d16d1071baff34cb7dabf4';


$client = new \Twilio\Rest\Client($sid, $token);
// Use the client to do fun stuff like send text messages!
if(!empty($users)){
  foreach($users as $user){

    $user_id = (isset($user->user_id))?$user->user_id:$user->ID;
    $employement_status = get_the_author_meta( 'employement_status', $user_id );
    
    if(!empty($status)){

      if(in_array($employement_status,$status)){
        continue;
      }
      if(in_array('all',$status)){
        $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December'); 
        $months=array_flip($months);
        $calendar_date= $months[$shift->month].'/'.$shift->date.'/'.$shift->year;

        $total_hourse = get_staff_hours($user_id,$start_week_date);
        if($total_hourse >= 40){
          continue;
        }
      }
    }
    $phone_array = substr(get_the_author_meta( 'user_phone_number', $user_id ),0,1);
    $phone = '+1'.get_the_author_meta( 'user_phone_number', $user_id );
    $user_data = get_user_by( 'id', $user_id);
    $display_name = $user_data->display_name;
    try {
      if($message_type == 'custom'){
        $body_message = ' You have received a message from '.$get_current_user->display_name.': '.$msg;

      }
      else{
        $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December'); 
        $months=array_flip($months);
        $body_message = 'Hey '.$display_name.'!, You have received a message from '.$get_current_user->display_name.': '.$shift->shift_name.' on '.$months[$shift->month].'/'.$shift->date.'/'.$shift->year.' is available. If interested, reply with code '.$schedule_id.'xx'.$user_id.'.' ;
        if($msg){
          $body_message .=' Extra note: ' .$msg;
        }
      }
      $phone = str_replace('-', '', $phone);
      if(strlen($phone) == 12 && $phone_array != '0'){

        $client->messages->create(
    // the number you'd like to send the message to
         $phone,
         array(
        // A Twilio phone number you purchased at twilio.com/console
          'from' => '+17045591986',
        // the body of the text message you'd like to send
          'body' => $body_message
        )
       );
        
      }

      /*printr($user_week_schedule);*/
      if($message_type != 'custom'){
        $table       = $wpdb->prefix.'staff_schedule';
        $wpdb->update( 
          $table, 
          array( 
            'notification_sent'   => 1, 
          ), 
          array( 'id' => $schedule_id ), 
          array( 
            '%s', 
          ), 
          array( '%d' ) 
        );
      }
      $table = $wpdb->prefix.'intive_sent';
      $wpdb->insert( 
       $table, 
       array( 
        'schedule_id' => $schedule_id, 
      ), 
       array( 
        '%s',
      ) 
     );
    }
    catch (TwilioException $ex) {
     $output = '<div class="alert alert-danger">'.$ex->getMessage().'</div>';
     wp_send_json( array( 'status' => 'error', 'message' => $output) );
   }
 }
}
$refresh = 'no';
if($schedule_id ){
  $refresh = 'yes';
}
$output = '<div class="alert alert-success">Request has been sent successfully</div>';
wp_send_json( array( 'status' => 'error', 'message' => $output,'refresh'=>$refresh) );


}
/*Add Calender View For SubAdmin*/
function add_staff_calender_for_subadmin_preview(){


  global $wpdb;

  $current_user_id = get_current_user_id();
  $get_roles_with_data = get_roles_with_data();
  $orderby = ($_REQUEST['order_by'])?$_REQUEST['order_by']:'';
  $allow_single_date = ($_REQUEST['allow_single_date'])?$_REQUEST['allow_single_date']:'';
  
  $make_schedul_link = 'make-nurses-schedule-copy';
  $users = get_users( $args );

  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

  $calendar_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
  
  $weekly_days = array('Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7);
  $today_date = $weekly_days[date('l')];
  /*$remaing_days = ($_REQUEST['date'])?7: 8 - $today_date;*/
  $number_of_days = ($allow_single_date)?1:7;
  $calendar_date =  explode('/',  $calendar_date);
  $day    =  (int)nurse_set($calendar_date,1);
  $month =  (int)nurse_set($calendar_date,0);
  $year   = nurse_set($calendar_date,2);
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $inner_day = $day;
  $new_date = 1;
  $new_year_date = 1;
  $shifts = array(1 =>'Day',2=>'Eve',3=>'Night');
  $class = array(1 =>'day-slot',2=>'eve-slot',3=>'night-slot');
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  $orderby = ($_REQUEST['order_by'])?$_REQUEST['order_by']:'';
  $staff_id = ($_REQUEST['staff_id'])?$_REQUEST['staff_id']:'';

  ob_start();
  echo '<div class="table-wrap">';
  echo '<table class="table">';
  echo ' <tr>';
  echo '<th style="text-align: center;">Name</th>';
  echo '<th class="hidden-cols" style="text-align: center;">Hours</th>';
  echo '<th class="hidden-cols" style="text-align: center;">Roles</th>';
  echo '<th style="text-align: center;" class="hidden-cols">Status</th>';
  for ($date=1; $date<=$number_of_days; $date++){

    if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $current_date = $month.'/'. $new_date.'/'.$year;
        echo '<th align="center" style="text-align: center;font-size:16px;">'.$current_date.'</th>';
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        echo '<th align="center" style="text-align: center;font-size:16px;">'.$current_date.'</th>';
        $new_date++;
      }

    }
    else{
      $current_date = $month.'/'. $inner_day.'/'.$year;
      echo '<th align="center" style="text-align: center;font-size:16px;">'.$current_date.'</th>';

    }
    $inner_day++;

  }
  echo ' </tr>';
  $groups = get_user_groups();
  $added_users = array();
  foreach($groups as $group){

    $user_ids = get_group_staff($group->id);
    if($orderby == 'name'){
      $args = array(
        'role'         => 'nurse',
        'orderby'      => 'display_name',
        'order'        => 'ASC',
        'include'      => $user_ids,
        'meta_query' => array(
          'relation' => 'AND',
          array(
            'key'     => 'nurse_subadmin',
            'value'   => $current_user_id,
            'compare' => '='
          )
        )
      );
    }
    elseif($staff_id){

     $args = array(
      'role'         => 'nurse',
      'orderby'      => 'display_name',
      'order'        => 'ASC',
      'include'      => array($staff_id),
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key'     => 'nurse_subadmin',
          'value'   => $current_user_id,
          'compare' => '='
        )
      )
    );

   }
   else{
     $args = array(
      'role'         => 'nurse',
      'meta_key' => 'employement_status',
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'include'      => $user_ids,
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key'     => 'nurse_subadmin',
          'value'   => $current_user_id,
          'compare' => '='
        )
      )
    ); 

   }
   
   $users = get_users( $args );

   foreach ($users as $user) {
    if(in_array($user->ID, $added_users)){
      continue;
    }
    ?>

    <?php
    $added_users[] = $user->ID;

    $calendar_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
    $standing_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
    $total_hourse = get_staff_hours($user->ID,$calendar_date);
    $span = get_span_count($user->ID,$calendar_date);
    $calendar_date =  explode('/',  $calendar_date);
    $day    =  (int)nurse_set($calendar_date,1);
    $month =  (int)nurse_set($calendar_date,0);
    $year   = nurse_set($calendar_date,2);
    $inner_day = $day;
    $new_date = 1;
    $new_year_date = 1;
    $span = ($span == 0)?1:$span;

    $spans = $span + 1;
    $staff_roles = getStaffRoles($user->ID);
    $user_id = $user->ID;

    $user_data= get_user_by( 'id', $user_id );

    echo '<tr>';
    echo '<td class="nurse-name" rowspan="'.$spans.'"><b>' . esc_html( $user->display_name ) . '</b></td>';
    echo ' <td class="nurse-group hidden-cols" rowspan="'.$spans.'">'.$total_hourse.'</td><td class="nurse-group hidden-cols" rowspan="'.$spans.'" style="min-width:80px;">';
    $slot_comma = '';
    $slot_counter = 1;
    if(!empty($staff_roles)){
      foreach($staff_roles as $staff_role){
        if($slot_counter > 1)
          $slot_comma=',';
        echo $slot_comma.' '. $staff_role->role_name;

        $slot_counter++;
      }
    }
    echo '</td>  
    <td class="nurse-name hidden-cols" rowspan="'.$spans.'">' . get_the_author_meta('employement_status',$user->ID) . '</td>
    </tr>';
    $slot_counter = 1;
    for($tr=0; $tr < $span; $tr++){

     $calendar_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
     $calendar_date =  explode('/',  $calendar_date);
     $day    =  (int)nurse_set($calendar_date,1);
     $month =  (int)nurse_set($calendar_date,0);
     $year   = nurse_set($calendar_date,2);
     $inner_day = $day;
     $new_date = 1;
     $new_year_date = 1;
     $db     = \WeDevs\ORM\Eloquent\Database::instance();
     echo '<tr>'; 
     for ($week_date=1; $week_date<=$number_of_days; $week_date++){

      if($inner_day > nurse_set($month_days,$month)){

        if($month == 12){

          $month = 1;
          $year = $year+1;
          $new_date = $new_year_date;
          $current_date = $month.'/'. $new_date.'/'.$year;
          $schedule    = get_slot_data($user_id,$current_date);

          if(isset($schedule[$tr])){
            $slt_schedul = $schedule[$tr];
            $uniqueclass = 'td-'.uniqid()
            ?>
            <style type="text/css">
              td .<?php echo $uniqueclass ?>{
                background-color:<?php echo $slt_schedul->shift_color; ?>;
                padding: 12px;
                border-radius: 5px;
              }
            </style>
            <?php
            echo' <td class="nurse-group" style="min-width:200px;"><div class="'.$uniqueclass.'">'.$slt_schedul->shift_name.'</div></td>';
          }
          else{
            if($tr == 0){
              $note = get_user_text($user_id,$current_date);
              ?>
              <td class="nurse-group" >
                <select class="form-control selected-note" id="requestShift" data-date="<?php echo $current_date; ?>" data-user_id="<?php echo $user_id; ?>" data-shift_color="<?php echo $slt_schedul->shift_color; ?>">
                  <?php if($note): ?>
                    <option value="">Mark Available</option>
                  <?php endif; ?>
                  <option value="Off" <?php echo ($note && $note->note == 'Off')?'selected':'';?>>Off</option>
                  <option value="Vacation" <?php echo ($note && $note->note == 'Vacation')?'selected':'';?>>Vacation</option>
                  <option value="Vacation" <?php echo ($note && $note->note == 'Call-Out')?'selected':'';?>>Call-Out</option>
                </select>
              </td>
              <?php
            }
            else{
             echo' <td class="nurse-group" >
             <br/><br/>
             </td>';
           }
         }
         $month = 12;
         $new_year_date++;
         $year = $year-1;

       }
       else{
        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        $schedule    = get_slot_data($user_id,$current_date);

        if(isset($schedule[$tr])){
          $slt_schedul = $schedule[$tr];
          $uniqueclass = 'td-'.uniqid();
          ?>
          <style type="text/css">
           td .<?php echo $uniqueclass ?>{
            background-color:<?php echo $slt_schedul->shift_color; ?>;
            padding: 12px;
            border-radius: 5px;
          }
        </style>
        <?php
        echo' <td class="nurse-group" style="min-width:200px;"><div class="'.$uniqueclass.'">'.$slt_schedul->shift_name.' </div></td>';
      }
      else{
        if($tr == 0){
          $note = get_user_text($user_id,$current_date);
          ?>
          <td class="nurse-group" >
            <select class="form-control selected-note" id="requestShift" data-date="<?php echo $current_date; ?>" data-user_id="<?php echo $user_id; ?>" data-shift_color="<?php echo $slt_schedul->shift_color; ?>" >

             <?php if($note): ?>
              <option value="">Mark Available</option>
            <?php endif; ?>

            <option value="Off" <?php echo ($note && $note->note == 'Off')?'selected':'';?>>Off</option>
            <option value="Vacation" <?php echo ($note && $note->note == 'Vacation')?'selected':'';?>>Vacation</option>
            <option value="Vacation" <?php echo ($note && $note->note == 'Call-Out')?'selected':'';?>>Call-Out</option>
          </select>
        </td>
        <?php
      }
      else{
       echo' <td class="nurse-group" >
       <br/><br/>
       </td>';
     }
   }


   $new_date++;
 }

}

else{
 $current_date = $month.'/'. $inner_day.'/'.$year;
 $schedule    = get_slot_data($user_id,$current_date);


 if(isset($schedule[$tr])){
  $slt_schedul = $schedule[$tr];
  $uniqueclass = 'td-'.uniqid();
  ?>
  <style type="text/css">
    td .<?php echo $uniqueclass ?>{
      background-color:<?php echo $slt_schedul->shift_color; ?> !important;
      padding: 12px;
      border-radius: 5px;
    }

  </style>
  <?php
  echo' <td class="nurse-group" style="min-width:200px;"><div class="'.$uniqueclass.'">'.$slt_schedul->shift_name.'</div></td>';
}
else{
  if($tr == 0){

   $note = get_user_text($user_id,$current_date);
   ?>
   <td class="nurse-group" >
    <select class="form-control selected-note" id="requestShift" data-date="<?php echo $current_date; ?>" data-user_id="<?php echo $user_id; ?>" >
     <?php if($note): ?>
      <option value="">Mark Available</option>
    <?php endif; ?>          
    <option value="Off" <?php echo ($note && $note->note == 'Off')?'selected':'';?>>Off</option>
    <option value="Vacation" <?php echo ($note && $note->note == 'Vacation')?'selected':'';?>>Vacation</option>
    <option value="Vacation" <?php echo ($note && $note->note == 'Call-Out')?'selected':'';?>>Call-Out</option>
  </select>
</td>
<?php
}
else{
 echo' <td class="nurse-group" >
 <br/><br/>
 </td>';
}
}
}

$inner_day++;
}
echo '</tr>';
$slot_counter++;
}

}

}

echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );



}
/*Duplicate Schedule*/
function dublicate_staff_week_data(){

  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  global $wpdb;
  $table          = $wpdb->prefix.'staff_schedule';
  $updated_ids    = $_REQUEST['updated_id'];
  $user_with_date = $_REQUEST['user_with_date'];
  $slots_data = $_REQUEST['selected_staff'];
  $slot_ids = $_REQUEST['slot_data'];
  $current_user_id = get_current_user_id();
  $dublicate_date   = $_REQUEST['dublicate_date'];

  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
  $calendar_date   =  explode('/',  $dublicate_date);
  $day   = (int) nurse_set($calendar_date,1);
  $month = (int) nurse_set($calendar_date,0);
  $year  = nurse_set($calendar_date,2);
  $schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$day)->where('owner_id',$current_user_id)->first();
 /* if($schedule) {
    $output = '<div class="alert alert-danger">Schedule for this week already added</div>';
    return wp_send_json( array( 'status' => 'error', 'message' => $output ) );
  }*/
  $slots = get_slots_with_data();
  if(!empty($slots)){
    $counter = 0;
    foreach ($slots as $slot_data) {

      $calendar_date = $_REQUEST['dublicate_date'];
      $calendar_date =  explode('/',  $calendar_date);
      $day    =  (int)nurse_set($calendar_date,1);
      $month =  (int)nurse_set($calendar_date,0);
      $year   = nurse_set($calendar_date,2);
      $group_key =  get_user_meta($user->id,'nursing_group',true);
      $inner_day = $day;
      $new_date = 1;
      $new_year_date = 1; 
      $user = explode(",",$sngl_user_data);
      if($slot_data != 0){
        $work_hours = 8;
      }
      else{
        $work_hours = 0;
      }

      $key_counter = $key + 1;

      for ($i=0; $i<=6; $i++){

       if($inner_day > nurse_set($month_days,$month)){
        if($month == 12){
          $month = 1;
          $year = $year+1;
          $new_date = $new_year_date;
          $current_date = $month.'/'. $new_date.'/'.$year;
          $note    = $db->table('nusre_schedule_notes')->where('user_id',nurse_set($slots_data,$counter))->where('date',$current_date)->first();
         /* if($note){
           $wpdb->query(
            'DELETE  FROM '.$wpdb->prefix.'nusre_schedule_notes
            WHERE id = "'.$note->id.'"');
          }*/
          $shcdule_exists = $db->table('staff_schedule')->where('slot_id',nurse_set($slot_ids,$counter))->where('date',$new_date)->where('year', $year)->where('month',$months[$month])->first();
          if(empty($note)){
           if($shcdule_exists){

            $schedule_id = $shcdule_exists->id;

            $wpdb->update( 
             $table, 
             array( 
               'user_id'      => nurse_set($slots_data,$counter), 
               'month'        => $months[$month],
               'year'         => $year,
               'date'         => $new_date,
               'owner_id'     => $current_user_id,
               'work_hours'   => $work_hours,
               'slot_id'      => nurse_set($slot_ids,$counter),
             ), 
             array( 'id' => $schedule_id ), 
             array( 
               '%s', 
               '%s',
               '%s',
               '%s',
               '%s',
               '%s'
             ), 
             array( '%d' ) 
           );

          }
          else{
            $wpdb->insert( 
             $table, 
             array( 
              'user_id'      => nurse_set($slots_data,$counter), 
              'month'        => $months[$month],
              'year'         => $year,
              'date'         => $new_date,
              'owner_id'     => $current_user_id,
              'work_hours'   => $work_hours,
              'slot_id'      => nurse_set($slot_ids,$counter),

            ), 
             array( 
               '%s', 
               '%s',
               '%s',
               '%s',
               '%s',
               '%s'
             ) 
           );
          }
        }
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        $note    = $db->table('nusre_schedule_notes')->where('user_id',nurse_set($slots_data,$counter))->where('date',$current_date)->first();
        /*if($note){
         $wpdb->query(
          'DELETE  FROM '.$wpdb->prefix.'nusre_schedule_notes
          WHERE id = "'.$note->id.'"');
        }*/
        $shcdule_exists = $db->table('staff_schedule')->where('slot_id',nurse_set($slot_ids,$counter))->where('date',$new_date)->where('year', $year)->where('month',$months[$new_month])->first();
        if(empty($note)){
         if($shcdule_exists){

          $schedule_id = $shcdule_exists->id;

          $wpdb->update( 
           $table, 
           array( 
             'user_id'      => nurse_set($slots_data,$counter), 
             'month'        => $months[$new_month],
             'year'         => $year,
             'date'         => $new_date,
             'owner_id'     => $current_user_id,
             'work_hours'   => $work_hours,
             'slot_id'      => nurse_set($slot_ids,$counter),
           ), 
           array( 'id' => $schedule_id ), 
           array( 
             '%s', 
             '%s',
             '%s',
             '%s',
             '%s',
             '%s'
           ), 
           array( '%d' ) 
         );

        }
        else{
          $wpdb->insert( 
           $table, 
           array( 
            'user_id'      => nurse_set($slots_data,$counter), 
            'month'        => $months[$new_month],
            'year'         => $year,
            'date'         => $new_date,
            'owner_id'     => $current_user_id,
            'work_hours'   => $work_hours,
            'slot_id'      => nurse_set($slot_ids,$counter),
          ), 
           array( 
             '%s', 
             '%s',
             '%s',
             '%s',
             '%s',
             '%s'
           ) 
         );
        }
      }
      $new_date++;
    }

  }
  else{
    $current_date = $month.'/'. $inner_day.'/'.$year;
    $note    = $db->table('nusre_schedule_notes')->where('user_id',nurse_set($slots_data,$counter))->where('date',$current_date)->first();
    /*if($note){
     $wpdb->query(
      'DELETE  FROM '.$wpdb->prefix.'nusre_schedule_notes
      WHERE id = "'.$note->id.'"');
    }*/
    $shcdule_exists = $db->table('staff_schedule')->where('slot_id',nurse_set($slot_ids,$counter))->where('date',$inner_day)->where('year', $year)->where('month',$months[$month])->first();
    if(empty($note)){
     if($shcdule_exists){

      $schedule_id = $shcdule_exists->id;

      $wpdb->update( 
       $table, 
       array( 
        'user_id'      => nurse_set($slots_data,$counter), 
        'month'        => $months[$month],
        'year'         => $year,
        'date'         => $inner_day,
        'owner_id'     => $current_user_id,
        'work_hours'   => $work_hours,
        'slot_id'      => nurse_set($slot_ids,$counter),
      ), 
       array( 'id' => $schedule_id ), 
       array( 
        '%s', 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
      ), 
       array( '%d' ) 
     );

    }
    else{
      $wpdb->insert( 
       $table, 
       array( 

        'user_id'      => nurse_set($slots_data,$counter), 
        'month'        => $months[$month],
        'year'         => $year,
        'date'         => $inner_day,
        'owner_id'     => $current_user_id,
        'work_hours'   => $work_hours,
        'slot_id'      => nurse_set($slot_ids,$counter),
      ), 
       array( 
         '%s', 
         '%s',
         '%s',
         '%s',
         '%s',
         '%s'
       ) 
     );
    }
  }

}
$inner_day++;
$counter++;

}

}
}
$output = '<div class="alert alert-success">Schedule have been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
/*Save Week Data to DataBase*/
function add_week_data_for_staff(){
  global $wpdb;
  $table          = $wpdb->prefix.'staff_schedule';
  $updated_ids    = $_REQUEST['updated_id'];
  $user_with_date = $_REQUEST['user_with_date'];
  $slots_data = $_REQUEST['selected_staff'];
  $slot_ids = $_REQUEST['slot_data'];
  $current_user_id = get_current_user_id();

  $make_schedul_link = 'make-nurses-schedule-copy';

  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');

  if(!empty($slots_data)){

    foreach ($slots_data as $key => $slot_data) {

      $calender_date   = nurse_set($user_with_date,$key);        
      $calendar_date   = explode('/', $calender_date);
      $selected_date   = str_replace(' ', '',nurse_set($calendar_date,1));
      $selected_year   = str_replace(' ', '',nurse_set($calendar_date,2));
      $selected_month  = nurse_set($months,str_replace(' ', '', nurse_set($calendar_date,0)));
      if($slot_data != 0){
        $work_hours = 8;
      }
      else{
        $work_hours = 0;
      }
      if(nurse_set($updated_ids,$key)){

       $wpdb->update( 
         $table, 
         array( 
           'user_id'      => $slot_data, 
           'month'        => $selected_month,
           'year'         => $selected_year,
           'date'         => $selected_date,
           'owner_id'     => $current_user_id,
           'work_hours'   => $work_hours,
           'slot_id'      => nurse_set($slot_ids,$key),
         ), 
         array( 'id' => nurse_set($updated_ids,$key) ), 
         array( 
           '%s', 
           '%s',
           '%s',
           '%s',
           '%s',
           '%s'
         ), 
         array( '%d' ) 
       );

     }
     else{
      $wpdb->insert( 
       $table, 
       array( 
        'user_id'      => $slot_data, 
        'month'        => $selected_month,
        'year'         => $selected_year,
        'date'         => $selected_date,
        'owner_id'     => $current_user_id,
        'work_hours'   => $work_hours,
        'slot_id'      => nurse_set($slot_ids,$key),
      ), 
       array( 
        '%s', 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
        
      ) 
     );
    }
  }
}

$output = '<div class="alert alert-success">Schedule has been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );

}
/*Save data for each drop down*/
function auto_save_for_staff(){
  global $wpdb;
  $table          = $wpdb->prefix.'staff_schedule';
  $updated_id    = $_REQUEST['updated_id'];
  $user_with_date = $_REQUEST['user_with_date'];
  $selected_staff = $_REQUEST['selected_staff'];
  $slot_id = $_REQUEST['slot_data'];
  $current_user_id = get_current_user_id();

  $make_schedul_link = 'make-nurses-schedule-copy';

  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');


  $calender_date   = $user_with_date;        
  $calendar_date2   = explode('/', $calender_date);
  $selected_date   = str_replace(' ', '',nurse_set($calendar_date2,1));
  $selected_year   = str_replace(' ', '',nurse_set($calendar_date2,2));
  $selected_month  = nurse_set($months,str_replace(' ', '', nurse_set($calendar_date2,0)));
  
  if($selected_staff != 0){
    $work_hours = 8;
  }
  else{
    $work_hours = 0;
  }
  if($updated_id){

   $wpdb->update( 
     $table, 
     array( 
       'user_id'      => $selected_staff, 
       'month'        => $selected_month,
       'year'         => $selected_year,
       'date'         => $selected_date,
       'owner_id'     => $current_user_id,
       'work_hours'   => $work_hours,
       'slot_id'      => $slot_id,
     ), 
     array( 'id' => $updated_id ), 
     array( 
       '%s', 
       '%s',
       '%s',
       '%s',
       '%s',
       '%s'
     ), 
     array( '%d' ) 
   );

 }
 else{
  $wpdb->insert( 
   $table, 
   array( 
    'user_id'      => $selected_staff, 
    'month'        => $selected_month,
    'year'         => $selected_year,
    'date'         => $selected_date,
    'owner_id'     => $current_user_id,
    'work_hours'   => $work_hours,
    'slot_id'      => $slot_id,
  ), 
   array( 
    '%s', 
    '%s',
    '%s',
    '%s',
    '%s',
    '%s'

  ) 
 );
  $lastid = $wpdb->insert_id;
}


$output = '<div class="alert alert-success">Schedule has been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output, 'updated_id' => $lastid ) );

}
/*Save Note*/
function save_note_for_user(){
  global $wpdb;
  $table   = $wpdb->prefix.'nusre_schedule_notes';
  $user_id =$_POST['user_id'];
  $date    = $_POST['date'];
  $user_note    = $_POST['note'];

  $db      = \WeDevs\ORM\Eloquent\Database::instance();
  $note    = $db->table('nusre_schedule_notes')->where('user_id',$user_id)->where('date',$date)->first();
  if(empty( $user_note)){
   $wpdb->query(
    'DELETE  FROM '.$wpdb->prefix.'nusre_schedule_notes
    WHERE id = "'.$note->id.'"');
 }
 else{
   if($note){
     $wpdb->update( 
       $table, 
       array( 
         'user_id' => $user_id,
         'note'    => $user_note,
         'date'    => $date

       ), 
       array( 'id' => $note->id ), 
       array( 
         '%s', 
         '%s',
         '%s'
       ), 
       array( '%d' ) 
     );
   }
   else{
    $wpdb->insert( 
     $table, 
     array( 
       'user_id' => $user_id,
       'note'    => $user_note,
       'date'    => $date

     ), 
     array( 
      '%s', 
      '%s',
      '%s'

    ) 
   );

  }
}
$output = '<div class="alert alert-success">Schedule has been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
/*Add week preview for the nurse*/
function add_week_staff_preview(){

  global $wpdb;

  $current_user_id = get_current_user_id();

  $args = array(
    'role'         => 'nurse',
    'meta_key'     => 'nurse_subadmin',
    'meta_value'   => $current_user_id,
    'meta_compare' => '=',
  ); 

  $make_schedul_link = 'make-nurses-schedule-copy';

  $users = get_users( $args );

  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

  $calendar_date = $_REQUEST['date'];
  update_option( get_current_user_id().'schdule_date', $calendar_date );
  setcookie('schdule_date', $calendar_date, time() + (86400 * 30), "/");
  $start_calendar_date = $calendar_date;
  $calendar_date =  explode('/',  $calendar_date);
  
  $day    =  (int)nurse_set($calendar_date,1);
  $month =  (int)nurse_set($calendar_date,0);
  $year   = nurse_set($calendar_date,2);
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $inner_day = $day;
  $new_date = 1;
  $new_year_date = 1;
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  ob_start();
  echo '<div class="table-wrap">';
  echo '<table border="1">';
  echo ' <tr>';  
  echo '<th>Need</th>';
  echo '<th class="non-show-column" style="display:none;"></th>';
  for ($date=1; $date<=7; $date++){

    if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $current_date = $month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $new_date++;
      }

    }
    else{
      $current_date = $month.'/'. $inner_day.'/'.$year;
      echo '<th align="center">'.$current_date.'</th>';

    }
    $inner_day++;

  }
  echo ' </tr>';
  $slots = get_slots_with_data();
  if(!empty($slots)){
    foreach($slots as $slot){

      $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
      $calendar_date = $_REQUEST['date'];
      $calendar_date =  explode('/',  $calendar_date);
      $day    =  (int)nurse_set($calendar_date,1);
      $month =  (int)nurse_set($calendar_date,0);
      $year   = nurse_set($calendar_date,2);
      $inner_day = $day;
      $new_date = 1;
      $new_year_date = 1;
      $enable_duplicate = 0;
      $dates_array = $day + 6;
      $new_date_new_year = 1;
      $new_date_month = 1;
      $nurse_hourse  = 0;
      $user_ids = get_user_against_role($slot->role_id);

      echo '<tr>';
      echo '<td width="200px" > '.$slot->slot_name.'</td>';

      for ($date=1; $date<=7; $date++){

        if($inner_day > nurse_set($month_days,$month)){
          if($month == 12){
            $month = 1;
            $year = $year+1;
            $new_date = $new_year_date;           
            $current_date = $month.'/'. $new_date.'/'.$year;
            $schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('slot_id',$slot->slot_id)->first();
            $current_id =  ($schedule)?$schedule->id:'';
            $staff_id = ($schedule)?$schedule->user_id:'';
            $enable_duplicate = ($schedule)?1:$enable_duplicate;
            $month = 12;
            $new_year_date++;
            $year = $year-1;

            $shift_color = ($staff_id)?$slot->shift_color:'';
            $shift_dynamic_color = $slot->shift_color;

            ?>
            <input type="hidden" class="user_with_date" name="user_with_date[]" value="<?php echo $current_date ; ?>">
            <input type="hidden" class="slot_data" name="slot_data[]" value="<?php echo $slot->slot_id ; ?>">

            <?php
            echo '<td style="background-color:'.$shift_color.';">'.$slot->shift_name;
            ?>
            <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $current_id; ?>">
            <select class="form-control selected-staff" id="requestShift" name="selected-staff[]" data-slot_color="<?php echo $shift_dynamic_color; ?>" data-curret_date="<?php echo $current_date ; ?>" data-current_slot="<?php echo $slot->slot_id; ?>" data-updated_id="<?php echo $current_id; ?>">
              <option value="0">Open</option>
              <?php
              if(!empty($user_ids)):
                foreach($user_ids as $user_id):
                  $user= get_user_by( 'id', $user_id->user_id );

                  $note    = $db->table('nusre_schedule_notes')->where('user_id',$user_id->user_id)->where('date',$current_date)->first();
                  if(empty($note)):
                    ?>
                    <option value="<?php echo $user_id->user_id; ?>" <?php echo ($staff_id == $user_id->user_id)?'selected':''; ?> ><?php echo $user->display_name; ?></option>
                    <?php
                  endif;
                endforeach;
              endif;
              ?>
            </select>
            <?php if($schedule && empty($staff_id)): ?>
              <a href="<?php echo $make_schedul_link.'?schedule_id='.$schedule->id.'&start_week_date='.$start_calendar_date;?>"  target="_blank" class="send-reuqest-link"><?php echo ($schedule->notification_sent == '1')? 'Resend Request':'Send Request'; ?></a>
              <?php elseif(empty($staff_id)): ?>
               <a href="<?php echo $make_schedul_link.'?schedule_id=001&start_week_date='.$start_calendar_date;?>&curret_date=<?php echo $current_date ; ?>&current_slot=<?php echo $slot->slot_id; ?>"  target="_blank" class="send-reuqest-link"> <?php echo ($schedule && $schedule->notification_sent == '1')? 'Resend Request':'Send Request'; ?></a>
             <?php endif; ?>
             <?php
             echo '</td>';
           }
           else{

            $new_month = $month + 1;

            $current_date = $new_month.'/'. $new_date.'/'.$year;
            $schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('slot_id',$slot->slot_id)->first();
            $current_id =  ($schedule)?$schedule->id:'';
            $staff_id = ($schedule)?$schedule->user_id:'';
            $shift_color = ($staff_id)?$slot->shift_color:'';
            $enable_duplicate = ($schedule)?1:$enable_duplicate;
            $shift_dynamic_color = $slot->shift_color;
            $new_date++;
            ?>
            <input type="hidden" class="user_with_date" name="user_with_date[]" value="<?php echo $current_date; ?>">

            <input type="hidden" class="slot_data" name="slot_data[]" value="<?php echo $slot->slot_id ; ?>">

            <?php
            echo '<td style="background-color:'.$shift_color.';">'.$slot->shift_name;
            ?>
            <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $current_id; ?>">
            <select class="form-control selected-staff" id="requestShift" name="selected-staff[]" data-slot_color="<?php echo $shift_dynamic_color; ?>" data-curret_date="<?php echo $current_date ; ?>" data-current_slot="<?php echo $slot->slot_id; ?>" data-updated_id="<?php echo $current_id; ?>">
              <option value="0">Open</option>
              <?php
              if(!empty($user_ids)):
                foreach($user_ids as $user_id):
                  $user= get_user_by( 'id', $user_id->user_id );
                  $note    = $db->table('nusre_schedule_notes')->where('user_id',$user_id->user_id)->where('date',$current_date)->first();
                  if(empty($note)):
                    ?>
                    <option value="<?php echo $user_id->user_id; ?>" <?php echo ($staff_id == $user_id->user_id)?'selected':''; ?> ><?php echo $user->display_name; ?></option>
                    <?php
                  endif;
                endforeach;
              endif;
              ?>
            </select>
            <?php if($schedule && empty($staff_id)): ?>
              <a href="<?php echo $make_schedul_link.'?schedule_id='.$schedule->id.'&start_week_date='.$start_calendar_date;?>"  target="_blank" class="send-reuqest-link"> <?php echo ($schedule->notification_sent != '1')? 'Send Request':'Resend Request'; ?></a>
              <?php elseif(empty($staff_id)): ?>
               <a href="<?php echo $make_schedul_link.'?schedule_id=001';?>&curret_date=<?php echo $current_date ; ?>&current_slot=<?php echo $slot->slot_id.'&start_week_date='.$start_calendar_date; ?>"  target="_blank" class="send-reuqest-link"> <?php echo ($schedule && $schedule->notification_sent == '1')? 'Resend Request':'Send Request'; ?></a>
             <?php endif; ?>
             <?php
             echo '</td>';

           }

         }
         else{

          $current_date = $month.'/'. $inner_day.'/'.$year;
          $current_id   =  ($schedule)?$schedule->slot_id:'';
          $staff_id     = ($schedule)?$schedule->user_id:'';
          $schedule    = $db->table('staff_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('slot_id',$slot->slot_id)->first();
          $current_id =  ($schedule)?$schedule->id:'';
          $staff_id = ($schedule)?$schedule->user_id:'';
          $shift_color = ($staff_id)?$slot->shift_color:'';
          $enable_duplicate = ($schedule)?1:$enable_duplicate;
          $shift_dynamic_color = $slot->shift_color;
          ?>
          <input type="hidden" class="user_with_date" name="user_with_date[]" value=" <?php echo $current_date; ?>">
          <input type="hidden" class="slot_data" name="slot_data[]" value="<?php echo $slot->slot_id ; ?>">
          <?php
          echo '<td style="background-color:'.$shift_color.';">'.$slot->shift_name;
          ?>
          <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $current_id; ?>">
          <select class="form-control selected-staff" id="requestShift" name="selected-staff[]" data-slot_color="<?php echo $shift_dynamic_color; ?>" data-curret_date="<?php echo $current_date ; ?>" data-current_slot="<?php echo $slot->slot_id; ?>" data-updated_id="<?php echo $current_id; ?>" >
            <option value="0">Open</option>
            <?php
            if(!empty($user_ids)):
              foreach($user_ids as $user_id):
                $user= get_user_by( 'id', $user_id->user_id );
                $note    = $db->table('nusre_schedule_notes')->where('user_id',$user_id->user_id)->where('date',$current_date)->first();
                if(empty($note)):
                  ?>
                  <option value="<?php echo $user_id->user_id; ?>" <?php echo ($staff_id == $user_id->user_id)?'selected':''; ?> >

                    <?php echo $user->display_name; ?>

                  </option>
                  <?php
                endif;
              endforeach;
            endif;
            ?>
          </select>
          <?php if($schedule && empty($staff_id)): ?>
            <a href="<?php echo $make_schedul_link.'?schedule_id='.$schedule->id.'&start_week_date='.$start_calendar_date;?>"  target="_blank" class="send-reuqest-link"> <?php echo ($schedule->notification_sent != '1')? 'Send Request':'Resend Request'; ?></a>
            <?php elseif(empty($staff_id)): ?>
             <a href="<?php echo $make_schedul_link.'?schedule_id=001';?>&curret_date=<?php echo $current_date ; ?>&current_slot=<?php echo $slot->slot_id.'&start_week_date='.$start_calendar_date; ?>"  target="_blank" class="send-reuqest-link"> <?php echo ($schedule && $schedule->notification_sent == '1')? 'Resend Request':'Send Request'; ?></a>
             <?php elseif($staff_id):?>
               <a href="<?php echo $make_schedul_link.'?schedule_id='.$schedule->id.'&start_week_date='.$start_calendar_date;?>"  target="_blank" class="send-reuqest-link" style="display: none;"> <?php echo ($schedule && $schedule->notification_sent == '1')? 'Resend Request':'Send Request'; ?></a>
             <?php endif; ?>
             <?php
             echo '</td>';
           }
           $inner_day++;
         }
         echo '</tr>';
       }
     }


     echo '</table>';
     echo '</div>';
     $output = ob_get_clean();
     wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );

   }
   /*Register New Nurse*/
   function registerNewNurse(){
    global $wpdb;
    if (!count($_POST))
      return;
    $messages           = '';
    $errors             = array();
    $email              = /*nurse_set($_POST,'email')*/'admin'.uniqid().'@gmail.com';
    $name               = nurse_set($_POST,'name');
    $phone              = nurse_set($_POST,'phone');
    $password           = 'admin123'.uniqid();
    $roles              = nurse_set($_POST,'roles');
    $employement_status = nurse_set($_POST,'employement_status');
    $current_nurse_id = nurse_set($_POST,'current_nurse_id');
    $roles_table = $wpdb->prefix.'users_roles';
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$email))
    {
      $errors[] = 'Please enter correct email';
    }
    if(empty($email)){
      $errors[] = 'Please enter email address';
    }
    if(empty($name)){
      $errors[] = 'Please enter full name';
    }
    if(empty($phone)){
      $errors[] = 'Please enter phone number';
    } 
    if(empty($password) && empty($current_nurse_id)){
      $errors[] = 'Please enter password';
    }
    if(empty($roles)){
      $errors[] = 'Please select atleast role';
    }
    if(empty($employement_status)){
      $errors[] = 'Please select employement status';
    }
    if(username_exists( $email_address )){
      $errors[] = 'Email already exists';
    }
   /* if(empty($current_nurse_id)){
      $args = array(
        'role'         => 'nurse',
        'meta_key'     => 'user_phone_number',
        'meta_value'   => nurse_set($_POST,'phone'),
        'meta_compare' => '=',
      ); 
      $users = get_users( $args );

      if(!empty($users)){
        $errors[] = 'Phone number already exist';
      }
    }*/
    if ( ! empty( $errors ) ) {
      foreach ( $errors as $msg) {
        $messages .= '<div class="alert alert-danger">' . esc_html__('Error! ', 'hotelroom') . $msg . '</div>';

        wp_send_json( array('status' => 'error', 'message' => $messages ) );
      }
    }

    else{

     if($current_nurse_id){
      $user_id = $current_nurse_id;
      $userdata = array(
        'ID' =>(int)$user_id,
        'display_name' =>$name,
        'user_email' =>$email,

      );

      if($password){
       $userdata['user_pass']=$password;
     }

     wp_update_user( $userdata );
     //printr($userdate);
     $wpdb->query(
      'DELETE  FROM '.$wpdb->prefix.'users_roles
      WHERE user_id = "'.$user_id.'"'
    );

   }
   else{
    $user_name = $name;
    if(username_exists( $name )){
      $user_name = $name.uniqid();
    }
    $user_id = wp_create_user ( $user_name, $password, $email );

    $userdata = array(
      'ID' =>(int)$user_id,
      'display_name' =>$name,
      'user_email' =>$email,

    );
    wp_update_user( $userdata );
  }

  $user = new WP_User( $user_id );

  $user->set_role( 'nurse' );

  if(!empty($roles)){
    foreach ($roles as $role) {
      $wpdb->insert( 
       $roles_table, 
       array( 
        'user_id'   => $user_id, 
        'role_id' => $role
      ), 
       array( 
        '%s', 
        '%s',

      ) 
     );
    }
  }
  $current_user_id = get_current_user_id();

  update_user_meta( $user_id, 'nurse_subadmin', $current_user_id  );
  update_user_meta( $user_id, 'user_phone_number', $phone  );
  update_user_meta( $user_id, 'employement_status', $employement_status  );

  $messages ='<div class="alert alert-success">' . esc_html__('Success! Added successfully', 'hotelroom') . '</div>';

}
wp_send_json( array('status' => 'success', 'message' => $messages ) );

}
/*Add Role for the staff*/
function add_staff_role() {
 global $wpdb;
 $table         = $wpdb->prefix.'staff_roles';
 $user_id       = get_current_user_id();
 $role_name     = $_REQUEST['role_name'];
 $role_id       = $_REQUEST['role_id'];
 if($role_id){
   $wpdb->update( 
     $table, 
     array( 
      'user_id'   => $user_id, 
      'role_name' => $role_name,
    ), 
     array( 'id' => $role_id), 
     array( 
      '%s', 
      '%s'
    ), 
     array( '%d' ) 
   );
 }
 else{
  $wpdb->insert( 
   $table, 
   array( 
    'user_id'   => $user_id, 
    'role_name' => $role_name
  ), 
   array( 
    '%s', 
    '%s',
    
  ) 
 );
}
$output = '<div class="alert alert-success">Role has been saved successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
/*Add Role for the staff*/
function add_staff_groups() {
 global $wpdb;
 $table         = $wpdb->prefix.'staff_group';
 $user_id       = get_current_user_id();
 $group_name     = $_REQUEST['group_name'];
 $group_id       = $_REQUEST['group_id'];
 if($group_id){
   $wpdb->update( 
     $table, 
     array( 
      'user_id'   => $user_id, 
      'group_name' => $group_name,
    ), 
     array( 'id' => $group_id), 
     array( 
      '%s', 
      '%s'
    ), 
     array( '%d' ) 
   );
 }
 else{
  $wpdb->insert( 
   $table, 
   array( 
    'user_id'   => $user_id, 
    'group_name' => $group_name
  ), 
   array( 
    '%s', 
    '%s',
    
  ) 
 );
}
$output = '<div class="alert alert-success">Group has been saved successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
/*Add Shift for the staff*/
function add_staff_shift() {
 global $wpdb;
 $table         = $wpdb->prefix.'staff_shifts';
 $table2        = $wpdb->prefix.'interval_shifts';
 $user_id       = get_current_user_id();
 $shift_name    = $_REQUEST['shift_name'];
 $shift_id      = $_REQUEST['shift_id'];
 $shift_color   = $_REQUEST['shift_color'];
 $interval_id   = $_REQUEST['interval_id'];
 if($shift_id){
   $wpdb->update( 
     $table, 
     array( 
      'user_id'   => $user_id, 
      'shift_name' => $shift_name,
      'shift_color' => $shift_color,
    ), 
     array( 'id' => $shift_id), 
     array( 
      '%s', 
      '%s'
    ), 
     array( '%d' ) 
   );
   $wpdb->query(
    'DELETE  FROM '.$wpdb->prefix.'interval_shifts
    WHERE shift_id = "'.$shift_id.'"'
  );
   $wpdb->insert( 
     $table2, 
     array( 
       'shift_id'  => $shift_id, 
       'interval_id' => $interval_id,
     ), 
     array( 
      '%s', 
      '%s'

    ) 
   );
 }
 else{
  $wpdb->insert( 
   $table, 
   array( 
    'user_id'   => $user_id, 
    'shift_name' => $shift_name,
    'shift_color' => $shift_color,
  ), 
   array( 
    '%s', 
    '%s',
    
  ) 
 );
  $lastid = $wpdb->insert_id;
  $wpdb->insert( 
   $table2, 
   array( 
     'shift_id'  => $lastid, 
     'interval_id' => $interval_id,
   ), 
   array( 
    '%s', 
    '%s'

  ) 
 );
}
$output = '<div class="alert alert-success">Shift has been saved successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
/*Add Slot for the staff*/
function add_staff_slot() {
 global $wpdb;
 $table         = $wpdb->prefix.'staff_slots';
 $table2         = $wpdb->prefix.'slots_group';
 $user_id       = get_current_user_id();
 $slot_name     = $_REQUEST['slot_name'];
 $shift_id       = $_REQUEST['shift_id'];
 $role_id       = $_REQUEST['role_id'];
 $slot_id       = $_REQUEST['slot_id'];
 $group_id       = $_REQUEST['group_id'];

 if($slot_id){
   $wpdb->update( 
     $table, 
     array( 
      'user_id'   => $user_id, 
      'slot_name' => $slot_name,
      'shift_id'  =>  $shift_id,
      'role_id'   =>  $role_id
    ), 
     array( 'id' => $slot_id), 
     array( 
      '%s', 
      '%s',
      '%s',
      '%s'
    ), 
     array( '%d' ) 
   );

   $wpdb->query(
    'DELETE  FROM '.$wpdb->prefix.'slots_group
    WHERE slot_id = "'.$slot_id.'"'
  );
   $wpdb->insert( 
     $table2, 
     array( 
       'slot_id'  => $slot_id, 
       'group_id' => $group_id,
     ), 
     array( 
      '%s', 
      '%s'

    ) 
   );
 }
 else{
  $wpdb->insert( 
   $table, 
   array( 
     'user_id'   => $user_id, 
     'slot_name' => $slot_name,
     'shift_id'  =>  $shift_id,
     'role_id'   =>  $role_id
   ), 
   array( 
    '%s', 
    '%s',
    '%s',
    '%s'
    
  ) 
 );
  $lastid = $wpdb->insert_id;
  $wpdb->insert( 
   $table2, 
   array( 
     'slot_id'  => $lastid, 
     'group_id' => $group_id,
   ), 
   array( 
    '%s', 
    '%s'

  ) 
 );
}
$output = '<div class="alert alert-success">Need has been saved successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}


/*---------------------------
Old Functions Of the System Here
-----------------------------------*/
function save_start_week_date(){
  $day = $_POST['day']; 
  /*printr($day);*/

  update_option( get_current_user_id().'week_starts_from', $day );

  wp_send_json( array( 'status' => 'success', 'message'=>'Week Start Day Saved'));
}
function fill_slot_by_nurse(){
  global $wpdb;


  $slot_data = $_REQUEST['data'];
  $slot_data = explode(',', $slot_data);
  /*printr($slot_data);*/
  $table       = $wpdb->prefix.'nusre_schedule';
  $wpdb->update( 
    $table, 
    array( 
      'assign_to'   => $slot_data[0], 
    ), 
    array( 'id' => $slot_data[1] ), 
    array( 
      '%s', 
    ), 
    array( '%d' ) 
  );

  $output = '<div class="alert alert-success">Schedule saved</div>';
  return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}
function add_calender_for_subadmin_preview(){
  global $wpdb;

  $current_user_id = get_current_user_id();

  $orderby = ($_REQUEST['order_by'])?$_REQUEST['order_by']:'';
  /*printr($orderby);*/
  if($orderby == 'group'){
    $args = array(
      'role'         => 'nurse',
      'meta_key' => 'nursing_group',
      'orderby' => 'meta_value',
      'order' => 'ASC', 
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key'     => 'nurse_subadmin',
          'value'   => $current_user_id,
          'compare' => '='
        )
      )
    ); 
  }
  else{
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
  }
  $make_schedul_link = 'make-nurses-schedule-copy';
  $users = get_users( $args );

  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

  $calendar_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
  $weekly_days = array('Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7);
  /*printr(date('l'));  */
  $today_date = $weekly_days[date('l')];
  $remaing_days =7/* 8 - $today_date*/;
  $number_of_days = ($_REQUEST['date'])?7:$remaing_days;
  $calendar_date =  explode('/',  $calendar_date);
  $day    =  (int)nurse_set($calendar_date,1);
  $month =  (int)nurse_set($calendar_date,0);
  $year   = nurse_set($calendar_date,2);
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $inner_day = $day;
  $new_date = 1;
  $new_year_date = 1;
  $shifts = array(1 =>'Day',2=>'Eve',3=>'Night');
  $class = array(1 =>'day-slot',2=>'eve-slot',3=>'night-slot');
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  ob_start();
  echo '<div class="table-wrap">';
  echo '<table class="table">';
  echo ' <tr>';
  echo '<th>User</th>';
  echo '<th>Group</th>';
  echo '<th>Hours</th>';
  for ($date=1; $date<=$number_of_days; $date++){

    if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $current_date = $month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $new_date++;
      }

    }
    else{
      $current_date = $month.'/'. $inner_day.'/'.$year;
      echo '<th align="center">'.$current_date.'</th>';

    }
    $inner_day++;

  }
  echo ' </tr>';

  foreach ( $users as $user ) {

    $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

    $calendar_date = ($_REQUEST['date'])?$_REQUEST['date']:date ('n/j/Y' );
    $calendar_date =  explode('/',  $calendar_date);
    $day    =  (int)nurse_set($calendar_date,1);
    $month =  (int)nurse_set($calendar_date,0);
    $year   = nurse_set($calendar_date,2);
    $group_key =  get_user_meta($user->id,'nursing_group',true);
    $group = get_nursing_group_by_id($group_key);
    $inner_day = $day;
    $new_date = 1;
    $new_year_date = 1;
    $enable_duplicate = 0;

    $hours = 0;

    $included_schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$day)->where('user_id',$user->id)->first();
    if($included_schedule){
      $hours = $included_schedule->week_hourse;
    }
    echo '<tr>';
    echo '<td class="nurse-name"><b>' . esc_html( $user->display_name ) . '</b></td>';
    echo '<td class="nurse-group"><b>' .$group_key . '</b></td>';
    echo '<td class="nurse-group"><b>'.$hours.'</b></td>';

    for ($date=1; $date<=$number_of_days; $date++){

      if($inner_day > nurse_set($month_days,$month)){
        if($month == 12){
          $month = 1;
          $year = $year+1;
          $new_date = $new_year_date;
          $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user->id)->first();
          $current_date = $month.'/'. $new_date.'/'.$year;
          $month = 12;
          $new_year_date++;
          $year = $year-1;
          ?>

          <?php
          if($schedule){
            ?>

            <td style="padding:0px;">

              <?php if ($schedule->assign_to == 0 && $schedule->is_open == 1) : ?>
                <div class="open-shift-area" style="padding-top:8px;min-height:93px;">
                  <?php echo get_shift_id($schedule->shift); ?><br>
                  Open
                  <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
                    <option value="">Open</option>
                    <?php foreach($users as $nurse):?>
                      <option value="<?php echo $nurse->id.','.$schedule->id; ?>" ><?php echo $nurse->display_name; ?></option>
                    <?php endforeach; ?>

                  </select>
                  <?php
                  $group = get_nursing_group_table_by_id($schedule->gorup_id);
                  ?>
                  <a class="send-request-link" href="/<?php echo $make_schedul_link; ?>?groud_key=<?php echo $group->group_key; ?>&slot_id=<?php echo $schedule->id; ?>&month=<?php echo $schedule->month; ?>&year=<?php echo $schedule->year; ?>&date=<?php echo $schedule->date; ?>" target="_blank"> <?php echo ($schedule->notification_sent ==1)?'Resend Request':'Send Request'; ?>
                </a>
              </div>
              <?php elseif ($schedule->status == 1 ) : ?>
                <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
                  <?php echo get_shift_id($schedule->shift); ?><br>
                  Assigned
                </div>
                <?php elseif($schedule->status == 0): ?>
                 <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
                  <?php echo get_shift_id($schedule->shift); ?><br>
                  Vacation
                </div>
                <?php
              elseif($schedule->assign_to != 0):
               $user_obj = get_user_by('id', $schedule->assign_to);
               ?>
               <div class="open-shift-area <?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
                 <?php echo get_shift_id($schedule->shift); ?><br>
                 <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
                  <option value="">Open</option>
                  <?php foreach($users as $nurse):?>
                    <option value="<?php echo $nurse->id.','.$schedule->id; ?>" <?php echo ($user_obj->ID == $nurse->id)?'Selected':''; ?> ><?php echo $nurse->display_name; ?></option>
                  <?php endforeach; ?>

                </select>
              </div>
            <?php endif; ?>



          </td>
          <?php
        }
        else{
          echo ' <td style="padding:0px;"><div class="" style="padding-top:8px;min-height: 60px;">
          Vacation
          </div></td>';
        }

      }
      else{

        $new_month = $month + 1;
        $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('user_id',$user->id)->first();
        $current_date = $new_month.'/'. $new_date.'/'.$year;

        $new_date++;

        if($schedule){
          $enable_duplicate = '1';
          ?>
          <td style="padding:0px;">
           <?php if ($schedule->assign_to == 0 && $schedule->is_open == 1) : ?>
            <div class="open-shift-area" style="padding-top:8px;min-height:93px;">
              <?php echo get_shift_id($schedule->shift); ?><br>
              Open
              <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
                <option value="">Open</option>
                <?php foreach($users as $nurse):?>
                  <option value="<?php echo $nurse->id.','.$schedule->id; ?>" ><?php echo $nurse->display_name; ?></option>
                <?php endforeach; ?>

              </select>
              <?php
              $group = get_nursing_group_table_by_id($schedule->gorup_id);
              ?>
              <a class="send-request-link" href="/<?php echo $make_schedul_link; ?>?groud_key=<?php echo $group->group_key; ?>&slot_id=<?php echo $schedule->id; ?>&month=<?php echo $schedule->month; ?>&year=<?php echo $schedule->year; ?>&date=<?php echo $schedule->date; ?>" target="_blank">
                <?php echo ($schedule->notification_sent ==1)?'Resend Request':'Send Request'; ?>
              </a>
            </div>
            <?php elseif ($schedule->status == 1 ) : ?>
              <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
                <?php echo get_shift_id($schedule->shift); ?><br>
                Assigned
              </div>
              <?php elseif($schedule->status == 0): ?>
               <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
                <?php echo get_shift_id($schedule->shift); ?><br>
                Vacation
              </div>
              <?php
            elseif($schedule->assign_to != 0):
             $user_obj = get_user_by('id', $schedule->assign_to);
             ?>
             <div class="open-shift-area <?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
               <?php echo get_shift_id($schedule->shift); ?><br>
               <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
                <option value="">Open</option>
                <?php foreach($users as $nurse):?>
                  <option value="<?php echo $nurse->id.','.$schedule->id; ?>" <?php echo ($user_obj->ID == $nurse->id)?'Selected':''; ?> ><?php echo $nurse->display_name; ?></option>
                <?php endforeach; ?>

              </select>

            </div>
          <?php endif; ?>
        </td>
        <?php
      }
      else{
        echo ' <td style="padding:0px;"><div class="" style="padding-top:8px;min-height: 60px;">
        Vacation
        </div></td>';
      }
    }

  }
  else{

    $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('user_id',$user->id)->first();

    $current_date = $month.'/'. $inner_day.'/'.$year;
    ?>

    <?php
    if($schedule){
      ?>
      <td style="padding:0px;">
        <?php if ($schedule->assign_to == 0 && $schedule->is_open == 1) : ?>
          <div class="open-shift-area" style="padding-top:8px;min-height:93px;">
            <?php echo get_shift_id($schedule->shift); ?><br>
            Open
            <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
              <option value="">Open</option>
              <?php foreach($users as $nurse):?>
                <option value="<?php echo $nurse->id.','.$schedule->id; ?>" ><?php echo $nurse->display_name; ?></option>
              <?php endforeach; ?>

            </select>
            <?php
            $group = get_nursing_group_table_by_id($schedule->gorup_id);
            ?>
            <a class="send-request-link" href="/<?php echo $make_schedul_link; ?>?groud_key=<?php echo $group->group_key; ?>&slot_id=<?php echo $schedule->id; ?>&month=<?php echo $schedule->month; ?>&year=<?php echo $schedule->year; ?>&date=<?php echo $schedule->date; ?>" target="_blank"><?php echo ($schedule->notification_sent ==1)?'Resend Request':'Send Request'; ?>
          </a>
        </div>
        <?php elseif ($schedule->status == 1 ) : ?>
          <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
            <?php echo get_shift_id($schedule->shift); ?><br>
            Assigned
          </div>
          <?php elseif($schedule->status == 0): ?>
           <div class="<?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
            <?php echo get_shift_id($schedule->shift); ?><br>
            Vacation
          </div>
          <?php
        elseif($schedule->assign_to != 0):
         $user_obj = get_user_by('id', $schedule->assign_to);
         ?>
         <div class="open-shift-area <?php echo $class[$schedule->shift]; ?>" style="padding-top:8px;">
           <?php echo get_shift_id($schedule->shift); ?><br>
           <select class="form-control selected_shift" id="requestShift" name="selected-nurse[]">
            <option value="">Open</option>
            <?php foreach($users as $nurse):?>
              <option value="<?php echo $nurse->id.','.$schedule->id; ?>" <?php echo ($user_obj->ID == $nurse->id)?'Selected':''; ?> ><?php echo $nurse->display_name; ?></option>
            <?php endforeach; ?>

          </select>


        </div>
      <?php endif; ?>
    </td>
    <?php
  }
  else{
    echo '<td style="padding:0px;"><div class="" style="padding-top:8px;min-height: 60px;">
    Vacation
    </div></td>';
  }

}
$inner_day++;

}
echo '</tr>';
}

echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );



}
function dublicate_week_data(){

  global $wpdb;
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  $current_user_id = get_current_user_id();
  $table           = $wpdb->prefix.'nusre_schedule';
  $user_data       = $_REQUEST['user_with_group'];
  $updated_ids     = $_REQUEST['updated_id'];
  $shifts          = $_REQUEST['shifts'];
  $cheked_values   = $_REQUEST['cheked_values'];
  $dublicate_date   = $_REQUEST['dublicate_date'];
  $total_hourse   = $_REQUEST['total_hourse'];
  $custom_hourse   = $_REQUEST['custom_hourse'];
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
  $calendar_date   =  explode('/',  $dublicate_date);
  $day   = (int) nurse_set($calendar_date,1);
  $month = (int) nurse_set($calendar_date,0);
  $year  = nurse_set($calendar_date,2);
  $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$day)->where('owner_id',$current_user_id)->first();
  if($schedule) {
    $output = '<div class="alert alert-danger">Schedule for this week already added</div>';
    return wp_send_json( array( 'status' => 'error', 'message' => $output ) );
  }
  if(!empty($user_data)){
    $counter = 0;
    foreach ($user_data as $key=>$sngl_user_data) {

     $hourse = 0;
     if($total_hourse[$key] && $total_hourse[$key] != 0 && $custom_hourse[$key] == 1 ){
      $hourse = $total_hourse[$key];
    }else{
      $nrs_slt    = $counter+6;
      $nrs_coverd = 1;
      for($nrs_horse=$counter; $nrs_horse<=$nrs_slt; $nrs_horse++){
        if(nurse_set($cheked_values,$nrs_horse) == 1)
        {
          $hourse = 8 * (int)$nrs_coverd;
          $nrs_coverd ++;
        }

      }
    }

    $calendar_date = $_REQUEST['dublicate_date'];
    $calendar_date =  explode('/',  $calendar_date);
    $day    =  (int)nurse_set($calendar_date,1);
    $month =  (int)nurse_set($calendar_date,0);
    $year   = nurse_set($calendar_date,2);
    $group_key =  get_user_meta($user->id,'nursing_group',true);
    $group = get_nursing_group_by_id($group_key);
    $inner_day = $day;
    $new_date = 1;
    $new_year_date = 1; 
    $user = explode(",",$sngl_user_data);

    $is_open = '';
    $key_counter = $key + 1;
    if(nurse_set($cheked_values,$counter) == 2){
     $is_open =1;
   }

   for ($i=0; $i<=6; $i++){

     if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $current_date = $month.'/'. $new_date.'/'.$year;
        $wpdb->insert( 
         $table, 
         array( 
          'user_id'   => nurse_set($user,0), 
          'date'      => $new_date,
          'shift'     => nurse_set($shifts,$counter),
          'gorup_id'  => nurse_set($user,1),
          'is_open'   => $is_open,
          'month'     => $months[$month],
          'year'      => $year,
          'status'    => nurse_set($cheked_values,$counter),
          'owner_id'  => $current_user_id
        ), 
         array( 
          '%s', 
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s'
        ) 
       );
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        $wpdb->insert( 
         $table, 
         array( 
          'user_id'   => nurse_set($user,0), 
          'date'      => $new_date,
          'shift'     => nurse_set($shifts,$counter),
          'gorup_id'  => nurse_set($user,1),
          'is_open'   => $is_open,
          'month'     => $months[$new_month],
          'year'      => $year,
          'status'    => nurse_set($cheked_values,$counter),
          'owner_id'  => $current_user_id,
          'week_hourse' => $hourse
        ), 
         array( 
          '%s', 
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%d'
        ) 
       );
        $new_date++;
      }

    }
    else{
      $current_date = $month.'/'. $inner_day.'/'.$year;
      $wpdb->insert( 
       $table, 
       array( 
        'user_id'   => nurse_set($user,0), 
        'date'      => $inner_day,
        'shift'     => nurse_set($shifts,$counter),
        'gorup_id'  => nurse_set($user,1),
        'is_open'   => $is_open,
        'month'     => $months[$month],
        'year'      => $year,
        'status'    => nurse_set($cheked_values,$counter),
        'owner_id'  => $current_user_id,
        'week_hourse' => $hourse
      ), 
       array( 
        '%s', 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d'
      ) 
     );

    }
    $inner_day++;
    $counter++;

  }

}
}
$output = '<div class="alert alert-success">Schedule have been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}

function add_week_data(){
  global $wpdb;
  $table         = $wpdb->prefix.'nusre_schedule';
  $user_data     = $_REQUEST['user_with_group'];
  $updated_ids   = $_REQUEST['updated_id'];
  $shifts        = $_REQUEST['shifts'];
  $cheked_values = $_REQUEST['cheked_values'];
  $user_with_date = $_REQUEST['user_with_date'];
  $total_hourse   = $_REQUEST['total_hourse'];
  $custom_hourse   = $_REQUEST['custom_hourse'];

  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  if(!empty($user_data)){
    $counter = 0;
    
    foreach ($user_data as $key=>$sngl_user_data) {
     /* printr($total_hourse[$key]);*/
     $user = explode(",",$sngl_user_data);
     $hourse = 0;
     if($total_hourse[$key] && $total_hourse[$key] != 0 && $custom_hourse[$key] == 1 ){
      $hourse = $total_hourse[$key];
    }else{
      $nrs_slt    = $counter+6;
      $nrs_coverd = 1;
      for($nrs_horse=$counter; $nrs_horse<=$nrs_slt; $nrs_horse++){
        if(nurse_set($cheked_values,$nrs_horse) == 1)
        {
          $hourse = 8 * (int)$nrs_coverd;
          $nrs_coverd ++;
        }

      }
    }


    for ($i=0; $i<=6; $i++){
      $is_open = '';

      if(nurse_set($cheked_values,$counter) == 2){
       $is_open =1;
     }
     $current_user_id = get_current_user_id();
     $calender_date   = nurse_set($user_with_date,$counter);        
     $calendar_date   = explode('/', $calender_date);
     $selected_date   = str_replace(' ', '',nurse_set($calendar_date,1));
     $selected_year   = str_replace(' ', '',nurse_set($calendar_date,2));
     $selected_month  = nurse_set($months,str_replace(' ', '', nurse_set($calendar_date,0)));

     if(nurse_set($updated_ids,$counter)){

       $wpdb->update( 
         $table, 
         array( 
          'user_id'     => nurse_set($user,0), 
          'date'        => $selected_date,
          'shift'       => nurse_set($shifts,$counter),
          'gorup_id'    => nurse_set($user,1),
          'is_open'     => $is_open,
          'month'       => $selected_month,
          'year'        => $selected_year,
          'status'      => nurse_set($cheked_values,$counter),
          'owner_id'    => $current_user_id,
          'week_hourse' => $hourse
        ), 
         array( 'id' => nurse_set($updated_ids,$counter) ), 
         array( 
          '%s', 
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%d'
        ), 
         array( '%d' ) 
       );

     }
     else{
      $wpdb->insert( 
       $table, 
       array( 
        'user_id'      => nurse_set($user,0), 
        'date'         => $selected_date,
        'shift'        => nurse_set($shifts,$counter),
        'gorup_id'     => nurse_set($user,1),
        'is_open'      => $is_open,
        'month'        => $selected_month,
        'year'         => $selected_year,
        'status'       => nurse_set($cheked_values,$counter),
        'owner_id'     => $current_user_id,
        'week_hourse'  => $hourse,
      ), 
       array( 
        '%s', 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d'
      ) 
     );
    }

    $counter++;

  }
}
}
$output = '<div class="alert alert-success">Schedule has been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );

}

function add_week_preview(){
  global $wpdb;

  $current_user_id = get_current_user_id();

  $args = array(
    'role'         => 'nurse',
    'meta_key'     => 'nurse_subadmin',
    'meta_value'   => $current_user_id,
    'meta_compare' => '=',
  ); 

  $users = get_users( $args );

  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

  $calendar_date = $_REQUEST['date'];
  //printr($calendar_date);
  $calendar_date =  explode('/',  $calendar_date);
  $day    =  (int)nurse_set($calendar_date,1);
  $month =  (int)nurse_set($calendar_date,0);
  $year   = nurse_set($calendar_date,2);
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $inner_day = $day;
  $new_date = 1;
  $new_year_date = 1;
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  ob_start();
  echo '<div class="table-wrap">';
  echo '<table border="1">';
  echo ' <tr>';
  echo '<th>User</th>';
  echo '<th>Group</th>';
  echo '<th>Hours In Week</th>';
  echo '<th class="non-show-column" style="display:none;"></th>';
  for ($date=1; $date<=7; $date++){

    if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $current_date = $month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $month = 12;
        $new_year_date++;
        $year = $year-1;

      }
      else{

        $new_month = $month + 1;
        $current_date = $new_month.'/'. $new_date.'/'.$year;
        echo '<th align="center">'.$current_date.'</th>';
        $new_date++;
      }

    }
    else{
      $current_date = $month.'/'. $inner_day.'/'.$year;
      echo '<th align="center">'.$current_date.'</th>';

    }
    $inner_day++;

  }
  echo ' </tr>';

  foreach ( $users as $user ) {

    $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

    $calendar_date = $_REQUEST['date'];
    $calendar_date =  explode('/',  $calendar_date);
    $day    =  (int)nurse_set($calendar_date,1);
    $month =  (int)nurse_set($calendar_date,0);
    $year   = nurse_set($calendar_date,2);
    $group_key =  get_user_meta($user->id,'nursing_group',true);
    $group = get_nursing_group_by_id($group_key);
    $inner_day = $day;
    $new_date = 1;
    $new_year_date = 1;
    $enable_duplicate = 0;
    $dates_array = $day + 6;
    $new_date_new_year = 1;
    $new_date_month = 1;
    $nurse_hourse  = 0;

    for ($included_dates = $day; $included_dates <= $dates_array; $included_dates ++){

      if($included_dates > nurse_set($month_days,$month)){

        if($month == 12){
          $included_new_year_month = 1;
          $included_new_year = $year+1;
          
          $included_schedule    = $db->table('nusre_schedule')->where('year',$included_new_year)->where('month',$months[$included_new_year_month])->where('date',$new_date_new_year)->where('user_id',$user->id)->whereNotNull('week_hourse')->first();
          if($included_schedule){
            printr($included_schedule);
            $nurse_hourse = $included_schedule->week_hourse;
            break;
          }
          $new_date_new_year ++;

        }
        else{

         $included_new_month = $month + 1;
         $included_schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$included_new_month])->where('date',$new_date_month)->where('user_id',$user->id)->whereNotNull('week_hourse')->first();
         if($included_schedule){
          $nurse_hourse = $included_schedule->week_hourse;
          break;
        }
        $new_date_month++;
      }

    }
    else{
      $included_schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$included_dates)->where('user_id',$user->id)->whereNotNull('week_hourse')->first();
      if($included_schedule){
        $nurse_hourse = $included_schedule->week_hourse;
        break;
      }
      

    }

  }

  $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user->id)->first();

  echo ' <tr>';
  echo '<td style="display:none;">
  <input type="hidden" class="user_with_group" name="user_with_group[]" value="'.$user->id.','.$group->id.'"></td>';
  echo '<td width="200px">' . esc_html( $user->display_name ) . '</td>';
  echo '<td>' .$group_key . '</td>';
  echo '<td>
  <div class="having-week-schedule"  style="padding:5px;"><input class="total_hourse" type="number" place_hodler="Total Hourse" name="week_hourse[]" value='.$nurse_hourse.'><input type="hidden" name="custom_hourse[]" class="custom-hours"></div></td>';

  for ($date=1; $date<=7; $date++){

    if($inner_day > nurse_set($month_days,$month)){
      if($month == 12){
        $month = 1;
        $year = $year+1;
        $new_date = $new_year_date;
        $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$new_date)->where('user_id',$user->id)->first();
        $current_date = $month.'/'. $new_date.'/'.$year;
        $month = 12;
        $new_year_date++;
        $year = $year-1;
        ?>
        <input type="hidden" class="user_with_date" name="user_with_date[]" value="<?php echo $current_date ; ?>">
        <?php
        if($schedule){
          $enable_duplicate = '1';
          ?>

          <td>

            <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $schedule->id; ?>">

            <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
              <option value="">Select Shift</option>
              <option value="1" <?php echo ($schedule->shift == 1)?'selected':''; ?>>1st (7a-3p)</option>
              <option value="2" <?php echo ($schedule->shift == 2)?'selected':''; ?>>2nd (3p-11p)</option>
              <option value="3" <?php echo ($schedule->shift == 3)?'selected':''; ?>>3rd (11p-7a)</option>
            </select>
            <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]" <?php echo ($schedule->assign_to != 0)?'disabled':'';?>>
              <option value="0" <?php echo ($schedule->status == 0)?'selected':''; ?>>Select Option</option>
              <option value="1" <?php echo ($schedule->status == 1)?'selected':''; ?>>Covered</option>
              <?php
              if($schedule->assign_to != 0):
               $user_obj = get_user_by('id', $schedule->assign_to);
               ?>
               <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Assigned-<?php echo $user_obj->display_name; ?></option>
               <?php else: ?>
                <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Open</option>
              <?php endif; ?>
            </select>
          </td>
          <?php
        }
        else{
          echo '<td> 
          <input type="hidden" class="updated_id" name="updated_id[]">
          <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
          <option value="">Select Shift</option>
          <option value="1" >1st (7a-3p)</option>
          <option value="2">2nd (3p-11p)</option>
          <option value="3">3rd (11p-7a)</option>
          </select>
          <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]">
          <option value="0">Select Option</option>
          <option value="1" >Covered</option>
          <option value="2">Open</option>
          </select>
          </td>';
        }

      }
      else{

        $new_month = $month + 1;
        $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$new_month])->where('date',$new_date)->where('user_id',$user->id)->first();
        $current_date = $new_month.'/'. $new_date.'/'.$year;

        $new_date++;
        ?>
        <input type="hidden" class="user_with_date" name="user_with_date[]" value="<?php echo $current_date; ?>">
        <?php

        if($schedule){
          $enable_duplicate = '1';
          ?>
          <td>
            <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $schedule->id; ?>">

            <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
              <option value="">Select Shift</option>
              <option value="1" <?php echo ($schedule->shift == 1)?'selected':''; ?>>1st (7a-3p)</option>
              <option value="2" <?php echo ($schedule->shift == 2)?'selected':''; ?>>2nd (3p-11p)</option>
              <option value="3" <?php echo ($schedule->shift == 3)?'selected':''; ?>>3rd (11p-7a)</option>
            </select>
            <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]" <?php echo ($schedule->assign_to != 0)?'disabled':'';?>>
              <option value="0" <?php echo ($schedule->status == 0)?'selected':''; ?>>Select Option</option>
              <option value="1" <?php echo ($schedule->status == 1)?'selected':''; ?>>Covered</option>
              <?php
              if($schedule->assign_to != 0):
               $user_obj = get_user_by('id', $schedule->assign_to);
               ?>
               <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Assigned-<?php echo $user_obj->display_name; ?></option>
               <?php else: ?>
                <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Open</option>
              <?php endif; ?>
            </select>
          </td>
          <?php
        }
        else{
          echo '<td> 
          <input type="hidden" class="updated_id" name="updated_id[]">
          <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
          <option value="">Select Shift</option>
          <option value="1" >1st (7a-3p)</option>
          <option value="2">2nd (3p-11p)</option>
          <option value="3">3rd (11p-7a)</option>
          </select>
          <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]">
          <option value="0">Select Option</option>
          <option value="1" >Covered</option>
          <option value="2">Open</option>
          </select>
          </td>';
        }
      }

    }
    else{

      $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$inner_day)->where('user_id',$user->id)->first();

      $current_date = $month.'/'. $inner_day.'/'.$year;
      ?>
      <input type="hidden" class="user_with_date" name="user_with_date[]" value=" <?php echo $current_date; ?>">
      <?php
      if($schedule){
        $enable_duplicate = '1';
        ?>
        <td>
          <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $schedule->id; ?>">

          <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
            <option value="">Select Shift</option>
            <option value="1" <?php echo ($schedule->shift == 1)?'selected':''; ?>>1st (7a-3p)</option>
            <option value="2" <?php echo ($schedule->shift == 2)?'selected':''; ?>>2nd (3p-11p)</option>
            <option value="3" <?php echo ($schedule->shift == 3)?'selected':''; ?>>3rd (11p-7a)</option>
          </select>
          <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]" <?php echo ($schedule->assign_to != 0)?'disabled':'';?>>
            <option value="0" <?php echo ($schedule->status == 0)?'selected':''; ?>>Select Option</option>
            <option value="1" <?php echo ($schedule->status == 1)?'selected':''; ?>>Covered</option>
            <?php
            if($schedule->assign_to != 0):
             $user_obj = get_user_by('id', $schedule->assign_to);
             ?>
             <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Assigned-<?php echo $user_obj->display_name; ?></option>
             <?php else: ?>
              <option value="2" <?php echo ($schedule->status == 2)?'selected':''; ?>>Open</option>
            <?php endif; ?>
          </select>
        </td>
        <?php
      }
      else{
        echo '<td> 
        <input type="hidden" class="updated_id" name="updated_id[]">
        <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
        <option value="">Select Shift</option>
        <option value="1" >1st (7a-3p)</option>
        <option value="2">2nd (3p-11p)</option>
        <option value="3">3rd (11p-7a)</option>
        </select>
        <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]">
        <option value="0">Select Option</option>
        <option value="1" >Covered</option>
        <option value="2">Open</option>
        </select>
        </td>';
      }

    }
    $inner_day++;

  }
  echo '</tr>';
}

echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );
  /*$table         = $wpdb->prefix.'nusre_schedule';

  $month_days = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  $schedule    = $db->table('nusre_schedule')->where('year',$year)->where('month',$month)->where('date',$date)->first();
  if($schedule){

  }
  else{

  }*/


}
function add_month_data() {
 global $wpdb;
 $table         = $wpdb->prefix.'nusre_schedule';
 $user_data     = $_REQUEST['user_with_group'];
 $updated_ids   = $_REQUEST['updated_id'];
 $shifts        = $_REQUEST['shifts'];
 $cheked_values = $_REQUEST['cheked_values'];
 $year          = date("Y");
 $months        = array(
  1  => 'January-'.$year,
  2  => 'Febraury-'.$year,
  3  => 'March-'.$year,
  4  => 'April-'.$year,
  5  => 'May-'.$year,
  6  => 'June-'.$year,
  7  => 'July-'.$year,
  8  => 'August-'.$year,
  9  => 'September-'.$year,
  10 => 'October-'.$year,
  11 => 'November-'.$year,
  12 => 'December-'.$year
);
 if(!array_filter($shifts)) {
  $output = '<div class="alert alert-danger">Please Select Shifts for all nurses</div>';
  return wp_send_json( array( 'status' => 'error', 'message' => $output ) );
}
$month_with_year =  nurse_set($months,$_REQUEST['month']);
$month_with_year = explode("-",$month_with_year);
if(!empty($user_data)){
  $counter = 0;
  foreach ($user_data as $key=>$sngl_user_data) {
   $user = explode(",",$sngl_user_data);
   for ($i=0; $i<=30; $i++){
    $is_open = '';
    $date = $i+1;
    $key_counter = $key + 1;
    if(nurse_set($cheked_values,$counter) == 2){
     $is_open =1;
   }
   $current_user_id = get_current_user_id();
   if(nurse_set($updated_ids,$counter)){
     $wpdb->update( 
       $table, 
       array( 
        'user_id'   => nurse_set($user,0), 
        'date'      => $date,
        'shift'     => nurse_set($shifts,$counter),
        'gorup_id'  => nurse_set($user,1),
        'is_open'   => $is_open,
        'month'     => nurse_set($month_with_year,0),
        'year'      => nurse_set($month_with_year,1),
        'status'    => nurse_set($cheked_values,$counter),
        'owner_id'  => $current_user_id
      ), 
       array( 'id' => nurse_set($updated_ids,$counter) ), 
       array( 
        '%s', 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
      ), 
       array( '%d' ) 
     );
   }
   else{
    $wpdb->insert( 
     $table, 
     array( 
      'user_id'   => nurse_set($user,0), 
      'date'      => $date,
      'shift'     => nurse_set($shifts,$counter),
      'gorup_id'  => nurse_set($user,1),
      'is_open'   => $is_open,
      'month'     => nurse_set($month_with_year,0),
      'year'      => nurse_set($month_with_year,1),
      'status'    => nurse_set($cheked_values,$counter),
      'owner_id'  => $current_user_id
    ), 
     array( 
      '%s', 
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
    ) 
   );
  }

  $counter++;
}
}
$output = '<div class="alert alert-success">Schedule have been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}

}
function add_calender_preview(){

 $year = date("Y");
 $enable_duplicate = '0';
 $months = array(1=> 'January-'.$year, 2=> 'Febraury-'.$year, 3=> 'March-'.$year,4=> 'April-'.$year, 5=> 'May-'.$year, 6=> 'June-'.$year,7=> 'July-'.$year, 8=> 'August-'.$year, 9=> 'September-'.$year,10=> 'October-'.$year, 11=> 'November-'.$year,12=> 'December-'.$year);
 $output='';

 $current_user_id = get_current_user_id();
 $args = array(
  'role'         => 'nurse',
  'meta_key'     => 'nurse_subadmin',
  'meta_value'   => $current_user_id,
  'meta_compare' => '=',
); 

 $users = get_users( $args );
 ob_start();
 echo '<div class="table-wrap">';
 echo '<table border="1">';
 echo ' <tr>';
 echo '<th>User</th>';
 echo '<th>Group</th>';
 echo '<th class="non-show-column" style="display:none;"></th>';
 for ($date=1; $date<=31; $date++){

   echo '<th align="center">'.$date.'</th>';

 }
 echo ' </tr>';
 foreach ( $users as $user ) {
   $group_key =  get_user_meta($user->id,'nursing_group',true);
   $group = get_nursing_group_by_id($group_key);
   echo ' <tr>';
   echo '<td width="200px">' . esc_html( $user->display_name ) . '</td>';
   echo '<td>' .$group_key . '</td>';

   $get_user_single_data = get_schedule_by_user($user->id,nurse_set($months,$_REQUEST['month']),true);
   if($get_user_single_data){
    $enable_duplicate = '1';
    ?>
    <td class="non-show-column" style="display:none;">
     <input type="hidden" class="user_with_group" name="user_with_group[]" value="<?php echo $user->id; ?>,<?php echo $group->id; ?>">

   </td>
   <?php
 }
 else{
   echo '<td style="display:none;">
   <input type="hidden" class="user_with_group" name="user_with_group[]" value="'.$user->id.','.$group->id.'"></td>';
 }

 $get_user_complete_data = get_schedule_by_user($user->id,nurse_set($months,$_REQUEST['month']));
 if($get_user_complete_data->first()){
   foreach ($get_user_complete_data as $single_data) {
    ?>
    <td>
     <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $single_data->id; ?>">

     <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
      <option value="">Select Shift</option>
      <option value="1" <?php echo ($single_data->shift == 1)?'selected':''; ?>>1st (7a-3p)</option>
      <option value="2" <?php echo ($single_data->shift == 2)?'selected':''; ?>>2nd (3p-11p)</option>
      <option value="3" <?php echo ($single_data->shift == 3)?'selected':''; ?>>3rd (11p-7a)</option>
    </select>
    <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]" <?php echo ($single_data->assign_to != 0)?'disabled':'';?>>
      <option value="0" <?php echo ($single_data->status == 0)?'selected':''; ?>>Select Option</option>
      <option value="1" <?php echo ($single_data->status == 1)?'selected':''; ?>>Covered</option>
      <?php
      if($single_data->assign_to != 0):
       $user_obj = get_user_by('id', $single_data->assign_to);
       ?>
       <option value="2" <?php echo ($single_data->status == 2)?'selected':''; ?>>Assigned-<?php echo $user_obj->display_name; ?></option>
       <?php else: ?>
        <option value="2" <?php echo ($single_data->status == 2)?'selected':''; ?>>Open</option>
      <?php endif; ?>
    </select>
  </td>
  <?php
}

}
else{
  for ($date=1; $date<=31; $date++){

   echo '<td> 
   <input type="hidden" class="updated_id" name="updated_id[]">
   <select class="form-control selected_shift" id="requestShift" name="selected-shift[]">
   <option value="">Select Shift</option>
   <option value="1" >1st (7a-3p)</option>
   <option value="2">2nd (3p-11p)</option>
   <option value="3">3rd (11p-7a)</option>
   </select>
   <select class="form-control request_shift mark-checked" id="requestShift" name="mark-checked[]">
   <option value="0">Select Option</option>
   <option value="1" >Covered</option>
   <option value="2">Open</option>
   </select>
   </td>';

 }
}
echo ' </tr>';
}
echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );

}


function duplicate_month_data() {
 global $wpdb;
 $current_user_id = get_current_user_id();
 $table         = $wpdb->prefix.'nusre_schedule';
 $user_data     = $_REQUEST['user_with_group'];
 $updated_ids   = $_REQUEST['updated_id'];
 $shifts        = $_REQUEST['shifts'];
 $cheked_values = $_REQUEST['cheked_values'];
 $year          = date("Y");
 $months        = array(
  1  => 'January-'.$year,
  2  => 'Febraury-'.$year,
  3  => 'March-'.$year,
  4  => 'April-'.$year,
  5  => 'May-'.$year,
  6  => 'June-'.$year,
  7  => 'July-'.$year,
  8  => 'August-'.$year,
  9  => 'September-'.$year,
  10 => 'October-'.$year,
  11 => 'November-'.$year,
  12 => 'December-'.$year
);
 if(!array_filter($shifts)) {
  $output = '<div class="alert alert-danger">Please Select Shifts for all nurses</div>';
  return wp_send_json( array( 'status' => 'error', 'message' => $output ) );
}
$month_with_year =  nurse_set($months,$_REQUEST['month']);
$exist_month     = get_schedule_by_month($month_with_year,$current_user_id);
if($exist_month->first()) {
  $output = '<div class="alert alert-danger">Schedule for this month is already added</div>';
  return wp_send_json( array( 'status' => 'error', 'message' => $output ) );
}
$month_with_year = explode("-",$month_with_year);
if(!empty($user_data)){
  $counter = 0;
  foreach ($user_data as $key=>$sngl_user_data) {
   $user = explode(",",$sngl_user_data);
   for ($i=0; $i<=30; $i++){
    $is_open = '';
    $date = $i+1;
    $key_counter = $key + 1;
    if(nurse_set($cheked_values,$counter) == 2){
     $is_open =1;
   }

   $wpdb->insert( 
     $table, 
     array( 
      'user_id'   => nurse_set($user,0), 
      'date'      => $date,
      'shift'     => nurse_set($shifts,$counter),
      'gorup_id'  => nurse_set($user,1),
      'is_open'   => $is_open,
      'month'     => nurse_set($month_with_year,0),
      'year'      => nurse_set($month_with_year,1),
      'status'    => nurse_set($cheked_values,$counter),
      'owner_id'  => $current_user_id
    ), 
     array( 
      '%s', 
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
    ) 
   );


   $counter++;
 }
}
$output = '<div class="alert alert-success">Schedule have been Saved Successfully</div>';
return wp_send_json( array( 'status' => 'success', 'message' => $output ) );
}

}



/*Calender Preview For Nurse*/
function add_nurse_calender_preview(){
  $current_user_id = get_current_user_id();
  $year = date("Y");
  $month = date('n');
  $enable_duplicate = '0';
  $months = array(1=> 'January-'.$year, 2=> 'Febraury-'.$year, 3=> 'March-'.$year,4=> 'April-'.$year, 5=> 'May-'.$year, 6=> 'June-'.$year,7=> 'July-'.$year, 8=> 'August-'.$year, 9=> 'September-'.$year,10=> 'October-'.$year, 11=> 'November-'.$year,12=> 'December-'.$year);
  $output='';

  $args = array(
   'role'         => 'nurse',
   'orderby'      => 'role',
   'order'        => 'ASC',
   'meta_key'     => 'nurse_subadmin',
   'meta_value'   => get_the_author_meta( 'nurse_subadmin', $current_user_id ),
   'meta_compare' => '='
 );
  $users = get_users( $args );
  $days =  (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?$_REQUEST['days']:0;
  $current_day = (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?date("j")+1:1;
  $dates = (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?$_REQUEST['days']+$current_day:31;
  $single_date = (isset($_REQUEST['single_date']))? $_REQUEST['single_date']:'';
  $users = get_users( $args );
  ob_start();
  echo '<div class="table-wrap">';
  echo '<table border="1" class="table">';
  echo ' <tr>';
  echo '<th>User</th>';
  echo '<th>Group</th>';
  echo '<th>Shift</th>';
  if($single_date){
   echo '<th align="center">'.$single_date.'</th>';
   
 }
 else{
   for ($date = $current_day; $date <= $dates; $date++){

     echo '<th align="center">'.$date.'</th>';

     if($date == 31){
      break;
    }

  }
}
echo ' </tr>';
foreach ( $users as $user ) {
 $group_key =  get_user_meta($user->id,'nursing_group',true);
 $group = get_nursing_group_by_id($group_key);
 echo ' <tr>';
 echo '<td width="200px">' . esc_html( $user->display_name ) . '</td>';
 echo '<td>' .$group_key . '</td>';

 $get_user_single_data = get_schedule_by_user($user->id,nurse_set($months,$month),true);
 if($get_user_single_data){
  $enable_duplicate = '1';
  ?>
  <td>
   <input type="hidden" class="user_with_group" name="user_with_group[]" value="<?php echo $user->id; ?>,<?php echo $group->id; ?>">
   <?php echo ($get_user_single_data->shift == 1)?'1st (7a-3p)':''; ?>
   <?php echo ($get_user_single_data->shift == 2)?'2nd (3p-11p)':''; ?>
   <?php echo ($get_user_single_data->shift == 3)?'3rd (11p-7a)':''; ?>
 </td>
 <?php
}

$get_user_complete_data = get_schedule_by_user($user->id,nurse_set($months,$month),false,$days,$single_date);
if($get_user_complete_data->first()){
 foreach ($get_user_complete_data as $single_data) {
  ?>
  <td>
   <input type="hidden" class="updated_id" name="updated_id[]" value="<?php echo $single_data->id; ?>">
   <select class="form-control button_width request_shift mark-checked" id="requestShift" name="mark-checked[]" <?php echo ($single_data->assign_to == 0 && $single_data->is_open == 1)?'style="color: green;"':'disabled';?>>
    <?php if($single_data->assign_to != 0 || $single_data->is_open != 1){?>
      <option value="0" <?php echo ($single_data->status == 0)?'selected':''; ?>>No Status</option>
      <option value="1" <?php echo ($single_data->status == 1)?'selected':''; ?>>Assigned</option>
      <?php
    }
    if($single_data->assign_to != 0):
     $user_obj = get_user_by('id', $single_data->assign_to);
     ?>
     <option value="2" <?php echo ($single_data->status == 2)?'selected':''; ?>>Assigned-<?php echo $user_obj->display_name; ?></option>
     <?php else: ?>
      <option value="" >--select Option--</option>
      <option value="<?php echo $single_data->id; ?>" >Fill Open Need</option>
    <?php endif; ?>
  </select>
</td>
<?php
}

}

echo ' </tr>';
}
echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );

}

/*Calender Preview For Subadmin*/
function add_subadmin_calender_preview(){
  $current_user_id = get_current_user_id();
  $year = date("Y");
  $month = date('n');
  $enable_duplicate = '0';
  $months = array(1=> 'January-'.$year, 2=> 'Febraury-'.$year, 3=> 'March-'.$year,4=> 'April-'.$year, 5=> 'May-'.$year, 6=> 'June-'.$year,7=> 'July-'.$year, 8=> 'August-'.$year, 9=> 'September-'.$year,10=> 'October-'.$year, 11=> 'November-'.$year,12=> 'December-'.$year);
  $shifts = array(1 =>'Day',2=>'Eve',3=>'Night');
  $class = array(1 =>'day-slot',2=>'eve-slot',3=>'night-slot');
  $output='';

  $args = array(
   'role'         => 'nurse',
   'orderby'      => 'role',
   'order'        => 'ASC',
   'meta_key'     => 'nurse_subadmin',
   'meta_value'   => $current_user_id,
   'meta_compare' => '='
 );
  $users = get_users( $args );
  $days =  (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?$_REQUEST['days']:0;
  $current_day = (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?date("j")+1:1;
  $dates = (isset($_REQUEST['days']) && !empty($_REQUEST['days']))?$_REQUEST['days']+$current_day:31;
  $single_date = (isset($_REQUEST['single_date']))? $_REQUEST['single_date']:'';
  if($single_date){
    $single_date = date('j',strtotime($single_date));
  }
  $users = get_users( $args );
  ob_start();
  echo '<div class="table-wrap">';
  echo '<table class="table">';
  echo ' <tr>';
  echo '<th>User</th>';
  echo '<th>Group</th>';
  if($single_date){
    $cuurent_day = date('l',strtotime($month.'/'.$single_date.'/'.$year));
    echo '<th align="center">'.$cuurent_day.'<br>'.$month.'/'.$single_date.'/'.$year.'</th>';

  }
  else{
   for ($date = $current_day; $date <= $dates; $date++){
    $cuurent_day = date('l',strtotime($month.'/'.$date.'/'.$year));
    echo '<th align="center">'.$cuurent_day.'<br>'.$month.'/'.$date.'/'.$year.'</th>';

    if($date == 31){
      break;
    }

  }
}
echo ' </tr>';
foreach ( $users as $user ) {
 $group_key =  get_user_meta($user->id,'nursing_group',true);
 $group = get_nursing_group_by_id($group_key);
 echo ' <tr>';
 echo '<td class="nurse-name"><b>' . esc_html( $user->display_name ) . '</b></td>';
 echo '<td class="nurse-group"><b>' .$group_key . '</b></td>';

 $get_user_single_data = get_schedule_by_user($user->id,nurse_set($months,$month),true);
 
 $get_user_complete_data = get_schedule_by_user($user->id,nurse_set($months,$month),false,$days,$single_date);
 if($get_user_complete_data->first()){
   foreach ($get_user_complete_data as $single_data) {
    ?>
    <td style="padding:0px;">
     <?php if ($single_data->assign_to == 0 && $single_data->is_open == 1) : ?>
      <div class="open-shift-area" style="padding-top:8px;">
        <?php echo get_shift_id($single_data->shift); ?><br>
        Open
      </div>
      <?php elseif ($single_data->status == 1 ) : ?>
        <div class="<?php echo $class[$single_data->shift]; ?>" style="padding-top:8px;">
          <?php echo get_shift_id($single_data->shift); ?><br>
          Assigned
        </div>
        <?php elseif($single_data->status == 0): ?>
         <div class="<?php echo $class[$single_data->shift]; ?>" style="padding-top:8px;">
          <?php echo get_shift_id($single_data->shift); ?><br>
          empty
        </div>
        <?php
      elseif($single_data->assign_to != 0):
       $user_obj = get_user_by('id', $single_data->assign_to);
       ?>
       <div class="<?php echo $class[$single_data->shift]; ?>" style="padding-top:8px;">
        <?php echo get_shift_id($single_data->shift); ?><br>
        Assigned to <?php echo $user_obj->display_name; ?> 
      </div>
    <?php endif; ?>

  </td>
  <?php
}

}

echo ' </tr>';
}
echo '</table>';
echo '</div>';
$output = ob_get_clean();
wp_send_json( array( 'status' => 'success', 'data' => $output,'enable_duplicate'=>$enable_duplicate ) );

}

function search_open_slots(){
  require_once ABSPATH . 'wp-content/plugins/nursing-schedule/inc/Model.php';

  $db     = \WeDevs\ORM\Eloquent\Database::instance();
  $current_user_id = get_current_user_id();
  $year = date("Y");
  if (!count($_POST))
    return;
  $month    = nurse_set($_POST,'select-month');
  $date     = nurse_set($_POST,'select-date');
  $preselected_slot = nurse_set($_POST,'preselected_slot');
  $months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
  $schedules    = $db->table('nusre_schedule')->where('year',$year)->where('month',$months[$month])->where('date',$date)->where('is_open',1)->where('assign_to',0)->where('owner_id',$current_user_id)->get();
  if($schedules->first()){
    ob_start();
    echo '<select class="select-slot form-control">';
    echo '<option value="0">Select Option</option>';
    foreach ($schedules as $schedule) {

     /* $user_obj = get_user_by('id', $schedule->user_id);
     echo '<option value="'.$schedule->id.'" >Shift '.get_shift_id($schedule->shift).' - Nurse ('.$user_obj->nickname.' ) - Date ('.$schedule->date.'-'.$schedule->month.'-'.$schedule->year.') </option>';*/
     ?>
     <option value="<?php echo $schedule->id; ?>" <?php echo ($preselected_slot == $schedule->id)?'selected':''; ?> >Shift <?php echo get_shift_id($schedule->shift); ?> - Nurse (<?php echo $user_obj->display_name; ?> ) - Date (<?php echo $schedule->date.'-'.$schedule->month.'-'.$schedule->year; ?>) </option>
     <?php
   }
   echo '</select>';
   $output = ob_get_clean();
   wp_send_json( array( 'status' => 'success', 'data' => $output) );
 }
 $output = '<div class="alert alert-danger">No record found against this search</div>';
 wp_send_json( array( 'status' => 'error', 'message' =>$output) );
}
function fil_slot(){
 require_once ABSPATH . 'wp-content/plugins/nursing-schedule/inc/Model.php';

 $db     = \WeDevs\ORM\Eloquent\Database::instance();
 $user_id     = get_current_user_id();
 global $wpdb;

 $data_id     = $_REQUEST['data_id'];

 $schedule    = $db->table('nusre_schedule')->where('id',$data_id)->first();
 $date        =  $schedule->date.' '.$schedule->month .' '.$schedule->year;
 
 $newDate = date("N", strtotime($date));

 if($newDate <= 7){
  $greater_days = 7 - $newDate;
  $less_days = $greater_days - 6;
  $start_date = $schedule->date + $less_days;
  $end_date = $schedule->date + $greater_days;
  $dates = [];
  $previous_dates = [];
  $coming_dates = [];
  for ($i = $start_date; $i <= $end_date; $i++){
    if($i <= 0){
      $previous_dates[] = $i;
    }
    if($i > 31){
      $coming_dates[] = $i;
    }
    else{
      $dates[] = $i;
    }
  }
}

$week_schedule    = $db->table('nusre_schedule')->whereIn('date',$dates)->where('month',$schedule->month)->where('year',$schedule->year)->where('status',1)->where('user_id',$user_id)->get();
$working_days_count = $week_schedule->count();
$months = array(1=> 'January', 2=> 'Febraury', 3=> 'March',4=> 'April', 5=> 'May', 6=> 'June',7=> 'July', 8=> 'August', 9=> 'September',10=> 'October', 11=> 'November',12=> 'December');
if(!empty($coming_dates)){
  $coming_days = [];
  $counter = 1;
  foreach($coming_dates as $coming_date){
    $coming_days [] = $counter;
    $counter++;
  }
  $month = array_search ($schedule->month, $months)+1;
  $week_schedule_next_month   = $db->table('nusre_schedule')->whereIn('date',$coming_days)->where('month',$months[$month])->where('year',$schedule->year)->where('status',1)->where('user_id',$user_id)->get();

  $working_days_count = $working_days_count + $week_schedule_next_month->count();
}

if(!empty($previous_dates)){
  $previous_days = [];
  $counter = 31;
  foreach($previous_dates as $previous_date){
    $coming_days [] = $counter;
    $counter--;
  }
  $month = array_search ($schedule->month, $months)-1;
  $week_schedule_pre_month   = $db->table('nusre_schedule')->whereIn('date',$coming_days)->where('month',$months[$month])->where('year',$schedule->year)->where('status',1)->where('user_id',$user_id)->get();

  $working_days_count = $working_days_count + $week_schedule_pre_month->count();
}

if($working_days_count >= 5){
  $output = '<div class="alert alert-danger">Your schedule for this week is completed</div>';
  wp_send_json( array( 'status' => 'success', 'message' => $output) );
}
$table       = $wpdb->prefix.'nusre_schedule';
$wpdb->update( 
 $table, 
 array( 
  'assign_to'   => $user_id, 
), 
 array( 'id' => $data_id ), 
 array( 
  '%s', 
), 
 array( '%d' ) 
);
$output = '<div class="alert alert-success">You have avail this need</div>';
wp_send_json( array( 'status' => 'success', 'message' => $output) );
}


