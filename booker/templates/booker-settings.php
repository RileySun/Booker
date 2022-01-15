<?php

wp_enqueue_script('jquery');
wp_enqueue_media();

$settings = get_option('booker_settings');
$URL = get_site_url(null, '/wp-admin/admin.php?page=booker-settings', 'https');
$homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker', 'https');
$icons =  get_site_url(null, '/wp-content/plugins/booker/assets/', 'https');

$emailType = (isset($_POST['Preview'])) ? $_POST['Preview'] : 'Book';
$emailTypes = array('Book', 'Confirm', 'Alert', 'Remind', 'Cancel', 'Admin', 'AdminCancel');
$posts = get_pages();


?>

<div class="Email">
	<h1 class="Email-Title">Booker - Settings</h1>
	
	<div class="Email-Main">
		<div class="Email-Side">		
			<form class="Email-Form" method="post">
				<h1 class="Email-SubTitle">Settings</h1>
				Email Image: <input id="Upload" class="Email-Button" type="button" value="Change Image" />
				<IMG class="Email-IMG" id="IMG-Display" src="<?php echo $settings['IMG'];?>" /><br>
				Company: <input type="text" name="Company" id="Company" class="Email-Input" placeholder="Company Name" value="<?php echo $settings['Company'];?>" /><br>
				Booker Theme Color: <input type="color" name="Color" class="Email-Color" value="<?php echo $settings['Color'];?>" /><br>
				Booker Appointment Page: <select name="Widget" class="Email-Select">
					<?php
						foreach ($posts as $single) {
							$selected = ($single->ID == $settings['Widget']) ? 'selected' : '';
							echo '<option value="'.$single->ID.'" '.$selected.'>'.$single->post_title.'</option>';
						}
					?> 
				</select><br>
				<input type="submit" class="Email-Button" value="Save Settings" />
				<input type="hidden" name="IMG" id="IMG" value="" /><br>
				<input type="hidden" name="Save" value="1" /><br>
			</form>
		</div><!--
	 --><div class="Email-Side">
			<h1 class="Email-SubTitle">Email Preview</h1>
			<form class="Email-Preview-Form" method="post">
				<select class="Email-Preview-Select" onchange="previewChange(this);">
					<?php
						foreach ($emailTypes as $type) {
							$selected = ($emailType == $type) ? 'selected' : '';
							echo '<option value="'.$type.'" '.$selected.'>'.$type.'</option>';
						}
					?>
				</select>
				<input type="hidden" name="Preview" id="Preview" value="" />
			</form>
			<div class="Email-Preview">
				<?php echo include 'booker-email.php'?>
			</div>
		</div>
	</div>
	
	<a class="Booker-Home" href="<?php echo $homeURL;?>">
		<IMG src="<?php echo $icons.'Home.svg'; ?>" />
	</a>
</div>

<style>
#wpbody-content {position: relative;}
#wpbody-content div, #wpbody-content h1, #wpbody-content a {font-family: lato; font-weight: 100;}

.Email {
	position: absolute;
	width: 98%;
	height: 82vh;
	padding: 0 0 3%;
	top: 20%;
	left: 0;
	background: #FFF;
	text-align: center;
}
.Email-Title {
	display: block;
	width: 67%;
	margin: 1% auto 2%;
	padding: 0 0 1%;
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid;
}
.Email-Side {
	display: inline-block;
	width: 50%;
	vertical-align: top;
}
.Email-Form {
	display: block;
	width: 100%;
	margin: 0 auto;
	font-size: 1.8vw;
}
.Email-SubTitle {
	margin: 3% 0 5%;
	font-size: 3vw !important;
}

#IMG-Display {
	display: inline-block;
	width: 10%;
	vertical-align: middle;
	margin: 0 2% 0 0;
}
.Email-Input, input[type="text"] {
	display: inline-block;
	width: 46.5%;
	margin: 3% 1%;
	color: #23282d;
	font-size: 1.8vw;
}
.Email-Color, input[type="color"] {
	display: inline-block;
	width: 8%;
	margin: 3% 1%;
	padding: 0;
	color: #23282d;
	font-size: 1.8vw;
	vertical-align: middle;
	height: 3vw;
}
.Email-Select, .Email-Form select {
	margin: 4% 0;
	font-size: 2vw;
}
.Email-Button {
	display: inline-block;
	width: 36%;
	margin: 1% 1%;
	padding: 1% 0;
	background: transparent;
	color: #23282d;
	font-size: 1.8vw;
	text-decoration: none !important;
	border: 0.3vw solid <?php echo $settings['Color'];?>;
	border-radius: 10px;
	outline: none !important;
	transition: all 1s;
	cursor: pointer;
}
.Email-Button:hover {
	background: <?php echo $settings['Color'];?>;
	color: #FFF;
}
.Email-Preview-Select, .Email-Preview-Form select {
	margin: 1% 0;
	font-size: 2vw;
}
.Email-Preview {
	display: block;
	width: 99%;
	max-height: 25vw;
	margin: 0 auto;
	border: 0.3vw solid;
	overflow: scroll;
}

.Booker-Home {position: absolute; width: 4%; top: 1.5%; right: 0.5%; z-index: 60;}
.Booker-Home img {display: block; width: 100%;}
</style>

<script>
function setUp() {
	document.getElementById("Upload").addEventListener("click", onClickMedia, false);
}
function onClickMedia(event) {
	event.preventDefault();
	var image = wp.media({title: 'Upload Image', multiple: false}).open().on('select', function() {onSelectMedia(image);});
	//image.on('select', onSelectMedia);
}
function onSelectMedia(obj) {
	const uploaded_image = obj.state().get('selection').first();
	const URL = "https:" + uploaded_image.toJSON().url.split(":")[1];
	document.getElementById("IMG").value = URL;
	document.getElementById("IMG-Display").src = URL;
	document.getElementsByClassName("Email-Template-IMG")[0].src = URL;
}
function previewChange(obj) {
	let value = obj.children[obj.selectedIndex].value;
	document.getElementById("Preview").value = value;
	obj.parentElement.submit();
}
window.addEventListener("load", setUp, false);
</script>