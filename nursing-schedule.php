<?php
   /*
   Plugin Name: Nursing Schedule
   Plugin URI: wantechsolution.com
   description:a plugin who managed the nursing schedule
   Version: 1.0
   Author: Sabeeh Hashmi "sabeeh.hashmi@yahoo.com"
   Author URI: #
   License: GPL2
   */

   add_action( 'user_new_form', 'dontchecknotify_register_form' );
   
   function dontchecknotify_register_form() { 
    echo '<scr'.'ipt>jQuery(document).ready(function($) { 
      $("#send_user_notification").removeAttr("checked"); 
    } ); </scr'.'ipt>';
  }
  include_once 'inc/functions.php';
  include_once 'inc/Framework/enque.php';
  include_once 'inc/Framework/ajax.php';
  include_once 'inc/Framework/register-new-role.php';

  function addRole()
  {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/add-role.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('add-role','addRole');
 function rolesList()
 {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/roles-list.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('roles-list','rolesList');

 function addshift()
 {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/add-shift.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('add-shift','addshift');
 function shiftsList()
 {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/shifts-list.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('shifts-list','shiftsList');
 function addslot()
 {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/add-slot.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('add-slot','addslot');
 function slotsList()
 {

   ob_start();
   $userinfo = get_userdata(get_current_user_id());
   $role = $userinfo->roles[0];

   if($role!='subadmin' && $role!='administrator')
   {

     wp_redirect('/login');
     exit();
   }

   include_once 'inc/Framework/pages/slots-list.php';
   $new_content = ob_get_contents();
   ob_end_clean();  
   echo $new_content;
 }
 add_shortcode('slots-list','slotsList');
 
 /*Old Functions Shortcodes*/
 
 function makeRequestToNurse()
 {
  ob_start();
  $userinfo = get_userdata(get_current_user_id());
  $role = $userinfo->roles[0];
  if($role!='clients' && $role!='administrator')
  {
    wp_redirect('/login');
    exit();
  }
  include_once 'inc/Framework/pages/make-request-to-nurse.php';
  $new_content = ob_get_contents();
  ob_end_clean();  
  echo $new_content;
}
add_shortcode('make-request-to-nurse','makeRequestToNurse'); 
function nurseCalendar()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='nurse' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/nurse-calendar.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('nurse-calendar','nurseCalendar');

function subAdminCalendar()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/subadmin-calendar.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('subadmin-calendar','subAdminCalendar');

function addNurse()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/add-nurse.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('add-nurse','addNurse');


function makeNursesSchedule()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/nursing-schedule.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('make-nurses-schedule','makeNursesSchedule');
function sendRequestToNurse()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/send-request.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('send-request-to-nurse','sendRequestToNurse');
function NurseList()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/nurse-list.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('nurses-list','NurseList');
function GroupsList()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/groups-list.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('groups-list','GroupsList');
function AddGroup()
{

 ob_start();
 $userinfo = get_userdata(get_current_user_id());
 $role = $userinfo->roles[0];

 if($role!='subadmin' && $role!='administrator')
 {

   wp_redirect('/login');
   exit();
 }

 include_once 'inc/Framework/pages/add-group.php';
 $new_content = ob_get_contents();
 ob_end_clean();  
 echo $new_content;
}
add_shortcode('add-group','AddGroup');

function nursing_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
  if (isset($user->roles) && is_array($user->roles)) {
        //check for subscribers
    if (in_array('clients', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
      $redirect_to =  home_url().'/client-status/';
    }
    if (in_array('agencies', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
      $redirect_to =  home_url().'/vendor-status/';
    }
    if (in_array('nurse', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
      $redirect_to =  home_url().'/client-status-copy/';
    }
    if (in_array('subadmin', $user->roles)) {
            // redirect them to another URL, in this case, the homepage 
      $redirect_to =  home_url().'/subadmin-calendar/';
    }
  }

  return $redirect_to;
}

add_filter( 'login_redirect', 'nursing_login_redirect', 10, 3 );

 /*function checkifuseronhome(){

  $userinfo = get_userdata(get_current_user_id());
  $role = $userinfo->roles[0];
  if(is_home() && $role=='subadmin'){
    wp_redirect('/subadmin-calendar');
  }
}
add_action('init','checkifuseronhome');*/

include_once 'inc/Framework/backend-pages/nursing-schedule.php';
include_once 'inc/Framework/backend-pages/send-request.php';

?>

