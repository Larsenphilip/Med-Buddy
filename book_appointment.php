<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = intval($_POST['doctor_id']);
    $patient_name = $_POST['name'];
    $patient_email = $_POST['email'];
    $patient_phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time']; // Expecting HH:MM format

    // Basic validation
    if (empty($doctor_id) || empty($patient_name) || empty($date) || empty($time)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Insert appointment
    $sql = "INSERT INTO appointments (doctor_id, patient_name, patient_email, patient_phone, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $doctor_id, $patient_name, $patient_email, $patient_phone, $date, $time);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to book appointment. Please try again.']);
    }
}
?>
