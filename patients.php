<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    // Redirect to home/login if not logged in
    header("Location: index.html");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Prepare statement to fetch patient details
$sql = "SELECT * FROM patients WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Database query failed: " . $conn->error);
}

if (!$patient) {
    // Handle case where session exists but patient not found (deleted?)
    session_destroy();
    header("Location: index.html");
    exit();
}

// Helper function to handle NULL/Empty values
function displayField($value) {
    return (!empty($value)) ? htmlspecialchars($value) : '<span class="not-provided">Not provided</span>';
}

// Calculate Age
$age = 'N/A';
if (!empty($patient['date_of_birth'])) {
    $dob = new DateTime($patient['date_of_birth']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

// Get First Name for Welcome Message
$fullName = $patient['full_name'];
$firstName = explode(' ', $fullName)[0];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Med Buddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Using Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            /* Inherited Theme from Index */
            --primary-color: #0066CC;
            --primary-hover: #004C99;
            --secondary-color: #E6F4FF;
            --accent-color: #00D2D3;
            --text-dark: #1F2937;
            --text-light: #6B7280;
            --white: #FFFFFF;
            --card-bg: #FFFFFF;
            --border-color: #E5E7EB;
            
            /* Dashboard Specific */
            --sidebar-width: 260px;
            --sidebar-bg: #F3F4F6;
            --header-height: 70px;
            --dashboard-bg: #F8FAFC;
        
            --container-padding: 5%;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--dashboard-bg);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--secondary-color);
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border-color);
            padding: 1.5rem;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: -0.5px;
            margin-bottom: 3rem;
            padding-left: 0.5rem;
        }

        .logo span {
            color: var(--text-dark);
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-menu a {
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            padding: 0.85rem 1rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--primary-color);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.2);
        }

        .sidebar-menu a i {
            font-size: 1.25rem;
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            position: relative;
        }

        /* --- Header --- */
        .top-bar {
            height: var(--header-height);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
            border-bottom: 1px solid var(--border-color);
        }

        .top-bar-info {
            display: flex;
            align-items: center;
            gap: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .patient-id span {
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-link {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .logout-link:hover {
            color: #ef4444;
        }

        /* --- Dashboard Grid --- */
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .welcome-text h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .welcome-banner-img {
            position: absolute;
            right: 0;
            top: -20px;
            height: 180px;
            opacity: 0.9;
        }

        /* Layout Grid (Old UI) */
        .grid-layout {
            display: grid;
            grid-template-columns: 1.5fr 1fr; /* Summary vs Appointment */
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Health Summary Card */
        .summary-card {
            background-color: var(--secondary-color);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
            height: 100%;
        }
        
        .summary-header {
            background-color: #FEF3C7; /* Soft yellow accent */
            padding: 1.5rem 2rem;
            font-weight: 600;
            color: #92400E;
            font-size: 1.1rem;
        }

        .summary-content {
            padding: 2rem;
        }

        .info-row {
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
            color: var(--text-dark);
        }

        .info-row span {
            font-weight: 600;
            min-width: 120px;
            display: inline-block;
            color: var(--text-light);
        }

        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        /* Appointment Card */
        .appointment-card {
            background: var(--white);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            height: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .doctor-preview {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .doc-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .doc-info h4 {
            font-size: 1rem;
            font-weight: 600;
        }
        
        .doc-info p {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .appt-meta {
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
        }

        .appt-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-sm {
            flex: 1;
            padding: 0.7rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .btn-fill {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
        }

        /* --- New UI: Profile Details Grid --- */
        .section-header-large {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .detail-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: var(--transition);
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            border-color: rgba(0, 102, 204, 0.3);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .card-title i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.85rem;
            font-size: 0.95rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: var(--text-light);
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-dark);
            font-weight: 600;
            text-align: right;
            max-width: 60%;
        }

        .not-provided {
            color: #9ca3af;
            font-style: italic;
            font-weight: 400;
        }

        /* Badge styles for specific fields */
        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .blood-group { background: #fee2e2; color: #991b1b; }
        .condition-tag { display: inline-block; background: #e0f2fe; color: #075985; padding: 2px 8px; border-radius: 4px; margin-bottom: 4px; font-size: 0.85rem; }

        /* Report Grid & Visits (Old UI) */
        .reports-section {
            margin-bottom: 3rem;
        }

        .section-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .view-all {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .report-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
        }
        
        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--primary-color);
        }

        .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .icon-red { background: #FEF2F2; color: #EF4444; }
        .icon-blue { background: #EBF8FF; color: #3182CE; }
        
        .report-info {
            flex: 1;
        }

        .report-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .report-date {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        /* Doctor Visits */
        .visit-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            transition: var(--transition);
        }
        
        .visit-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .visit-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .btn-view {
            padding: 0.5rem 1rem;
            background: #2563EB;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Footer Help */
        .help-container {
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        .help-text h3 {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        
        .contact-methods {
            display: flex;
            gap: 2rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .contact-item i {
            color: var(--primary-color);
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
            .welcome-banner-img {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .dashboard-container {
                padding: 1rem;
            }
            .help-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .contact-methods {
                flex-direction: column;
                gap: 1rem;
            }
            .profile-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="index.html" class="logo">
            <i class="ri-heart-pulse-fill" style="color: var(--primary-color);"></i>
            <span>Med Buddy</span>
        </a>

        <ul class="sidebar-menu">
            <li><a href="#" class="active"><i class="ri-dashboard-fill"></i> Dashboard</a></li>
            <li><a href="#"><i class="ri-file-user-line"></i> My Profile</a></li>
            <li><a href="appointment.php"><i class="ri-calendar-check-line"></i> Book Appointment</a></li>
            <li><a href="#"><i class="ri-notification-3-line"></i> Notifications</a></li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Header -->
        <div class="top-bar">
            <div class="top-bar-info">
                <div class="patient-id">Patient ID: <span><?php echo displayField($patient['patient_id']); ?></span></div>
                <div class="user-profile">
                    <span>Welcome, <?php echo displayField($patient['full_name']); ?></span> | <a href="logout.php" onclick="event.preventDefault(); document.cookie = 'PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; window.location.href='logout.php';" class="logout-link">Logout</a>
                </div>
            </div>
        </div>

        <div class="dashboard-container">
            
            <!-- Welcome Header -->
            <div class="welcome-section">
                <div class="welcome-text">
                    <h1>Welcome Back, <?php echo $firstName; ?></h1>
                    <p>Here is your personal health dashboard.</p>
                </div>
                <button onclick="openUpdateModal()" class="btn-update">
                    <i class="ri-edit-box-line"></i> Update Profile
                </button>
            </div>

            <!-- Health Summary & Appointment (OLD UI + Dynamic Data) -->
            <div class="grid-layout">
                <!-- Left Column: Health Summary -->
                <div class="summary-card">
                    <div class="summary-header">Patient Health Summary</div>
                    <div class="summary-content">
                        <div class="info-row"><span>Name:</span> <?php echo displayField($patient['full_name']); ?></div>
                        <div class="info-row"><span>Age:</span> <?php echo $age; ?></div>
                        <div class="info-row"><span>Blood Group:</span> <?php echo !empty($patient['blood_group']) ? htmlspecialchars($patient['blood_group']) : 'N/A'; ?></div>
                        <div class="info-row"><span>Conditions:</span> 
                            <?php 
                            if (!empty($patient['chronic_conditions'])) {
                                echo htmlspecialchars(substr($patient['chronic_conditions'], 0, 30)) . (strlen($patient['chronic_conditions']) > 30 ? '...' : '');
                            } else {
                                echo 'None';
                            }
                            ?>
                        </div>
                        
                        <a href="#" class="download-link">
                            <i class="ri-download-cloud-2-line"></i> Download All Records
                        </a>
                    </div>
                </div>

                <!-- Right Column: Appointment (Keeping Static as Placeholder for now) -->
                <div class="appointment-card">
                    <div class="card-header">
                        <h3>Upcoming Appointment</h3>
                        <a href="#"><i class="ri-arrow-right-line" style="color: var(--text-light);"></i></a>
                    </div>
                    
                    <div class="doctor-preview">
                        <div class="doc-avatar">👨‍⚕️</div>
                        <div class="doc-info">
                            <h4>Dr. Mehta</h4>
                            <p>Cardiologist</p>
                        </div>
                    </div>
                    
                    <div class="appt-meta">
                        <span>Date: 25/04/2024</span>
                        <span>10:00 AM</span>
                    </div>

                    <div class="appt-actions">
                        <button class="btn-sm btn-fill">Reschedule</button>
                        <button class="btn-sm btn-outline">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Detailed Profile Information (NEW UI) -->
            <div class="section-header-large">
                Full Patient Profile
            </div>

            <div class="profile-grid">
                
                <!-- Personal Information -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="ri-user-line"></i> Personal Information
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value"><?php echo displayField($patient['full_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value"><?php echo displayField($patient['date_of_birth']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Gender</span>
                        <span class="detail-value"><?php echo displayField($patient['gender']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Blood Group</span>
                        <span class="detail-value"><?php echo !empty($patient['blood_group']) ? '<span class="badge blood-group">' . htmlspecialchars($patient['blood_group']) . '</span>' : displayField(''); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Height</span>
                        <span class="detail-value"><?php echo displayField($patient['height']); ?> cm</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Weight</span>
                        <span class="detail-value"><?php echo displayField($patient['weight']); ?> kg</span>
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="ri-contacts-book-line"></i> Contact Details
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value"><?php echo displayField($patient['phone_number']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo displayField($patient['email']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address</span>
                        <span class="detail-value"><?php echo displayField($patient['address']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">City</span>
                        <span class="detail-value"><?php echo displayField($patient['city']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">State</span>
                        <span class="detail-value"><?php echo displayField($patient['state']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Postal Code</span>
                        <span class="detail-value"><?php echo displayField($patient['postal_code']); ?></span>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="ri-stethoscope-line"></i> Medical Information
                    </div>
                    <div class="detail-row" style="display: block;">
                        <span class="detail-label" style="display: block; margin-bottom: 0.5rem;">Allergies</span>
                        <div class="detail-value" style="text-align: left; max-width: 100%;">
                            <?php 
                                if (!empty($patient['allergies'])) {
                                    echo htmlspecialchars($patient['allergies']);
                                } else {
                                    echo displayField('');
                                }
                            ?>
                        </div>
                    </div>
                    <div class="detail-row" style="display: block; margin-top: 1rem;">
                        <span class="detail-label" style="display: block; margin-bottom: 0.5rem;">Chronic Conditions</span>
                        <div class="detail-value" style="text-align: left; max-width: 100%;">
                            <?php 
                                if (!empty($patient['chronic_conditions'])) {
                                    $conditions = explode(',', $patient['chronic_conditions']);
                                    foreach ($conditions as $cond) {
                                        echo '<span class="condition-tag">' . htmlspecialchars(trim($cond)) . '</span> ';
                                    }
                                } else {
                                    echo displayField(''); 
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="ri-alert-line"></i> Emergency Contact
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Contact Name</span>
                        <span class="detail-value"><?php echo displayField($patient['emergency_contact_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value"><?php echo displayField($patient['emergency_contact_phone']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Reports Section (Old UI) -->
            <section class="reports-section">
                <div class="section-header-row">
                    <h2 class="section-title">Reports & Scans</h2>
                    <a href="#" class="view-all">View All <i class="ri-arrow-right-s-line"></i></a>
                </div>
                
                <div class="reports-grid">
                    <div class="report-card">
                        <div class="file-icon icon-red"><i class="ri-file-pdf-line"></i></div>
                        <div class="report-info">
                            <div class="report-name">Blood Test Report</div>
                            <div class="report-date">Uploaded today</div>
                        </div>
                        <div>24/04/2024</div>
                    </div>
                    
                    <div class="report-card">
                        <div class="file-icon icon-blue"><i class="ri-image-line"></i></div>
                        <div class="report-info">
                            <div class="report-name">Chest X-Ray Scan</div>
                            <div class="report-date">Uploaded yesterday</div>
                        </div>
                        <div>23/04/2024</div>
                    </div>
                    
                     <div class="report-card">
                        <div class="file-icon icon-blue"><i class="ri-image-line"></i></div>
                        <div class="report-info">
                            <div class="report-name">Chest X-Ray</div>
                            <div class="report-date">2 days ago</div>
                        </div>
                        <div>23/01/2024</div>
                    </div>
                    
                    <div class="report-card">
                        <div class="file-icon icon-red"><i class="ri-file-pdf-line"></i></div>
                        <div class="report-info">
                            <div class="report-name">ECG Report</div>
                            <div class="report-date">Last Month</div>
                        </div>
                        <div>22/07/2023</div>
                    </div>
                </div>
            </section>

            <!-- Doctor Visits (Old UI) -->
            <section class="visits-section">
                 <div class="section-header-row">
                    <h2 class="section-title">My Doctor Visits</h2>
                    <a href="#" class="view-all">View All <i class="ri-arrow-right-s-line"></i></a>
                </div>
                
                <div class="doctors-grid">
                     <div class="visit-card">
                        <div class="visit-left">
                            <div class="doc-avatar">👨‍⚕️</div>
                            <div class="doc-info">
                                <h4>Dr. Mehta</h4>
                                <p>Cardiologist</p>
                            </div>
                        </div>
                        <div style="font-weight: 500;">12/01/2024</div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                    
                    <div class="visit-card">
                        <div class="visit-left">
                            <div class="doc-avatar">👩‍⚕️</div>
                            <div class="doc-info">
                                <h4>Dr. Rao</h4>
                                <p>Endocrinologist</p>
                            </div>
                        </div>
                        <div style="font-weight: 500;">05/03/2023</div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                </div>
            </section>

            <!-- Need Help (Old UI) -->
            <div class="help-container" style="margin-top: 3rem;">
                <div class="help-text">
                    <h3>Need Help? Contact Support</h3>
                    <p style="color: var(--text-light);">We are here to assist you 24/7</p>
                </div>
                <div class="contact-methods">
                    <div class="contact-item">
                        <i class="ri-mail-send-line"></i>
                        support@medbuddy.com
                    </div>
                    <div class="contact-item">
                        <i class="ri-phone-line"></i>
                        +1 800 123 4567
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Update Profile Modal -->
    <div id="updateModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Profile</h2>
                <button class="close-btn" onclick="closeUpdateModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm">
                    <!-- Personal Info -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($patient['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($patient['date_of_birth'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="">Select</option>
                                    <option value="Male" <?php if(($patient['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if(($patient['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if(($patient['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select name="blood_group">
                                    <option value="">Select</option>
                                    <option value="A+" <?php if(($patient['blood_group'] ?? '') == 'A+') echo 'selected'; ?>>A+</option>
                                    <option value="A-" <?php if(($patient['blood_group'] ?? '') == 'A-') echo 'selected'; ?>>A-</option>
                                    <option value="B+" <?php if(($patient['blood_group'] ?? '') == 'B+') echo 'selected'; ?>>B+</option>
                                    <option value="B-" <?php if(($patient['blood_group'] ?? '') == 'B-') echo 'selected'; ?>>B-</option>
                                    <option value="O+" <?php if(($patient['blood_group'] ?? '') == 'O+') echo 'selected'; ?>>O+</option>
                                    <option value="O-" <?php if(($patient['blood_group'] ?? '') == 'O-') echo 'selected'; ?>>O-</option>
                                    <option value="AB+" <?php if(($patient['blood_group'] ?? '') == 'AB+') echo 'selected'; ?>>AB+</option>
                                    <option value="AB-" <?php if(($patient['blood_group'] ?? '') == 'AB-') echo 'selected'; ?>>AB-</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Height (cm)</label>
                                <input type="number" name="height" value="<?php echo htmlspecialchars($patient['height'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="number" name="weight" value="<?php echo htmlspecialchars($patient['weight'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info (Address) -->
                    <div class="form-section">
                        <h3>Address Details</h3>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($patient['address'] ?? ''); ?>">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($patient['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" value="<?php echo htmlspecialchars($patient['state'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code" value="<?php echo htmlspecialchars($patient['postal_code'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Read-only Security Info -->
                    <div class="form-section">
                        <h3>Account Info (Read Only)</h3>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" value="<?php echo htmlspecialchars($patient['email'] ?? ''); ?>" disabled style="background: #e5e7eb; cursor: not-allowed;">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" value="<?php echo htmlspecialchars($patient['phone_number'] ?? ''); ?>" disabled style="background: #e5e7eb; cursor: not-allowed;">
                            </div>
                        </div>
                    </div>

                    <!-- Medical & Emergency -->
                    <div class="form-section">
                        <h3>Medical & Emergency</h3>
                        <div class="form-group">
                            <label>Allergies (Comma separated)</label>
                            <input type="text" name="allergies" value="<?php echo htmlspecialchars($patient['allergies'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Chronic Conditions (Comma separated)</label>
                            <input type="text" name="chronic_conditions" value="<?php echo htmlspecialchars($patient['chronic_conditions'] ?? ''); ?>">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" value="<?php echo htmlspecialchars($patient['emergency_contact_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" value="<?php echo htmlspecialchars($patient['emergency_contact_phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Styles for Modal -->
    <style>
        .btn-update {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        .btn-update:hover {
            background-color: var(--primary-hover);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 800px;
            border-radius: 16px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 1.25rem;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }

        .modal-body {
            padding: 2rem;
            overflow-y: auto;
        }

        .form-section {
            margin-bottom: 2rem;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 1rem;
        }

        .form-section h3 {
            font-size: 1rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
        }

        .form-actions {
            text-align: right;
            position: sticky;
            bottom: -2rem;
            background: white;
            padding: 1rem 0;
            border-top: 1px solid var(--border-color);
        }

        .btn-save {
            padding: 0.75rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>

    <script>
        function openUpdateModal() {
            document.getElementById('updateModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Build simple object from form data
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch('update_patient_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Profile updated successfully!');
                    window.location.reload(); // Refresh to fetch new data
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            });
        });

        // Close on outside click
        document.getElementById('updateModal').addEventListener('click', function(e) {
            if (e.target === this) closeUpdateModal();
        });
    </script>
</body>
</html>
