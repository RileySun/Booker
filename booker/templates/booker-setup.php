<?php

$settings = get_option('booker_settings');
$IMG = get_site_url(null, '/wp-content/plugins/booker/assets/Confirmed.svg', 'https');

?>

<div class="Setup">
	<h1 class="Setup-Title">Booker - Setup</h1>
	
	<div class="Setup-Main">
		<div class="Setup-Intro">Welcome to the Booker appointment booking system. We need some information from you to get started here. You can change these anytime on the settings page or by coming back here.</div>
		<form method="post">
			<div class="Setup-Form">
				<span class="Setup-Label">Administrator Email: </span>
				<input type="email" name="Email" id="Email" class="Setup-Input" value="<?php echo (isset($settings['Email'])) ? $settings['Email'] : ''; ?>" required />
				<IMG class="Setup-Complete-Icon" src="<?php echo $IMG; ?>" /><br>
			
				<span class="Setup-Label">Company Name: </span>
				<input type="text" name="Company" id="Company" class="Setup-Input" value="<?php echo (isset($settings['Company'])) ? $settings['Company'] : ''; ?>" required />
				<IMG class="Setup-Complete-Icon" src="<?php echo $IMG; ?>" /><br>
			
				<span class="Setup-Label">Main Color: </span> 
				<input type="color" name="Color" id="Color" class="Setup-Input" value="<?php echo (isset($settings['Color'])) ? $settings['Color'] : ''; ?>" required />
				<IMG class="Setup-Complete-Icon" src="<?php echo $IMG; ?>" /><br>
			</div>
			
			<input class="Setup-Submit" type="submit" value="Save" />
			<input type="hidden" name="Setup" value="1" />
		</form>
	</div>
</div>

<style>
#wpbody-content {position: relative;}
#wpbody-content div, #wpbody-content h1 {font-family: lato; font-weight: 100;}

.Setup {
	position: absolute;
	width: 98%;
	height: 82vh;
	padding: 0 0 3%;
	top: 20%;
	left: 0;
	background: #FFF;
	text-align: center;
}
.Setup-Title {
	display: block;
	width: 67%;
	margin: 1% auto 2%;
	padding: 0 0 1%;
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid;
}
.Setup-Intro {
	display: block;
	width: 76%;
	margin: 2% auto;
	font-size: 2vw;
	line-height: 2.5vw;
}
.Setup-Form {
	display: block;
	width: 51%;
	margin: 0 auto;
	text-align: right;
}
.Setup-Label {
	font-size: 1.75vw;
}
/*Color Label & Input Fix*/
.Setup-Label:nth-child(9) {
	vertical-align: sub;
}
input[type="color"] {
	width: 48%;
}
.Setup-Input {
	min-height: 3vw;
	margin: 2% 0;
	padding: 0;
	font-size: 1.75vw;
	vertical-align: middle;
}
.Setup-Complete-Icon {
	display: inline-block;
	width: 11%;
	margin: 0 0 0 1%;
	opacity: 0;
	vertical-align: middle;
	transition: opacity 1s;
}
.Setup-Submit {
	display: block;
	width: 16%;
	margin: 2% auto 0;
	padding: 0.5% 0;
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
.Setup-Submit:hover {
	color: #FFF;
	background: <?php echo $settings['Color'] ?>;

</style>