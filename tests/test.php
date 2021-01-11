<?php

require_once("vendor/autoload.php");

$dbh = new PDO('mysql:host=voinquery.cluster-cwm0ve9zn18f.us-west-2.rds.amazonaws.com;dbname=voinquery;charset=utf8mb4', 'admin', 'YS5kdRF9cNd2ckUUdFjP');

$stmt = $dbh->prepare("SELECT * FROM Events;");
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_CLASS, \obray\tests\Event::class);
print_r($events[0]->event_id);
