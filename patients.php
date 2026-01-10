<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Med Buddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Using Remix Icon for premium icons found in the screenshot -->
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

        /* Layout Grid */
        .grid-layout {
            display: grid;
            grid-template-columns: 1.5fr 1fr; /* Summary vs Appointment */
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Health Summary Card */
        .summary-card {
            background-color: var(--secondary-color);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
            position: relative;
        }
        
        .summary-header {
            background-color: #FEF3C7; /* Soft yellow accent from screenshot */
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
        
        /* Reports Grid */
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
            <li><a href="#"><i class="ri-file-list-3-line"></i> My Records</a></li>
            <li><a href="appointment.php"><i class="ri-calendar-check-line"></i> Book Appointment</a></li>
            <li><a href="#"><i class="ri-notification-3-line"></i> Notifications</a></li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Header -->
        <div class="top-bar">
            <div class="top-bar-info">
                <div class="patient-id">Patient ID: <span>PAT-2025-ABX123</span></div>
                <div class="user-profile">
                    <span>Welcome, Rahul Verma</span> | <a href="index.html" class="logout-link">Logout</a>
                </div>
            </div>
        </div>

        <div class="dashboard-container">
            
            <!-- Welcome Header -->
            <div class="welcome-section">
                <div class="welcome-text">
                    <h1>Welcome, Rahul Verma</h1>
                    <p>Patient ID: PAT-2025-ABX113</p>
                </div>
                <!-- Abstract Doctor Image from screenshot suggestion -->
                <!-- Ideally this would be an actual image asset -->
            </div>

            <div class="grid-layout">
                <!-- Left Column: Health Summary -->
                <div class="summary-card">
                    <div class="summary-header">Patient Health Summary</div>
                    <div class="summary-content">
                        <div class="info-row"><span>Name:</span> Rahul Verma</div>
                        <div class="info-row"><span>Age:</span> 45</div>
                        <div class="info-row"><span>Blood Group:</span> B+</div>
                        <div class="info-row"><span>Conditions:</span> Diabetes, Hypertension</div>
                        
                        <a href="#" class="download-link">
                            <i class="ri-download-cloud-2-line"></i> Download All Records
                        </a>
                    </div>
                </div>

                <!-- Right Column: Appointment -->
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

            <!-- Reports Section -->
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

            <!-- Doctor Visits -->
            <section class="visits-section">
                 <div class="section-header-row">
                    <h2 class="section-title">My Doctor Visits</h2>
                    <a href="#" class="view-all">View All <i class="ri-arrow-right-s-line"></i></a>
                </div>
                
                <div class="doctors-grid">
                    <!-- Since grid was used in index, let's reuse a similar list feeling for visits -->
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

            <!-- Need Help -->
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

</body>

</html>
