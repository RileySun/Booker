<?php

$settings = get_option('booker_settings');
$URL =  get_site_url(null, '/wp-admin/admin.php?page=booker-manage', 'https');
$homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker', 'https');
$assetURL = get_site_url(null, '/wp-content/plugins/booker/assets', 'https');
$iconIMGURL = array(
	"Open" => $assetURL.'/Open.svg',
	"Unconfirmed" => $assetURL.'/Unconfirmed.svg',
	"Confirmed" => $assetURL.'/Confirmed.svg',
	"Missing" => $assetURL.'/Missing.svg',
	"Cancelled" => $assetURL.'/Cancelled.svg',
	"Home" => $assetURL.'/Home.svg',
);

$inputDate = (isset($_GET['date'])) ? $_GET['date'] : "";
$today = getToday($inputDate);
	
$schedule = new schedule();
$todayAppointments = $schedule->getBy('Date', $today['ISO']);


?>

<div class="Manage">
	<div class="Booker-Date-Bar">
		<div class="Booker-Month">
			<?php echo '
				<a href="'.$URL.'&date='.$today['prevMonth'].'" class="Booker-Date-Prev">&lt;</a>
				'.$today['monthName'].'
				<a href="'.$URL.'&date='.$today['nextMonth'].'" class="Booker-Date-Next">&gt;</a>
			'; ?>		
		</div>
		<div class="Booker-Day">
			<?php
				for ($i = 1; $i <= $today['daysInMonth']; $i++) {
					$class = ($today['day'] == $i) ? 'Booker-Day-Box Booker-Day-Box-Active' : 'Booker-Day-Box';
					$date = '2020-'.$today['month'].'-'.$i;
					echo '
						<a href="'. $URL.'&date='.$date.'" class="'.$class.'">'.$i.'</a>
					';
				}
			?>
		</div>
	</div>
	
	<div class="Booker-Main">
		<div class="Booker-Side-Book">
			<h1 class="Booker-Book-Title">
				Selected Date: 
				<span class="Booker-Book-Title-Selected"><?php echo $today['todayTitle']; ?></span>
			</h1>
			<form class="Booker-Book-Inputs" method="post">
				Add Time Slot: 
				<input name="Time-Start" class="Booker-Book-Input" type="time" value="" />
				<span class="Booker-Book-Title-Selected">-</span>
				<input name="Time-End" class="Booker-Book-Input" type="time" value="" /><br>
				<input type="hidden" name="date" value="<?php echo $today['ISO'];?>" />
				<input type="hidden" name="add" value="1">
				<input class="Booker-Submit" type="submit" value="Add" />
			</form>
		</div>
	
		<div class="Booker-Side-Schedule">
			<div class="Booker-Display-Container">
				<?php
					if (!empty($todayAppointments)) {
						foreach ($todayAppointments as $appoint) {
							$displayTime = formatTimeSlot($appoint['Time-Start'], $appoint['Time-End']);
							$IMG = $iconIMGURL[$appoint['Status']];
							echo '
								<div class="Booker-Display-Item" data-id="'.$appoint['ID'].'">
									<img class="Booker-Display-Item-Icon" src="'.$IMG.'" /><!--
								 --><div class="Booker-Display-Item-Info">
										<h1 class="Booker-Display-Item-Name">'.$appoint['Name'].'</h1>
										<h1 class="Booker-Display-Item-Time">'.$displayTime.'</h1>
									</div>
									
									<form method="post">
										<input type="hidden" name="delete" value="'.$appoint['ID'].'">
										<input type="submit" class="Booker-Display-Item-Remove" value="X" onclick="return confirm(\'Are you sure you wish to delte this appointment? Appointments that have been deleted can not be recovered.\')" />
									</form>
									
									<div class="Booker-Display-Item-Edit" onclick="updateForm(this);">&#9998;</div>
								</div>
							';
						}
					}
					else {
						echo '
							<div class="Booker-Display-Item" data-id="0">
								<img class="Booker-Display-Item-Icon" src="'.$iconIMGURL['Missing'].'" /><!--
							 --><div class="Booker-Display-Item-Info">
									<h1 class="Booker-Display-Item-Name">No Appointments</h1>
									<h1 class="Booker-Display-Item-Time"> - </h1>
								</div>
							</div>
						';
					}
				?>
			</div>
		</div>
		
		<form class="Booker-Info" method="post">
			<h1 class="Booker-Info-Title">Edit Appointment</h1>
			<div class="Booker-Info-Left">
				<div class="Booker-Info-Input-Box">
		 			<h1 class="Booker-Info-Label">Date:</h1>
					<input name="Book-Date" class="Booker-Info-Input" type="date" value="" required>
				</div>
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Status:</h1>
					<select name="Book-Status" onchange="changeStatus();">
						<option value="Open">Open</option>
						<option value="Unconfirmed">Unconfirmed</option>
						<option value="Confirmed">Confirmed</option>
						<option value="Cancelled">Cancelled</option>
					</select>
				</div>
				<IMG id="Info-Icon" class="Booker-Info-Icon" src="<?php echo $assetURL.'/Open.svg';?>" />
				<select class="Booker-Info-Select" form="Booker-Form" id="Select-Service" required onchange="changeSelect(this, 'Service');">
					<option value="">Service</option>
					<option value="Long Haircut">Long Haircut</option>
					<option value="Short Haircut">Short Haircut</option>
					<option value="Kid Haircut">Kid Haircut</option>
					<option value="Haircut & Beard Trim">Haircut & Beard Trim</option>
				</select>
				<?php
					if (!empty($settings['Barbers'])) {
						echo '<select class="Booker-Info-Select" form="Booker-Form" id="Select-Barber" required onchange="changeSelect(this, \'Barber\');">
									<option value="">Barber</option>';
	
						foreach($settings['Barbers'] as $barber) {
							echo '<option value="'.$barber.'">'.$barber.'</option>';
						}
						echo '</select>';
					}
				
				?>
			</div><!--
		 --><div class="Booker-Info-Right">
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Time Slot:</h1>
					<input name="Book-timeStart" class="Booker-Info-Input" type="time" value="" required />
					<span class="Booker-Book-Title-Selected">-</span>
					<input name="Book-timeEnd" class="Booker-Info-Input" type="time" value="" required /><br>
				</div>
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Name:</h1>
					<input name="Book-Name" class="Booker-Info-Input" type="text" value="" required />
				</div>
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Email:</h1>
					<input name="Book-Email" class="Booker-Info-Input" type="email" value="" required />
					<a href="#" class="Booker-Info-Link">Email The Client</a>
				</div>
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Phone:</h1>
					<input name="Book-Phone" class="Booker-Info-Input" type="tel" value="" />
					<a href="#" class="Booker-Info-Link">Call The Client</a>
				</div>
				<div class="Booker-Info-Input-Box">
					<h1 class="Booker-Info-Label">Address:</h1>
					<input name="Book-Address" class="Booker-Info-Input" type="text" value="" />
					<a href="#" target="_blank" class="Booker-Info-Link">Open Address on Google Maps</a>
				</div>
			</div>
			<input type="hidden" name="Book-ID" value="" />
			<input type="hidden" name="Book-Service" value="" />
			<input type="hidden" name="Book-Barber" value="" />
			<input type="hidden" name="update" value="1" />
			<input type="submit" class="Booker-Info-Submit" value="Update">
			<div class="Booker-Info-Close" onclick="this.parentElement.style.display = 'none';">X</div>
		</form>	
	</div>
	
	<a class="Booker-Home" href="<?php echo $homeURL;?>">
		<IMG src="<?php echo $iconIMGURL['Home']; ?>" />
	</a>
</div>

<style>
#wpbody-content {position: relative;}
#wpbody-content div, #wpbody-content h1 {font-family: lato; font-weight: 100;}

.Manage {
	position: absolute;
	width: 98%;
	height: 87vh;
	top: 20%;
	left: 0;
	background: #FFF;
}
.Booker-Date-Bar {
	text-align: center;
}
.Booker-Month {
	display: block;
	width: 50%;
	margin: 0 auto 2%;
	padding: 2% 0 1%;
	color: <?php echo $settings['Color'] ?>;
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid #A9A9A9;
}
.Booker-Date-Prev, .Booker-Date-Next {
	color: #A9A9A9;
	font-weight: 400;
	text-decoration: none !important;
}
.Booker-Date-Prev:hover, .Booker-Date-Next:hover {
	color:  <?php echo $settings['Color'] ?>;
}
.Booker-Day {
	font-size: 2vw;
	line-height: 3vw;
}

.Booker-Day {
	display: block;
	width: 71%;
	margin: 0 auto;
}
.Booker-Day-Box {
	display: inline-block;
	width: 3vw;
	margin: 0.2% 0;
	padding: 0.25% 0;
	color: #A9A9A9;
	text-decoration: none !important;
	border: 0.2vw solid #A9A9A9;
	border-radius: 13px;
	vertical-align: middle;
}
.Booker-Day-Box:hover, .Booker-Day-Box-Active {
	color: <?php echo $settings['Color'] ?>;
	border-color: <?php echo $settings['Color'] ?>;
}

.Booker-Main {
	padding: 3% 0 0;
}

.Booker-Side-Book {
	display: inline-block;
	width: 60%;
	vertical-align: top;
	text-align: right;
}
.Booker-Book-Title {
	display: block;
	width: 88%;
	margin: 0 auto 4%;
	font-size: 2vw;
	text-align: right;
}
.Booker-Book-Title-Selected {
	font-weight: 400;
}
.Booker-Book-Inputs {
	display: block;
	width: 92%;
	margin: 0 auto 4%;
	font-size: 2vw;
	text-align: right;
}
.Booker-Book-Input, input[type="text"], input[type="email"], input[type="tel"], input[type="date"] {
	margin: 1% 2%;
	padding: 0 1% 0 5%;
	font-size: 2vw;
}
.Booker-Submit {
	display: inline-block;
	margin: 4%;
	padding: 0.5% 5%;
	background: transparent;
	font-size: 2vw;
	border: 0.3vw solid <?php echo $settings['Color'] ?>;
	border-radius: 10px;
	outline: none !important;
	transition: background 1s;
	cursor: pointer;
}
.Booker-Submit:hover {
	background:  <?php echo $settings['Color'] ?>;
}

.Booker-Side-Schedule {
	display: inline-block;
	width: 36%;
	max-height: 20.8vw;
	border: 0.3vw solid;
	vertical-align: top;
	overflow-y: scroll;
}
.Booker-Display-Container {
	
}
.Booker-Display-Item {
	position: relative;
}
.Booker-Display-Item-Icon {
	display: inline-block;
	width: 15%;
	margin: 0 0 0 6%;
	vertical-align: middle;
}
.Booker-Display-Item-Info {
	display: inline-block;
	width: 74%;
	text-align: right;
	vertical-align: middle;
}
.Booker-Display-Item:nth-child(odd) {
	background: #E9E9E9;
}
.Booker-Display-Item-Text {
	font-size: 2vw;
}
.Booker-Display-Item-Remove {
	position: absolute;
	top: 0%;
	left: -1%;
	background: none !important;
	color: #737373;
	font-weight: 100;
	font-size: 1.75vw;
	text-decoration: none !important;
	border: none !important;
	cursor: pointer;
}
.Booker-Display-Item-Edit {
	position: absolute;
	bottom: 6%;
	left: 1%;
	color: #737373;
	font-weight: 100;
	font-size: 1.75vw;
	text-decoration: none !important;
	cursor: pointer;
}
.Booker-Display-Item-Remove:hover, .Booker-Display-Item-Edit:hover {
	color: <?php echo $settings['Color'] ?>;
}

.Booker-Info {
	position: absolute;
	display: none;
	width: 70%;
	margin: 0 auto;
	padding: 3%;
	top: 4%;
	left: 0;
	right: 0;
	background: #E9E9E9;
	border-radius: 13px;
	border: 0.3vw solid;
}
.Booker-Info-Title {
	margin: 0 0 1%;
	text-align: center;
	font-size: 3vw;
	font-weight: 200 !important;
}
.Booker-Info-Left {
	display: inline-block;
	width: 35%;
	padding: 1% 0 0;
	text-align: right;
	vertical-align: top;
}
.Booker-Info-Right {
	display: inline-block;
	width: 61%;
	text-align: right;
	vertical-align: top;
}
.Booker-Info-Label {
	display: inline-block;
	margin: 0;
}
.Booker-Info-Label-Middle {
	vertical-align: middle;
}
.Booker-Info-Input-Box {
	position: relative;
	margin: 4% 0;
}
.Booker-Info-Link {
	position: absolute;
	display: none;
	width: 60%;
	margin: 0 auto;
	right: 6%;
	text-align: center;
	font-size: 1.5vw;
	text-decoration: none !important;
}
.wp-core-ui select {
	margin: 6% 2%;
	padding: 0 6% 0 4%;
	font-size: 2vw;
}
.Booker-Info-Select {
	margin: 4% 0 !important;
}
.Booker-Info-Icon {
	display: inline-block;
	width: 21%;
	margin: 0;
	background: #FFF;
	border-radius: 7px;
	vertical-align: middle;
}
.Booker-Info-Submit {
	position: absolute;
	display: block;
	margin: 0;
	padding: 0.5% 2%;
	bottom: 7%;
	left: 3%;
	background: transparent;
	font-size: 2vw;
	border: 0.3vw solid <?php echo $settings['Color'] ?>;
	border-radius: 13px;
	cursor: pointer;
	transition: background 1s;
}
.Booker-Info-Submit:hover {
	background: <?php echo $settings['Color'] ?>;
}
.Booker-Info-Close {
	position: absolute;
	top: 7%;
	right: 3%;
	font-size: 2vw;
	font-weight: 300 !important;
	cursor: pointer;
}
.Booker-Info-Close:hover {
	color: #C00C00;
}
.Booker-Info input[type="time"] {
	margin: 1% 2%;
	padding: 0;
	font-size: 2vw;
}

.Booker-Home {position: absolute; width: 4%; top: 1.5%; right: 0.5%; z-index: 60;}
.Booker-Home img {display: block; width: 100%;}

@media only screen and (max-width: 767px) {
	.Manage {height: auto !important; padding: 0 0 6% !important;}
	.Booker-Home {width: 13% !important; top: 0 !important;}
	.Booker-Month {width: 85% !important; margin: 6% auto 7% !important; padding: 2% 0 2% !important; font-size: 10vw !important; line-height: 11vw !important; border-width: 0.6vw !important;}
	.Booker-Day {width: 92% !important;}
	.Booker-Day-Box {width: 9vw !important; margin: 1% !important; padding: 3% 0 !important; font-size: 6vw !important; border-width: 0.6vw !important;}
	.Booker-Side-Book {display: block !important; width: 92% !important; margin: 0 auto !important;}
	.Booker-Book-Title {font-size: 6vw !important; line-height: 7vw !important; text-align: center !important;}
	.Booker-Book-Inputs {font-size: 6vw;}
	.Booker-Book-Input {font-size: 7vw !important; padding: 0 4% !important; width: 50% !important;}
	.Booker-Submit {padding: 1% 10% !important; font-size: 6vw !important; border-width: 0.6vw !important;}
	.Booker-Side-Schedule {display: block !important; width: 90% !important; margin: 0 auto !important; border: 0.3vw solid; border-width: 0.6vw !important;}
	.Booker-Display-Item-Remove {font-size: 7vw !important;}
	.Booker-Display-Item-Edit {bottom: 12% !important; font-size: 7vw !important;}
	.Booker-Info {position: absolute !important; width: 98% !important; margin: 0 auto !important; padding: 3% 0 !important; top: 0 !important; border-width: 0.6vw !important; z-index: 200 !important;}
	.Booker-Info-Close {top: 3% !important; font-size: 10vw !important;}
	.Booker-Info-Title {margin: 3% 0 !important; font-size: 7vw !important;}
	.Booker-Info-Left, .Booker-Info-Right {display: block; width: 90% !important; margin: 0 auto !important; text-align: right !important;}
	.Booker-Info-Input-Box {margin: 8% 0 !important;}
	.Booker-Book-Input, input[type="text"], input[type="email"], input[type="tel"], input[type="date"], .wp-core-ui select, .Booker-Info input[type="time"] {font-size: 6vw !important;}
	.Booker-Info-Icon {position: absolute !important; top: 22.5% !important; left: 3% !important; width: 14% !important;}
	.Booker-Book-Input, input[type="text"], input[type="email"], input[type="tel"] {max-width: 67% !important;}
	.Booker-Info input[type="time"] {margin: 0 !important; width: 32% !important;}
	.Booker-Info-Submit {position: relative !important; width: 32% !important; margin: 14% auto 2% !important; padding: 2% 0 !important; font-size: 5vw !important; border-width: 0.5vw !important; background: #FFF !important;}
	.Booker-Info-Link {font-size: 6vw !important; line-height: 6vw !important;}
}
</style>

<script>
const schedule = <?php echo json_encode($todayAppointments);?>;
function updateForm(obj) {
	const box = obj.parentElement;
	const ID = box.getAttribute("data-id");
	const IMG = <?php echo json_encode($iconIMGURL);?>;
	for (let i = 0; i < schedule.length; i++) {
		if (ID == schedule[i]['ID']) {
			console.log(schedule[i]);
			switch (schedule[i]['Status']) {
				case "Open":
					document.getElementsByName("Book-Status")[0].selectedIndex = 0;
					break;
				case "Unconfirmed":
					document.getElementsByName("Book-Status")[0].selectedIndex = 1;
					break;
				case "Confirmed":
					document.getElementsByName("Book-Status")[0].selectedIndex = 2;
					break;
				case "Cancelled":
					document.getElementsByName("Book-Status")[0].selectedIndex = 3;
					break;
				default:
			}
			document.getElementsByName("Book-ID")[0].value = schedule[i]['ID'];
			document.getElementsByName("Book-Date")[0].value = schedule[i]['Date'];
			document.getElementsByName("Book-timeStart")[0].value = schedule[i]['Time-Start'];
			document.getElementsByName("Book-timeEnd")[0].value = schedule[i]['Time-End'];
			document.getElementsByName("Book-Name")[0].value = schedule[i]['Name'];
			
			
			
			let service = document.getElementById("Select-Service");
			for (let j = 0; j < service.children.length; j++) {
				let value = service.children[j].value;
				console.log('Service', value, schedule[i]['Service'])
				if (value == schedule[i]['Service']) {
					service.children[j].selected = true;
				}
			}
			let barber = document.getElementById("Select-Barber");
			for (let j = 0; j < barber.children.length; j++) {
				let value = barber.children[j].value;
				console.log('Barber', value, schedule[i]['Service'])
				if (value == schedule[i]['Barber']) {
					barber.children[j].selected = true; 
				}
			}
			
			//Links show or not.
			
			if (schedule[i]['Email'] != '') {
				let email = document.getElementsByName("Book-Email")[0];
				let link = email.nextElementSibling;
				
				email.value = schedule[i]['Email'];
				link.href = 'mailto:' + schedule[i]['Email'];
				link.style.display = 'block';
			}
			if (schedule[i]['Phone'] != '') {
				let phone = document.getElementsByName("Book-Phone")[0];
				let link = phone.nextElementSibling;
				
				phone.value = schedule[i]['Phone'];
				link.href = 'tel:' + schedule[i]['Phone'];
				link.style.display = 'block';
			}
			if (schedule[i]['Address'] != '') {
				let address = document.getElementsByName("Book-Address")[0];
				let link = address.nextElementSibling;
				
				address.value = schedule[i]['Address'];
				link.href = 'https://www.google.com/maps/search/' + schedule[i]['Address'] + '/';
				link.style.display = 'block';
			}
			
			document.getElementById("Info-Icon").src = IMG[schedule[i]['Status']];
			document.getElementsByClassName("Booker-Info")[0].style.display = 'block';
		}
	}
}
function changeSelect(obj, type) {
	if (type == 'Service') {
		document.getElementsByName("Book-Service")[0].value = obj.children[obj.selectedIndex].value;
	}
	else {
		document.getElementsByName("Book-Barber")[0].value = obj.children[obj.selectedIndex].value;
	}
}
function changeStatus() {
	const IMG = <?php echo json_encode($iconIMGURL);?>;
	const status = document.getElementsByName("Book-Status")[0].value;
	document.getElementById("Info-Icon").src = IMG[status];
}
</script>