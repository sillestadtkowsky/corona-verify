<?php
class CV_UTILS
{
	public static function debugCode($message){
		if (defined('WP_DEBUG') && true === WP_DEBUG) {
			echo esc_html( 'DEBUG_MESSAGE: ' . $message );
		}
	}

	public static function is_logged_in()
	{
		if (function_exists('is_user_logged_in')) {
			return is_user_logged_in();
		}
	}

	/**
	 * Recursive sanitation for an array
	 * 
	 * @param $array
	 *
	 * @return mixed
	 */
	public static function recursive_sanitize_text_field($array) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = recursive_sanitize_text_field($value);
			}
			else {
				$value = sanitize_text_field( $value );
			}
		}

	return $array;
	}

	public static function isGueltig($expiredDate)
	{
		$now = CV_UTILS::getNow();
		$expiredDateFormat = new DateTime($expiredDate, new DateTimeZone("CET"));

		if ($expiredDateFormat < $now) {
			return 0;
		} else {
			return 1;
		}
	}

	public static function getNow()
	{
		$now = new DateTime();
		return $now->setTimezone(new DateTimeZone("Europe/Berlin"));
	}

	public static function fa_custom_setup_cdn_webfont($cdn_url = '', $integrity = null)
	{
		$matches = [];
		$match_result = preg_match('|/([^/]+?)\.css$|', $cdn_url, $matches);
		$resource_handle_uniqueness = ($match_result === 1) ? $matches[1] : md5($cdn_url);
		$resource_handle = "font-awesome-cdn-webfont-$resource_handle_uniqueness";

		foreach (['wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts'] as $action) {
			add_action(
				$action,
				function () use ($cdn_url, $resource_handle) {
					wp_enqueue_style($resource_handle, $cdn_url, [], null);
				}
			);
		}

		if ($integrity) {
			add_filter(
				'style_loader_tag',
				function ($html, $handle) use ($resource_handle, $integrity) {
					if (in_array($handle, [$resource_handle], true)) {
						return preg_replace(
							'/\/>$/',
							'integrity="' . $integrity .
								'" crossorigin="anonymous" />',
							$html,
							1
						);
					} else {
						return $html;
					}
				},
				10,
				2
			);
		}
	}
}
