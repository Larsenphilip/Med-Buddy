<?php
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../doctor_login.php");
    exit;
}

include '../db_config.php';
$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

// Handle Availability Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_availability'])) {
    $day = $_POST['day'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    
    // Check if entry exists
    $check = $conn->prepare("SELECT id FROM doctor_availability WHERE doctor_id = ? AND day_of_week = ?");
    $check->bind_param("is", $doctor_id, $day);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $update = $conn->prepare("UPDATE doctor_availability SET start_time = ?, end_time = ? WHERE doctor_id = ? AND day_of_week = ?");
        $update->bind_param("ssis", $start, $end, $doctor_id, $day);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $insert->bind_param("isss", $doctor_id, $day, $start, $end);
        $insert->execute();
    }
    $success_msg = "Availability updated for $day!";
}

// Handle Appointment Status Update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appt_id = intval($_GET['id']);
    $status = $_GET['action'] == 'confirm' ? 'Confirmed' : 'Cancelled';
    
    $update_appt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
    $update_appt->bind_param("sii", $status, $appt_id, $doctor_id);
    $update_appt->execute();
    header("Location: doc.php"); // Refresh to clear query params
    exit;
}

// Fetch Appointments
$sql_appt = "SELECT * FROM appointments WHERE doctor_id = ? ORDER BY appointment_date DESC, appointment_time ASC";
$stmt = $conn->prepare($sql_appt);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result();

// Fetch Availability
$sql_avail = "SELECT * FROM doctor_availability WHERE doctor_id = ?";
$stmt_avail = $conn->prepare($sql_avail);
$stmt_avail->bind_param("i", $doctor_id);
$stmt_avail->execute();
$availability_result = $stmt_avail->get_result();
$availability = [];
while ($row = $availability_result->fetch_assoc()) {
    $availability[$row['day_of_week']] = $row;
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Med Buddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0066CC;
            --secondary-color: #f4fdf0; /* Matching green theme background */
            --white: #ffffff;
            --text-dark: #1F2937;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: #f9fafb;
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--white);
            border-right: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 3rem;
        }

        .menu-item {
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .menu-item.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .menu-item:hover {
            background-color: #f3f4f6;
        }

        .logout {
            margin-top: auto;
            color: red;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        h2 {
            margin-top: 0;
            color: var(--text-dark);
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-Pending { background-color: #FEF3C7; color: #D97706; }
        .status-Confirmed { background-color: #D1FAE5; color: #059669; }
        .status-Cancelled { background-color: #FEE2E2; color: #DC2626; }

        .btn-action {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            margin-right: 0.5rem;
        }
        
        .btn-confirm { background-color: #059669; color: white; }
        .btn-cancel { background-color: #DC2626; color: white; }

        /* Availability Form */
        .availability-row {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 6px;
        }
        
        .availability-row h4 { margin: 0; width: 100px; }
        
        input[type="time"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-save {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .alert {
            padding: 1rem;
            background-color: #D1FAE5;
            color: #059669;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Med Buddy</div>
        <a href="#appointments" class="menu-item active">Appointments</a>
        <a href="#availability" class="menu-item">My Schedule</a>
        <a href="../doctor_login.php" class="menu-item logout">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($doctor_name); ?></h1>
        </div>
        
        <?php if(isset($success_msg)) echo "<div class='alert'>$success_msg</div>"; ?>

        <!-- Appointments Section -->
        <div id="appointments" class="card">
            <h2>Upcoming Appointments</h2>
            <?php if ($appointments->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($appt = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $appt['appointment_date']; ?></td>
                        <td><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></td>
                        <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($appt['patient_phone']); ?></td>
                        <td><span class="status status-<?php echo $appt['status']; ?>"><?php echo $appt['status']; ?></span></td>
                        <td>
                            <?php if($appt['status'] == 'Pending'): ?>
                            <a href="?action=confirm&id=<?php echo $appt['id']; ?>" class="btn-action btn-confirm">Confirm</a>
                            <a href="?action=cancel&id=<?php echo $appt['id']; ?>" class="btn-action btn-cancel">Cancel</a>
                            <?php else: ?>
                            --
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No appointments found.</p>
            <?php endif; ?>
        </div>

        <!-- Availability Section -->
        <div id="availability" class="card">
            <h2>Manage Weekly Availability</h2>
            <p style="margin-bottom: 1.5rem; color: #666;">Set your start and end working hours for each day.</p>
            
            <?php foreach($days as $day): 
                $start = isset($availability[$day]) ? $availability[$day]['start_time'] : '';
                $end = isset($availability[$day]) ? $availability[$day]['end_time'] : '';
            ?>
            <form method="POST" class="availability-row">
                <input type="hidden" name="update_availability" value="1">
                <input type="hidden" name="day" value="<?php echo $day; ?>">
                
                <h4><?php echo $day; ?></h4>
                <label>Start:</label>
                <input type="time" name="start_time" value="<?php echo $start; ?>" required>
                <label>End:</label>
                <input type="time" name="end_time" value="<?php echo $end; ?>" required>
                
                <button type="submit" class="btn-save">Save</button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
