<?php
if(!defined('ABSPATH')){
	exit;
}
$wpie_options = get_option('wpie_options');
if(!isset($wpie_options['enabled'])){
	$wpie_options['enabled'] = true;
}


if (!function_exists('boolval')) {
	function boolval($val) {
		return (bool) $val;
	}
}

/**
 * Add meta for client hints
 * @return void
 */
function wpie_add_lite_meta() {
	echo '<meta http-equiv="Accept-CH" content="DPR, Viewport-Width, Width, Save-Data">';
}

/**
 * Announce Client Hints support in HTTP header
 * @return void
 */
function wpie_add_header_acceptch() {
	header( 'Accept-CH: DPR, Viewport-Width, Width, Save-Data' );
}

/**
 * Add prefetch in
 * @return void
 */
function wpie_add_preconnect_link( $user_setting = array() ) {
	$data = wpie_get_real_settings( $user_setting );
	$host  = '//';
	$host .= empty( $data['key'] ) ? 'try.' : $data['key'] . '.';
	$host .= ( 'lite' == $data['key_type'] || empty( $data['key'] ) ) ? 'lite.' : '';
	$host .= 'imgeng.in';
	/**
	* If you rather want the preconnect in a meta tag, uncomment the below line.
	*/
	//echo '<link rel="preconnect" href="'.$host.'" crossorigin>';
	
	header( 'Link: <'.$host.'>; rel=preconnect; crossorigin' );	
}

if($wpie_options['enabled']){
	add_action( 'send_headers', 'wpie_add_header_acceptch' );
	/**
	* If you rather want the Accept-CH in a meta tag, uncomment the below line.
	*/
	/*
	add_action( 'wp_head', 'wpie_add_lite_meta' );
	*/
	add_action( 'send_headers', 'wpie_add_preconnect_link' );
}

/**
 * Get default options
 * @return array
 */
function wpie_get_default_settings() {
	$data = array(
		'enabled'  => true,
		'key'      => '',
		'key_type' => 'normal',
		'w'        => 0,
		'h'        => 0,
		'pc'       => 90,
		'cmpr'     => 10,
		'fit'      => ''
	);

	return $data;
}

/**
 * Return current configuration parameters.
 * Defaults parameters will be override by configurations settings. Eventually, parameters  $user_settings override configuration setings
 *
 * @param array $user_setting
 *
 * @return array
 */
function wpie_get_real_settings( $user_setting = array() ) {
	$default_values = wpie_get_default_settings();
	$global_values  = get_option( 'wpie_options' );
	$data           = wp_parse_args( $global_values, $default_values );
	$data2          = wp_parse_args( $user_setting, $data );

	return $data2;
}

/**
 * Create first half of image URI.
 *
 * @param array $user_setting
 *
 * @return string
 */
function wpie_url_generate_first_part( $user_setting = array() ) {
	$data = wpie_get_real_settings( $user_setting );
	$url  = '//';
	$url .= empty( $data['key'] ) ? 'try.' : $data['key'] . '.';
	$url .= ( 'lite' == $data['key_type'] || empty( $data['key'] ) ) ? 'lite.' : '';
	$url .= 'imgeng.in/';

	if ( ! empty( $data['w'] ) ) {
		$url .= 'w_' . trim( $data['w'] ) . '/';
	}
	if ( ! empty( $data['h'] ) ) {
		$url .= 'h_' . trim( $data['h'] ) . '/';
	}
	if ( ! empty( $data['cmpr'] ) ) {
		$url .= 'cmpr_' . trim( $data['cmpr'] ) . '/';
	}
	if ( ! empty( $data['pc'] ) && empty($data['h']) && empty($data['w']) ) {
		$url .= 'pc_' . trim( $data['pc'] ) . '/';
	}
	if ( ! empty( $data['fit'] ) && in_array( $data['fit'], array( 'stretch', 'box', 'letterbox', 'cropbox' ) ) ) {
		$url .= 'm_' . trim( $data['fit'] ) . '/';
	}

	return $url;
}

/**
 * Return imageengine URL
 *
 * @param string $url_to_convert
 * @param array  $options
 *
 * @return string
 */
function wpie_get_converted_url( $url_to_convert, $options = array() ) {
	$new_url = wpie_url_generate_first_part( $options );
	$new_url .= $url_to_convert;

	return $new_url;
}

/**
 * Strips the URL of everything but the hostname and the path
 * example: https://user:pass@www.google.com/foo.jpg?a=b => www.google.com/foo.jpg
 * @param $url URL
 * @return string
 */
function wpie_get_url_stripped( $url ) {
	$host = parse_url($url, PHP_URL_HOST);
	$path = parse_url($url, PHP_URL_PATH);
	return $host.$path;
}


/**
 * Filter text
 * @return string
 */
function wpie_imgs_find_and_replace( $content ) {

	// Match all <img> tags in given text
	$search = preg_match_all( "/<img[^>]*>/", $content, $all_imgs );

	if ( ! empty( $search ) ) {
		$site_url = get_bloginfo( 'url' );
		$site_url_stripped = wpie_get_url_stripped($site_url);

		foreach ( $all_imgs[0] as $img_tag ) {
			$data = array();

			// Match src parameter
			if (preg_match( "/src=['\"]([^'\"]+)/", $img_tag, $src )) {
				$old_url = $src[1];
				$old_src = $src[0];

				$scheme ="http";
				if (is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
				    $scheme ="https";
				}

				// Fix relative schemes
				if (strpos($old_url, '//') === 0) {
					// Add the current scheme to the URL
					$old_url = "$scheme:$old_url";
				}

				// Fix relative paths
				if (!preg_match('#^https?://#', $old_url)) {
					if (!isset($_SERVER['HTTP_HOST'])) {
						// Without the HOST header, there is no way to determine the absolute URL to the image
						continue;
					}
					//Assume image path is absolute to host
					if ($old_url[0] != "/"){
						$old_url  = "/".$old_url;
					}

					// Rebuild the current URL - this will not work correctly for sites using ports other than 80/443
					$abs_url = $scheme."://".$_SERVER['HTTP_HOST'];
					$abs_url .= $old_url;
					$old_url = $abs_url;
				}

				$qualified_src = 'src="'.$old_url;

				// If the old URL for the image is not from this site, skip it
				$old_url_stripped = wpie_get_url_stripped($old_url);
				if (strpos($old_url_stripped, $site_url_stripped) === false) {
					continue;
				}

				// Check if Width parameter exists
				if (preg_match( "/width=['\"]([^'\"]+)/", $img_tag, $width )) {
					$data['w'] = $width[1];
				}

				// Check if Height parameter exists
				if (preg_match( "/height=['\"][^'\"]*/", $img_tag, $height )) {
					$data['h'] = $height[1];
				}

				$old_url_encoded = rawurlencode( $old_url );
				$trans           = array(
					'%3A' => ':',
					'%2F' => '/',
					'%26' => '&',
					'%3F' => '?',
				);
				$old_url_encoded = strtr( $old_url_encoded, $trans );

				$new_url = wpie_get_converted_url( $old_url_encoded, $data );
				$new_url_with_src = str_replace($old_url, $new_url, $qualified_src);
				$content = str_replace( $src[0], $new_url_with_src, $content );
			}
		}
	}

	return $content;
}
if($wpie_options['enabled']) {
	$data = wpie_get_real_settings( $user_setting );
	if(!empty($data['key'])){
		add_filter('the_content', 'wpie_imgs_find_and_replace'); // Add filter to post and page contents
		add_filter('widget_text', 'wpie_imgs_find_and_replace'); // Add filter to widgets text
		add_filter('post_thumbnail_html', 'wpie_imgs_find_and_replace'); // Add filter to wp thumbanils functions
	}
}
/**
 * srcset filter
 * @param array $sources all the available sources
 * @param int $attachment_id the attachment id
 * @return array all the available sources with filtered url
 */
function wpie_srcset_filter($sources, $size_array, $image_src, $image_meta, $attachment_id){
	//Removed this check since theme devs often forget to add add_theme_support( 'html5' ) to their functions.php file.
	//if(current_theme_supports('html5')) {
		//check for Jetpack Photon and disable for these urls:
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
			add_filter( 'jetpack_photon_skip_for_url', '__return_true' ); 
		}
		if (!empty($sources)) {
			$full_img_url = wp_get_attachment_image_src($attachment_id, 'full');
			foreach ($sources as $size => $source) {
				if ('w' == $source['descriptor']) {
					$options = array(
						'w' => $source['value']
					);
					$new_url = wpie_get_converted_url($full_img_url[0], $options);
					$sources[$size]['url'] = $new_url;
				}
			}
		}
	//}
	return $sources;
}

/**
 * set srcset filter only on WP >= 4.4
 */
if(function_exists('wp_calculate_image_srcset') && $wpie_options['enabled']) {
	$data = wpie_get_real_settings( $user_setting );
	if(!empty($data['key'])){
		add_filter('wp_calculate_image_srcset', 'wpie_srcset_filter', 21, 5);
	}
}