<?php
//return confirm('This will replace all your appointment data with the data from the selected backup. The old data will be deleted, make sure to take a backup!')
$settings = get_option('booker_settings');
$homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker', 'https');
$homeIcon =  get_site_url(null, '/wp-content/plugins/booker/assets/Home.svg', 'https');
$downloadURL =  get_site_url(null, '/wp-content/plugins/booker/backups/', 'https');

$backup = new backup();
$backups = $backup->get();
sort($backups);

?>

<div class="Backup">
	<h1 class="Backup-Title">Booker - Backups</h1>
	
	<div class="Backup-Main">
		<div class="Backup-Side">
			<h1 class="Backup-SubTitle">Actions</h1>
			<div class="Backup-Actions">
				<form class="Backup-Actions-Form" method="post">	
					<input type="hidden" name="create" value="1" />
					<input type="submit" class="Backup-Actions-Submit" value="Create" />
				</form>
				<form class="Backup-Actions-Form" method="post">	
					<input type="hidden" name="revert" class="hidden" value="" />
					<input type="submit" class="Backup-Actions-Submit" value="Revert" onclick="" />
				</form>
				<a class="Backup-Actions-Link" id="export" href="#">Download</a>
				<form class="Backup-Actions-Form" enctype="multipart/form-data" method="post">	
					<input type="file" name="import" style="display: none;">
					<input type="submit" class="Backup-Actions-Submit" value="Import" />
				</form>
				<form class="Backup-Actions-Form" method="post">	
					<input type="hidden" name="delete" class="hidden" value=""/>
					<input type="submit" class="Backup-Actions-Submit" value="Delete"/>
				</form>
				<form class="Backup-Actions-Form" method="post">	
					<input type="hidden" name="clear" value="1" />
					<input type="submit" class="Backup-Actions-Submit" value="Clear" onclick="return confirm('This will delete ALL appointments from your schedule. Make sure you take a backup first!')" />
				</form>
			</div>
		</div><!--
	 --><div class="Backup-Side">
			<h1 class="Backup-SubTitle">Backups</h1>
			
			<select id="Select" class="Backup-Select" size="6">
				<?php
					if (!empty($backups)) {
						foreach ($backups as $single) {
							echo '<option class="Backup-Option" value="'.$single.'">'.$single.'</option>';
						}
					}
					else {
						echo '<option class="Backup-Option" value="">No Backups</option>';
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

.Backup {
	position: absolute;
	width: 98%;
	height: 82vh;
	padding: 0 0 3%;
	top: 20%;
	left: 0;
	background: #FFF;
	text-align: center;
}
.Backup-Title {
	display: block;
	width: 67%;
	margin: 1% auto 2%;
	padding: 0 0 1%;
	
	font-size: 3vw;
	line-height: 4vw;
	border-bottom: 0.3vw solid;
}
.Backup-Side {
	display: inline-block;
	width: 50%;
	vertical-align: top;
}

.Backup-Actions {
	display: block;
	width: 100%;
	margin: 0 auto;
}
.Backup-Actions-Form {
	display: inline-block;
	width: 29%;
	margin: 2%;
}
.Backup-Actions-Link {
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
.Backup-Actions-Submit {
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
.Backup-Actions-Link:hover, .Backup-Actions-Submit:hover {
	background: <?php echo $settings['Color'];?>;
	color: #FFF;
}

.Backup-Select, .wp-core-ui select {
	display: inline-block;
	width: 73%;
	max-height: 27.7vw;
	padding: 0;
	border: 0.3vw solid ;
	vertical-align: top;
	overflow-y: scroll;
}
.Backup-Option {
	position: relative;
	padding: 2% 0 3% 2%;
	font-size: 2vw;
	-moz-user-select: none; 
}
.Backup-Option:nth-child(odd) {
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
	let revertInput = document.getElementsByName("revert")[0], importInput =  document.getElementsByName("import")[0], deleteInput = document.getElementsByName("delete")[0]
	
	document.getElementById("Select").addEventListener("click", updateAll, false);
	revertInput.nextElementSibling.addEventListener("click", (e) => {validate(event, revertInput);}, true);
	deleteInput.nextElementSibling.addEventListener("click", (e) => {validate(event, deleteInput);}, true);
	importInput.nextElementSibling.addEventListener("click", (e) => {fileValidate(event, importInput);}, false);
	importInput.addEventListener("change", (e) => {importInput.parentElement.submit();}, false);
}
function updateAll() {
	var value = document.getElementById("Select").value;
	if (value == "") {return;}
	document.getElementsByName("revert")[0].value = value;
	document.getElementsByName("delete")[0].value = value;
	document.getElementById("export").setAttribute("download", value)
	document.getElementById("export").href = "<?php echo $downloadURL;?>" + value;
}
function validate(e, obj) {
	e.preventDefault();
	let answer = (obj.name == 'revert') ? confirm('This will replace all your appointment data with the data from the selected backup. The old data will be deleted, make sure to take a backup!') : true;
	if (obj.value == "") {
		window.alert("No backup selected, please select a backup and try again.");
		return false;
	}
	else {
		if (answer) {obj.parentElement.submit();}
		else {return false;}
	}
}
function fileValidate(e, obj) {
	e.preventDefault();
	obj.click();
	return false;
}
document.getElementsByName("delete")[0].value = '';
window.addEventListener("load", setUp, false);
</script>