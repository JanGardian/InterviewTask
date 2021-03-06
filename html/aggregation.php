<?php

// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers

// setting of PDO, db and credentials
$db = new PDO('mysql:host=localhost;dbname=clock_db', 'root', 'fouvet3SAN');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

// getting current day 
$today = date("Y-m-d");
$dailyStopSum = 0;

// Select from logging table to summary of all stop durations
try {
        $stmt = $db->prepare("SELECT * FROM logging WHERE DATE(created) = CURDATE()");
        $stmt->execute();
        if($stmt !== false) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows as $row) {
                        $dailyStopSum = $dailyStopSum + ((int) $row['duration']);
                };
        }
} catch ( Exception $e ) {
    print_r($e->getMessage());
}

// Insert into daily_aggregations current day and sum of all stop duration for current day 
try {
        $stmtInsert = $db->prepare("INSERT INTO daily_aggregations (date, stop_sum) VALUES (:date, :sum)");
        $stmtInsert->execute(array(':date' => $today, ':sum' => $dailyStopSum));

} catch ( Exception $e ) {
        print_r($e->getMessage());
}

?>

