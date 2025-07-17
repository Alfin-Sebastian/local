<?php
include 'db.php';
session_start();
if ($_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider_id = $_POST['provider_id'];
    $service_id  = $_POST['service_id'];
    $date        = $_POST['date'];
    $customer_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO bookings (customer_id, provider_id, service_id, date_requested, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiis", $customer_id, $provider_id, $service_id, $date);
    if ($stmt->execute()) {
        echo "Booking submitted!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST">
    <h2>Book a Service</h2>
    Provider ID: <input type="number" name="provider_id" required><br>
    Service ID: <input type="number" name="service_id" required><br>
    Date/Time: <input type="datetime-local" name="date" required><br>
    <button type="submit">Book</button>
</form>
