<?php

/*Register Booker Home Page*/
add_action('admin_menu', 'registerBookerAdminHome');
function registerBookerAdminHome() {
	$hooknameTop = add_menu_page('Booker', 'Booker', 'manage_options', 'booker', 'bookerAdminHome', 'dashicons-book', 25);
	$hooknameSub = add_submenu_page('booker', 'Booker', 'Home', 'manage_options', 'booker', 'bookerAdminHome' );
}
function bookerAdminHome() {
	include 'templates/booker-home.php';
}


/*Register Booker Manage Page*/
add_action('admin_menu', 'registerBookerManagePage');
function registerBookerManagePage() {
	$hookname = add_submenu_page('booker', 'Manage', 'Manage', 'manage_options', 'booker-manage', 'bookerManagePage' );
	add_action( 'load-' . $hookname, 'saveBookerManagePage' );
}
function bookerManagePage() {
	include 'templates/booker-manage.php';
}
function saveBookerManagePage() {
	if (isset($_POST['add'])) {
		$schedule = new schedule();
		$schedule->add($_POST['date'], $_POST['Time-Start'], $_POST['Time-End']);
	}
	
	if (isset($_POST['delete'])) {
		$schedule = new schedule();
		$schedule->delete($_POST['delete']);
	}
	
	if (isset($_POST['update'])) {
		$schedule = new schedule();
		$ID = $_POST['Book-ID'];
		$data = array(
			'Date' => $_POST['Book-Date'],
			'Time-Start' => $_POST['Book-timeStart'],
			'Time-End' => $_POST['Book-timeEnd'],
			'Name' => $_POST['Book-Name'],
			'Email' => $_POST['Book-Email'],
			'Phone' => $_POST['Book-Phone'],
			'Address' => $_POST['Book-Address'],
			'Service' => $_POST['Book-Service'],
			'Barber' => $_POST['Book-Barber'],
			'Status' => $_POST['Book-Status'],
		);
		$schedule->update($ID, $data);
	}
}

/*Register Booker Barbers Page*/
add_action('admin_menu', 'registerBookerBarbersPage');
function registerBookerBarbersPage() {
	$hookname = add_submenu_page('booker', 'Barbers', 'Barbers', 'manage_options', 'booker-barbers', 'bookerBarbersPage' );
	add_action( 'load-' . $hookname, 'saveBookerBarbersPage' );
}
function bookerBarbersPage() {
	include 'templates/booker-barbers.php';
}
function saveBookerBarbersPage() {
	if (isset($_POST['create'])) {
		$settings = get_option('booker_settings');
		$barber = $_POST['barber'];
		array_push($settings['Barbers'], $barber);
		update_option('booker_settings', $settings);
	}
	
	if (isset($_POST['delete'])) {
		$settings = get_option('booker_settings');
		$exists = array_search($_POST['delete'], $settings['Barbers']);
		if ($exists !== false) {
    		unset($settings['Barbers'][$exists]);
    		update_option('booker_settings', $settings);
		}
	}
}


/*Register Booker History Page*/
add_action('admin_menu', 'registerBookerHistoryPage');
function registerBookerHistoryPage() {
	$hookname = add_submenu_page('booker', 'History', 'History', 'manage_options', 'booker-history', 'bookerHistoryPage');
	//add_action('load-'.$hookname, 'saveBookerHistoryPage');
}
function bookerHistoryPage() {
	include 'templates/booker-history.php';
}


/*Register Booker Settings Page*/
add_action('admin_menu', 'registerBookerSettingsPage');
function registerBookerSettingsPage() {
	$hookname = add_submenu_page('booker', 'Settings', 'Settings', 'manage_options', 'booker-settings', 'bookerSettingsPage');
	add_action('load-'.$hookname, 'saveBookerSettingsPage');
}
function bookerSettingsPage() {
	include 'templates/booker-settings.php';
}
function saveBookerSettingsPage() {
	if (isset($_POST['Save'])) {
		$settings = get_option('booker_settings');
		$settings['IMG'] = (isset($_POST['IMG'])) ? $_POST['IMG'] : $settings['IMG'];
		$settings['Color'] = (isset($_POST['Color'])) ? $_POST['Color'] : $settings['Color'];
		$settings['Company'] = (isset($_POST['Company'])) ? $_POST['Company'] : $settings['Company'];
		$settings['Widget'] = (isset($_POST['Widget'])) ? $_POST['Widget'] : $settings['Widget'];
		update_option('booker_settings', $settings);
	}
}


/*Register Booker Backups Page*/
add_action('admin_menu', 'registerBookerBackupPage');
function registerBookerBackupPage() {
	$hookname = add_submenu_page('booker', 'Backup', 'Backup', 'manage_options', 'booker-backup', 'bookerBackupPage' );
	add_action( 'load-' . $hookname, 'saveBookerBackupPage' );
}
function bookerBackupPage() {
	include 'templates/booker-backup.php';
}
function saveBookerBackupPage() {
	if (isset($_POST['create'])) {
		$backup = new backup();
		$backup->create();
	}
	
	if (isset($_POST['delete'])) {
		$backup = new backup();
		$backup->delete($_POST['delete']);
	}
	
	if (isset($_POST['revert'])) {
		$backup = new backup();
		$backup->replace($_POST['revert']);
	}
	
	if (isset($_FILES['import'])) {
		$backup = new backup();
		$backup->import($_FILES['import']);
	}
	
	if (isset($_POST['clear'])) {
		$backup = new backup();
		$backup->clear();
	}
}


/*Register Booker Setup Page*/
add_action('admin_menu', 'registerBookerSetupPage');
function registerBookerSetupPage() {
	$hookname = add_submenu_page('booker', 'Setup', 'Setup', 'manage_options', 'booker-setup', 'bookerSetupPage' );
	add_action( 'load-' . $hookname, 'saveBookerSetupPage' );
}
function bookerSetupPage() {
	include 'templates/booker-setup.php';
}
function saveBookerSetupPage() {
	if (isset($_POST['Setup'])) {
		$settings = get_option('booker_settings');
		$settings['Email'] = $_POST['Email'];
		$settings['Company'] = $_POST['Company'];
		$settings['Color'] = $_POST['Color'];
		update_option('booker_settings', $settings);
		wp_redirect(get_site_url(null, '/wp-admin/admin.php?page=booker', 'https'));
	}
}


/*Add Widget to Booker Frontend Page*/
add_filter( 'the_content', 'bookerShortcodeAdder', 20 );
function bookerShortcodeAdder( $content ) {
	global $post;
 	$settings = get_option('booker_settings');
 	if ($settings['Widget'] != '' && $post->ID == $settings['Widget']) {
		if (is_page() && in_the_loop() && is_main_query()) {
			$widget = do_shortcode('[Booker]'); 
			return $widget;
		}
	}
    return $content;
}

?>