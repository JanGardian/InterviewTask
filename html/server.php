<?php

// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
// setting of PDO, db and credentials
$db = new PDO('mysql:host=localhost;dbname=clock_db', 'root', 'fouvet3SAN');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

// loading data from POST sent by ajax
$method = $_POST['type'];
$unixTime = $_POST['time'];

$stopFinished;
$lastId;
$stopTime;

// Select from db to get if Stop was already clicked or not
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

// When ajax sent method stopTime new row/log will be inserted to logging table
// If previous click was also stopTime it will be ignored and no new log inserted
if ($method === 'stopTime') {
        if ($stopFinished == '1') {
                try {
                        $stmtStop = $db->prepare("INSERT INTO logging (stop_time) VALUES (:utime)");
                        // javascript unix time is in mili seconds and mysql in seconds so had to convert
						$stmtStop->execute(array(':utime' => (int) $unixTime/1000));
						
                } catch ( Exception $e ) {

                        print_r($e->getMessage());
                }

        }
}

// When ajax sent methor restartTime row will be update only if previous click was stopTime
if ($method === 'restartTime') {
        if ($stopFinished == '0') {
				// duration of stopped time is counted as difference between unixTime sent by ajax when clicked restartTime
				// and previous unix time when was clicked stopTime
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

