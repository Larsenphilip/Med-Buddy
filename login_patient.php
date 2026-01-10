<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$loginId = $conn->real_escape_string($data['loginId']); // Can be Email or Patient ID
$password = $data['password']; // Plain text password from input

if (empty($loginId) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email/ID and Password are required']);
    exit;
}

// Authenticate against patients table
// We check if the loginId matches either email or patient_id
$sql = "SELECT * FROM patients WHERE email = '$loginId' OR patient_id = '$loginId'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password check
    if (password_verify($password, $user['password'])) {
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();

