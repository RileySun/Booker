<?php

//Heavy use of bookerEmail($ID, $type, $email, $subject) - see booker-utils.php

include_once '../../../wp-load.php';

if (isset($_POST['Book'])) {
	if ($_POST['Book'] != 0) {
		$set = get_option('booker_settings');
		//Update Schedule
		$updateData = array(
			'Name' => (isset($_POST['Name'])) ? $_POST['Name'] : '',
			'Email' => (isset($_POST['Email'])) ? $_POST['Email'] : '',
			'Phone' => (isset($_POST['Phone'])) ? $_POST['Phone'] : '',
			'Address' => (isset($_POST['Address'])) ? $_POST['Address'] : '',
			'Service' => (isset($_POST['Service'])) ? $_POST['Service'] : '',
			'Barber' => (isset($_POST['Barber'])) ? $_POST['Barber'] : '',
			'Status' => 'Unconfirmed'
		);
		$schedule = new schedule();
		$schedule->update($_POST['Book'], $updateData);
		//Send Confirmation Email	
		$result = bookerEmail($_POST['Book'], 'Book', $_POST['Email'], 'Please Confirm Your Appointment');
		
		wp_redirect(get_site_url().'?Action=Book');
		exit;
	}
}

if (isset($_GET['Confirm'])) {
	$schedule = new schedule();
	$settings = get_option('booker_settings');
	$updateData = array('Status' => 'Confirmed');
	$schedule->update($_GET['Confirm'], $updateData);
	$appoint = $schedule->getBy('ID', $_GET['Confirm'])[0];
	
	//Send Confirmation Email to User
	$userResult = bookerEmail($_GET['Confirm'], 'Confirm', $appoint['Email'], $settings['Company'].' - Your Appointment Has Been Confirmed');
	//Send Confirmation Email to Admin
	$adminResult = bookerEmail($_GET['Confirm'], 'Admin', $settings['Email'], 'A New Appointment Has Been Booked and Confirmed');
	
	wp_redirect(get_site_url().'?Action=Confirm');
	exit;
}

if (isset($_GET['Cancel'])) {
	$schedule = new schedule();
	$settings = get_option('booker_settings');
	$updateData = array('Status' => 'Cancelled');
	$schedule->update($_GET['Cancel'], $updateData);
	$appoint = $schedule->getBy('ID', $_GET['Cancel'])[0];
	
	//Send Cancellation Email to User
	$userResult = bookerEmail($_GET['Cancel'], 'Cancel', $appoint['Email'], 'Your Appointment Has Been Cancelled');
	//Send Cancellation Email to Admin
	$adminResult = bookerEmail($_GET['Cancel'], 'AdminCancel', $settings['Email'], 'A Confirmed Appointment Was Canceled');
	
	wp_redirect(get_site_url().'?Action=Cancel');
	exit;
}

?>