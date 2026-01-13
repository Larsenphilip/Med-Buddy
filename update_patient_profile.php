<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Get raw POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

// Extract fields
$full_name = $data['full_name'] ?? '';
$date_of_birth = $data['date_of_birth'] ?? null;
$gender = $data['gender'] ?? '';
$address = $data['address'] ?? '';
$city = $data['city'] ?? '';
$state = $data['state'] ?? '';
$postal_code = $data['postal_code'] ?? '';
$blood_group = $data['blood_group'] ?? '';
$height = $data['height'] ?? null;
$weight = $data['weight'] ?? null;
$allergies = $data['allergies'] ?? '';
$chronic_conditions = $data['chronic_conditions'] ?? '';
$emergency_contact_name = $data['emergency_contact_name'] ?? '';
$emergency_contact_phone = $data['emergency_contact_phone'] ?? '';

// Basic validation (optional, can be expanded)
if (empty($full_name)) {
    echo json_encode(['success' => false, 'message' => 'Full Name is required']);
    exit();
}

// Update Query
$sql = "UPDATE patients SET 
        full_name = ?,
        date_of_birth = ?,
        gender = ?,
        address = ?,
        city = ?,
        state = ?,
        postal_code = ?,
        blood_group = ?,
        height = ?,
        weight = ?,
        allergies = ?,
        chronic_conditions = ?,
        emergency_contact_name = ?,
        emergency_contact_phone = ?
        WHERE patient_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters: s = string, d = double/float
    // full_name (s), dob (s), gender (s), address (s), city (s), state (s), zip (s), 
    // blood (s), height (s/d), weight (s/d), allergies (s), conditions (s), emerg_name (s), emerg_phone (s), id (s)
    
    // We treat empty strings as NULL for dates if needed, but for simplicity let's stick to string or empty
    // If date_of_birth is empty string, mysql might complain if column is DATE type.
    if (empty($date_of_birth)) $date_of_birth = null;
    if (empty($height)) $height = null;
    if (empty($weight)) $weight = null;

    $stmt->bind_param("ssssssssddsssss", 
        $full_name, 
        $date_of_birth, 
        $gender, 
        $address, 
        $city, 
        $state, 
        $postal_code, 
        $blood_group, 
        $height, 
        $weight,
        $allergies,
        $chronic_conditions,
        $emergency_contact_name,
        $emergency_contact_phone,
        $patient_id
    );

    if ($stmt->execute()) {
        // Update session name if changed
        $_SESSION['full_name'] = $full_name;
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
}

$conn->close();
?>
