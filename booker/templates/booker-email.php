<?php

$schedule = new schedule();
$previewAppoint = array('ID' => 0, 'Date' => getToday()['ISO'], 'Name' => 'Example Name', 'Time-Start' => '12:00', 'Time-End' => '12:00');
$appoint = (isset($appointID)) ? $schedule->getBy('ID', $appointID)[0] : $previewAppoint;
$today = getToday($appoint['Date']);

$URL = get_site_url(null, '/wp-content/plugins/booker/booker-confirm.php?', 'https');
$manageURL = get_site_url(null, '/wp-admin/admin.php?page=booker-manage', 'https');
$emailSettings = get_option('booker_settings');

$IMG = ($emailSettings['IMG'] != '') ? $emailSettings['IMG'] : get_site_url(null, '/wp-content/plugins/booker/assets/default-email-image.png', 'https');

$emailData = array();
switch ($emailType) {
	case 'Book':
		$emailData['Subject'] = $emailSettings['Company'].' - Please Confirm Your Appointment';
		$emailData['Message'] = '
			Thank you for booking an appointment with '.$emailSettings['Company'].'.
			Your appointment will be on 
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			The appointment is for a <span style="text-decoration:underline;">'.$appoint['Service'].'</span> with our barber, <span style="text-decoration:underline;">'.$appoint['Barber'].'</span>.
			You must confirm your appointment using the button below.<br><br>
			All unconfirmed appointments will be cancelled 24 hours before they are scheduled to start.
		';
		$emailData['URL'] = $URL.'Confirm='.$appoint['ID'];
		$emailData['Button'] = 'Confirm';
		break;
		
	case 'Confirm':
		$emailData['Subject'] = $emailSettings['Company'].' - Thank You For Confirming Your Appointment';
		$emailData['Message'] = '
			Thank you for confirming your appointment with '.$emailSettings['Company'].'.
			Your appointment will be on 
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			The appointment is for a <span style="text-decoration:underline;">'.$appoint['Service'].'</span> with our barber, <span style="text-decoration:underline;">'.$appoint['Barber'].'</span>.
			If you would like to cancel your appointment, click the button below.
		';
		$emailData['URL'] = $URL.'Cancel='.$appoint['ID'];
		$emailData['Button'] = 'Cancel';
		break;
		
	case 'Alert':
		$emailData['Subject'] = $emailSettings['Company'].'- Your Appointment Is Tomorrow';
		$emailData['Message'] = '
			You booked an appointment for
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			which is tomorrow. <br><br>
			The appointment is for a <span style="text-decoration:underline;">'.$appoint['Service'].'</span> with our barber, <span style="text-decoration:underline;">'.$appoint['Barber'].'</span>.
			If you would like to cancel your appointment, click the button below.
		';
		$emailData['URL'] = $URL.'Cancel='.$appoint['ID'];
		$emailData['Button'] = 'Cancel';
		break;
		
	case 'Remind':
		$emailData['Subject'] = $emailSettings['Company'].' - Your Appointment Is Still Unconfirmed';
		$emailData['Message'] = '
			You booked an appointment for
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			The appointment is for a <span style="text-decoration:underline;">'.$appoint['Service'].'</span> with our barber, <span style="text-decoration:underline;">'.$appoint['Barber'].'</span>.
			You have not confirmed your appointment, you MUST confirm your appointment using the button below.<br><br>
			<br>
			All unconfirmed appointments will be cancelled 24 hours before they are scheduled to start.
		';
		$emailData['URL'] = $URL.'Confirm='.$appoint['ID'];
		$emailData['Button'] = 'Confirm';
		break;
		
	case 'Cancel':
		$emailData['Subject'] = $emailSettings['Company'].' - Your Appointment Has Been Cancelled';
		$emailData['Message'] = '
			You booked an appointment for
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			This appointment was cancelled either by you, or by not confirming your appointment a day before the scheduled appointment start.<br><br>
			
			To rebook a different appointment, click the button below.
		';
		$emailData['URL'] = get_permalink($emailSettings['Widget']);
		$emailData['Button'] = 'Re-Book';
		break;
		
	case 'Admin':
		$emailData['Subject'] = $emailSettings['Company'].' - A New Appointment Has Been Booked';
		$emailData['Message'] = '
			An appointment was booked by '.$appoint['Name'].' for the date
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			The appointment is for a <span style="text-decoration:underline;">'.$appoint['Service'].'</span> with our barber, <span style="text-decoration:underline;">'.$appoint['Barber'].'</span>.
			<br><br>
			
			To see more information about this booking and to manage all your appointments, click the button below.
		';
		$emailData['URL'] = $manageURL.'&Date='.$appoint['Date'];
		$emailData['Button'] = 'Manage';
		break;
		
	case 'AdminCancel':
		$emailData['Subject'] = 'NV Barber - An Appointment Was Canceled';
		$emailData['Message'] = '
			The appointment booked by '.$appoint['Name'].' for the date
			<span style="text-decoration:underline;">'.$today['todayTitle'].' at '.formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']).'</span> at <span style="text-decoration:underline;">'.$appoint['Address'].'</span>.
			has been cancelled.<br><br>
			
			To see more information about this booking and to manage all your appointments, click the button below.
		';
		$emailData['URL'] = $manageURL.'&Date='.$appoint['Date'];
		$emailData['Button'] = 'Manage';
		break;
		
	default:
}

$output = '
<div class="Email-Template" style="display: block; width: 80%; margin: 0 auto;background: #FFF;">
	<a href="'.get_site_url().'"><IMG class="Email-Template-IMG" src="'.$IMG.'" style="display: block;width: 100%;margin: 0;" /></a>
	<h1 class="Email-Template-Title" style="display: block; width: 88%; margin: 3% auto 0; font-size: 2.25vw; line-height: 2.75vw;">'.$emailData['Subject'].'</h1>
	<div class="Email-Template-Text" style="display: block; width: 88%; margin: 5% auto; font-size: 1.75vw; line-height: 2.25vw;">'.$emailData['Message'].'</div>
	<a href="'.$emailData['URL'].'" class="Email-Template-Button" style="display: block; width: 18%; margin: 0 auto; padding: 0% 0; background: '.$emailSettings['Color'].'; color: #FFF; font-size: 1.8vw; text-decoration: none !important; border-radius: 10px; outline: none !important; text-align: center;">'.$emailData['Button'].'</a>
</div>

<style>
.Email-Template {width: 100% !important;}
.Email-Template-Button {width: 28% !important; margin: 1% auto 2% !important; padding: 2% 0 !important;}
</style>
';

return $output;
?>