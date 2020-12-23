<?php

define(WP_PLUGIN_URL, ABSPATH . 'wp-content/plugins/nursing-schedule');
add_action( 'init', 'my_script_enqueuer' );

function my_script_enqueuer() {
wp_register_script( "register_ajax_script", WP_PLUGIN_URL.'/assets/js/my_voter_script.js', array('jquery') );
wp_localize_script( 'register_ajax_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'register_ajax_script' );

}
?>