<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
include_once dirname(__FILE__).'/booker-utils.php';

//bookerEmail('5f5140ea8a585', 'Remind', 'riley@redpixiemedia.com', 'Please Confirm Your Appointment');

$settings = get_option('booker_settings');
$schedule = new schedule();
$appointments = $schedule->getBy();

$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format('Y-m-d');

foreach ($appointments as $appointment) {
	$reminderDateTime = new DateTime($appointment['Date']);
	$alertCancelDateTime = new DateTime($appointment['Date']);
	
	$reminderDateTime->modify('-3 day');
	$alertCancelDateTime->modify('-1 day');
	
	$reminderDate = $reminderDateTime->format('Y-m-d');
	$alertCancelDate = $alertCancelDateTime->format('Y-m-d');
	
	if ($alertCancelDate == $currentDate) {
		if ($appointment['Status'] == 'Unconfirmed') {
			$user = bookerEmail($appointment['ID'], 'Cancel', $appointment['Email'], 'Your Appointment Has Been Cancelled');
			$admin = bookerEmail($appointment['ID'], 'AdminCancel', $settings['Email'], 'A Confirmed Appointment Was Canceled');
		}
		else if ($appointment['Status'] == 'Confirmed') {
			bookerEmail($appointment['ID'], 'Alert', $appointment['Email'], 'Your Appointment Is Tomorrow');
		}
	}
	else if ($reminderDate == $currentDate) {
		echo json_encode(array($appointment['ID'], 'Remind', $appointment['Email'], 'Please Confirm Your Appointment'));
		if ($appointment['Status'] === 'Unconfirmed') {
			bookerEmail($appointment['ID'], 'Remind', $appointment['Email'], 'Please Confirm Your Appointment');
			echo json_encode($appointment['ID'], 'Remind', $appointment['Email'], 'Please Confirm Your Appointment');
		}
		else {
			echo ($appointment['Status'] === 'Unconfirmed').' '.$appointment['Status'].'/Unconfirmed';
		}
	}	
}

echo ' '.json_encode('END');

?>