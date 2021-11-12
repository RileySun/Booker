<?php

$settings = get_option('booker_settings');
$today = getToday();
$URL =  get_site_url(null, '/wp-admin/admin.php?page=', 'https');
$assetURL = get_site_url(null, '/wp-content/plugins/booker/assets', 'https');
$bookingURL = get_site_url(null, '?Date='.$today['ISO'], 'https');
$iconIMGURL = array(
	"Open" => $assetURL.'/Open.svg', 
	"Unconfirmed" => $assetURL.'/Unconfirmed.svg', 
	"Confirmed" => $assetURL.'/Confirmed.svg', 
	"Missing" => $assetURL.'/Missing.svg', 
);

$schedule = new schedule();
$feed = $schedule->feed();

?>

<div class="Home">
	<h1 class="Home-Title">Booker - Appointment Booking</h1>
	
	<div class="Home-Main">
		<div class="Home-Side">
			<h1 class="Home-SubTitle">Quick Links</h1>
			<div class="Home-Links">
				<a href="<?php echo $URL.'booker-manage';?>" class="Home-Button">Manage</a>
				<a href="<?php echo $URL.'booker-barbers';?>" class="Home-Button">Barbers</a>
				<a href="<?php echo $URL.'booker-history';?>" class="Home-Button">History</a>
				<a href="<?php echo $URL.'booker-settings';?>" class="Home-Button">Settings</a>
				<a href="<?php echo $URL.'booker-backup';?>" class="Home-Button">Backups</a>
				<a href="<?php echo $bookingURL; ?>" class="Home-Button">Booking</a>
			</div>
		</div><!--
	 --><div class="Home-Side">
			<h1 class="Home-SubTitle">Upcoming Appointments</h1>
			
			<div class="Home-Schedule">
				<div class="Home-Schedule-Container">
					<?php
						if (!empty($feed)) {
							foreach ($feed as $appoint) {
								$displayTime = formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']);
								$IMG = $iconIMGURL[$appoint['Status']];
								echo '
									<div class="Home-Schedule-Item" data-id="'.$appoint['ID'].'">
										<img class="Home-Schedule-Item-Icon" src="'.$IMG.'" /><!--
									 --><div class="Home-Schedule-Item-Info">
											<h1 class="Home-Schedule-Item-Name">'.$appoint['Date'].' - '.$appoint['Name'].'</h1>
											<h1 class="Home-Schedule-Item-Time">'.$displayTime.'</h1>
										</div>
									</div>
								';
							}
						}
						else {
							echo '
								<div class="Home-Schedule-Item" data-id="0">
									<img class="Home-Schedule-Item-Icon" src="'.$iconIMGURL['Missing'].'" /><!--
								 --><div class="Home-Schedule-Item-Info">
										<h1 class="Home-Schedule-Item-Name">No Appointments</h1>
										<h1 class="Home-Schedule-Item-Time"> - </h1>
									</div>
								</div>
							';
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
#wpbody-content {position: relative;}
#wpbody-content div, #wpbody-content h1 {font-family: lato; font-weight: 100;}

.Home {
	position: absolute;
	width: 98%;
	height: 82vh;
	padding: 0 0 3%;
	top: 20%;
	left: 0;
	background: #FFF;
	text-align: center;
}
.Home-Title {
	display: block;
	width: 67%;
	margin: 1% auto 2%;
	padding: 0 0 1%;
	
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid;
}
.Home-Side {
	display: inline-block;
	width: 50%;
	vertical-align: top;
}

.Home-Links {
	display: block;
	width: 100%;
	margin: 0 auto;
}
.Home-Button {
	display: inline-block;
	width: 17%;
	margin: 2% 1%;
	padding: 3% 5%;
	background: transparent;
	color: #23282d;
	font-size: 2vw;
	text-decoration: none !important;
	border: 0.3vw solid <?php echo $settings['Color'] ?>;
	border-radius: 10px;
	outline: none !important;
	transition: all 1s;
	cursor: pointer;
}
.Home-Button:hover {
	color: #FFF;
	background: <?php echo $settings['Color'] ?>;
}

.Home-Schedule {
	display: inline-block;
	width: 73%;
	max-height: 30.1vw;
	border: 0.3vw solid;
	vertical-align: top;
	overflow-y: scroll;
}
.Home-Schedule-Container {
	
}
.Home-Schedule-Item {
	position: relative;
}
.Home-Schedule-Item-Icon {
	display: inline-block;
	width: 15%;
	margin: 0;
	vertical-align: middle;
}
.Home-Schedule-Item-Info {
	display: inline-block;
	width: 74%;
	text-align: right;
	vertical-align: middle;
}
.Home-Schedule-Item-Name {
	width: 100%;
	margin: 4% 0;
	padding: 1% 0;
	line-height: 2vw;
	white-space: pre;
	overflow-y: hidden;
	overflow-x: scroll;
}
.Home-Schedule-Item:nth-child(odd) {
	background: #E9E9E9;
}
.Home-Schedule-Item-Text {
	font-size: 2vw;
}

@media only screen and (max-width: 767px) {
	.Home-Title {font-size: 7vw !important; line-height: 8vw !important; border-width: 0.6vw !important;}
	.Home-Side {display: block !important; width: 90% !important; margin: 0 auto !important;}
	.Home-Button {width: 32% !important; padding: 4% 5% !important; font-size: 6vw !important; border-width: 0.6vw !important;}
	.Home-Schedule {width: 100% !important;}
	.Home-Schedule-Item-Name {padding: 5% 0 !important;}
}
</style>

<script>
function checkWidget() {
	let URL = "<?php echo $widgetURL;?>";
	if (URL != '') {
		window.location = URL;
	}
	else {
		alert('No booking page set, redirecting you to the settings page. Please select a page for the Booker frontend widget.')
		window.location = "<?php echo $URL.'booker-settings';?>";
	}
}
</script>