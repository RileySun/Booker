<?php

$settings = get_option('booker_settings');
$URL = get_site_url(null, '', 'https');
$formURL =  get_site_url(null, '/wp-content/plugins/booker/booker-confirm.php', 'https');
$assetURL = get_site_url(null, '/wp-content/plugins/booker/assets', 'https');
$iconIMGURL = array(
	"Open" => $assetURL.'/Open.svg', 
	"Unconfirmed" => $assetURL.'/Unconfirmed.svg', 
	"Confirmed" => $assetURL.'/Confirmed.svg', 
	"Missing" => $assetURL.'/Missing.svg', 
	"Cancelled" => $assetURL.'/Cancelled.svg', 
);

$query = (isset($_GET['Date'])) ? $_GET['Date'] : "";
$today = getToday($query);
$schedule = new schedule();
$feed = $schedule->openFeed($today['ISO']);

$alert = '';
if (isset($_GET['Action'])) {
	switch ($_GET['Action']) {
		case 'Book':
			$title = 'Your appointment has been booked.';
			$text = 'The appointment you have selected has been scheduled, but you must still confirm your appointment. We have sent you an email with a link to confirm this appointment. Any appointment not confirmed 24 hours before the appointment time will be cancelled.';
			break;
		case 'Confirm':
			$title = 'Your appointment has been confirmed.';
			$text = 'Your appointment has been confirmed for the date and time you selected. We will send you a reminder email the night before your appointment.';
			break;
		case 'Cancel':
			$title = 'Your appointment has been cancelled.';
			$text = 'We have confirmed your appointment has been cancelled. Please feel free to book another appointment on this page.';
			break;
		default:
	}
	
	$alert = '
		<div class="Booker-Alert">
			<h1 class="Booker-Alert-Title">'.$title.'</h1>
			<div class="Booker-Alert-Text">'.$text.'</div>
			<div class="Booker-Alert-Close" onclick="closeAlert()">&#10799;</div>
		</div>
	';
}
echo $alert;

$html = '
<div class="Booker">
	<div class="Booker-Main">
		<div class="Booker-Side">
			<input type="Date" class="Booker-Date" value="'.$today['ISO'].'" onchange="dateChange(this);">
			<select class="Booker-Select" name="Service" form="Booker-Form" required>
				<option value="">Select A Service</option>
				<option value="Long Haircut">Long Haircut</option>
				<option value="Short Haircut">Short Haircut</option>
				<option value="Kid Haircut">Kid Haircut</option>
				<option value="Haircut & Beard Trim">Haircut & Beard Trim</option>
			</select>
';

//Barbers
if (!empty($settings['Barbers'])) {
	$html .= '<select class="Booker-Select" name="Barber" form="Booker-Form" required>
				<option value="">Select A Barber</option>';
	
	foreach($settings['Barbers'] as $barber) {
		$html .= '<option value="'.$barber.'">'.$barber.'</option>';
	}
	$html .= '</select>';
}
			
$html .= '			
			<div class="Booker-Schedule">
				<div class="Booker-Schedule-Container">
';


//Feed Schedule			
if (!empty($feed)) {
	foreach ($feed as $appoint) {
		$displayTime = formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']);
		$IMG = $iconIMGURL[$appoint['Status']];
		$html .= '
			<div class="Booker-Schedule-Item" data-id="'.$appoint['ID'].'" onclick="pickAppointment(this);">
				<img class="Booker-Schedule-Item-Icon" src="'.$iconIMGURL['Missing'].'" /><!--
			 --><div class="Booker-Schedule-Item-Info">
					<div class="Booker-Schedule-Item-Name">'.$appoint['Date'].'</div>
					<div class="Booker-Schedule-Item-Time">'.$displayTime.'</div>
				</div>
			</div>
		';
	}
}
else {
	$html .= '
		<div class="Booker-Schedule-Item" data-id="0">
			<img class="Booker-Schedule-Item-Icon" src="'.$iconIMGURL['Missing'].'" /><!--
		 --><div class="Booker-Schedule-Item-Info">
				<div class="Booker-Schedule-Item-Name">No Appointments</div>
				<div class="Booker-Schedule-Item-Time"> - </div>
			</div>
		</div>
	';
}

$html .= '
				</div>
			</div>
			<div class="Booker-Schedule-Notif">No Appointment Selected, Please Click One or Click A New Date</div>
			<div class="Booker-Schedule-Notif-IMG"></div>
		</div>
		<div class="Booker-Side">
			<form id="Booker-Form" class="Booker-Form" action="'.$formURL.'" method="post">
				<input type="text" name="Name" class="Booker-Input" placeholder="Name" value="" required />
				<input type="email" name="Email" class="Booker-Input" placeholder="Email" value="" required />
				<input type="tel" name="Phone" class="Booker-Input" placeholder="Phone" value="" required />
				<input type="text" name="Address" class="Booker-Input" placeholder="Address" value="" required />
				<input type="hidden" id="Book" name="Book" value="" />
				<input type="submit" class="Booker-Submit" value="Book" onclick="return validate();">
			</form>
		</div>
	</div>
</div>

<style>
.Booker, .Booker > *, .Booker-Input, .Booker input[type="text"], .Booker input[type="email"], .Booker input[type="tel"] {font-weight: 600;}

#site-content div, #site-content h1 {font-family: lato; }
.post-inner {padding: 0;}

.Booker {
	display: block;
	width: 95%;
	margin: 0 auto;
	padding: 0;
	background: #FFF;
	text-align: center;
}
.Booker-Title {
	display: block;
	width: 87%;
	margin: 1% auto 2%;
	padding: 2% 0 1%;
	font-size: 3vw;
	
	line-height: 4vw;
	border-bottom: 0.3vw solid  '.$settings['Color'].';
}
.Booker-Side {
	position: relative;
	display: inline-block;
	width: 49%;
	margin: 2% 0;
	vertical-align: middle;
}

.Booker-Select {
	display: block;
	width: 75%;
	margin: 4% auto;
	font-size: 1.75vw;
	font-weight: 600;
}

.Booker-Links {
	display: block;
	width: 100%;
	margin: 0 auto;
}
.Booker-SubTitle {
	display: block;
	margin: 1% auto;
	width: 90%;
	font-size: 3vw;
}
.Booker-Form {
	display: block;
	width: 90%;
	margin: 0 auto;
}
.Booker-Input, .Booker input[type="text"], .Booker input[type="email"], .Booker input[type="tel"] {
	display: block;
	width: 100%;
	margin: 7% auto;
	padding: 2% 0 2% 2%;
	text-align: left;
	font-size: 2vw;
	border: none;
	border-bottom: 0.1vw solid '.$settings['Color'].';
	box-shadow: none;
	cursor: pointer;
}
.Booker-Submit, .Booker input[type="submit"] {
	display: inline-block;
	width: 40%;
	margin: 4% 1%;
	padding: 1% 5%;
	background: transparent;
	color: #23282d;
	font-size: 2vw;
	text-decoration: none !important;
	border: 0.3vw solid '.$settings['Color'].';
	border-radius: 10px;
	outline: none !important;
	transition: all 1s;
	cursor: pointer;
}
.Booker-Submit:hover, .Booker input[type="submit"]:hover {
	background: '.$settings['Color'].';
	color: #FFF;
}
.Booker-Schedule-Notif {
	display: inline-block;
	margin: 1% 0 0;
	padding: 1% 2%;
	
	font-size: 1.6vw;
	border-bottom: 0.2vw solid transparent;
	transition: 1s all;
}
.Booker-Schedule-Notif-Error {
	color: #C00C00;
	border-color: #C00C00;
}
.Booker-Schedule-Notif-Success {
	color: #16b920;
	border-color: #16b920;
}
.Booker-Schedule-Notif-IMG {
	display: none;
}
.Booker-Schedule-Notif-IMG:after {
	content: "\27A4";
	position: absolute;
	bottom: 19%;
	left: -1%;
	color: #C00C00;
	font-size: 5vw;
	animation: blink 1s infinite;
}

.Booker-Schedule {
	display: inline-block;
	width: 73%;
	max-height: 14.4vw;
	border: 0.3vw solid;
	vertical-align: top;
	overflow-y: scroll;
}
.Booker-Date, .Booker input[type="date"] {
	display: block;
	margin: 1% auto;
	padding: 1%;
	font-family: lato;
	font-size: 2vw;
	border: none;
	border-bottom: 0.1vw solid '.$settings['Color'].';
	box-shadow: none;
}
.Booker-Schedule-Container {
	
}
.Booker-Schedule-Item {
	position: relative;
	cursor: pointer;
}
.Booker-Schedule-Item-Icon {
	display: inline-block;
	width: 15%;
	margin: 0;
	opacity: 0;
	transition: opacity 1s;
	vertical-align: middle;
}
.Booker-Schedule-Item:hover .Booker-Schedule-Item-Icon {
	opacity: 1;
}
.Booker-Schedule-Item-Info {
	display: inline-block;
	width: 74%;
	margin: 1% 0;
	text-align: right;
	font-size: 1.75vw;
	vertical-align: middle;
}
.Booker-Schedule-Item:nth-child(odd) {
	background: #E9E9E9;
}
.Booker-Schedule-Item-Name {
	margin: 1% 0;
	font-size: 2vw;
}

.Booker-Alert {
	position: fixed;
	display: block;
	width: 80%;
	margin: 0 auto;
	padding: 3% 0;
	top: 50%;
	left: 0;
	right: 0;
	background: #FFF;
	transform: translateY(-50%);
	border: 0.3vw solid '.$settings['Color'].';
	border-radius: 8px;
	z-index: 100;
}
.Booker-Alert-Title {
	display: block;
	width: 87%;
	margin: 1% auto 2%;
	padding: 2% 0 1%;
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid '.$settings['Color'].';
}
.Booker-Alert-Text {
	display: block;
	width: 70%;
	margin: 0 auto;
	text-align: left;
	font-size: 2.25vw;
	line-height: 2.75vw;
}
.Booker-Alert-Close {
	position: absolute;
	padding: 0.3% 0.75% 1.25% 0.75%;
	top: 2%;
	right: 1%;
	background: transparent;
	color: #16b920;
	font-size: 2.5vw;
	line-height: 50%;
	border: 0.2vw solid #16b920;
	border-radius: 100%;
	cursor: pointer;
	transition: all 1s;
	z-index: 65;
}
.Booker-Alert-Close:hover {
	background: '. $settings['Color'].';
	color: #FFF;
	border-color: : '.$settings['Color'].';
}

.mcb-section-inner {
	margin: 0 !important;
	padding: 0 !important;
	max-width: unset !important;
}

@keyframes blink {
	0% {opacity: 0;}
	50% {opacity: 1;}
	90% {opacity: 0;}
	100% {opacity: 0;}
}
</style>

<script>
function dateChange(obj) {
	let value = obj.value;
	let url = "'.$URL.$_SERVER['REQUEST_URI'].'".split("?")[0] + "?Date=" + value;
	window.location = url;
}
function pickAppointment(obj) {
	let date = obj.children[1].children[0].textContent;
	let time = obj.children[1].children[1].textContent;
	let notif = document.getElementsByClassName("Booker-Schedule-Notif")[0];
	notif.classList.remove("Booker-Schedule-Notif-Error");
	document.getElementsByClassName("Booker-Schedule-Notif-IMG")[0].style.display = "none";
	notif.classList.add("Booker-Schedule-Notif-Success");
	notif.textContent = "Appointment Selected: " + date + " @ " + time;
	document.getElementById("Book").value = obj.getAttribute("data-id");
	
	//Icon nonsense
	let icons = document.getElementsByClassName("Booker-Schedule-Item-Icon");
	for (let i = 0; i < icons.length; i++) {
		icons[i].style.opacity = (icons[i].parentElement == obj) ? 1 : "";
		icons[i].src = (icons[i].parentElement == obj) ? "'. $iconIMGURL['Confirmed'].'" : "'. $iconIMGURL['Missing'].'";
	}
}
function validate() {
	let isset = document.getElementById("Book").value != "";
	if (!isset) {
		document.getElementsByClassName("Booker-Schedule-Notif")[0].classList.add("Booker-Schedule-Notif-Error");
		document.getElementsByClassName("Booker-Schedule-Notif-IMG")[0].style.display = "block";
	}
	return isset;
}
function closeAlert() {
	document.getElementsByClassName("Booker-Alert")[0].style.display = "none";
}
document.getElementById("Book").value = "";
</script>

';

echo $html;

?>