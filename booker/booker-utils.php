<?php

//Classes
class schedule {
	//Main Functions
	function getBy($key = '', $value = '') {
		//Search By Keys ID, Month, Date, or leave Blank for all
		if ($key != '' && $value == '') {
			return array('Name' => 'Schedule Error: No Search Value');
		} //If key, but no value
		$out = ($key == '') ? get_option('booker_data') : $this->search($key, $value);
		return sortByTime($out);
	}
	
	function feed() {
		$schedule = get_option('booker_data');
		$today = getToday();
		$out = array();
		foreach ($schedule as $appoint) {
			$appointStart = strtotime($appoint['Date'].' '.$appoint['Time-Start']);
			$appointEnd = strtotime($appoint['Date'].' '.$appoint['Time-End']);
			$currentTime = strtotime($today['ISO'].' '.$today['time']);
			$endOfWeek = strtotime('sunday this week');//This week?
			//Mathes if appoint hasnt started, or if appoint isnt over yet.
			if ($currentTime < $appointStart && $appointStart < $endOfWeek) {
				$out[] = $appoint;
			}		
			else {
				if ($currentTime < $appointEnd && $appointStart < $endOfWeek) {
					$out[] = $appoint;
				}
			}
		}
		return sortByTime($out);
	}
	
	function openFeed($date) {
		$today = getToday();
		$unsorted = $this->getBy('Date', $date);
		
		$out = array();
		foreach ($unsorted as $item) {
			if ($item['Status'] == 'Open') {
				$out[] = $item;
			}
		}
		return $out;
	}
	
	//Util functions
	function search($key, $value) {
		//returns array even if only one item
		$schedule = get_option('booker_data');
		$out = array();
		foreach ($schedule as $appoint) {
			if ($appoint[$key] == $value) {
				$out[] = $appoint;
			}
		}
		return $out;
	}
	
	function add($date, $start, $end) {
		$schedule = get_option('booker_data');
		$appointment = array(
			'ID' => uniqid(),
			'Date' => $date,
			'Time-Start' => $start,
			'Time-End' => $end,
			'Name' => 'Open Appointment',
			'Email' => '',
			'Phone' => '',
			'Address' => '',
			'Service' => '',
			'Barber' => '',
			'Status' => 'Open'
		);
		array_push($schedule, $appointment);
		update_option('booker_data', $schedule);
	}
	
	function update($ID, $data) {
		$schedule = get_option('booker_data');
		foreach ($schedule as $index=>$appoint) {
			if ($appoint['ID'] == $ID) {
				$schedule[$index]['Date'] = (isset($data['Date'])) ? $data['Date'] : $schedule[$index]['Date'];
				$schedule[$index]['Time-Start'] = (isset($data['Time-Start'])) ? $data['Time-Start'] : $schedule[$index]['Time-Start'];
				$schedule[$index]['Time-End'] = (isset($data['Time-End'])) ? $data['Time-End'] : $schedule[$index]['Time-End'];
				$schedule[$index]['Name'] = (isset($data['Name'])) ? $data['Name'] : $schedule[$index]['Name'];
				$schedule[$index]['Email'] = (isset($data['Email'])) ? $data['Email'] : $schedule[$index]['Email'];
				$schedule[$index]['Phone'] = (isset($data['Phone'])) ? $data['Phone'] : $schedule[$index]['Phone'];
				$schedule[$index]['Address'] = (isset($data['Address'])) ? $data['Address'] : $schedule[$index]['Address'];
				$schedule[$index]['Service'] = (isset($data['Service'])) ? $data['Service'] : $schedule[$index]['Service'];
				$schedule[$index]['Barber'] = (isset($data['Barber'])) ? $data['Barber'] : $schedule[$index]['Barber'];
				$schedule[$index]['Status'] = (isset($data['Status'])) ? $data['Status'] : $schedule[$index]['Status'];
				update_option('booker_data', $schedule);
			}
		}
	}
	
	function delete($ID) {
		$schedule = get_option('booker_data');
		foreach ($schedule as $index=>$appoint) {
			if ($appoint['ID'] == $ID) {
				unset($schedule[$index]);
				update_option('booker_data', $schedule);
			}
		}
	}
}

class backup {
	function get() {
		$out = array();
		$dir = new DirectoryIterator(dirname(__FILE__).'/backups');
		foreach ($dir as $fileinfo) {
			if (!$fileinfo->isDot()) {
				$out[] = $fileinfo->getFilename();
			}
		}
		return $out;
	}
	
	function create() {
		$today = getToday();
		$name = $today['backup'];
		$schedule = maybe_serialize(get_option('booker_data'));
		$file = dirname(__FILE__).'/backups/'.$name; 
		$open = fopen($file, "a");
		$write = fwrite($open, $schedule);
		fclose($open);
		return $file;
	}
	
	function delete($filename) {
		$file = dirname(__FILE__).'/backups/'.$filename;
		$writable = is_writable($file);
		unlink($file);
	}
	
	function replace($filename) {
		$path = dirname(__FILE__).'/backups/';
		$file = file_get_contents($path.$filename);
		
		if ($file === false) {
			return 'Backup Error: File does not exist';
		}
		else {
			update_option('booker_data', maybe_unserialize($file));
		}
	}
	
	function import($FILE) {
		$path = dirname(__FILE__).'/backups/';
		$filename = str_replace(" ", ":", $FILE['name']);
		$file = $path.$filename;
		$out = (move_uploaded_file($FILE["tmp_name"], $file)) ? "File Uploaded" : "Error:".error_get_last();
		$this->replace($filename);
	}
	
	function clear() {
		update_option('booker_data', array());
	}
	
	function sorter($backups) {
		return sort($backups);
	}
}

//Util Functions
function getToday($date = "") {
	$today = ($date != "") ? DateTime::createFromFormat("Y-m-j", $date) : new DateTime();
	//$today->setTimezone(new DateTimeZone('America/New_York'));
	$today->setTimezone(new DateTimeZone('Europe/Berlin'));
	$spareToday = DateTime::createFromFormat("Y-m-d", $today->format("Y-m-j"));
	$out = array(
		"month" => $today->format("m"),
		"monthName" => $today->format("F"),
		"day" => $today->format("j"),
		"dayName" => $today->format("l"),
		"daysInMonth" => $today->format("t"),
		"year" => $today->format("Y"),
		"ISO" => $today->format("Y-m-d"),
		"time" => $today->format("H:i"),
		"backup" => $today->format("Y-m-d_H:i:s"),
		"todayTitle" => $today->format("l, F j, Y"),
		"prevMonth" => $today->modify('first day of previous month')->format("Y-m-d"),
		"nextMonth" => $spareToday->modify('first day of next month')->format("Y-m-d"),
		
	);
	return $out; 
}
function formatTimeSlot($start, $end) {
	$startTime = date("h:i A", strtotime($start));
	$endTime = date("h:i A", strtotime($end));
	$out = $startTime.' - '.$endTime;
	return $out;
}
function sortByTime($array) {
	usort($array, function ($a, $b) {return strtotime($a['Date'].' '.$a['Time-Start']) - strtotime($b['Date'].' '.$b['Time-Start']);});
	return $array;
}
function bookerEmail($ID, $type, $email, $subject) {
	add_filter('wp_mail_content_type', function() { return "text/html"; } );
	$settings = get_option('booker_settings');
	$to = $email;
	$subject = $settings['Company'].' - '.$subject;
	
	$appointID = $ID;
	$emailType = $type;
	$message = include 'templates/booker-email.php';
	
	$headers = array(
		'From: '.$settings['Company'].' <wordpress@'.$_SERVER["SERVER_NAME"].'>',
		'Reply-To: contact@'.$_SERVER["SERVER_NAME"],
		'Content-Type: text/html; charset=UTF-8;'
	);
	
	$mail = wp_mail($to, $subject, $message, $headers);
	add_filter('wp_mail_content_type', function() { return "text/plain"; } );
	return $mail;
}

?>