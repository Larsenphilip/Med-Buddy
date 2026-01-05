<?php
include 'db_config.php';

if (isset($_GET['doctor_id']) && isset($_GET['date'])) {
    $doctor_id = intval($_GET['doctor_id']);
    $date = $_GET['date'];
    
    // Get day of week
    $timestamp = strtotime($date);
    $day_of_week = date('l', $timestamp);
    
    // Check doctor's working hours for that day
    $sql = "SELECT start_time, end_time FROM doctor_availability WHERE doctor_id = ? AND day_of_week = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $day_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $start_time = strtotime($row['start_time']);
        $end_time = strtotime($row['end_time']);
        $slots = [];
        
        // Generate 30-minute slots
        for ($time = $start_time; $time < $end_time; $time += 1800) {
            $slot_time = date('H:i', $time);
            
            // Check if this slot is already booked
            $check_booking = "SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?";
            $check_stmt = $conn->prepare($check_booking);
            $check_stmt->bind_param("iss", $doctor_id, $date, $slot_time);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                $slots[] = $slot_time;
            }
        }
        
        echo json_encode(['success' => true, 'slots' => $slots]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Doctor not available on this day.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
