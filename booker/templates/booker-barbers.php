<?php
//return confirm('This will replace all your appointment data with the data from the selected Barber. The old data will be deleted, make sure to take a Barber!')
$settings = get_option('booker_settings');
$homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker', 'https');
$homeIcon =  get_site_url(null, '/wp-content/plugins/booker/assets/Home.svg', 'https');
$downloadURL =  get_site_url(null, '/wp-content/plugins/booker/Barbers/', 'https');

$barbers = $settings['Barbers'];
?>

<div class="Barber">
	<h1 class="Barber-Title">Booker - Barbers</h1>
	
	<div class="Barber-Main">
		<div class="Barber-Side">
			<h1 class="Barber-SubTitle">Actions</h1>
			<div class="Barber-Actions">
				<form class="Barber-Actions-Form" method="post">
					<input type="text" name="barber" class="Barber-Actions-Input" placeholder="Barber Name" required />
					<input type="hidden" name="create" value="1" />
					<input type="submit" class="Barber-Actions-Submit" value="Create" />
				</form>
				<form class="Barber-Actions-Form" method="post">	
					<input type="hidden" name="delete" class="hidden" value=""/>
					<input type="submit" class="Barber-Actions-Submit" value="Delete"/>
				</form>
			</div>
		</div><!--
	 --><div class="Barber-Side">
			<h1 class="Barber-SubTitle">Barbers</h1>
			
			<select id="Select" class="Barber-Select" size="6">
				<?php
					if (!empty($barbers)) {
						foreach ($barbers as $barber) {
							echo '<option class="Barber-Option" value="'.$barber.'">'.$barber.'</option>';
						}
					}
					else {
						echo '<option class="Barber-Option" value="">No Barbers</option>';
					}
				?>
			</select> 
		</div>
	</div>
	
	<a class="Booker-Home" href="<?php echo $homeURL;?>">
		<IMG src="<?php echo $homeIcon; ?>" />
	</a>
</div>

<style>
#wpbody-content {position: relative;}
#wpbody-content div, #wpbody-content h1, #wpbody-content a {font-family: lato; font-weight: 100;}

.Barber {
	position: absolute;
	width: 98%;
	height: 82vh;
	padding: 0 0 3%;
	top: 20%;
	left: 0;
	background: #FFF;
	text-align: center;
}
.Barber-Title {
	display: block;
	width: 67%;
	margin: 1% auto 2%;
	padding: 0 0 1%;
	
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid;
}
.Barber-Side {
	display: inline-block;
	width: 50%;
	vertical-align: top;
}

.Barber-Actions {
	display: block;
	width: 100%;
	margin: 0 auto;
}
.Barber-Actions-Form {
	display: inline-block;
	width: 29%;
	margin: 2%;
}
.Barber-Actions-Input {
	width: 215%;
	margin: 0 auto 6%;
	font-size: 2vw;
}
.Barber-Actions-Link {
	display: inline-block;
	width: 29%;
	margin: 2%;
	padding: 2.5% 0;
	background: transparent;
	color: #23282d;
	font-size: 2vw;
	text-decoration: none !important;
	border: 0.3vw solid <?php echo $settings['Color'];?>;
	border-radius: 10px;
	outline: none !important;
	transition: all 1s;
	cursor: pointer;
}
.Barber-Actions-Submit {
	display: block;
	width: 100%;
	margin: 2% 1%;
	padding: 3% 0;
	background: transparent;
	color: #23282d;
	font-size: 2vw;
	text-decoration: none !important;
	border: 0.3vw solid <?php echo $settings['Color'];?>;
	border-radius: 10px;
	outline: none !important;
	transition: all 1s;
	cursor: pointer;
}
.Barber-Actions-Link:hover, .Barber-Actions-Submit:hover {
	background: <?php echo $settings['Color'];?>;
	color: #FFF;
}

.Barber-Select, .wp-core-ui select {
	display: inline-block;
	width: 73%;
	max-height: 27.7vw;
	padding: 0;
	border: 0.3vw solid ;
	vertical-align: top;
	overflow-y: scroll;
}
.Barber-Option {
	position: relative;
	padding: 2% 0 3% 2%;
	font-size: 2vw;
	-moz-user-select: none; 
}
.Barber-Option:nth-child(odd) {
	background: #E9E9E9;
}
option[selected] {
	background: <?php echo $settings['Color'];?> !important;
	font-size: 2vw;
}

.Booker-Home {position: absolute; width: 4%; top: 1.5%; right: 0.5%; z-index: 60;}
.Booker-Home img {display: block; width: 100%;}
</style>

<script>
function setUp() {
	let deleteInput = document.getElementsByName("delete")[0];
	document.getElementById("Select").addEventListener("click", updateAll, false);
	deleteInput.nextElementSibling.addEventListener("click", (e) => {validate(event, deleteInput);}, true);
}
function updateAll() {
	var value = document.getElementById("Select").value;
	if (value == "") {return;}
	document.getElementsByName("delete")[0].value = value;
}
function validate(e, obj) {
	e.preventDefault();
	let answer = (obj.name == 'revert') ? confirm('This will replace all your appointment data with the data from the selected Barber. The old data will be deleted, make sure to take a Barber!') : true;
	if (obj.value == "") {
		window.alert("No Barber selected, please select a Barber and try again.");
		return false;
	}
	else {
		if (answer) {obj.parentElement.submit();}
		else {return false;}
	}
}
document.getElementsByName("delete")[0].value = '';
window.addEventListener("load", setUp, false);
</script>