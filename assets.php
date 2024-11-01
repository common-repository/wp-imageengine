<?php
if(!defined('ABSPATH')){
	exit;
}
/**
 * return @void
 */
function wpie_enqueue_scripts( $hook_suffix ){
	if('settings_page_wpie-options-page' == $hook_suffix){
		wp_enqueue_style('imageengine-bootstrap-css',IMGENG_URL.'/css/bootstrap.min.css',array(),'3.3.6');
		wp_enqueue_style('imageengine-css',IMGENG_URL.'/css/imageengine.css',array(),IMGENG_VERSION);
		wp_enqueue_script('imageengine-js',IMGENG_URL.'/js/imageengine.js',array('jquery'),IMGENG_VERSION);

		$params = array(
			'wpie_lite_registration_url' => IMGENG_LITE_REGISTRATION_URL,
			'wpie_registration_url' => IMGENG_REGISTRATION_URL
		);
		wp_localize_script( 'imageengine-js', 'wpie_options', $params );

	}
}
add_action('admin_enqueue_scripts' , 'wpie_enqueue_scripts');