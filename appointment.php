<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Book Doctor Appointments - Med Buddy. Select from our top specialists across various departments.">
    <title>Book Appointment - Med Buddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

            /* Specific for Appointment Cards */
            --card-header-bg: #EAEAEA;
            --btn-yellow: #0066CC;
            --btn-yellow-hover: #0066CC;
            
            --container-padding: 5%;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: #F9FAFB;
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
        }

        /* --- Header (Copied from Index) --- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem var(--container-padding);
            background-color: var(--white);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
        }

        .logo span {
            color: var(--text-dark);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2.5rem;
        }

        nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
        }

     


        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-text {
            color: var(--primary-color);
            background: transparent;
            font-weight: 600;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

     

        /* --- Page Layout --- */
        .page-header {
            background: linear-gradient(135deg, #F8FAFC 0%, var(--secondary-color) 100%);
            padding: 4rem 5%;
            margin-bottom: 2rem;
            display: flex;
            justify-content: center; /* Center the content */
            align-items: center;
            position: relative;
        }

        .filter-container {
            position: absolute;
            left: 5%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .header-text {
            text-align: center;
            max-width: 800px;
        }

        /* Custom Dropdown Styles */
        .custom-dropdown {
            position: relative;
            min-width: 280px;
            font-family: 'Inter', sans-serif;
        }

        .dropdown-selected {
            background-color: var(--white);
            color: var(--text-dark);
            padding: 0.8rem 1.5rem;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .dropdown-selected:hover {
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.15);
            transform: translateY(-2px);
            border-color: var(--primary-color);
        }

        .dropdown-selected::after {
            content: "";
            width: 1rem;
            height: 1rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%230066CC' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: contain;
            transition: transform 0.3s ease;
        }

        .custom-dropdown.open .dropdown-selected::after {
            transform: rotate(180deg);
        }

        .dropdown-options {
            position: absolute;
            top: 120%;
            left: 0;
            right: 0;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            display: none;
            z-index: 100;
            animation: fadeIn 0.2s ease;
        }

        .custom-dropdown.open .dropdown-options {
            display: block;
        }

        .dropdown-option {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: background 0.2s;
            color: var(--text-dark);
        }

        .dropdown-option:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }
        
        .dropdown-option.selected {
            background-color: var(--primary-color);
            color: white;
        }

        /* Responsive Adjustments for Filter */
        /* Responsive Adjustments for Filter */
        @media (max-width: 900px) {
            .page-header {
                flex-direction: column;
                gap: 1.5rem;
                padding-top: 4rem;
                text-align: center;
            }
            .filter-container {
                position: relative;
                left: auto;
                top: auto;
                transform: none;
                width: 100%;
                order: 2; /* Filter below text */
            }
            .header-text {
                text-align: center;
                order: 1; /* Text on top */
            }
            .custom-dropdown {
                width: 100%;
            }
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .page-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* --- Doctor Sections --- */
        .department-section {
            padding: 3rem var(--container-padding);
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2rem;
            color: #003366; /* Dark Navy from image reference style */
            font-weight: 700;
            margin-bottom: 2rem;
            border-left: 5px solid var(--btn-yellow);
            padding-left: 1rem;
        }

        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        /* --- Doctor Card Design --- */
        .doctor-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px; /* Slightly sharper as per image */
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        
        .card-image-container {
            background-color: var(--card-header-bg);
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .doctor-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background-color: #ddd; /* Placeholder color */
        }
        
        .doctor-img-placeholder {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background-color: #D1D5DB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border: 4px solid var(--white);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .card-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }

        .doctor-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .doctor-designation {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-weight: 400;
            line-height: 1.4;
        }

        .doctor-department {
            font-size: 1rem;
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .btn-request {
            background-color: var(--btn-yellow);
            color: #FFFFFF; /* Dark text for contrast */
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            text-transform: capitalize;
        }

     

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 2rem;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

      
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border: none;
            width: 100%;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }


        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .time-slot {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            background: #f9f9f9;
        }

        .time-slot.selected {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        #availability-message {
            margin-top: 10px;
            font-size: 0.9rem;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
            }
            
            nav ul {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }

        /* --- Doctor Card Premium Design Overrides --- */
        .doctor-card {
            background: var(--white);
            border: none !important; /* Override previous border */
            border-radius: 20px !important;
            overflow: visible !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            text-align: center;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            position: relative;
        }

    

        .card-image-container {
            background: linear-gradient(to bottom, var(--secondary-color), transparent) !important;
            padding: 2.5rem 1rem 1.5rem !important;
            border-radius: 20px 20px 0 0;
            position: relative;
        }

        .doctor-img-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            border: 4px solid var(--white);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            margin: 0 auto;
        }

        .card-content {
            padding: 2rem !important;
        }

        .doctor-name {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            letter-spacing: -0.5px;
        }

        .doctor-designation {
            font-size: 0.9rem;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doctor-department {
            font-size: 0.95rem;
            margin-bottom: 2rem;
            background: #f3f4f6;
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .btn-request {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: #FFFFFF;
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 102, 204, 0.2);
        }


    </style>
</head>

<body>

    <!-- Header Navigation -->
    <header>
        <a href="index.html" class="logo">
            <span>Med Buddy</span>
        </a>

        <nav>
            <ul>
                <li><a href="index.html#home">Home</a></li>
                <li><a href="index.html#about">About</a></li>
                <li><a href="index.html#features">Features</a></li>
                <li><a href="index.html#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <a href="index.html#login" class="btn-text">Login</a>
            <a href="index.html#register" class="btn-text">Register</a>
        </div>
    </header>

    <!-- Page Title & Filter -->
    <div class="page-header">
        <div class="filter-container">
             <div class="custom-dropdown" id="hospitalDropdown">
                <input type="hidden" id="hospitalFilter" value="all">
                <div class="dropdown-selected" onclick="toggleDropdown()">All Hospitals</div>
                <div class="dropdown-options">
                    <div class="dropdown-option selected" onclick="selectHospital('all', 'All Hospitals')">All Hospitals</div>
                    <div class="dropdown-option" onclick="selectHospital('1', 'City General Hospital')">City General Hospital</div>
                    <div class="dropdown-option" onclick="selectHospital('2', 'Green Valley Clinic')">Green Valley Clinic</div>
                    <div class="dropdown-option" onclick="selectHospital('3', 'St. Mary Medical Center')">St. Mary Medical Center</div>
                    <div class="dropdown-option" onclick="selectHospital('4', 'Global Health Institute')">Global Health Institute</div>
                    <div class="dropdown-option" onclick="selectHospital('5', 'Community Care Center')">Community Care Center</div>
                </div>
            </div>
        </div>
        <div class="header-text">
            <h1>Book an Appointment</h1>
            <p>Choose from our specialized doctors and book your slot today.</p>
        </div>
    </div>

    <!-- Section 1: Cardiac Sciences -->
    <section class="department-section">
        <h2 class="section-title">Cardiac Sciences</h2>
        <div class="doctors-grid">
            <!-- Doc 1 (Hospital 1) -->
            <div class="doctor-card" data-hospital="1">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👨‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Jyotirmaya Dash</h3>
                    <p class="doctor-designation">Senior Consultant - Interventional Cardiologist</p>
                    <p class="doctor-department">Cardiology</p>
                    <button class="btn-request" onclick="openBookingModal(1, 'Dr. Jyotirmaya Dash')">Request Appointment</button>
                </div>
            </div>
            <!-- Doc 2 (Hospital 1) -->
            <div class="doctor-card" data-hospital="1">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👩‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Sarah Smith</h3>
                    <p class="doctor-designation">Consultant - Cardiac Surgeon</p>
                    <p class="doctor-department">Cardiology</p>
                    <button class="btn-request" onclick="openBookingModal(2, 'Dr. Sarah Smith')">Request Appointment</button>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Section 2: Neurology -->
    <section class="department-section">
        <h2 class="section-title">Neurology</h2>
        <div class="doctors-grid">
            <!-- Doc 1 (Hospital 2) -->
            <div class="doctor-card" data-hospital="2">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👨‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Alex Doe</h3>
                    <p class="doctor-designation">Senior Consultant - Neurologist</p>
                    <p class="doctor-department">Neurology</p>
                    <button class="btn-request" onclick="openBookingModal(3, 'Dr. Alex Doe')">Request Appointment</button>
                </div>
            </div>
            <!-- Doc 2 (Hospital 2) -->
            <div class="doctor-card" data-hospital="2">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👩‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Emily Blunt</h3>
                    <p class="doctor-designation">Specialist - Neurosurgeon</p>
                    <p class="doctor-department">Neurology</p>
                    <button class="btn-request" onclick="openBookingModal(4, 'Dr. Emily Blunt')">Request Appointment</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Orthopedics -->
    <section class="department-section">
        <h2 class="section-title">Orthopedics</h2>
        <div class="doctors-grid">
            <!-- Doc 1 (Hospital 3) -->
            <div class="doctor-card" data-hospital="3">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👨‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Mark Wood</h3>
                    <p class="doctor-designation">Senior Consultant - Orthopedic Surgeon</p>
                    <p class="doctor-department">Orthopedics</p>
                    <button class="btn-request" onclick="openBookingModal(5, 'Dr. Mark Wood')">Request Appointment</button>
                </div>
            </div>
            <!-- Doc 2 (Hospital 3) -->
            <div class="doctor-card" data-hospital="3">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👩‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Lisa Ray</h3>
                    <p class="doctor-designation">Sports Medicine Specialist</p>
                    <p class="doctor-department">Orthopedics</p>
                    <button class="btn-request" onclick="openBookingModal(6, 'Dr. Lisa Ray')">Request Appointment</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Pediatrics -->
    <section class="department-section">
        <h2 class="section-title">Pediatrics</h2>
        <div class="doctors-grid">
            <!-- Doc 1 (Hospital 4) -->
            <div class="doctor-card" data-hospital="4">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👩‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. John Green</h3>
                    <p class="doctor-designation">Senior Consultant - Pediatrician</p>
                    <p class="doctor-department">Pediatrics</p>
                    <button class="btn-request" onclick="openBookingModal(7, 'Dr. John Green')">Request Appointment</button>
                </div>
            </div>
            <!-- Doc 2 (Hospital 4) -->
            <div class="doctor-card" data-hospital="4">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👨‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Mary Pop</h3>
                    <p class="doctor-designation">Pediatric Surgeon</p>
                    <p class="doctor-department">Pediatrics</p>
                    <button class="btn-request" onclick="openBookingModal(8, 'Dr. Mary Pop')">Request Appointment</button>
                </div>
            </div>
            
        </div>
        
    </section>

    <!-- Section 5: General Medicine -->
    <section class="department-section">
        <h2 class="section-title">General Medicine</h2>
        <div class="doctors-grid">
            <!-- Doc 1 (Hospital 5) -->
            <div class="doctor-card" data-hospital="5">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👨‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. James King</h3>
                    <p class="doctor-designation">Consultant - General Physician</p>
                    <p class="doctor-department">General Medicine</p>
                    <button class="btn-request" onclick="openBookingModal(9, 'Dr. James King')">Request Appointment</button>
                </div>
            </div>
            <!-- Doc 2 (Hospital 5) -->
            <div class="doctor-card" data-hospital="5">
                <div class="card-image-container">
                    <div class="doctor-img-placeholder">👩‍⚕️</div>
                </div>
                <div class="card-content">
                    <h3 class="doctor-name">Dr. Anna Scott</h3>
                    <p class="doctor-designation">Senior Consultant - Internal Medicine</p>
                    <p class="doctor-department">General Medicine</p>
                    <button class="btn-request" onclick="openBookingModal(10, 'Dr. Anna Scott')">Request Appointment</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBookingModal()">&times;</span>
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Book Appointment</h2>
            <p id="modalDeviceName" style="margin-bottom: 1.5rem; font-weight: 600;"></p>
            
            <form id="bookingForm">
                <input type="hidden" id="doctorId" name="doctor_id">
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="date" required onchange="checkAvailability()">
                </div>

                <div class="form-group">
                    <label>Available Time Slots</label>
                    <div id="timeSlotsContainer" class="time-slots">
                        <p style="color: #666; font-size: 0.9rem;">Select a date to see available slots</p>
                    </div>
                    <input type="hidden" id="selectedTime" name="time">
                    <p id="availability-message"></p>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn" disabled>Confirm Booking</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("bookingModal");
        const form = document.getElementById("bookingForm");
        const timeSlotsContainer = document.getElementById("timeSlotsContainer");
        const submitBtn = document.getElementById("submitBtn");

        /* Custom Dropdown Logic */
        function toggleDropdown() {
            document.getElementById('hospitalDropdown').classList.toggle('open');
        }

        function selectHospital(value, text) {
            document.getElementById('hospitalFilter').value = value;
            document.querySelector('.dropdown-selected').innerText = text;
            document.getElementById('hospitalDropdown').classList.remove('open');
            
            // Re-render UI
            document.querySelectorAll('.dropdown-option').forEach(opt => {
                opt.classList.remove('selected');
                if(opt.innerText === text) opt.classList.add('selected');
            });
            
            filterDoctors();
        }

        function filterDoctors() {
            const selectedHospital = document.getElementById("hospitalFilter").value;
            const doctorCards = document.querySelectorAll(".doctor-card");
            const sections = document.querySelectorAll(".department-section");

            doctorCards.forEach(card => {
                if (selectedHospital === "all" || card.dataset.hospital === selectedHospital) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });

            // Hide empty sections
            sections.forEach(section => {
                const visibleDoctors = section.querySelectorAll(".doctor-card[style='display: flex;']").length;
                let hasVisible = false;
                 // Re-check visibility style
                 section.querySelectorAll(".doctor-card").forEach(c => {
                     if(c.style.display !== 'none') hasVisible = true;
                 });
                 
                 if(hasVisible) {
                     section.style.display = "block";
                 } else {
                     section.style.display = "none";
                 }
            });
        }

        function openBookingModal(id, name) {
            document.getElementById("doctorId").value = id;
            document.getElementById("modalDeviceName").innerText = "Booking with " + name;
            modal.style.display = "block";
            // Reset form
            form.reset();
            timeSlotsContainer.innerHTML = '<p style="color: #666; font-size: 0.9rem;">Select a date to see available slots</p>';
            submitBtn.disabled = true;
        }

        function closeBookingModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            // Dropdown close logic
            if (!event.target.matches('.dropdown-selected') && !event.target.matches('.dropdown-selected *')) {
                var dropdowns = document.getElementsByClassName("custom-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('open')) {
                        openDropdown.classList.remove('open');
                    }
                }
            }
            // Modal close logic
            if (event.target == modal) {
                closeBookingModal();
            }
        }

        function checkAvailability() {
            const doctorId = document.getElementById("doctorId").value;
            const date = document.getElementById("date").value;
            
            if (!date) return;

            // Simple AJAX to fetch slots
            fetch(`get_slots.php?doctor_id=${doctorId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    timeSlotsContainer.innerHTML = '';
                    if (data.success && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const div = document.createElement('div');
                            div.className = 'time-slot';
                            div.innerText = slot;
                            div.onclick = () => selectTime(div, slot);
                            timeSlotsContainer.appendChild(div);
                        });
                        document.getElementById("availability-message").innerText = "";
                    } else {
                        timeSlotsContainer.innerHTML = '<p style="color: red;">No slots available for this date.</p>';
                        document.getElementById("availability-message").innerText = data.message || "No slots available.";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    timeSlotsContainer.innerHTML = '<p>Error loading slots.</p>';
                });
        }

        function selectTime(element, time) {
            // Remove active class from all
            const slots = document.querySelectorAll('.time-slot');
            slots.forEach(s => s.classList.remove('selected'));
            
            // Add to clicked
            element.classList.add('selected');
            document.getElementById("selectedTime").value = time;
            submitBtn.disabled = false;
        }

        form.onsubmit = function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('book_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Appointment Booked Successfully!');
                    closeBookingModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>

</html>
