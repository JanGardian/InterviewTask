<?php

// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers

$db = new PDO('mysql:host=localhost;dbname=clock_db', 'root', 'fouvet3SAN');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);


$method = $_POST['type'];
$unixTime = $_POST['time'];
$stopFinished;
$lastId;
$stopTime;

try {
	$stmt = $db->prepare("SELECT * FROM logging WHERE id = (SELECT MAX(id) FROM logging)");
	$stmt->execute();
	if ($stmt->rowCount() === '0') {
		$stopFinished =1;	
	}
	if($stmt !== false) {	
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row) {
			$stopFinished = $row['finished'];
			$lastId = $row['id'];
			$stopTime = $row['stop_time'];
		};
	}

} catch ( Exception $e ) {
    print_r($e->getMessage());
}
var_dump((int)$unixTime);
var_dump($unixTime);
var_dump((int)$stopTime);
var_dump($stopTime);

//print_r($stopFinished);
//print_r($lastId);

if ($method === 'stopTime') {
	if ($stopFinished == '1') {
		try {
			$stmtStop = $db->prepare("INSERT INTO logging (stop_time) VALUES (:utime)");
			$stmtStop->execute(array(':utime' => (int) $unixTime/1000));

			
		} catch ( Exception $e ) {
			
			print_r($e->getMessage());
		}
		
	}
}

if ($method === 'restartTime') {
	if ($stopFinished == '0') {
		$duration = (((int) $unixTime/1000) - $stopTime);
		var_dump($duration);
		try {
			$stmtStart = $db->prepare("UPDATE logging SET finished=?, restart_time=?, duration=? WHERE id=?");
			$stmtStart->execute(array(1,(int) $unixTime/1000, $duration, $lastId));
			$affected_rows = $stmtStart->rowCount();
		} catch ( Exception  $e ) {

			print_r($e->getMessage());
		}
	}
}
