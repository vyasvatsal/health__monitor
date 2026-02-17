<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=health_monitor', 'root', '');
$stmt = $pdo->query("SELECT api_key FROM stores LIMIT 1");
$apiKey = $stmt->fetchColumn();
echo $apiKey;
