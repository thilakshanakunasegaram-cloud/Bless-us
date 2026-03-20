<?php
// process.php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = htmlspecialchars($_POST['firstName'] ?? '');
    $lastName = htmlspecialchars($_POST['lastName'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $attendance = htmlspecialchars($_POST['attendance'] ?? '');
    $guests = htmlspecialchars($_POST['guests'] ?? '0');
    $meal = htmlspecialchars($_POST['meal'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bless_us_db";

    // Create connection (without dbname first to create it if it doesn't exist)
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
        exit();
    }

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        $conn->select_db($dbname);
        
        // Create table if it doesn't exist
        $tableSql = "CREATE TABLE IF NOT EXISTS participants (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100),
            attendance VARCHAR(20),
            guests VARCHAR(10),
            meal VARCHAR(50),
            message TEXT,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->query($tableSql);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO participants (first_name, last_name, email, attendance, guests, meal, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $firstName, $lastName, $email, $attendance, $guests, $meal, $message);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Thank you for your response! We look forward to seeing you. Your detail has been saved into database."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error saving data: " . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error creating database: " . $conn->error
        ]);
    }
    
    $conn->close();
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid request method."
    ]);
}
?>
