<?php

/*
Plugin Name: Booker
Plugin URI: https://redpixiemedia.com
Description: Appointment Setting Plugin for Wordpress
Version: 0.8
Author: Red Pixie Media
Author URI: https://redpixiemedia.com
*/

include 'booker-utils.php';

include 'booker-admin.php';

function bookerShortcodeWidget() {
	ob_start();
	include 'templates/booker-widget.php';
	return ob_get_clean();
}
add_shortcode('Booker-Widget', 'bookerShortcodeWidget');

function bookerActivate() {
	if (get_option('booker_data') == false) {
		add_option('booker_data', array());
	}
	if (get_option('booker_settings') == false) {
		$settings = array(
			'Color' => '#16B920',
			'IMG' => get_site_url(null, '/wp-content/plugins/booker/assets/default-email-image.png', 'https'),
			'Company' => '',
			'Email' => '',
			'Widget' => '',
			'Barbers' => array()
		);
		add_option('booker_settings', $settings );
	}
	if (get_option('booker_setup') === false) {
		add_option('booker_setup', 0);
	}
}
function bookerSetup() {
	if (get_option('booker_setup') == 0) {
		update_option('booker_setup', 1);
		$URL = $homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker-setup', 'https');
		bookerCron();
		wp_redirect($URL);
	}
}
function bookerCron() {
	$cronScript = get_site_url(null, '/wp-content/plugins/booker/booker-cron.php', 'https');
	$cronJob = '0 0 * * * curl '.$cronScript;	
	$output = shell_exec('crontab -l');
	file_put_contents('/tmp/crontab.txt', $output.$cronJob.PHP_EOL);
	echo exec('crontab /tmp/crontab.txt');
}
register_activation_hook( __FILE__, 'bookerActivate' );
add_action('admin_init', 'bookerSetup');

?>