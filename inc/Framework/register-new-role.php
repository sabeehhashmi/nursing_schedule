<?php


/*Register New User Role as Nurse*/
function addNurseRole(){
  add_role(
   'nurse',
   __( 'Nurse' ),
   array(
        'read'         => true,  // true allows this capability
        'edit_posts'   => true,
      )
 );

  add_role(
    'subadmin',
    __( 'SubAdmin' ),
    array(
        'read'         => true,  // true allows this capability
        'edit_posts'   => true,
      )
  );
}
add_action('init','addNurseRole');

/*Add Option For Nursing Group*/
/*add_action( "user_new_form", "selectNursingGroupOnUpdate" );
add_action( 'show_user_profile', 'selectNursingGroupOnUpdate' );
add_action( 'edit_user_profile', 'selectNursingGroupOnUpdate' );*/

function selectNursingGroupOnUpdate( $user ) {
  $group    = (is_object($user))?get_the_author_meta( 'nursing_group', $user->ID ):'';
  $subadmin = (is_object($user))?get_the_author_meta( 'nurse_subadmin', $user->ID ):'';
  $employement_status = (is_object($user))?get_the_author_meta( 'employement_status', $user->ID ):'';
  ?>

  <table class="form-table">
   <tr>
    <th>
      <b><label for="employement_status"><?php esc_html_e( 'Select Employement Status', 'crf' ); ?></label></b></th>
     <td>
      <?php
      $employement_types = get_nursing_employement();
      ?>
      <select class="form-control button_width" id="employement_status" name="employement_status">
       <option value="">Type</option>
       <?php
       if($employement_types->first()):
        foreach ($employement_types as $employement_type):
          ?>
          <option value="<?php echo $employement_type->employement_key; ?>" <?php echo ($employement_status == $employement_type->employement_key)? 'selected':''; ?>><?php echo $employement_type->name; ?></option>

          <?
        endforeach;
      endif;
      ?>
    </select>

    <b>Note <span class="acf-required">*</span> This will only work when role will be nurse</b>
  </td>
</tr>
<tr>
  <th>
   <b><label for="nursing_group"><?php esc_html_e( 'Select Nursing Group', 'crf' ); ?></label></b></th>
   <td>
    <?php
    $groups = get_nursing_group();
    ?>
    <select class="form-control button_width" id="nursing_group" name="nursing_group">
     <option value="">Type</option>
     <?php
     if($groups->first()):
      foreach ($groups as $s_group):
        ?>
        <option value="<?php echo $s_group->group_key; ?>" <?php echo ($group == $s_group->group_key)? 'selected':''; ?>><?php echo $s_group->group_value; ?></option>

        <?
      endforeach;
    endif;
    ?>
  </select>

  <b>Note <span class="acf-required">*</span> This will only work when role will be nurse</b>
</td>
</tr>
<tr>
  <th>
    <b><label for="nursing_group"><?php esc_html_e( 'Select Nurse SubAdmin', 'crf' ); ?></label></b></th>
    <td>
      <?php
      $args = array(
       'role'    => 'subadmin',
       'orderby' => 'role',
       'order'   => 'ASC'
     );
      $users = get_users( $args );
      ?>
      <select class="form-control button_width" id="nurse_subadmin" name="nurse_subadmin">
       <option value="">SubAdmin</option>
       <?php
       if(!empty($users)):
        foreach ($users as $s_user):
          ?>
          <option value="<?php echo $s_user->ID; ?>" <?php echo ($s_user->ID == $subadmin)? 'selected':''; ?>><?php echo $s_user->display_name; ?></option>

          <?
        endforeach;
      endif;
      ?>
    </select>

    <b>Note <span class="acf-required">*</span> This will only work when role will be nurse</b>
  </td>
</tr>
</table>
<?php
}


/*add_action( 'personal_options_update', 'UpdateNursingGroup' );
add_action( 'edit_user_profile_update', 'UpdateNursingGroup' );
add_action('user_register', 'UpdateNursingGroup');*/

function UpdateNursingGroup( $user_id ) {

  if ( ! current_user_can( 'edit_user', $user_id ) ) {
   return false;
 }
 if ( ! empty( $_POST['nursing_group'] ) && $_POST['role'] =='nurse' ) {
   update_user_meta( $user_id, 'nursing_group', $_POST['nursing_group']  );
 }
 if ( ! empty( $_POST['nurse_subadmin'] ) && $_POST['role'] =='nurse' ) {
   update_user_meta( $user_id, 'nurse_subadmin', $_POST['nurse_subadmin']  );
 }
 if ( ! empty( $_POST['employement_status'] ) && $_POST['role'] =='nurse' ) {
   update_user_meta( $user_id, 'employement_status', $_POST['employement_status']  );
 }
}