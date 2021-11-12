<?php

$settings = get_option('booker_settings');
$homeURL = get_site_url(null, '/wp-admin/admin.php?page=booker', 'https');
$homeIcon =  get_site_url(null, '/wp-content/plugins/booker/assets/Home.svg', 'https');
$schedule = new schedule();
$items = $schedule->getBy();
$today = getToday();

?>

<div class="History">
	<h1 class="History-Title">History</div>
	<div class="History-Container">
		<div class="History-Bar">
			<div class="History-Column Column-Date">Date</div><!--
		 --><div class="History-Column Column-Name">Name</div><!--
		 --><div class="History-Column Column-TimeStart">Time Start</div><!--
		 --><div class="History-Column Column-TimeEnd">Time End</div><!--
		 --><div class="History-Column Column-Email">Email</div><!--
		 --><div class="History-Column Column-Phone">Phone</div><!--
		 --><div class="History-Column Column-Address">Address</div><!--
		 --><div class="History-Column Column-Service">Service</div><!--
		 --><div class="History-Column Column-Barber">Barber</div><!--
		 --><div class="History-Column Column-Status">Status</div>
		</div>
		
		<div class="History-Content">
			<?php
				if (sizeOf($items) == 0) {
					echo '
							<div class="History-Item">
								<div class="History-Column Column-Date">N/A</div><!--
							 --><div class="History-Column Column-Name">No History</div><!--
							 --><div class="History-Column Column-TimeStart">N/A</div><!--
							 --><div class="History-Column Column-TimeEnd">N/A</div><!--
							 --><div class="History-Column Column-Email">N/A</div><!--
							 --><div class="History-Column Column-Phone">N/A</div><!--
							 --><div class="History-Column Column-Address">N/A</div><!--
							 --><div class="History-Column Column-Status">N/A</div>
							</div>
						';
				}
				else {
					foreach ($items as $item) {
						echo '
							<div class="History-Item">
								<div class="History-Column Column-Date">'.$item['Date'].'</div><!--
							 --><div class="History-Column Column-Name">'.$item['Name'].'</div><!--
							 --><div class="History-Column Column-TimeStart">'.$item['Time-Start'].'</div><!--
							 --><div class="History-Column Column-TimeEnd">'.$item['Time-End'].'</div><!--
							 --><div class="History-Column Column-Email">'.$item['Email'].'</div><!--
							 --><div class="History-Column Column-Phone">'.$item['Phone'].'</div><!--
							 --><div class="History-Column Column-Address">'.$item['Address'].'</div><!--
							 --><div class="History-Column Column-Service">'.$item['Service'].'</div><!--
							 --><div class="History-Column Column-Barber">'.$item['Barber'].'</div><!--
							 --><div class="History-Column Column-Status">'.$item['Status'].'</div>
							</div>
						';
					}
				}		
			?>
		</div>
	</div>
	
	<a class="Booker-Home" href="<?php echo $homeURL;?>">
		<IMG src="<?php echo $homeIcon; ?>" />
	</a>
</div>

<style>
.History {
	position: relative;
}
.History-Title {
	margin: 1.5% 1%; 
}
.History-Container {
	display: block;
	width: 98%;
	margin: 0 auto;
	border: 0.2vw solid;
}
.History-Content {
	max-height: 35vw;
	overflow-y: scroll;
}
.History-Bar {
	display: block;
	border-bottom: 0.2vw solid;
	background: #e9e9e9;
}
.History-Column {
	display: inline-block;
	width: 13%;
	min-height: 1.4vw;
	padding: 1% 0.75% 1% 0;
	text-align: right;
	font-size: 1.1vw;
	border-right: 0.1vw solid;
	vertical-align: middle;
	white-space: pre;
	overflow-x: scroll;
}
.Column-Date {
	width: 9%;
}
.Column-Name, .Column-Email {
	width: 10%;
}
.Column-Address {
	width: 11%;
}
.Column-TimeStart, .Column-TimeEnd {
	width: 8%;
}
.Column-Phone {
	width: 11%;
}
.Column-Service, .Column-Barber {
	width: 8%;
}
.Column-Status {
	border: none;
	width: 8%;
}
.History-Item {
	background: #FFFFFF;
	border-bottom: 0.1vw solid;
}
.History-Item:hover {
	background: <?php echo $settings['Color'];?>;
}

.Booker-Home {position: absolute; width: 4%; top: 0%; right: 0.5%; z-index: 60;}
.Booker-Home img {display: block; width: 100%;}
#wpbody-content {padding-bottom: 60px;}
</style>