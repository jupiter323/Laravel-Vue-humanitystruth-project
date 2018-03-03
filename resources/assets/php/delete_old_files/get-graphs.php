<?php
	include "functions.php";

	global $con;

	$user = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '".$_GET['hash']."'"));

	if($user['type'] != "super_admin" || !isset($_GET['start_date']) || !isset($_GET['end_date'])) die();

	$start_date  = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$num_columns = isset($_GET['columns']) ? $_GET['columns'] : 6;

	$timeframes = array();
	
	$start = strtotime($start_date);
	$end = strtotime($end_date);

	$period = intval(($end - $start) / $num_columns); 

	for ($index = 0; $index < $num_columns; $index++) {
		$tmp = $start + ($index * $period);
		array_push($timeframes, array( $tmp , $tmp+$period));
	}


$content ='function drawCharts() {
			drawFinancial(function(){
				drawProductivity(function() {
					drawActivity();
				});
			});
		}
		';

$content .=	'function drawFinancial(chartReady) {
			var data1 = new google.visualization.DataTable();
			data1.addColumn("string", "Time");
			data1.addColumn("number", "Income");
			';


	for ($index = 0; $index < $num_columns; $index++) {
		
		$total = 0.00;
		$sql = "SELECT * FROM `transactions`";
		$result = mysqli_query($con, $sql);
		while($row = mysqli_fetch_array($result)) {
			$time = strpos($row['timestamp'], "(") != false ? trim(substr($row['timestamp'], 0, strpos($row['timestamp'], "("))) : trim($row['timestamp']);

			if($timeframes[$index][0] < strtotime($time) && strtotime($time) < $timeframes[$index][1]) {
				if($row['amount'] > 0) $total += $row['amount'];
			}
		}

		$dt = new DateTime("@".$timeframes[$index][0]);

		if($end - $start <= 60*60*24) $format = $dt->format('g:iA');
		else if($end - $start <= 60*60*24*7) $format = $dt->format('n/j/y g:iA');
		else $format = $dt->format('n/j/y');

		$content .='data1.addRow([\''.$format.'\', '.$total.']);
			';
	}

$content .=	'var options = {
				chart: {
					title: "Financial Chart",
					subtitle: "in dollars (USD)"
				},
				width: 1000,
				height: 450,
				vAxis: {
					format: "currency"
				},
				legend: { position: "none" }
			};

			//.visualization.LineChart || .charts.Line
			var chart = new google.charts.Line(document.getElementById("financial_chart"));
			if (typeof chartReady !== "undefined") google.visualization.events.addOneTimeListener(chart, "ready", chartReady);
			chart.draw(data1, options);
		}
		';


$content .=	'function drawActivity(chartReady) {

			var data3 = new google.visualization.DataTable();
			data3.addColumn("string", "Time");
			';


	$urls = array();
	//get activity
	$sql = "SELECT * FROM `traffic`";
	$result = mysqli_query($con, $sql);
	while($row = mysqli_fetch_array($result)) {
		//$row['action'] = substr($row['action'], 0, strpos($row['action'], ".php")+4);

		if(!in_array($row['action'], $urls)) {
			array_push($urls, $row['action']);
			$content .= 'data3.addColumn("number", "'.$row['action'].'");
			';
		}
		
	}

	for ($index = 0; $index < $num_columns; $index++) {
		$row_builder = array();

		foreach($urls as $url) {
			$count = 0;
			$sql = "SELECT * FROM `traffic` WHERE `action` = '".$url."'";
			$result = mysqli_query($con, $sql);
			while($row = mysqli_fetch_array($result)) {
				if($timeframes[$index][0] < strtotime($row['timestamp']) && strtotime($row['timestamp']) < $timeframes[$index][1]) {
					$count++;
				}
			}

			array_push($row_builder, $count);
		}

		$dt = new DateTime("@".$timeframes[$index][0]);

		if($end - $start <= 60*60*24) $format = $dt->format('g:iA');
		else if($end - $start <= 60*60*24*7) $format = $dt->format('n/j g:iA');
		else $format = $dt->format('n/j/y');

		$uniqueIps = array();

		$sql = "SELECT * FROM `traffic`";
		$result = mysqli_query($con, $sql);
		while($row = mysqli_fetch_array($result)) {
			if($timeframes[$index][0] < strtotime($row['timestamp']) && strtotime($row['timestamp']) < $timeframes[$index][1] && !in_array($row['ip'], $uniqueIps)) array_push($uniqueIps, $row['ip']);

		}

$content .=		'data3.addRow([\''.$format.' ('.count($uniqueIps).')\', '.implode( ",", $row_builder).']);
			';
		
	}


$content .=		'var options3 = {
				chart: {
					title: "Activity Chart",
					subtitle: "# of Visits",
					curveType: "function"
				},
				width: 1000,
				height: 450,
				legend: {
					position: "none"
				}
			};

			//.visualization.LineChart || .charts.Line
			var chart3 = new google.charts.Line(document.getElementById("page_counter"));
			if (typeof chartReady !== "undefined") google.visualization.events.addOneTimeListener(chart3, "ready", chartReady);
			chart3.draw(data3, options3);
		}';

	
	die($content);


?>
